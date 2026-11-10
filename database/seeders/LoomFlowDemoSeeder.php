<?php

namespace Database\Seeders;

use App\Models\FabricStock;
use App\Services\FactoryStockService;
use App\Services\LedgerPostingService;
use App\Services\PartaService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Safe, self-contained sample records for the 2026-11-06 LoomFlow Laravel source.
 * NEVER run against a real factory database.
 */
class LoomFlowDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('DEMO SEED BLOCKED: APP_ENV must be local or testing.');
        }

        // DB-name guard relaxed: allow any database in local/testing env.
        // Tables with existing data are truncated (operational tables only); users table is preserved.
        $operationalTables = [
            'work_orders', 'parta_records', 'kachi_parchis', 'fabric_stocks',
            'customers', 'bag_purchases', 'sizing_slips', 'financial_accounts',
            'parts', 'ledgers', 'sales_contracts', 'loom_assignments',
            'sizing_beams', 'thaan_rolls', 'pakki_parchis', 'invoices',
            'payment_receipts', 'yarn_movements', 'factory_worker_wages',
            'loom_stoppages',
        ];

        foreach ($operationalTables as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Missing table '{$table}'. Run php artisan migrate first.");
            }
        }

        // Truncate operational tables only — users/passwords are never touched.
        Schema::disableForeignKeyConstraints();
        foreach ($operationalTables as $table) {
            DB::table($table)->truncate();
        }
        // Also clear yarn_qualities master table that the demo data re-seeds.
        if (Schema::hasTable('yarn_qualities')) {
            DB::table('yarn_qualities')->truncate();
        }
        Schema::enableForeignKeyConstraints();

        /** @var PartaService $parta */
        $parta = app(PartaService::class);
        /** @var FactoryStockService $stock */
        $stock = app(FactoryStockService::class);
        /** @var LedgerPostingService $post */
        $post = app(LedgerPostingService::class);
        $today = Carbon::today();
        $date = static fn (int $daysAgo): string => $today->copy()->subDays($daysAgo)->toDateString();
        $timestamp = now();
        $put = static fn (string $table, array $values): int => (int) DB::table($table)->insertGetId([
            ...$values, 'created_at' => $timestamp, 'updated_at' => $timestamp,
        ]);

        DB::transaction(function () use ($date, $put, $parta, $stock, $post): void {
            // 1. Masters: fictional sample parties, yarn counts and fabric qualities.
            foreach (['DEMO 58.5/1 Cotton', 'DEMO 30/1 Cotton', 'DEMO 20/1 Cotton', 'DEMO 40/1 Cotton'] as $count) {
                $put('yarn_qualities', ['name' => $count]);
            }
            $people = [
                'rehman' => 'DEMO - Rehman Textile Traders',
                'jinnah' => 'DEMO - Jinnah Grey Fabrics',
                'nawab' => 'DEMO - Nawab Textiles',
                'ownbuyer' => 'DEMO - Punjab Cloth Merchant',
            ];
            $customers = [];
            foreach ($people as $key => $name) {
                $customers[$key] = $put('customers', [
                    'name' => $name, 'status' => 'active', 'opening_balance' => 0,
                    'address' => 'Faisalabad, Punjab (fictional demo party)',
                ]);
            }
            $cash = $put('financial_accounts', [
                'name' => 'DEMO - Factory Cash', 'type' => 'cash',
                'opening_balance' => 150000, 'is_active' => true,
            ]);
            $bank = $put('financial_accounts', [
                'name' => 'DEMO - Business Bank', 'type' => 'bank',
                'account_number' => 'DEMO-ONLY-NOT-REAL',
                'opening_balance' => 450000, 'is_active' => true,
            ]);
            $specs = [
                'q90' => ['quality' => 'DEMO Grey 90x66 / 58.5x58.5', 'read' => 90, 'pick' => 66, 'width' => 63.5, 'warp' => 58.5, 'weft' => 58.5, 'yarn_rate' => 600, 'pick_rate' => .53],
                'q76' => ['quality' => 'DEMO Grey 76x68 / 30x30', 'read' => 76, 'pick' => 68, 'width' => 63, 'warp' => 30, 'weft' => 30, 'yarn_rate' => 450, 'pick_rate' => .46],
                'q68' => ['quality' => 'DEMO Grey 68x56 / 20x20', 'read' => 68, 'pick' => 56, 'width' => 63, 'warp' => 20, 'weft' => 20, 'yarn_rate' => 405, 'pick_rate' => .45],
                'q80' => ['quality' => 'DEMO Grey 80x60 / 40x40', 'read' => 80, 'pick' => 60, 'width' => 62, 'warp' => 40, 'weft' => 40, 'yarn_rate' => 510, 'pick_rate' => .48],
            ];
            $fabrics = [];
            $partas = [];
            foreach ($specs as $key => $s) {
                $fid = $put('fabric_stocks', [
                    'quality_name' => $s['quality'],
                    'construction' => sprintf('%sx%s / %sx%s', $s['warp'], $s['weft'], $s['read'], $s['pick']),
                    'width_inches' => $s['width'],
                    'notes' => 'DEMO ONLY: grey fabric quality; weight/cost calculated by existing Parta service.',
                ]);
                $fabrics[$key] = $fid;
                $partas[$key] = $parta->save(FabricStock::findOrFail($fid), [
                    'read' => $s['read'], 'pick' => $s['pick'], 'width_inches' => $s['width'],
                    'warp_count' => $s['warp'], 'weft_count' => $s['weft'],
                    'warp_rate_per_lb' => $s['yarn_rate'], 'weft_rate_per_lb' => $s['yarn_rate'],
                    'conversion_rate_per_pick' => $s['pick_rate'],
                ]);
            }

            // 2. Three customer jobs and an own-manufacturing programme.
            $jobConfigs = [
                'jw101' => ['no' => 'DEMO-JW-101', 'contract' => 'DEMO-CNT-101', 'party' => $people['rehman'], 'q' => 'q90', 'target' => 2500, 'rate' => .50, 'warp_supply' => 'customer', 'weft_supply' => 'factory', 'days' => 12, 'status' => 'active'],
                'jw102' => ['no' => 'DEMO-JW-102', 'contract' => 'DEMO-CNT-102', 'party' => $people['jinnah'], 'q' => 'q76', 'target' => 1800, 'rate' => .42, 'warp_supply' => 'customer', 'weft_supply' => 'customer', 'days' => 9, 'status' => 'active'],
                'jw103' => ['no' => 'DEMO-JW-103', 'contract' => 'DEMO-CNT-103', 'party' => $people['nawab'], 'q' => 'q80', 'target' => 140, 'rate' => .45, 'warp_supply' => 'mixed', 'weft_supply' => 'factory', 'days' => 16, 'status' => 'closed'],
            ];
            $orders = [];
            $contracts = [];
            foreach ($jobConfigs as $key => $o) {
                $spec = $specs[$o['q']];
                $unitRate = round($spec['pick'] * $o['rate'], 2);
                $cid = $put('sales_contracts', [
                    'contract_no' => $o['contract'], 'contract_type' => 'Conversion',
                    'buyer_name' => $o['party'], 'fabric_stock_id' => $fabrics[$o['q']],
                    'fabric_quality' => $spec['quality'], 'read' => $spec['read'],
                    'pick' => $spec['pick'], 'warp_count' => $spec['warp'], 'weft_count' => $spec['weft'],
                    'width' => $spec['width'], 'yarn_warp_rate' => $spec['yarn_rate'],
                    'yarn_weft_rate' => $spec['yarn_rate'], 'conversion_rate' => $o['rate'],
                    'rate_per_meter' => $unitRate, 'total_meters' => $o['target'],
                    'total_amount' => round($o['target'] * $unitRate, 2),
                    'delivered_meters' => 0, 'accepted_meters' => 0,
                    'remaining_meters' => $o['target'], 'status' => $o['status'] === 'closed' ? 'completed' : 'active',
                    'start_date' => $date($o['days']),
                    'end_date' => $date(0), 'tax_enabled' => false,
                    'sale_tax_rate' => 0, 'sales_tax_amount' => 0,
                    'notes' => 'DEMO ONLY: example job-work contract with accepted-metre billing.',
                ]);
                $contracts[$key] = $cid;
                $orders[$key] = $put('work_orders', [
                    'order_no' => $o['no'], 'type' => 'job',
                    'sales_contract_id' => $cid, 'fabric_stock_id' => $fabrics[$o['q']],
                    'parta_record_id' => $partas[$o['q']]->id,
                    'party_name' => $o['party'], 'target_meters' => $o['target'],
                    'accepted_meters' => 0, 'rate_per_pick' => $o['rate'],
                    'warp_supply' => $o['warp_supply'], 'weft_supply' => $o['weft_supply'],
                    'status' => $o['status'], 'start_date' => $date($o['days']),
                    'closed_on' => $o['status'] === 'closed' ? $date(2) : null,
                    'closure_reason' => $o['status'] === 'closed' ? 'DEMO: contracted 140m accepted; rejected Thaan retained after settlement.' : null,
                    'notes' => 'DEMO ONLY: party materials and contract-linked looms.',
                ]);
            }
            $orders['own201'] = $put('work_orders', [
                'order_no' => 'DEMO-OWN-201', 'type' => 'own', 'fabric_stock_id' => $fabrics['q68'],
                'parta_record_id' => $partas['q68']->id,
                'target_meters' => null, 'accepted_meters' => 0, 'rate_per_pick' => .45,
                'warp_supply' => 'factory', 'weft_supply' => 'factory',
                'status' => 'active', 'start_date' => $date(11),
                'notes' => 'DEMO ONLY: own manufacturing with optional/no fixed target.',
            ]);

            $allocations = [];
            $assignmentSpecs = [
                ['jw101', 'Loom-01', 12, null], ['jw101', 'Loom-02', 12, null],
                ['jw102', 'Loom-04', 9, null], ['jw102', 'Loom-05', 9, null],
                ['own201', 'Loom-03', 11, null], ['own201', 'Loom-06', 11, null],
                ['jw103', 'Loom-07', 16, 2],
            ];
            foreach ($assignmentSpecs as [$orderKey, $loom, $startAgo, $endAgo]) {
                $allocations[$loom] = $put('loom_assignments', [
                    'work_order_id' => $orders[$orderKey], 'loom_no' => $loom,
                    'start_date' => $date($startAgo),
                    'end_date' => $endAgo === null ? null : $date($endAgo),
                    'change_reason' => $endAgo === null ? null : 'DEMO job completed',
                ]);
            }

            // 3. Factory yarn purchases and customer-owned receipts (separate ownership).
            $purchaseId = $put('bag_purchases', [
                'challan_no' => 'DEMO-YRN-001', 'supplier_name' => 'DEMO - Madina Yarn Suppliers',
                'quality' => 'DEMO 20/1 Cotton', 'total_bags' => 9.50,
                'bags_to_factory' => 6.00, 'bags_to_sizing' => 3.50,
                'sizing_destination' => 'DEMO - Faisal Sizing Unit',
                'rate_per_bag' => 22000, 'total_amount' => 209000,
                'purchase_date' => $date(10), 'notes' => 'DEMO purchase; 6 bags to store / 3.5 direct to sizing.',
            ]);
            $put('bag_transactions', [
                'transaction_type' => 'purchase_factory', 'reference_no' => 'DEMO-YRN-001',
                'quality' => 'DEMO 20/1 Cotton', 'quantity_bags' => 6,
                'source' => 'DEMO - Madina Yarn Suppliers', 'destination' => 'Factory Store',
                'party_name' => 'DEMO - Madina Yarn Suppliers', 'transaction_date' => $date(10),
                'owner_type' => 'factory', 'net_weight_kg' => 272.16,
            ]);
            $factorySizingReceipt = $put('bag_transactions', [
                'transaction_type' => 'purchase_sizing', 'reference_no' => 'DEMO-YRN-001',
                'quality' => 'DEMO 20/1 Cotton', 'quantity_bags' => 3.5,
                'source' => 'DEMO - Madina Yarn Suppliers', 'destination' => 'DEMO - Faisal Sizing Unit',
                'party_name' => 'DEMO - Madina Yarn Suppliers', 'transaction_date' => $date(10),
                'owner_type' => 'factory', 'net_weight_kg' => 158.76,
            ]);
            $post->post('bag_purchase', $purchaseId, [
                ['role' => 'supplier', 'party_name' => 'DEMO - Madina Yarn Suppliers', 'party_type' => 'yarn_supplier', 'transaction_date' => $date(10), 'voucher_no' => 'DEMO-YRN-001', 'description' => 'Demo yarn purchase', 'debit' => 0, 'credit' => 209000, 'notes' => null],
                ['role' => 'inventory', 'party_name' => 'Yarn Inventory', 'party_type' => 'inventory', 'transaction_date' => $date(10), 'voucher_no' => 'DEMO-YRN-001', 'description' => 'Demo yarn added to stock', 'debit' => 209000, 'credit' => 0, 'notes' => null],
            ]);
            $customerSizingReceipt = $put('bag_transactions', [
                'transaction_type' => 'customer_sizing_receipt',
                'reference_no' => 'DEMO-CUST-TAANA-101', 'quality' => 'DEMO 58.5/1 Cotton',
                'quantity_bags' => 4.75, 'source' => $people['rehman'],
                'destination' => 'DEMO - Faisal Sizing Unit', 'party_name' => $people['rehman'],
                'transaction_date' => $date(11), 'work_order_id' => $orders['jw101'],
                'owner_type' => 'customer', 'net_weight_kg' => 215.46,
                'notes' => 'DEMO customer supplied warp; not a factory purchase.',
            ]);
            $sizingConfigs = [
                ['no' => 'DEMO-SZ-101', 'order' => 'jw101', 'quality' => 'DEMO 58.5/1 Cotton', 'receipt' => $customerSizingReceipt, 'allocated' => 4.75, 'used' => 4.25, 'returned' => .25, 'held' => .25, 'beams' => 2, 'length' => 3800, 'gross_kg' => 220, 'returned_kg' => 5, 'days' => 9],
                ['no' => 'DEMO-SZ-201', 'order' => 'own201', 'quality' => 'DEMO 20/1 Cotton', 'receipt' => $factorySizingReceipt, 'allocated' => 3.5, 'used' => 3.25, 'returned' => .25, 'held' => 0, 'beams' => 2, 'length' => 1100, 'gross_kg' => 146, 'returned_kg' => 6, 'days' => 8],
            ];
            $slips = [];
            foreach ($sizingConfigs as $c) {
                $slips[$c['order']] = $put('sizing_slips', [
                    'slip_no' => $c['no'], 'slip_type' => 'final_perch',
                    'sizing_party' => 'DEMO - Faisal Sizing Unit', 'yarn_quality' => $c['quality'],
                    'work_order_id' => $orders[$c['order']], 'bag_transaction_id' => $c['receipt'],
                    'bags_allocated' => $c['allocated'], 'bags_used' => $c['used'],
                    'bags_returned_to_factory' => $c['returned'], 'bags_held_with_sizing' => $c['held'],
                    'total_beams' => $c['beams'], 'total_length_meters' => $c['length'],
                    'set_length_value' => $c['length'], 'set_length_unit' => 'meters',
                    'total_ends' => $c['order'] === 'jw101' ? 5715 : 4284,
                    'reed_count' => $c['order'] === 'jw101' ? 90 : 68,
                    'fabric_width_inches' => $c['order'] === 'jw101' ? 63.5 : 63,
                    'back_beam_count' => $c['beams'], 'width_multiplier' => 1,
                    'sizing_gross_weight_pounds' => round($c['gross_kg'] / 0.45359237, 2),
                    'sizing_weight_pounds' => round(($c['gross_kg'] - $c['returned_kg']) / 0.45359237, 2),
                    'wet_cone_weight_kg' => $c['returned_kg'],
                    'returned_materials_weight_kg' => $c['returned_kg'],
                    'sizing_rate_per_kg' => 20,
                    'sizing_bill_amount' => ($c['gross_kg'] - $c['returned_kg']) * 20,
                    'slip_date' => $date($c['days']), 'status' => 'completed',
                    'notes' => 'DEMO ONLY: bag variance is zero; named beams below.',
                ]);
            }
            $put('bag_transactions', [
                'transaction_type' => 'return_from_sizing', 'reference_no' => 'DEMO-SZ-201',
                'quality' => 'DEMO 20/1 Cotton', 'quantity_bags' => .25,
                'source' => 'DEMO - Faisal Sizing Unit', 'destination' => 'Factory Store',
                'party_name' => 'DEMO - Faisal Sizing Unit', 'transaction_date' => $date(8),
                'owner_type' => 'factory',
            ]);

            $beamSpecs = [
                ['DEMO-BM-101', 'jw101', 'q90', 'Loom-01', 1900, $slips['jw101'], 'DEMO 58.5/1 Cotton', 107.5],
                ['DEMO-BM-102', 'jw101', 'q90', 'Loom-02', 1900, $slips['jw101'], 'DEMO 58.5/1 Cotton', 107.5],
                ['DEMO-BM-201', 'own201', 'q68', 'Loom-03', 550, $slips['own201'], 'DEMO 20/1 Cotton', 70],
                ['DEMO-BM-202', 'own201', 'q68', 'Loom-06', 550, $slips['own201'], 'DEMO 20/1 Cotton', 70],
                ['DEMO-BM-301', 'jw102', 'q76', 'Loom-04', 3200, null, 'DEMO 30/1 Cotton', 90],
                ['DEMO-BM-302', 'jw102', 'q76', 'Loom-05', 3200, null, 'DEMO 30/1 Cotton', 90],
                ['DEMO-BM-401', 'jw103', 'q80', null, 1800, null, 'DEMO 40/1 Cotton', 65],
            ];
            $beams = [];
            foreach ($beamSpecs as [$num, $order, $quality, $loom, $len, $slip, $yarn, $kg]) {
                $beams[$num] = $put('sizing_beams', [
                    'beam_no' => $num, 'beam_name' => 'Beam '.$num,
                    'sizing_slip_id' => $slip, 'fabric_stock_id' => $fabrics[$quality],
                    'work_order_id' => $orders[$order],
                    'yarn_quality' => $yarn, 'total_length_meters' => $len,
                    'remaining_length_meters' => $len,
                    'assigned_loom_no' => $loom, 'status' => $loom ? 'mounted_on_loom' : 'ready_in_store',
                    'mounted_date' => $loom ? $date(7) : null,
                    'initial_net_weight_kg' => $kg,
                    'notes' => $slip === null ? 'DEMO: customer supplied ready beam' : 'DEMO: produced from sizing slip',
                ]);
                if ($loom) {
                    $put('beam_events', [
                        'sizing_beam_id' => $beams[$num], 'to_work_order_id' => $orders[$order],
                        'to_loom' => $loom, 'action' => 'mount',
                        'remaining_length_meters' => $len,
                        'event_date' => $date(7), 'notes' => 'DEMO initial loom mounting',
                    ]);
                }
            }

            // 4. Material khata, preserving ownership and taana/baana separately.
            $yarn = static function (string $key, string $component, string $owner, string $movement, float $kg, string $count, float $bags, int $days, string $destination) use ($put, $orders, $date): void {
                $put('yarn_movements', [
                    'work_order_id' => $orders[$key], 'component' => $component, 'owner_type' => $owner,
                    'movement_type' => $movement, 'weight_kg' => $kg, 'bags' => $bags,
                    'yarn_count' => $count, 'lot_no' => 'DEMO-LOT-'.$key,
                    'reference_no' => 'DEMO-YM-'.strtoupper($key).'-'.strtoupper($component).'-'.strtoupper($movement),
                    'movement_date' => $date($days), 'destination_type' => $destination,
                    'destination_name' => $destination === 'sizing' ? 'DEMO - Faisal Sizing Unit' : null,
                    'notes' => 'DEMO ONLY: separate owner/component balance.',
                ]);
            };
            $yarn('jw101', 'warp', 'customer', 'receipt', 215.46, 'DEMO 58.5/1 Cotton', 4.75, 11, 'sizing');
            $yarn('jw101', 'warp', 'customer', 'issue', 160, 'DEMO 58.5/1 Cotton', 3.52, 9, 'loom');
            $yarn('jw101', 'weft', 'factory', 'receipt', 180, 'DEMO 58.5/1 Cotton', 3.97, 10, 'factory_store');
            $yarn('jw101', 'weft', 'factory', 'issue', 90, 'DEMO 58.5/1 Cotton', 1.98, 5, 'loom');
            $yarn('jw102', 'warp', 'customer', 'receipt', 270, 'DEMO 30/1 Cotton', 5.95, 8, 'factory_store');
            $yarn('jw102', 'warp', 'customer', 'issue', 120, 'DEMO 30/1 Cotton', 2.64, 7, 'loom');
            $yarn('jw102', 'weft', 'customer', 'receipt', 130, 'DEMO 30/1 Cotton', 2.87, 8, 'factory_store');
            $yarn('jw102', 'weft', 'customer', 'issue', 65, 'DEMO 30/1 Cotton', 1.43, 6, 'loom');
            $yarn('own201', 'warp', 'factory', 'receipt', 320, 'DEMO 20/1 Cotton', 7.05, 10, 'factory_store');
            $yarn('own201', 'warp', 'factory', 'issue', 180, 'DEMO 20/1 Cotton', 3.97, 8, 'loom');
            $yarn('own201', 'weft', 'factory', 'receipt', 260, 'DEMO 20/1 Cotton', 5.73, 9, 'factory_store');
            $yarn('own201', 'weft', 'factory', 'issue', 140, 'DEMO 20/1 Cotton', 3.09, 6, 'loom');
            $yarn('jw103', 'warp', 'customer', 'receipt', 50, 'DEMO 40/1 Cotton', 1.10, 15, 'factory_store');
            $yarn('jw103', 'warp', 'customer', 'issue', 35, 'DEMO 40/1 Cotton', .77, 12, 'loom');
            $yarn('jw103', 'warp', 'factory', 'receipt', 30, 'DEMO 40/1 Cotton', .66, 15, 'factory_store');
            $yarn('jw103', 'warp', 'factory', 'issue', 18, 'DEMO 40/1 Cotton', .40, 12, 'loom');
            $yarn('jw103', 'weft', 'factory', 'receipt', 65, 'DEMO 40/1 Cotton', 1.43, 15, 'factory_store');
            $yarn('jw103', 'weft', 'factory', 'issue', 30, 'DEMO 40/1 Cotton', .66, 12, 'loom');

            // 5. Daily checking entries: only measured Thaans enter available stock.
            $rolls = [];
            $groups = [
                ['jw101', 'q90', 'Loom-01', 3, [72, 68, 70, 75, 65, 80]],
                ['jw101', 'q90', 'Loom-02', 2, [74, 77, 69, 73, 71, 76]],
                ['jw101', 'q90', 'Loom-01', 0, [72, 70, 74, 71]],
                ['jw102', 'q76', 'Loom-04', 2, [64, 66, 65, 67]],
                ['jw102', 'q76', 'Loom-04', 0, [68, 66, 65, 69]],
                ['jw102', 'q76', 'Loom-05', 0, [65, 70, 72, 68]],
                ['own201', 'q68', 'Loom-03', 0, [82, 78, 81, 79, 80]],
                ['own201', 'q68', 'Loom-06', 1, [76, 79, 77, 81, 78]],
                ['jw103', 'q80', 'Loom-07', 8, [70, 70, 75]],
            ];
            $runningMeters = [];
            $rollCounter = ['jw101' => 0, 'jw102' => 0, 'own201' => 0, 'jw103' => 0];
            $loomBeams = [
                'Loom-01' => 'DEMO-BM-101', 'Loom-02' => 'DEMO-BM-102',
                'Loom-03' => 'DEMO-BM-201', 'Loom-06' => 'DEMO-BM-202',
                'Loom-04' => 'DEMO-BM-301', 'Loom-05' => 'DEMO-BM-302',
                'Loom-07' => 'DEMO-BM-401',
            ];
            $operatorNames = ['Loom-01' => 'DEMO Operator Ali', 'Loom-02' => 'DEMO Operator Nasir', 'Loom-03' => 'DEMO Operator Iqbal', 'Loom-04' => 'DEMO Operator Javed', 'Loom-05' => 'DEMO Operator Hassan', 'Loom-06' => 'DEMO Operator Asif', 'Loom-07' => 'DEMO Operator Saleem'];
            foreach ($groups as [$key, $quality, $loom, $daysAgo, $lengths]) {
                $total = array_sum($lengths);
                foreach ($lengths as $length) {
                    $rollCounter[$key]++;
                    $rollNo = 'DEMO-TH-'.strtoupper($key).'-'.str_pad((string) $rollCounter[$key], 3, '0', STR_PAD_LEFT);
                    $rolls[$key][] = ['id' => $put('thaan_rolls', [
                        'roll_no' => $rollNo, 'work_order_id' => $orders[$key],
                        'fabric_stock_id' => $fabrics[$quality], 'loom_no' => $loom,
                        'checked_on' => $date($daysAgo), 'length_meters' => $length,
                        'grade' => 'A', 'status' => 'ready',
                        'owner_type' => $key === 'own201' ? 'factory' : 'customer',
                        'notes' => 'DEMO: checked and measured on same day as weaving.',
                    ]), 'length' => $length];
                }
                $stock->move(FabricStock::findOrFail($fabrics[$quality]), $total, count($lengths), $date($daysAgo), 'DEMO checking '.$key.' / '.$loom);
                $start = (float) ($runningMeters[$loom] ?? 1000);
                $measuredByLoom = $total + 3;
                $expected = $measuredByLoom + 1;
                $put('productions', [
                    'production_date' => $date($daysAgo), 'shift' => 'Day', 'loom_no' => $loom,
                    'operator_name' => $operatorNames[$loom],
                    'sizing_beam_id' => $beams[$loomBeams[$loom]],
                    'fabric_stock_id' => $fabrics[$quality], 'fabric_quality' => $specs[$quality]['quality'],
                    'work_order_id' => $orders[$key], 'loom_assignment_id' => $allocations[$loom],
                    'is_provisional' => true, 'start_meter' => $start,
                    'end_meter' => $start + $measuredByLoom,
                    'net_meters_produced' => $measuredByLoom,
                    'beam_meters_consumed' => $expected,
                    'expected_fabric_meters' => $expected,
                    'leakage_waste_meters' => 1, 'leakage_percentage' => round(100 / $expected, 2),
                    'picks_per_inch' => $specs[$quality]['pick'],
                    'rpm' => 340, 'efficiency_percentage' => 86.8,
                    'remarks' => 'DEMO provisional meter reading, checked lengths control stock.',
                ]);
                $runningMeters[$loom] = $start + $measuredByLoom;
                DB::table('sizing_beams')->where('id', $beams[$loomBeams[$loom]])
                    ->decrement('remaining_length_meters', $expected);
            }

            // Helper: dispatch specific marked pieces (not a second stock receipt).
            $kachi = function (string $key, string $no, array $items, int $daysAgo) use ($put, $orders, $stock, $date, $contracts): array {
                $length = array_sum(array_column($items, 'length'));
                $kid = $put('kachi_parchis', [
                    'parchi_no' => $no, 'work_order_id' => $orders[$key],
                    'dispatch_date' => $date($daysAgo), 'dispatched_meters' => $length,
                    'thaan_count' => count($items), 'vehicle_no' => 'DEMO-VEHICLE',
                    'notes' => 'DEMO: individually marked Thaans on Kachi.',
                ]);
                foreach ($items as $item) {
                    $put('kachi_parchi_items', [
                        'kachi_parchi_id' => $kid, 'thaan_roll_id' => $item['id'],
                        'length_meters' => $item['length'],
                    ]);
                    DB::table('thaan_rolls')->where('id', $item['id'])->update(['status' => 'dispatched']);
                }
                $stock->move(FabricStock::findOrFail((int) DB::table('work_orders')->where('id', $orders[$key])->value('fabric_stock_id')), -$length, -count($items), $date($daysAgo), 'DEMO Kachi '.$no);
                DB::table('sales_contracts')->where('id', $contracts[$key])->increment('delivered_meters', $length);

                return ['id' => $kid, 'length' => $length, 'items' => $items];
            };
            // Helper: complete one Pakki, invoice accepted metres only.
            $pakki = function (string $key, array $kp, string $number, int $acceptedPieces, float $acceptedM, float $rejectedM, int $daysAgo) use ($date, $put, $orders, $jobConfigs, $specs, $contracts, $customers, $people, $post): int {
                $order = $jobConfigs[$key];
                if (abs($acceptedM + $rejectedM - $kp['length']) > .001) {
                    throw new RuntimeException('Demo Parchi values do not reconcile.');
                }
                $metreRate = round($specs[$order['q']]['pick'] * $order['rate'], 2);
                $bill = round($acceptedM * $metreRate, 2);
                $customerKey = array_search($order['party'], $people, true);
                $invoiceId = $put('invoices', [
                    'invoice_no' => 'JW-'.$number, 'customer_id' => $customers[$customerKey],
                    'sales_contract_id' => $contracts[$key],
                    'invoice_date' => $date($daysAgo), 'subtotal' => $bill,
                    'discount' => 0, 'total' => $bill, 'status' => 'issued',
                    'notes' => 'DEMO Job Work bill from Pakki accepted metres only.',
                ]);
                $put('invoice_items', [
                    'invoice_id' => $invoiceId, 'description' => 'DEMO weaving '.$order['no'].' / '.$number,
                    'quantity' => $acceptedM, 'unit' => 'Meter', 'rate' => $metreRate, 'amount' => $bill,
                ]);
                $put('pakki_parchis', [
                    'parchi_no' => $number, 'kachi_parchi_id' => $kp['id'],
                    'invoice_id' => $invoiceId, 'received_date' => $date($daysAgo),
                    'accepted_thaans' => $acceptedPieces,
                    'accepted_meters' => $acceptedM, 'rejected_meters' => $rejectedM,
                    'bill_amount' => $bill,
                    'notes' => 'DEMO: Pakki accepted quantity governs weaving bill.',
                ]);
                DB::table('work_orders')->where('id', $orders[$key])->increment('accepted_meters', $acceptedM);
                DB::table('sales_contracts')->where('id', $contracts[$key])->increment('accepted_meters', $acceptedM);
                DB::table('sales_contracts')->where('id', $contracts[$key])->update([
                    'remaining_meters' => max(0, $order['target'] - (float) DB::table('sales_contracts')->where('id', $contracts[$key])->value('accepted_meters')),
                ]);
                $post->post('invoice', $invoiceId, [
                    ['role' => 'customer', 'party_name' => $order['party'], 'party_type' => 'fabric_buyer', 'transaction_date' => $date($daysAgo), 'voucher_no' => 'JW-'.$number, 'description' => 'DEMO Pakki weaving bill', 'debit' => $bill, 'credit' => 0, 'notes' => null],
                    ['role' => 'revenue', 'party_name' => 'Job Work Revenue', 'party_type' => 'income', 'transaction_date' => $date($daysAgo), 'voucher_no' => 'JW-'.$number, 'description' => 'DEMO accepted fabric revenue', 'debit' => 0, 'credit' => $bill, 'notes' => null],
                ]);
                if ($rejectedM === 0.0) {
                    foreach ($kp['items'] as $item) {
                        DB::table('thaan_rolls')->where('id', $item['id'])->update(['status' => 'accepted']);
                    }
                }

                return $invoiceId;
            };

            // JW-101: partial acceptance, two marked rejected returns, one re-sent on a NEW Kachi.
            $kp101 = $kachi('jw101', 'DEMO-KP-101', array_slice($rolls['jw101'], 0, 5), 2); // 350m
            $inv101 = $pakki('jw101', $kp101, 'DEMO-PP-101', 3, 210, 140, 1); // 210 x 66 x .50 = 6,930
            foreach (array_slice($rolls['jw101'], 3, 2) as $item) {
                $put('thaan_returns', ['kachi_parchi_id' => $kp101['id'], 'thaan_roll_id' => $item['id'], 'returned_on' => $date(1)]);
                DB::table('thaan_rolls')->where('id', $item['id'])->update(['status' => 'returned_hold']);
            }
            foreach (array_slice($rolls['jw101'], 0, 3) as $item) {
                DB::table('thaan_rolls')->where('id', $item['id'])->update(['status' => 'accepted']);
            }
            $resendItem = $rolls['jw101'][3]; // Original 75m, no remeasurement.
            $put('thaan_dispositions', [
                'thaan_roll_id' => $resendItem['id'], 'from_work_order_id' => $orders['jw101'],
                'action' => 'resend_a', 'action_date' => $date(0),
                'notes' => 'DEMO: original marked 75m Thaan released for re-dispatch.',
            ]);
            DB::table('thaan_rolls')->where('id', $resendItem['id'])->update(['status' => 'returned_ready']);
            $stock->move(FabricStock::findOrFail($fabrics['q90']), 75, 1, $date(0), 'DEMO rejected Thaan reclassified A');
            $kp102 = $kachi('jw101', 'DEMO-KP-102', [$resendItem, $rolls['jw101'][5]], 0); // 75 + 80 = 155, waiting for Pakki
            $kp103 = $kachi('jw101', 'DEMO-KP-103', [$rolls['jw101'][6], $rolls['jw101'][7]], 1);
            $inv103 = $pakki('jw101', $kp103, 'DEMO-PP-103', 2, 151, 0, 0);
            $kp201 = $kachi('jw102', 'DEMO-KP-201', array_slice($rolls['jw102'], 0, 4), 1); // 262m pending Pakki

            // JW-103: a completed contract, 140m accepted + a whole 75m Thaan rejected/settled.
            $kp301 = $kachi('jw103', 'DEMO-KP-301', $rolls['jw103'], 5);
            $inv301 = $pakki('jw103', $kp301, 'DEMO-PP-301', 2, 140, 75, 4);
            foreach (array_slice($rolls['jw103'], 0, 2) as $item) {
                DB::table('thaan_rolls')->where('id', $item['id'])->update(['status' => 'accepted']);
            }
            $returned = $rolls['jw103'][2];
            $put('thaan_returns', ['kachi_parchi_id' => $kp301['id'], 'thaan_roll_id' => $returned['id'], 'returned_on' => $date(4)]);
            DB::table('thaan_rolls')->where('id', $returned['id'])->update(['status' => 'ready', 'owner_type' => 'factory']);
            $put('thaan_dispositions', [
                'thaan_roll_id' => $returned['id'], 'from_work_order_id' => $orders['jw103'],
                'action' => 'settle_keep', 'settlement_amount' => 11250,
                'action_date' => $date(2), 'approval_reference' => 'DEMO-SETTLEMENT-001',
                'notes' => 'DEMO: factory retained 75m after agreed settlement; ownership changed.',
            ]);
            $stock->move(FabricStock::findOrFail($fabrics['q80']), 75, 1, $date(2), 'DEMO return settled and kept');
            DB::table('work_orders')->where('id', $orders['jw103'])->update(['accepted_meters' => 140, 'status' => 'closed', 'closed_on' => $date(2)]);
            DB::table('sales_contracts')->where('id', $contracts['jw103'])->update(['accepted_meters' => 140, 'remaining_meters' => 0, 'status' => 'completed']);

            // 6. Own manufactured cloth sold from specific checked Thaans; partial customer payment.
            $ownSalePieces = array_slice($rolls['own201'], 0, 2); // 82 + 78 = 160m
            $ownSaleMetres = 160;
            $ownSaleAmount = $ownSaleMetres * 240;
            $saleId = $put('sales', [
                'slip_no' => 'DEMO-SALE-001', 'sale_type' => 'open_market',
                'fabric_stock_id' => $fabrics['q68'], 'buyer_name' => $people['ownbuyer'],
                'fabric_quality' => $specs['q68']['quality'],
                'meters_sold' => $ownSaleMetres, 'rolls_sold' => count($ownSalePieces),
                'rate_per_meter' => 240, 'subtotal' => $ownSaleAmount,
                'discount' => 0, 'net_total' => $ownSaleAmount,
                'payment_type' => 'credit', 'payment_status' => 'partial',
                'sale_date' => $date(0), 'notes' => 'DEMO own fabric sale from measured Thaans.',
            ]);
            foreach ($ownSalePieces as $item) {
                DB::table('thaan_rolls')->where('id', $item['id'])->update(['status' => 'dispatched']);
            }
            $stock->move(FabricStock::findOrFail($fabrics['q68']), -$ownSaleMetres, -2, $date(0), 'DEMO own sale DEMO-SALE-001');
            $post->post('sale', $saleId, [
                ['role' => 'buyer', 'party_name' => $people['ownbuyer'], 'party_type' => 'fabric_buyer', 'transaction_date' => $date(0), 'voucher_no' => 'DEMO-SALE-001', 'description' => 'DEMO own-fabric sale', 'debit' => $ownSaleAmount, 'credit' => 0, 'notes' => null],
                ['role' => 'revenue', 'party_name' => 'Fabric Sales Revenue', 'party_type' => 'income', 'transaction_date' => $date(0), 'voucher_no' => 'DEMO-SALE-001', 'description' => 'DEMO own-fabric sale', 'debit' => 0, 'credit' => $ownSaleAmount, 'notes' => null],
            ]);
            $saleInvoice = $put('invoices', [
                'invoice_no' => 'DEMO-INV-SALE-001', 'customer_id' => $customers['ownbuyer'],
                'sale_id' => $saleId, 'invoice_date' => $date(0),
                'subtotal' => $ownSaleAmount, 'discount' => 0,
                'total' => $ownSaleAmount, 'status' => 'issued',
                'notes' => 'DEMO invoice for own fabric sale; ledger already posted by sale.',
            ]);
            $put('invoice_items', [
                'invoice_id' => $saleInvoice, 'description' => 'DEMO grey cloth 68x56',
                'quantity' => $ownSaleMetres, 'unit' => 'Meter', 'rate' => 240, 'amount' => $ownSaleAmount,
            ]);

            // 7. Cash/bank receipts: invoice allocations DO NOT count as additional sales revenue.
            $receipt = function (string $no, int $customerId, int $invoiceId, int $accountId, float $amount, int $daysAgo, string $method, string $party) use ($put, $date, $post): void {
                $rid = $put('payment_receipts', [
                    'receipt_no' => $no, 'customer_id' => $customerId,
                    'financial_account_id' => $accountId, 'payment_date' => $date($daysAgo),
                    'amount' => $amount, 'method' => $method,
                    'reference' => 'DEMO-TRANSFER', 'notes' => 'DEMO partial payment.',
                ]);
                $put('invoice_payment_allocations', ['payment_receipt_id' => $rid, 'invoice_id' => $invoiceId, 'amount' => $amount]);
                $put('account_transactions', [
                    'financial_account_id' => $accountId, 'payment_receipt_id' => $rid,
                    'transaction_date' => $date($daysAgo), 'direction' => 'in',
                    'amount' => $amount, 'description' => 'DEMO payment from '.$party,
                    'reference' => $no,
                ]);
                $post->post('receipt', $rid, [
                    ['role' => 'bank', 'party_name' => $method === 'cash' ? 'Cash' : 'Bank', 'party_type' => 'cash_bank', 'transaction_date' => $date($daysAgo), 'voucher_no' => $no, 'description' => 'DEMO receipt', 'debit' => $amount, 'credit' => 0, 'notes' => null],
                    ['role' => 'customer', 'party_name' => $party, 'party_type' => 'fabric_buyer', 'transaction_date' => $date($daysAgo), 'voucher_no' => $no, 'description' => 'DEMO payment against invoice', 'debit' => 0, 'credit' => $amount, 'notes' => null],
                ]);
            };
            $receipt('DEMO-RCPT-101', $customers['rehman'], $inv101, $bank, 3000, 0, 'bank_transfer', $people['rehman']);
            $receipt('DEMO-RCPT-201', $customers['ownbuyer'], $saleInvoice, $cash, 14000, 0, 'cash', $people['ownbuyer']);

            // 8. Spare parts, mazdoori, stoppages, and actual cash/bank expense entries.
            $parts = [
                ['DEMO-SP-001', 'Gripper', 'Rapier', 8, 3, 3200, 'Rack A'],
                ['DEMO-SP-002', 'Heald Wire', 'Heald & Dropper', 45, 20, 95, 'Rack B'],
                ['DEMO-SP-003', 'Loom Belt', 'Mechanical', 2, 3, 1800, 'Rack C'],
            ];
            foreach ($parts as [$code, $name, $cat, $q, $alert, $cost, $rack]) {
                $partId = $put('parts', [
                    'part_code' => $code, 'part_name' => $name, 'category' => $cat,
                    'stock_quantity' => $q, 'min_alert_threshold' => $alert,
                    'unit' => 'pcs', 'unit_price' => $cost, 'rack_location' => $rack,
                    'notes' => 'DEMO spare part',
                ]);
                if ($code === 'DEMO-SP-001') {
                    $put('part_issuances', [
                        'part_id' => $partId, 'loom_no' => 'Loom-02',
                        'issued_to_person' => 'DEMO Mechanic Farooq', 'quantity' => 1,
                        'unit_price' => $cost, 'total_cost' => $cost,
                        'issuance_date' => $date(1), 'remarks' => 'DEMO: gripper replaced',
                    ]);
                    DB::table('parts')->where('id', $partId)->decrement('stock_quantity');
                }
            }
            $fixedExpense = $put('fixed_expenses', [
                'name' => 'DEMO Factory Rent', 'payee' => 'DEMO Property Owner',
                'default_amount' => 25000, 'frequency' => 'monthly',
                'due_day' => 5, 'is_active' => true,
            ]);
            foreach ([
                ['Rent', 'fixed', 25000, $bank, $fixedExpense, 4, 'DEMO Property Owner'],
                ['Electricity', 'variable', 18500, $bank, null, 1, 'DEMO Electricity Utility'],
                ['Loom Repair', 'variable', 3200, $cash, null, 1, 'DEMO Mechanic Farooq'],
            ] as [$name, $type, $amount, $account, $fixed, $days, $payee]) {
                $eid = $put('expenses', [
                    'fixed_expense_id' => $fixed, 'financial_account_id' => $account,
                    'expense_type' => $type, 'title' => $name, 'expense_date' => $date($days),
                    'amount' => $amount, 'payee' => $payee,
                    'reference' => 'DEMO-EXP-'.$name, 'notes' => 'DEMO expense.',
                ]);
                $put('account_transactions', [
                    'financial_account_id' => $account, 'expense_id' => $eid,
                    'transaction_date' => $date($days), 'direction' => 'out',
                    'amount' => $amount, 'description' => 'DEMO expense: '.$name,
                    'reference' => 'DEMO-EXP-'.$eid,
                ]);
                $post->post('expense', $eid, [
                    ['role' => 'expense', 'party_name' => $name, 'party_type' => 'expense', 'transaction_date' => $date($days), 'voucher_no' => 'DEMO-EXP-'.$eid, 'description' => 'DEMO factory expense', 'debit' => (float) $amount, 'credit' => 0, 'notes' => null],
                    ['role' => 'cash', 'party_name' => 'Cash / Bank', 'party_type' => 'cash_bank', 'transaction_date' => $date($days), 'voucher_no' => 'DEMO-EXP-'.$eid, 'description' => 'DEMO payment', 'debit' => 0, 'credit' => (float) $amount, 'notes' => null],
                ]);
            }
            foreach ([
                ['DEMO Weaver Ali', 'jw101', 'Loom-01', 'metre', 430, 2.5, 300, 500, 3],
                ['DEMO Weaver Nasir', 'jw101', 'Loom-02', 'metre', 440, 2.5, 0, 500, 2],
                ['DEMO Weaver Iqbal', 'own201', 'Loom-03', 'day', 2, 1300, 300, 1200, 0],
            ] as [$worker, $key, $loom, $basis, $quantity, $rate, $advance, $paid, $days]) {
                $gross = round($quantity * $rate, 2);
                $put('factory_worker_wages', [
                    'worker_name' => $worker, 'work_order_id' => $orders[$key],
                    'loom_no' => $loom, 'wage_date' => $date($days),
                    'basis' => $basis, 'quantity' => $quantity, 'rate' => $rate,
                    'gross_amount' => $gross, 'advance_adjustment' => $advance,
                    'paid_amount' => $paid, 'balance_amount' => $gross - $advance - $paid,
                    'notes' => 'DEMO wages only; cash flow separately in Expenses.',
                ]);
            }
            $put('loom_stoppages', [
                'loom_no' => 'Loom-02', 'work_order_id' => $orders['jw101'],
                'stopped_on' => $date(1), 'downtime_minutes' => 45,
                'reason' => 'Weft breakage', 'repair_cost' => 0,
                'notes' => 'DEMO brief machine stoppage.',
            ]);
            $put('loom_stoppages', [
                'loom_no' => 'Loom-06', 'work_order_id' => $orders['own201'],
                'stopped_on' => $date(1), 'downtime_minutes' => 90,
                'reason' => 'Belt replacement', 'repair_cost' => 3200,
                'notes' => 'DEMO, repair payment recorded as expense.',
            ]);

            // Final availability reflects customer-owned stock versus factory-owned stock.
            foreach ($fabrics as $fabricId) {
                $stock->refreshAvailability(FabricStock::findOrFail($fabricId));
            }

            // Abort the entire transaction if any core demo total is inconsistent.
            $expectedStocks = ['q90' => 576, 'q76' => 543, 'q68' => 631, 'q80' => 75];
            foreach ($expectedStocks as $key => $meters) {
                $actual = (float) DB::table('fabric_stocks')->where('id', $fabrics[$key])->value('current_stock_meters');
                if (abs($actual - $meters) > .01) {
                    throw new RuntimeException("Demo stock mismatch for {$key}: {$actual}m instead of {$meters}m.");
                }
            }
            foreach (['thaan_rolls' => 41, 'kachi_parchis' => 5, 'pakki_parchis' => 3, 'work_orders' => 4, 'invoices' => 4, 'payment_receipts' => 2] as $table => $expected) {
                if (DB::table($table)->count() !== $expected) {
                    throw new RuntimeException("Demo record count mismatch for {$table}.");
                }
            }
            if (abs((float) DB::table('ledgers')->sum('debit') - (float) DB::table('ledgers')->sum('credit')) > .01) {
                throw new RuntimeException('Demo ledger entries are not balanced.');
            }
        });

        $this->command?->info('LoomFlow demo records loaded successfully into '.DB::connection()->getDatabaseName().'. Existing operator users were untouched.');
        $this->command?->table(['Table', 'Rows'], collect(['work_orders', 'loom_assignments', 'parta_records', 'sizing_beams', 'thaan_rolls', 'kachi_parchis', 'pakki_parchis', 'invoices', 'payment_receipts', 'yarn_movements', 'factory_worker_wages', 'loom_stoppages', 'parts'])->map(fn ($table) => [$table, DB::table($table)->count()])->all());
    }
}
