<?php

namespace Tests\Feature;

use App\Models\FabricStock;
use App\Models\KachiParchi;
use App\Models\ThaanRoll;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end acceptance checks for the specific local grey-fabric factory rules.
 * Run with: php artisan test --filter=FactoryWorkflowTest
 */
class FactoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;
    private FabricStock $quality;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operator = User::factory()->create();
        $this->quality = FabricStock::create([
            'quality_name' => 'Test Grey 90x66', 'construction' => '90x66',
            'width_inches' => 63.5,
        ]);
        $this->actingAs($this->operator)->post(route('factory.parta'), [
            'fabric_stock_id' => $this->quality->id,
            'read' => 90, 'pick' => 66, 'width_inches' => 63.5,
            'warp_count' => 58.5, 'weft_count' => 58.5,
            'warp_rate_per_lb' => 600, 'weft_rate_per_lb' => 600,
            'conversion_rate_per_pick' => 0.50,
        ])->assertSessionHasNoErrors();
    }

    private function openOrder(string $type = 'job', string $orderNo = 'TEST-001'): WorkOrder
    {
        $input = [
            'order_no' => $orderNo, 'type' => $type,
            'fabric_stock_id' => $this->quality->id,
            'start_date' => '2026-11-01',
            'warp_supply' => $type === 'job' ? 'customer' : 'factory',
            'weft_supply' => 'factory',
            'rate_per_pick' => 0.50,
        ];
        if ($type === 'job') {
            $input += ['target_meters' => 1000, 'party_name' => 'ABC Textile', 'contract_no' => 'CON-'.$orderNo];
        }
        $this->actingAs($this->operator)->post(route('factory.order.store'), $input)->assertSessionHasNoErrors();
        return WorkOrder::where('order_no', $orderNo)->firstOrFail();
    }

    private function assignAndCheck(WorkOrder $order): array
    {
        $this->actingAs($this->operator)->post(route('factory.loom.assign'), [
            'work_order_id' => $order->id, 'loom_no' => 'Loom-01', 'start_date' => '2026-11-01',
        ])->assertSessionHasNoErrors();
        $this->actingAs($this->operator)->post(route('factory.checking.store'), [
            'checked_on' => '2026-11-02', 'loom_no' => 'Loom-01',
            'rolls' => [
                ['roll_no' => $order->order_no.'-TH-01', 'length_meters' => 60],
                ['roll_no' => $order->order_no.'-TH-02', 'length_meters' => 90],
            ],
        ])->assertSessionHasNoErrors();
        return ThaanRoll::where('work_order_id', $order->id)->orderBy('id')->pluck('id')->all();
    }

    public function test_parta_uses_local_800_divisor_and_stores_gram_weight(): void
    {
        $parta = \App\Models\PartaRecord::firstOrFail();
        $expectedWarp = floor(((90 * 63.5 / 58.5 * 1.0936 / 800) + 0.000000001) * 10000) / 10000;
        $this->assertEqualsWithDelta($expectedWarp, (float) $parta->warp_lb_per_meter, 0.00001);
        $this->assertEqualsWithDelta((float) $parta->kg_per_meter * 1000, (float) $parta->grams_per_meter, 0.01);
    }

    public function test_own_internal_order_needs_no_target_or_customer_and_only_checked_rolls_enter_stock(): void
    {
        $order = $this->openOrder('own', 'OWN-NO-TARGET');
        $this->assertNull($order->target_meters);
        $this->assertNull($order->party_name);
        $rollIds = $this->assignAndCheck($order);
        $this->assertCount(2, $rollIds);
        $this->assertEquals(150, (float) $this->quality->fresh()->current_stock_meters);
        $this->assertEquals(2, (int) $this->quality->fresh()->total_rolls_in_stock);
        $this->assertDatabaseHas('thaan_rolls', ['id' => $rollIds[0], 'grade' => 'A', 'owner_type' => 'factory', 'loom_no' => 'Loom-01']);
        $this->assertEquals(0, \App\Models\Production::count());
    }

    public function test_kachi_pakki_billing_and_rejected_return_are_linked_without_double_stock(): void
    {
        $order = $this->openOrder();
        $rollIds = $this->assignAndCheck($order);
        $this->actingAs($this->operator)->post(route('factory.kachi.store'), [
            'parchi_no' => 'KACHI-1', 'work_order_id' => $order->id,
            'dispatch_date' => '2026-11-02', 'roll_ids' => $rollIds,
        ])->assertSessionHasNoErrors();
        $kachi = KachiParchi::where('parchi_no', 'KACHI-1')->firstOrFail();
        $this->assertEquals(0, (float) $this->quality->fresh()->current_stock_meters);
        $this->assertEquals(150, (float) $kachi->dispatched_meters);
        $this->assertEquals(0, \App\Models\Invoice::count());

        $this->actingAs($this->operator)->post(route('factory.pakki.store'), [
            'parchi_no' => 'PAKKI-1', 'kachi_parchi_id' => $kachi->id,
            'received_date' => '2026-11-03', 'accepted_thaans' => 1,
            'accepted_meters' => 60, 'rejected_meters' => 90,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pakki_parchis', ['kachi_parchi_id' => $kachi->id, 'bill_amount' => 1980]);
        $this->assertDatabaseHas('invoices', ['invoice_no' => 'JW-PAKKI-1', 'total' => 1980]); // 60m * 66 picks * Rs 0.5
        $this->assertEquals(60, (float) $order->fresh()->accepted_meters);
        $this->assertEquals(0, (float) $this->quality->fresh()->current_stock_meters);

        $this->actingAs($this->operator)->post(route('factory.returns.store'), [
            'kachi_parchi_id' => $kachi->id, 'roll_ids' => [$rollIds[1]], 'returned_on' => '2026-11-03',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('thaan_rolls', ['id' => $rollIds[1], 'status' => 'returned_hold', 'length_meters' => 90]);
        $this->assertEquals(0, (float) $this->quality->fresh()->current_stock_meters);

        $this->actingAs($this->operator)->post(route('factory.disposition.store'), [
            'thaan_roll_id' => $rollIds[1], 'action' => 'resend_a', 'action_date' => '2026-11-03',
        ])->assertSessionHasNoErrors();
        $this->assertEquals(90, (float) $this->quality->fresh()->current_stock_meters);
        $this->actingAs($this->operator)->post(route('factory.kachi.store'), [
            'parchi_no' => 'KACHI-2', 'work_order_id' => $order->id,
            'dispatch_date' => '2026-11-04', 'roll_ids' => [$rollIds[1]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(0, (float) $this->quality->fresh()->current_stock_meters);
        $this->assertEquals(2, \App\Models\KachiParchi::count());
        $this->assertEquals(2, \App\Models\ThaanRoll::count()); // no new production on resend
    }

    public function test_fabric_cannot_be_checked_without_loom_assignment(): void
    {
        $this->openOrder();
        $this->actingAs($this->operator)->post(route('factory.checking.store'), [
            'checked_on' => '2026-11-02', 'loom_no' => 'Loom-01',
            'rolls' => [['roll_no' => 'UNASSIGNED-ROLL', 'length_meters' => 100]],
        ])->assertSessionHasErrors('loom_no');
        $this->assertEquals(0, (float) $this->quality->fresh()->current_stock_meters);
    }
}
