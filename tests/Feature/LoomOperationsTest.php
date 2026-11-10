<?php

namespace Tests\Feature;

use App\Models\BagTransaction;
use App\Models\Customer;
use App\Models\FabricStock;
use App\Models\FinancialAccount;
use App\Models\Invoice;
use App\Models\LoomAssignment;
use App\Models\Part;
use App\Models\PartaRecord;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SizingBeam;
use App\Models\SizingSlip;
use App\Models\ThaanRoll;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\YarnMovement;
use App\Models\YarnQuality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoomOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        YarnQuality::create(['name' => '30/1 PC Combed']);
        YarnQuality::create(['name' => '40/1 Cotton']);
    }

    public function test_bag_purchase_with_split_stores_and_creates_transactions(): void
    {
        $response = $this->actingAs($this->user)->post(route('bags.purchase'), [
            'challan_no' => 'CH-TEST-1',
            'supplier_name' => 'Test Spinning Mill',
            'quality' => '30/1 PC Combed',
            'total_bags' => 100,
            'bags_to_sizing' => 60,
            'bags_to_factory' => 40,
            'sizing_destination' => 'Test Sizing Mill',
            'rate_per_bag' => 14000.00,
            'purchase_date' => now()->toDateString(),
            'notes' => 'Test Split Order',
        ]);

        $response->assertRedirect(route('bags.index'));
        $this->assertDatabaseHas('bag_purchases', [
            'challan_no' => 'CH-TEST-1',
            'total_bags' => 100,
            'bags_to_sizing' => 60,
            'bags_to_factory' => 40,
        ]);
        $this->assertDatabaseHas('bag_transactions', [
            'reference_no' => 'CH-TEST-1',
            'transaction_type' => 'purchase_factory',
            'quantity_bags' => 40,
        ]);
        $this->assertDatabaseHas('bag_transactions', [
            'reference_no' => 'CH-TEST-1',
            'transaction_type' => 'purchase_sizing',
            'quantity_bags' => 60,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'bag_purchase',
            'source_id' => 1,
            'source_role' => 'inventory',
            'party_name' => 'Yarn Inventory',
            'debit' => 1400000,
        ]);
    }

    public function test_expenses_post_to_payee_and_cash_bank_ledgers_with_running_balances(): void
    {
        $account = FinancialAccount::create([
            'name' => 'Main Cash',
            'type' => 'cash',
            'opening_balance' => 1000,
            'is_active' => true,
        ]);

        $this->actingAs($this->user)->post(route('expenses.variable.store'), [
            'title' => 'Generator Fuel',
            'amount' => 75.50,
            'expense_date' => now()->toDateString(),
            'financial_account_id' => $account->id,
            'payee' => 'Fuel Vendor',
            'reference' => 'FUEL-1',
        ])->assertRedirect(route('expenses.index'));

        $this->actingAs($this->user)->post(route('expenses.variable.store'), [
            'title' => 'Generator Fuel',
            'amount' => 24.50,
            'expense_date' => now()->toDateString(),
            'financial_account_id' => $account->id,
            'payee' => 'Fuel Vendor',
            'reference' => 'FUEL-2',
        ])->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('ledgers', [
            'party_name' => 'Fuel Vendor',
            'party_type' => 'expense',
            'voucher_no' => 'FUEL-2',
            'debit' => 24.50,
            'balance' => 100.00,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'party_name' => 'Main Cash',
            'party_type' => 'cash_bank',
            'voucher_no' => 'FUEL-2',
            'credit' => 24.50,
            'balance' => -100.00,
        ]);

        $this->actingAs($this->user)->get(route('ledgers.index', ['party_type' => 'expense']))
            ->assertOk()
            ->assertSee('Fuel Vendor')
            ->assertSee('Generator Fuel');
    }

    public function test_invoice_and_customer_receipt_update_customer_and_account_ledgers(): void
    {
        $customer = Customer::create(['name' => 'Ledger Customer', 'status' => 'active', 'opening_balance' => 0]);
        $account = FinancialAccount::create(['name' => 'Receipt Bank', 'type' => 'bank', 'opening_balance' => 0, 'is_active' => true]);

        $this->actingAs($this->user)->post(route('invoices.store'), [
            'invoice_no' => 'INV-LEDGER-1',
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'items' => [['description' => 'Fabric', 'quantity' => 10, 'unit' => 'Meter', 'rate' => 100]],
        ])->assertRedirect(route('invoices.invoices'));

        $invoice = Invoice::where('invoice_no', 'INV-LEDGER-1')->firstOrFail();
        $this->assertSame(1000.0, (float) $invoice->total);

        $this->actingAs($this->user)->post(route('invoices.payment.store'), [
            'receipt_no' => 'RCPT-LEDGER-1',
            'customer_id' => $customer->id,
            'financial_account_id' => $account->id,
            'payment_date' => now()->toDateString(),
            'amount' => 400,
            'method' => 'bank_transfer',
            'allocation_mode' => 'invoice',
            'invoice_id' => $invoice->id,
        ])->assertRedirect(route('invoices.payments'));

        $this->assertDatabaseHas('ledgers', [
            'party_name' => 'Ledger Customer',
            'party_type' => 'fabric_buyer',
            'source_type' => 'payment_receipt',
            'source_role' => 'customer',
            'credit' => 400,
            'balance' => 600,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'party_name' => 'Receipt Bank',
            'party_type' => 'cash_bank',
            'source_type' => 'payment_receipt',
            'source_role' => 'account',
            'debit' => 400,
            'balance' => 400,
        ]);
    }

    public function test_bag_purchase_accepts_fractional_bags_and_keeps_all_records_in_sync(): void
    {
        $response = $this->actingAs($this->user)->post(route('bags.purchase'), [
            'challan_no' => 'CH-FRACTIONAL-PURCHASE',
            'supplier_name' => 'Test Spinning Mill',
            'quality' => '30/1 PC Combed',
            'total_bags' => 10.5,
            'bags_to_sizing' => 6.25,
            'bags_to_factory' => 4.25,
            'sizing_destination' => 'Test Sizing Mill',
            'rate_per_bag' => 14000.50,
            'purchase_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('bags.index'));
        $this->assertDatabaseHas('bag_purchases', [
            'challan_no' => 'CH-FRACTIONAL-PURCHASE',
            'total_bags' => 10.5,
            'bags_to_sizing' => 6.25,
            'bags_to_factory' => 4.25,
            'total_amount' => 147005.25,
        ]);
        $this->assertDatabaseHas('bag_transactions', [
            'reference_no' => 'CH-FRACTIONAL-PURCHASE',
            'transaction_type' => 'purchase_factory',
            'quantity_bags' => 4.25,
        ]);
        $this->assertDatabaseHas('bag_transactions', [
            'reference_no' => 'CH-FRACTIONAL-PURCHASE',
            'transaction_type' => 'purchase_sizing',
            'quantity_bags' => 6.25,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'voucher_no' => 'CH-FRACTIONAL-PURCHASE',
            'credit' => 147005.25,
        ]);
    }

    public function test_new_yarn_quality_saves_its_fabric_specification_and_truncated_weight_calculation(): void
    {
        $response = $this->actingAs($this->user)->post(route('yarn-qualities.store'), [
            'name' => '60 Read Quality',
            'construction' => '60x58 / 63 inch',
            'read' => 60,
            'pick' => 58,
            'warp_count' => 58.5,
            'weft_count' => 60,
            'width_inches' => 63,
            'unit_cost_meter' => 125.75,
        ]);

        $response->assertRedirect();
        $quality = YarnQuality::where('name', '60 Read Quality')->firstOrFail();
        $this->assertSame('60x58 / 63 inch', $quality->construction);
        $this->assertSame(125.75, (float) $quality->unit_cost_meter);
        $this->assertSame(0.0883, (float) $quality->warp_weight_per_meter_lb);
        $this->assertSame(0.0832, (float) $quality->weft_weight_per_meter_lb);
        $this->assertSame(0.1715, (float) $quality->total_weight_per_meter_lb);
    }

    public function test_sizing_slip_creates_beams_and_tracks_returned_bags(): void
    {
        $bagTransaction = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-SIZING-10',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 50,
            'source' => 'Test Spinning Mill',
            'destination' => 'Kohinoor Sizing',
            'party_name' => 'Kohinoor Sizing',
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->post(route('sizing.slip.store'), [
            'slip_type' => 'raw_perch',
            'slip_no' => 'SZ-TEST-10',
            'sizing_party' => 'Kohinoor Sizing',
            'yarn_quality' => '40/1 Cotton',
            'bags_allocated' => 50,
            'bags_used' => 45,
            'bags_returned_to_factory' => 3,
            'bags_held_with_sizing' => 2,
            'total_beams' => 3,
            'beam_names' => ['Beam A', 'Beam B', 'Beam C'],
            'back_beam_count' => 5,
            'width_multiplier' => 2,
            'set_length_value' => 12000.00,
            'set_length_unit' => 'meters',
            'total_ends' => 10080,
            'sizing_weight_kg' => 2266,
            'bag_transaction_id' => $bagTransaction->id,
            'slip_date' => now()->toDateString(),
            'notes' => 'Test sizing batch',
        ]);

        $response->assertRedirect(route('sizing.index'));
        $this->assertDatabaseHas('sizing_slips', [
            'slip_no' => 'SZ-TEST-10',
            'bags_used' => 45,
            'bags_returned_to_factory' => 3,
            'bags_held_with_sizing' => 2,
            'back_beam_count' => 5,
            'width_multiplier' => 2,
            'total_length_meters' => 12000,
        ]);

        // Beam records store physical warp length; the slip tracks cloth output separately.
        $this->assertEquals(3, SizingBeam::count());
        $this->assertDatabaseHas('sizing_beams', [
            'yarn_quality' => '40/1 Cotton',
            'total_length_meters' => 4000.00,
        ]);
        $this->assertEquals(24000, SizingSlip::first()->expectedFabricMeters());
        $this->assertEquals(8000, SizingSlip::first()->expectedFabricMetersPerBeam());
        $this->assertNotNull(SizingSlip::first()->estimatedWarpCountNe());

        // Verify returned bags logged to factory store
        $this->assertDatabaseHas('bag_transactions', [
            'transaction_type' => 'return_from_sizing',
            'quantity_bags' => 3,
        ]);
    }

    public function test_beam_lengths_preserve_the_exact_set_total_and_each_beam_keeps_its_slip_quality(): void
    {
        $firstReceipt = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-BEAM-A',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 20,
            'source' => 'Test Spinning Mill',
            'destination' => 'Sizing Mill A',
            'party_name' => 'Sizing Mill A',
            'transaction_date' => now()->toDateString(),
        ]);
        $secondReceipt = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-BEAM-B',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 20,
            'source' => 'Test Spinning Mill',
            'destination' => 'Sizing Mill B',
            'party_name' => 'Sizing Mill B',
            'transaction_date' => now()->toDateString(),
        ]);

        foreach ([[$firstReceipt, 'SZ-BEAM-A'], [$secondReceipt, 'SZ-BEAM-B']] as [$receipt, $slipNumber]) {
            $response = $this->actingAs($this->user)->post(route('sizing.slip.store'), [
                'slip_no' => $slipNumber,
                'slip_type' => 'raw_perch',
                'sizing_party' => 'Ignored form party',
                'yarn_quality' => '30/1 PC Combed',
                'bags_allocated' => 20,
                'bags_used' => 20,
                'bags_returned_to_factory' => 0,
                'bags_held_with_sizing' => 0,
                'total_beams' => 4,
                'beam_names' => ['A', 'B', 'C', 'D'],
                'width_multiplier' => 1,
                'set_length_value' => 1000,
                'set_length_unit' => 'meters',
                'total_ends' => 1000,
                'bag_transaction_id' => $receipt->id,
                'slip_date' => now()->toDateString(),
            ]);

            $response->assertRedirect(route('sizing.index'));
        }

        $firstSlip = SizingSlip::where('slip_no', 'SZ-BEAM-A')->firstOrFail();
        $secondSlip = SizingSlip::where('slip_no', 'SZ-BEAM-B')->firstOrFail();
        $this->assertSame('30/1 PC Combed', $firstSlip->yarn_quality);
        $this->assertSame('40/1 Cotton', $secondSlip->yarn_quality);
        $this->assertSame(1000.0, (float) $firstSlip->beams()->sum('total_length_meters'));
        $this->assertSame(1000.0, (float) $secondSlip->beams()->sum('total_length_meters'));
        $this->assertSame([250.0, 250.0, 250.0, 250.0], $firstSlip->beams()->orderBy('id')->pluck('total_length_meters')->map(fn ($length): float => (float) $length)->all());
        $this->assertSame(['30/1 PC Combed'], $firstSlip->beams()->distinct()->pluck('yarn_quality')->all());
        $this->assertSame(['40/1 Cotton'], $secondSlip->beams()->distinct()->pluck('yarn_quality')->all());
    }

    public function test_beam_lengths_distribute_centimeter_remainders_without_losing_set_length(): void
    {
        $receipt = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-BEAM-REMAINDER',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 10,
            'source' => 'Test Spinning Mill',
            'destination' => 'Sizing Mill',
            'party_name' => 'Sizing Mill',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->user)->post(route('sizing.slip.store'), [
            'slip_no' => 'SZ-BEAM-REMAINDER',
            'slip_type' => 'raw_perch',
            'sizing_party' => 'Sizing Mill',
            'yarn_quality' => '40/1 Cotton',
            'bags_allocated' => 10,
            'bags_used' => 10,
            'bags_returned_to_factory' => 0,
            'bags_held_with_sizing' => 0,
            'total_beams' => 3,
            'beam_names' => ['A', 'B', 'C'],
            'width_multiplier' => 1,
            'set_length_value' => 1000.01,
            'set_length_unit' => 'meters',
            'total_ends' => 1000,
            'bag_transaction_id' => $receipt->id,
            'slip_date' => now()->toDateString(),
        ])->assertRedirect(route('sizing.index'));

        $slip = SizingSlip::where('slip_no', 'SZ-BEAM-REMAINDER')->firstOrFail();
        $this->assertSame([333.34, 333.34, 333.33], $slip->beams()->orderBy('id')->pluck('total_length_meters')->map(fn ($length): float => (float) $length)->all());
        $this->assertSame(1000.01, (float) $slip->beams()->sum('total_length_meters'));
    }

    public function test_dashboard_stock_totals_are_summed_across_independent_yarn_quality_balances(): void
    {
        BagTransaction::create([
            'transaction_type' => 'purchase_factory',
            'reference_no' => 'CH-QUALITY-A',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 10,
            'source' => 'Supplier',
            'destination' => 'Factory Yarn Store',
            'transaction_date' => now()->toDateString(),
        ]);
        BagTransaction::create([
            'transaction_type' => 'purchase_factory',
            'reference_no' => 'CH-QUALITY-B',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 2,
            'source' => 'Supplier',
            'destination' => 'Factory Yarn Store',
            'transaction_date' => now()->toDateString(),
        ]);
        BagTransaction::create([
            'transaction_type' => 'issue_to_sizing',
            'reference_no' => 'ISSUE-QUALITY-A',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 8,
            'source' => 'Factory Yarn Store',
            'destination' => 'Sizing Mill',
            'party_name' => 'Sizing Mill',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->user)->get(route('bags.index'))
            ->assertOk()
            ->assertSee('Stock by Yarn Quality')
            ->assertSee('40/1 Cotton')
            ->assertSee('2.00', false);
    }

    public function test_manual_return_requires_matching_factory_issue_and_cannot_exceed_outstanding_quantity(): void
    {
        BagTransaction::create([
            'transaction_type' => 'purchase_factory',
            'reference_no' => 'CH-RETURN-BASE',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 10,
            'source' => 'Supplier',
            'destination' => 'Factory Yarn Store',
            'transaction_date' => now()->toDateString(),
        ]);
        BagTransaction::create([
            'transaction_type' => 'issue_to_sizing',
            'reference_no' => 'ISSUE-RETURN-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 3,
            'source' => 'Factory Yarn Store',
            'destination' => 'Sizing Mill',
            'party_name' => 'Sizing Contractor',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->user)->post(route('bags.return'), [
            'reference_no' => 'ISSUE-RETURN-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 3.01,
            'source' => 'Sizing Mill',
            'party_name' => 'Sizing Contractor',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect();
        $this->assertDatabaseMissing('bag_transactions', ['transaction_type' => 'return_from_sizing']);

        $this->actingAs($this->user)->post(route('bags.return'), [
            'reference_no' => 'ISSUE-RETURN-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 2.5,
            'source' => 'Sizing Mill',
            'party_name' => 'Sizing Contractor',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('bags.index'));
        $this->assertDatabaseHas('bag_transactions', [
            'transaction_type' => 'return_from_sizing',
            'reference_no' => 'ISSUE-RETURN-1',
            'quantity_bags' => 2.5,
        ]);

        $this->actingAs($this->user)->post(route('bags.return'), [
            'reference_no' => 'ISSUE-RETURN-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 0.5,
            'source' => 'Sizing Mill',
            'party_name' => 'Sizing Contractor',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('bags.index'));

        $this->actingAs($this->user)->post(route('bags.return'), [
            'reference_no' => 'ISSUE-RETURN-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 0.01,
            'source' => 'Sizing Mill',
            'party_name' => 'Sizing Contractor',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect();
        $this->assertSame(3.0, (float) BagTransaction::where('transaction_type', 'return_from_sizing')->sum('quantity_bags'));
    }

    public function test_factory_bag_issue_links_to_order_yarn_khata_and_return_reverses_kg(): void
    {
        BagTransaction::create([
            'transaction_type' => 'purchase_factory', 'reference_no' => 'CH-LINKED-YARN',
            'quality' => '30/1 PC Combed', 'quantity_bags' => 10, 'source' => 'Supplier',
            'destination' => 'Factory Yarn Store', 'transaction_date' => now()->toDateString(),
        ]);
        $fabric = FabricStock::create([
            'quality_name' => 'Linked Yarn Test', 'construction' => '90x66', 'width_inches' => 63.5,
        ]);
        $order = WorkOrder::create([
            'order_no' => 'ORDER-LINKED-YARN', 'type' => 'own', 'fabric_stock_id' => $fabric->id,
            'start_date' => now()->toDateString(), 'warp_supply' => 'factory', 'weft_supply' => 'factory',
        ]);

        $this->actingAs($this->user)->post(route('bags.issue'), [
            'reference_no' => 'ISS-LINKED-YARN', 'quality' => '30/1 PC Combed', 'quantity_bags' => 2.5,
            'destination' => 'Test Sizing Mill', 'party_name' => 'Test Sizer',
            'transaction_date' => now()->toDateString(), 'work_order_id' => $order->id,
            'component' => 'warp', 'net_weight_kg' => 82.75,
        ])->assertRedirect(route('bags.index'))->assertSessionHasNoErrors();

        $issue = BagTransaction::where('reference_no', 'ISS-LINKED-YARN')->firstOrFail();
        $this->assertSame((int) $order->id, (int) $issue->work_order_id);
        $this->assertSame(82.75, (float) $issue->net_weight_kg);
        $this->assertDatabaseHas('yarn_movements', [
            'work_order_id' => $order->id, 'component' => 'warp', 'owner_type' => 'factory',
            'movement_type' => 'issue', 'weight_kg' => 82.75, 'bags' => 2.5,
            'reference_no' => 'ISS-LINKED-YARN',
        ]);

        $this->actingAs($this->user)->post(route('bags.return'), [
            'reference_no' => 'ISS-LINKED-YARN', 'quality' => '30/1 PC Combed', 'quantity_bags' => 1,
            'source' => 'Test Sizing Mill', 'party_name' => 'Test Sizer', 'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('bags.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('yarn_movements', [
            'work_order_id' => $order->id, 'component' => 'warp', 'owner_type' => 'factory',
            'movement_type' => 'return', 'weight_kg' => 33.1, 'bags' => 1,
            'reference_no' => 'ISS-LINKED-YARN-RETURN-3',
        ]);
        $this->assertSame(49.65, (float) YarnMovement::where('work_order_id', $order->id)
            ->where('component', 'warp')->whereIn('movement_type', ['issue', 'return'])
            ->selectRaw('SUM(CASE WHEN movement_type = "issue" THEN weight_kg ELSE -weight_kg END) as balance_kg')
            ->value('balance_kg'));
        $this->actingAs($this->user)->get(route('factory.reports'))
            ->assertOk()->assertSee('Taana 49.65 / Baana 0.00');
    }

    public function test_sizing_slip_records_unaccounted_bags_instead_of_blocking_the_slip(): void
    {
        $bagTransaction = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-GATE-PASS-98',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 52,
            'source' => 'Test Spinning Mill',
            'destination' => 'Kohinoor Sizing',
            'party_name' => 'Kohinoor Sizing',
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->post(route('sizing.slip.store'), [
            'slip_type' => 'raw_perch',
            'slip_no' => 'SZ-GATE-PASS-98',
            'sizing_party' => 'Kohinoor Sizing',
            'yarn_quality' => '40/1 Cotton',
            'bags_allocated' => 52,
            'bags_used' => 51,
            'bags_returned_to_factory' => 0,
            'bags_held_with_sizing' => 0,
            'total_beams' => 4,
            'beam_names' => ['Beam A', 'Beam B', 'Beam C', 'Beam D'],
            'back_beam_count' => 5,
            'width_multiplier' => 2,
            'set_length_value' => 22400,
            'set_length_unit' => 'gaz',
            'total_ends' => 10080,
            'sizing_gross_weight_kg' => 2550,
            'sizing_weight_kg' => 2266,
            'bag_transaction_id' => $bagTransaction->id,
            'slip_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('sizing.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('sizing_slips', [
            'slip_no' => 'SZ-GATE-PASS-98',
            'bags_allocated' => 52,
            'bags_used' => 51,
            'total_beams' => 4,
            'back_beam_count' => 5,
            'total_length_meters' => 20482.56,
            'width_multiplier' => 2,
        ]);
        $this->assertEquals(1, SizingSlip::first()->bags_allocated - SizingSlip::first()->bags_used);
        $this->assertEquals(40965.12, SizingSlip::first()->expectedFabricMeters());
        $this->assertEquals(4, SizingBeam::count());
        $this->assertEquals(5120.64, (float) SizingBeam::first()->total_length_meters);
    }

    public function test_sizing_slip_accepts_fractional_bag_usage_and_returns_fractional_stock(): void
    {
        $bagTransaction = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-FRACTIONAL-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 52,
            'source' => 'Test Spinning Mill',
            'destination' => 'Kohinoor Sizing',
            'party_name' => 'Kohinoor Sizing',
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->post(route('sizing.slip.store'), [
            'slip_no' => 'SZ-FRACTIONAL-1',
            'slip_type' => 'raw_perch',
            'sizing_party' => 'Kohinoor Sizing',
            'yarn_quality' => '40/1 Cotton',
            'bags_allocated' => 52,
            'bags_used' => 51.5,
            'bags_returned_to_factory' => 0.5,
            'bags_held_with_sizing' => 0,
            'total_beams' => 1,
            'beam_names' => ['Beam A'],
            'width_multiplier' => 1,
            'set_length_value' => 1000,
            'set_length_unit' => 'meters',
            'total_ends' => 10080,
            'bag_transaction_id' => $bagTransaction->id,
            'slip_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('sizing.index'));
        $response->assertSessionHas('success');

        $slip = SizingSlip::where('slip_no', 'SZ-FRACTIONAL-1')->firstOrFail();
        $this->assertSame(52.0, (float) $slip->bags_allocated);
        $this->assertSame(51.5, (float) $slip->bags_used);
        $this->assertSame(0.5, (float) $slip->bags_returned_to_factory);

        $returnedTransaction = BagTransaction::where('transaction_type', 'return_from_sizing')->firstOrFail();
        $this->assertSame(0.5, (float) $returnedTransaction->quantity_bags);

        $this->actingAs($this->user)
            ->get(route('bags.index'))
            ->assertOk()
            ->assertSee('0.50 bags');
    }

    public function test_blank_final_perch_returned_material_fields_are_saved_as_zero(): void
    {
        $bagTransaction = BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-FINAL-BLANK-1',
            'quality' => '40/1 Cotton',
            'quantity_bags' => 52,
            'source' => 'Test Spinning Mill',
            'destination' => 'Kohinoor Sizing',
            'party_name' => 'Kohinoor Sizing',
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->post(route('sizing.slip.store'), [
            'slip_no' => 'SZ-FINAL-BLANK-1',
            'slip_type' => 'final_perch',
            'sizing_party' => 'Kohinoor Sizing',
            'yarn_quality' => '40/1 Cotton',
            'bags_allocated' => 52,
            'bags_used' => 52,
            'bags_returned_to_factory' => 0,
            'bags_held_with_sizing' => 0,
            'total_beams' => 1,
            'beam_names' => ['Beam A'],
            'width_multiplier' => 1,
            'set_length_value' => 1000,
            'set_length_unit' => 'meters',
            'total_ends' => 10080,
            'sizing_gross_weight_kg' => 2550,
            'sizing_rate_per_kg' => 61,
            'bag_transaction_id' => $bagTransaction->id,
            'slip_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('sizing.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sizing_slips', [
            'slip_no' => 'SZ-FINAL-BLANK-1',
            'wet_cone_bags' => 0,
            'wet_cone_weight_kg' => 0,
            'empty_bag_count' => 0,
            'empty_bag_weight_kg' => 0,
            'paper_bag_count' => 0,
            'paper_bag_weight_kg' => 0,
            'sound_cone_count' => 0,
            'sound_cone_weight_kg' => 0,
            'carton_count' => 0,
            'carton_weight_kg' => 0,
            'returned_materials_weight_kg' => 0,
            'sizing_weight_pounds' => round(2550 / 0.45359237, 2),
        ]);
    }

    public function test_part_issuance_decrements_store_stock(): void
    {
        $part = Part::create([
            'part_name' => 'Rapier Gripper Clip',
            'part_code' => 'GRP-CLP',
            'category' => 'Gripper',
            'stock_quantity' => 10,
            'min_alert_threshold' => 2,
            'unit' => 'pcs',
            'unit_price' => 500.00,
        ]);

        $response = $this->actingAs($this->user)->post(route('parts.issue'), [
            'part_id' => $part->id,
            'loom_no' => 'Loom-05',
            'issued_to_person' => 'Master Aslam',
            'quantity' => 2,
            'issuance_date' => now()->toDateString(),
            'remarks' => 'Replacement clip',
        ]);

        $response->assertRedirect(route('parts.index'));
        $this->assertEquals(8, $part->fresh()->stock_quantity);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'part_issuance',
            'source_role' => 'expense',
            'party_name' => 'Factory Parts Expense',
            'debit' => 1000,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'part_issuance',
            'source_role' => 'inventory',
            'party_name' => 'Parts Inventory',
            'credit' => 1000,
        ]);
    }

    public function test_production_calculates_leakage_and_decrements_beam_length(): void
    {
        $slip = SizingSlip::create([
            'slip_no' => 'SZ-PROD-1',
            'sizing_party' => 'Test Sizing Mill',
            'yarn_quality' => '30/1 PC Combed',
            'bags_allocated' => 40,
            'bags_used' => 40,
            'total_beams' => 1,
            'total_length_meters' => 4000,
            'width_multiplier' => 2,
            'slip_date' => now()->toDateString(),
        ]);

        $fabric = FabricStock::create([
            'quality_name' => 'Twill 2/1',
            'construction' => '40x40',
            'width_inches' => 63,
            'current_stock_meters' => 0,
            'available_open_meters' => 0,
            'total_rolls_in_stock' => 0,
        ]);

        $beam = SizingBeam::create([
            'sizing_slip_id' => $slip->id,
            'beam_no' => 'BM-TEST-1',
            'yarn_quality' => '30/1 PC',
            'total_length_meters' => 4000.00,
            'remaining_length_meters' => 4000.00,
            'status' => 'mounted_on_loom',
            'assigned_loom_no' => 'Loom-01',
        ]);

        $order = WorkOrder::create([
            'order_no' => 'OWN-PROD-1', 'type' => 'own', 'fabric_stock_id' => $fabric->id,
            'start_date' => now()->toDateString(), 'warp_supply' => 'factory', 'weft_supply' => 'factory',
        ]);
        LoomAssignment::create([
            'work_order_id' => $order->id, 'loom_no' => 'Loom-01', 'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->post(route('production.store'), [
            'production_date' => now()->toDateString(),
            'shift' => 'Day',
            'loom_no' => 'Loom-01',
            'operator_name' => 'Tariq Weaver',
            'sizing_beam_id' => $beam->id,
            'fabric_stock_id' => $fabric->id,
            'start_meter' => 0.00,
            'end_meter' => 300.00, // 300m produced
            'beam_meters_consumed' => 310.00, // 310m taken -> 10m leakage
            'picks_per_inch' => 60,
            'rpm' => 350,
            'remarks' => 'Smooth shift',
        ]);

        $response->assertRedirect(route('production.index'));

        // Check production record
        $this->assertDatabaseHas('productions', [
            'loom_no' => 'Loom-01',
            'net_meters_produced' => 300.00,
            'beam_meters_consumed' => 310.00,
            'expected_fabric_meters' => 620.00,
            'leakage_waste_meters' => 320.00,
        ]);

        // Check beam remaining (4000 - 310 = 3690m)
        $this->assertEquals(3690.00, $beam->fresh()->remaining_length_meters);
        $this->assertEquals('mounted_on_loom', $beam->fresh()->status);
        // Loom readings are provisional; ONLY checked Thaans enter physical stock.
        $this->assertEquals(0, $fabric->fresh()->current_stock_meters);
        $this->assertEquals(0, $fabric->fresh()->daily_received_meters);
        $this->assertDatabaseHas('productions', ['work_order_id' => $order->id, 'is_provisional' => 1]);
    }

    public function test_production_leakage_summary_counts_only_used_beams_and_combines_looms_by_sizing_slip(): void
    {
        $slip = SizingSlip::create([
            'slip_no' => 'SZ-MULTI-LOOM-1',
            'sizing_party' => 'Sizing Mill',
            'yarn_quality' => '40/1 Cotton',
            'bags_allocated' => 40,
            'bags_used' => 40,
            'total_beams' => 4,
            'total_length_meters' => 1000,
            'width_multiplier' => 2,
            'slip_date' => now()->toDateString(),
        ]);

        $fabric = FabricStock::create([
            'quality_name' => 'Multi Loom Fabric',
            'construction' => '40x40',
            'width_inches' => 63,
            'current_stock_meters' => 0,
            'available_open_meters' => 0,
            'total_rolls_in_stock' => 0,
        ]);

        foreach (['Loom-01', 'Loom-02', 'Loom-03', null] as $index => $loom) {
            $beam = SizingBeam::create([
                'sizing_slip_id' => $slip->id,
                'beam_no' => 'BM-MULTI-'.$index,
                'yarn_quality' => '40/1 Cotton',
                'total_length_meters' => 250,
                'remaining_length_meters' => $loom === null ? 250 : 0,
                'status' => $loom === null ? 'ready_in_store' : 'exhausted',
                'assigned_loom_no' => $loom,
            ]);

            if ($loom === null) {
                continue;
            }

            Production::create([
                'production_date' => now()->toDateString(),
                'shift' => 'Day',
                'loom_no' => $loom,
                'operator_name' => 'Test Weaver',
                'sizing_beam_id' => $beam->id,
                'fabric_stock_id' => $fabric->id,
                'fabric_quality' => $fabric->quality_name,
                'start_meter' => 0,
                'end_meter' => 450,
                'net_meters_produced' => 450,
                'beam_meters_consumed' => 250,
                'expected_fabric_meters' => 500,
                'leakage_waste_meters' => 50,
                'leakage_percentage' => 10,
                'picks_per_inch' => 60,
                'rpm' => 350,
                'efficiency_percentage' => 90,
            ]);
        }

        $this->actingAs($this->user)->get(route('production.leakage'))
            ->assertOk()
            ->assertSee('Production by Sizing Slip')
            ->assertSee('SZ-MULTI-LOOM-1')
            ->assertSee('3 / 4')
            ->assertSee('1,500.00m')
            ->assertSee('1,350.00m')
            ->assertSee('3', false);

        $this->actingAs($this->user)->get(route('sizing.slips'))
            ->assertOk()
            ->assertSee('Expected Cloth')
            ->assertSee('150.00m short')
            ->assertSee('used warp: 1,500.00m expected');

        $this->actingAs($this->user)->get(route('sizing.beams'))
            ->assertOk()
            ->assertSee('Short / Over on Used Warp')
            ->assertSee('50.00m short')
            ->assertSee('against 500.00m expected on consumed warp')
            ->assertSee('No production yet');
    }

    public function test_sales_slip_decrements_available_fabric_stock(): void
    {
        $fabric = FabricStock::create([
            'quality_name' => 'Cotton Sheeting 68x68',
            'construction' => '30x30',
            'width_inches' => 63.0,
            'current_stock_meters' => 5000.00,
            'available_open_meters' => 5000.00,
            'total_rolls_in_stock' => 50,
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.store'), [
            'slip_no' => 'SLIP-TEST-1',
            'sale_type' => 'open_market',
            'buyer_name' => 'Test Cloth Merchant',
            'fabric_stock_id' => $fabric->id,
            'meters_sold' => 1000.00,
            'rolls_sold' => 10,
            'rate_per_meter' => 250.00,
            'discount' => 1000.00,
            'payment_type' => 'cash',
            'payment_status' => 'paid',
            'sale_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('sales.index'));
        $this->assertEquals(4000.00, $fabric->fresh()->current_stock_meters);
        $this->assertEquals(4000.00, $fabric->fresh()->available_open_meters);
        $this->assertEquals(40, $fabric->fresh()->total_rolls_in_stock);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'sale',
            'source_role' => 'buyer',
            'party_name' => 'Test Cloth Merchant',
            'debit' => 0,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'sale',
            'source_role' => 'cash',
            'party_name' => 'Cash / Bank Clearing',
            'debit' => 249000,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'sale',
            'source_role' => 'revenue',
            'party_name' => 'Fabric Sales Revenue',
            'credit' => 249000,
        ]);
    }

    public function test_own_sale_uses_and_saves_order_parta_cost_snapshot(): void
    {
        $fabric = FabricStock::create([
            'quality_name' => 'Frozen Parta Fabric', 'construction' => '90x66', 'width_inches' => 63.5,
            'unit_cost_meter' => 900, 'current_stock_meters' => 10,
            'available_open_meters' => 10, 'total_rolls_in_stock' => 1,
        ]);
        $parta = PartaRecord::create([
            'fabric_stock_id' => $fabric->id, 'version' => 1,
            'read' => 90, 'pick' => 66, 'width_inches' => 63.5,
            'warp_count' => 58.5, 'weft_count' => 58.5,
            'warp_rate_per_lb' => 600, 'weft_rate_per_lb' => 600,
            'conversion_rate_per_pick' => 0.5, 'warp_lb_per_meter' => 0.1,
            'weft_lb_per_meter' => 0.1, 'total_lb_per_meter' => 0.2,
            'kg_per_meter' => 0.09, 'grams_per_meter' => 90,
            'gsm' => 100, 'cost_per_meter' => 123.4567,
        ]);
        $order = WorkOrder::create([
            'order_no' => 'OWN-FROZEN-COST', 'type' => 'own', 'fabric_stock_id' => $fabric->id,
            'parta_record_id' => $parta->id, 'start_date' => now()->toDateString(),
            'warp_supply' => 'factory', 'weft_supply' => 'factory',
        ]);
        $roll = ThaanRoll::create([
            'roll_no' => 'OWN-FROZEN-TH-1', 'work_order_id' => $order->id,
            'fabric_stock_id' => $fabric->id, 'loom_no' => 'Loom-01',
            'checked_on' => now()->toDateString(), 'length_meters' => 10,
            'grade' => 'A', 'status' => 'ready', 'owner_type' => 'factory',
        ]);

        $this->actingAs($this->user)->post(route('factory.own-sale.store'), [
            'slip_no' => 'SALE-FROZEN-COST', 'roll_ids' => [$roll->id],
            'buyer_name' => 'Local Buyer', 'rate_per_meter' => 200,
            'sale_date' => now()->toDateString(),
        ])->assertSessionHasNoErrors();

        $sale = Sale::where('slip_no', 'SALE-FROZEN-COST')->firstOrFail();
        $this->assertEqualsWithDelta(123.4567, (float) $sale->cost_per_meter, 0.001);
        $this->assertSame(1234.57, (float) $sale->cost_of_goods_sold);
        $this->assertDatabaseHas('ledgers', [
            'source_type' => 'sale', 'source_id' => $sale->id,
            'source_role' => 'cost_of_goods_sold', 'debit' => 1234.57,
        ]);
    }
}
