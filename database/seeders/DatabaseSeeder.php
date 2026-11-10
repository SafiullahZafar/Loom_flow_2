<?php

namespace Database\Seeders;

use App\Models\BagPurchase;
use App\Models\BagTransaction;
use App\Models\Customer;
use App\Models\FabricStock;
use App\Models\Ledger;
use App\Models\Part;
use App\Models\PartIssuance;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SalesContract;
use App\Models\SizingBeam;
use App\Models\SizingSlip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive textile and loom data.
     */
    public function run(): void
    {
        // Sample data with known credentials must NEVER be seeded into production.
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Demo database seed is restricted to local/testing environments.');
        }

        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@loomflow.com'],
            [
                'name' => 'Factory Admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'store@loomflow.com'],
            [
                'name' => 'Store Incharge',
                'role' => 'store_incharge',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'master@loomflow.com'],
            [
                'name' => 'Loom Master (Production)',
                'role' => 'loom_master',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Bags Purchases (with split between sizing & factory stock)
        $p1 = BagPurchase::create([
            'challan_no' => 'CH-8821',
            'supplier_name' => 'Al-Karam Spinning Mills',
            'quality' => '30/1 PC Combed',
            'total_bags' => 100,
            'bags_to_sizing' => 60,
            'bags_to_factory' => 40,
            'rate_per_bag' => 14500.00,
            'total_amount' => 1450000.00,
            'purchase_date' => now()->subDays(12)->toDateString(),
            'notes' => 'Contract #889 - Direct dispatch of 60 bags to National Sizing Industries',
        ]);

        $p2 = BagPurchase::create([
            'challan_no' => 'CH-8845',
            'supplier_name' => 'Nishat Spinning Mills',
            'quality' => '40/1 100% Cotton Carded',
            'total_bags' => 80,
            'bags_to_sizing' => 50,
            'bags_to_factory' => 30,
            'rate_per_bag' => 16200.00,
            'total_amount' => 1296000.00,
            'purchase_date' => now()->subDays(8)->toDateString(),
            'notes' => '50 bags delivered directly to Kohinoor Sizing, 30 bags stored in Factory Godown',
        ]);

        $p3 = BagPurchase::create([
            'challan_no' => 'CH-8890',
            'supplier_name' => 'Suraj Cotton Mills',
            'quality' => '52/1 PV Blend Super',
            'total_bags' => 50,
            'bags_to_sizing' => 30,
            'bags_to_factory' => 20,
            'rate_per_bag' => 17800.00,
            'total_amount' => 890000.00,
            'purchase_date' => now()->subDays(3)->toDateString(),
            'notes' => 'Urgent lot for shirting contract order',
        ]);

        // 3. Bag Stock Ledger / Transactions
        BagTransaction::create([
            'transaction_type' => 'purchase_sizing',
            'reference_no' => 'CH-8821',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 60,
            'source' => 'Al-Karam Spinning',
            'destination' => 'National Sizing Mill',
            'party_name' => 'National Sizing Mill',
            'transaction_date' => now()->subDays(12)->toDateString(),
            'notes' => 'Dispatched directly from spinning mill to sizing unit',
        ]);

        BagTransaction::create([
            'transaction_type' => 'purchase_factory',
            'reference_no' => 'CH-8821',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 40,
            'source' => 'Al-Karam Spinning',
            'destination' => 'Factory Yarn Store',
            'party_name' => 'Internal Store',
            'transaction_date' => now()->subDays(12)->toDateString(),
            'notes' => 'Received at Main Factory Godown Rack Y-1',
        ]);

        BagTransaction::create([
            'transaction_type' => 'issue_to_sizing',
            'reference_no' => 'ISS-104',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 15,
            'source' => 'Factory Yarn Store',
            'destination' => 'National Sizing Mill',
            'party_name' => 'National Sizing Mill',
            'transaction_date' => now()->subDays(10)->toDateString(),
            'notes' => 'Additional 15 bags issued to complete beam lot',
        ]);

        BagTransaction::create([
            'transaction_type' => 'return_from_sizing',
            'reference_no' => 'RET-44',
            'quality' => '30/1 PC Combed',
            'quantity_bags' => 5,
            'source' => 'National Sizing Mill',
            'destination' => 'Factory Yarn Store',
            'party_name' => 'National Sizing Mill',
            'transaction_date' => now()->subDays(5)->toDateString(),
            'notes' => 'Unused balance yarn bags returned back from sizing person',
        ]);

        BagTransaction::create([
            'transaction_type' => 'purchase_factory',
            'reference_no' => 'CH-8845',
            'quality' => '40/1 100% Cotton Carded',
            'quantity_bags' => 30,
            'source' => 'Nishat Spinning Mills',
            'destination' => 'Factory Yarn Store',
            'party_name' => 'Internal Store',
            'transaction_date' => now()->subDays(8)->toDateString(),
            'notes' => 'Stored in Factory Godown Rack Y-3',
        ]);

        // 4. Sizing Slips & Beams
        $slip1 = SizingSlip::create([
            'slip_no' => 'SZ-2026-081',
            'sizing_party' => 'National Sizing Industries',
            'yarn_quality' => '30/1 PC Combed',
            'bags_allocated' => 75,
            'bags_used' => 65,
            'bags_returned_to_factory' => 5,
            'bags_held_with_sizing' => 5,
            'total_beams' => 4,
            'total_length_meters' => 18000.00,
            'slip_date' => now()->subDays(9)->toDateString(),
            'status' => 'completed',
            'notes' => 'Set of 4 Beams (4,500m each). 5 bags returned to factory, 5 bags in reserve with sizing master.',
        ]);

        $beam1 = SizingBeam::create([
            'sizing_slip_id' => $slip1->id,
            'beam_no' => 'BM-101',
            'yarn_quality' => '30/1 PC Combed',
            'total_length_meters' => 4500.00,
            'remaining_length_meters' => 1250.00,
            'assigned_loom_no' => 'Loom-01',
            'status' => 'mounted_on_loom',
            'mounted_date' => now()->subDays(7)->toDateString(),
            'notes' => 'Running on Loom-01 for Twill Weave 2/1',
        ]);

        $beam2 = SizingBeam::create([
            'sizing_slip_id' => $slip1->id,
            'beam_no' => 'BM-102',
            'yarn_quality' => '30/1 PC Combed',
            'total_length_meters' => 4500.00,
            'remaining_length_meters' => 2100.00,
            'assigned_loom_no' => 'Loom-02',
            'status' => 'mounted_on_loom',
            'mounted_date' => now()->subDays(6)->toDateString(),
            'notes' => 'Running on Loom-02 for Poplin Weave',
        ]);

        $beam3 = SizingBeam::create([
            'sizing_slip_id' => $slip1->id,
            'beam_no' => 'BM-103',
            'yarn_quality' => '30/1 PC Combed',
            'total_length_meters' => 4500.00,
            'remaining_length_meters' => 4500.00,
            'assigned_loom_no' => null,
            'status' => 'ready_in_store',
            'mounted_date' => null,
            'notes' => 'Stored in Beam Stand B-3 ready for mounting',
        ]);

        $beam4 = SizingBeam::create([
            'sizing_slip_id' => $slip1->id,
            'beam_no' => 'BM-104',
            'yarn_quality' => '30/1 PC Combed',
            'total_length_meters' => 4500.00,
            'remaining_length_meters' => 4500.00,
            'assigned_loom_no' => null,
            'status' => 'ready_in_store',
            'mounted_date' => null,
            'notes' => 'Stored in Beam Stand B-4',
        ]);

        $slip2 = SizingSlip::create([
            'slip_no' => 'SZ-2026-088',
            'sizing_party' => 'Kohinoor Sizing Mills',
            'yarn_quality' => '40/1 100% Cotton Carded',
            'bags_allocated' => 50,
            'bags_used' => 46,
            'bags_returned_to_factory' => 2,
            'bags_held_with_sizing' => 2,
            'total_beams' => 3,
            'total_length_meters' => 13500.00,
            'slip_date' => now()->subDays(4)->toDateString(),
            'status' => 'completed',
            'notes' => '3 Beams (4,500m each) for Cotton Sheeting',
        ]);

        $beam5 = SizingBeam::create([
            'sizing_slip_id' => $slip2->id,
            'beam_no' => 'BM-105',
            'yarn_quality' => '40/1 100% Cotton Carded',
            'total_length_meters' => 4500.00,
            'remaining_length_meters' => 3800.00,
            'assigned_loom_no' => 'Loom-03',
            'status' => 'mounted_on_loom',
            'mounted_date' => now()->subDays(2)->toDateString(),
            'notes' => 'Running on Loom-03',
        ]);

        // 5. Parts Inventory & Issuance
        $gripperRight = Part::create([
            'part_name' => 'Rapier Gripper (Right Hand)',
            'part_code' => 'GRP-RH-01',
            'category' => 'Gripper',
            'stock_quantity' => 14,
            'min_alert_threshold' => 5,
            'unit' => 'pcs',
            'unit_price' => 3800.00,
            'rack_location' => 'Rack P-1',
            'notes' => 'High-speed Rapier gripper head right',
        ]);

        $gripperLeft = Part::create([
            'part_name' => 'Rapier Gripper (Left Hand)',
            'part_code' => 'GRP-LH-02',
            'category' => 'Gripper',
            'stock_quantity' => 12,
            'min_alert_threshold' => 5,
            'unit' => 'pcs',
            'unit_price' => 3800.00,
            'rack_location' => 'Rack P-1',
            'notes' => 'High-speed Rapier gripper head left',
        ]);

        $rapierTape = Part::create([
            'part_name' => 'Carbon Fiber Rapier Tape',
            'part_code' => 'TAP-CF-10',
            'category' => 'Rapier',
            'stock_quantity' => 8,
            'min_alert_threshold' => 4,
            'unit' => 'pcs',
            'unit_price' => 9500.00,
            'rack_location' => 'Rack P-2',
            'notes' => 'Standard 190cm rapier tape',
        ]);

        $healdWires = Part::create([
            'part_name' => 'Simplex Heald Wires 330mm',
            'part_code' => 'HLD-330',
            'category' => 'Heald & Dropper',
            'stock_quantity' => 2500,
            'min_alert_threshold' => 1000,
            'unit' => 'pcs',
            'unit_price' => 3.50,
            'rack_location' => 'Box H-1',
            'notes' => 'Steel heald wires for dobby frames',
        ]);

        $dropPins = Part::create([
            'part_name' => 'Warp Stop Drop Pins',
            'part_code' => 'DRP-PIN',
            'category' => 'Heald & Dropper',
            'stock_quantity' => 4500,
            'min_alert_threshold' => 1500,
            'unit' => 'pcs',
            'unit_price' => 1.80,
            'rack_location' => 'Box D-1',
            'notes' => 'Electrical warp stop motion pins',
        ]);

        $weftSensor = Part::create([
            'part_name' => 'Piezo Weft Feeler Sensor',
            'part_code' => 'SNS-WEFT',
            'category' => 'Sensor',
            'stock_quantity' => 4,
            'min_alert_threshold' => 3,
            'unit' => 'pcs',
            'unit_price' => 12500.00,
            'rack_location' => 'Locker S-1',
            'notes' => 'Electronic optical weft detector',
        ]);

        // Parts Issuances
        PartIssuance::create([
            'part_id' => $gripperRight->id,
            'loom_no' => 'Loom-01',
            'issued_to_person' => 'Master Aslam (Mechanic)',
            'quantity' => 1,
            'unit_price' => 3800.00,
            'total_cost' => 3800.00,
            'issuance_date' => now()->subDays(4)->toDateString(),
            'remarks' => 'Replaced worn gripper clip on right side',
        ]);

        PartIssuance::create([
            'part_id' => $healdWires->id,
            'loom_no' => 'Loom-02',
            'issued_to_person' => 'Rashid Ali (Dobby Incharge)',
            'quantity' => 200,
            'unit_price' => 3.50,
            'total_cost' => 700.00,
            'issuance_date' => now()->subDays(3)->toDateString(),
            'remarks' => 'Frame replacement for high warp density quality',
        ]);

        PartIssuance::create([
            'part_id' => $gripperLeft->id,
            'loom_no' => 'Loom-03',
            'issued_to_person' => 'Master Aslam (Mechanic)',
            'quantity' => 1,
            'unit_price' => 3800.00,
            'total_cost' => 3800.00,
            'issuance_date' => now()->subDay()->toDateString(),
            'remarks' => 'Routine gripper overhaul',
        ]);

        // 6. Fabric Stocks
        FabricStock::create([
            'quality_name' => 'Cotton Sheeting 68x68 / 30x30',
            'construction' => '30x30 / 68x68 63" Grey',
            'width_inches' => 63.0,
            'total_meters_produced' => 28400.00,
            'total_rolls_in_stock' => 142,
            'current_stock_meters' => 14200.00,
            'committed_contract_meters' => 8000.00,
            'available_open_meters' => 6200.00,
            'unit_cost_meter' => 195.00,
            'notes' => 'Top running export quality grey fabric',
        ]);

        FabricStock::create([
            'quality_name' => 'Twill 2/1 108x56 / 40x40',
            'construction' => '40x40 / 108x56 63" Twill',
            'width_inches' => 63.0,
            'total_meters_produced' => 19500.00,
            'total_rolls_in_stock' => 96,
            'current_stock_meters' => 9600.00,
            'committed_contract_meters' => 5000.00,
            'available_open_meters' => 4600.00,
            'unit_cost_meter' => 240.00,
            'notes' => 'Uniform and workwear fabric',
        ]);

        FabricStock::create([
            'quality_name' => 'Poplin 133x72 / 40x40',
            'construction' => '40x40 / 133x72 58" Shirting',
            'width_inches' => 58.0,
            'total_meters_produced' => 15200.00,
            'total_rolls_in_stock' => 74,
            'current_stock_meters' => 7400.00,
            'committed_contract_meters' => 4000.00,
            'available_open_meters' => 3400.00,
            'unit_cost_meter' => 280.00,
            'notes' => 'Fine shirting plain weave fabric',
        ]);

        // 7. Sales Contracts (Multi quality contract)
        $apexCustomer = Customer::firstOrCreate(
            ['name' => 'Apex Dyeing & Finishing Mills'],
            ['phone' => '+92 300 8472911', 'status' => 'active']
        );
        $crescentCustomer = Customer::firstOrCreate(
            ['name' => 'Crescent Garments Ltd'],
            ['phone' => '+92 321 4455667', 'status' => 'active']
        );
        $hajiCustomer = Customer::firstOrCreate(
            ['name' => 'Haji Cloth Merchant (Faisalabad)'],
            ['phone' => '+92 312 9988776', 'status' => 'active']
        );

        $contract1 = SalesContract::create([
            'contract_no' => 'CNT-2026-041',
            'customer_id' => $apexCustomer->id,
            'buyer_name' => 'Apex Dyeing & Finishing Mills',
            'buyer_phone' => '+92 300 8472911',
            'fabric_quality' => 'Cotton Sheeting 68x68 / 30x30',
            'total_meters' => 15000.00,
            'rate_per_meter' => 235.00,
            'total_amount' => 3525000.00,
            'delivered_meters' => 7000.00,
            'remaining_meters' => 8000.00,
            'start_date' => now()->subDays(15)->toDateString(),
            'end_date' => now()->addDays(25)->toDateString(),
            'status' => 'active',
            'notes' => 'Bi-weekly delivery schedule of 3,500m per lot',
        ]);

        $contract2 = SalesContract::create([
            'contract_no' => 'CNT-2026-042',
            'customer_id' => $crescentCustomer->id,
            'buyer_name' => 'Crescent Garments Ltd',
            'buyer_phone' => '+92 321 4455667',
            'fabric_quality' => 'Twill 2/1 108x56 / 40x40',
            'total_meters' => 10000.00,
            'rate_per_meter' => 290.00,
            'total_amount' => 2900000.00,
            'delivered_meters' => 5000.00,
            'remaining_meters' => 5000.00,
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => 'active',
            'notes' => 'Strict quality inspection on delivery',
        ]);

        // 8. Sales Slips (Open Market & Contract Sales)
        Sale::create([
            'slip_no' => 'SLIP-9011',
            'sale_type' => 'contract',
            'sales_contract_id' => $contract1->id,
            'customer_id' => $apexCustomer->id,
            'buyer_name' => 'Apex Dyeing & Finishing Mills',
            'buyer_phone' => '+92 300 8472911',
            'fabric_quality' => 'Cotton Sheeting 68x68 / 30x30',
            'meters_sold' => 3500.00,
            'rolls_sold' => 35,
            'rate_per_meter' => 235.00,
            'subtotal' => 822500.00,
            'discount' => 2500.00,
            'net_total' => 820000.00,
            'payment_type' => 'credit',
            'payment_status' => 'paid',
            'sale_date' => now()->subDays(5)->toDateString(),
            'vehicle_no' => 'LEA-4491',
            'notes' => 'Lot-2 against Contract CNT-2026-041',
        ]);

        Sale::create([
            'slip_no' => 'SLIP-9012',
            'sale_type' => 'open_market',
            'sales_contract_id' => null,
            'customer_id' => $hajiCustomer->id,
            'buyer_name' => 'Haji Cloth Merchant (Faisalabad)',
            'buyer_phone' => '+92 312 9988776',
            'fabric_quality' => 'Poplin 133x72 / 40x40',
            'meters_sold' => 1200.00,
            'rolls_sold' => 12,
            'rate_per_meter' => 320.00,
            'subtotal' => 384000.00,
            'discount' => 4000.00,
            'net_total' => 380000.00,
            'payment_type' => 'cash',
            'payment_status' => 'paid',
            'sale_date' => now()->subDays(2)->toDateString(),
            'vehicle_no' => 'FSD-7102',
            'notes' => 'Spot market cash sale. Delivery ex-godown.',
        ]);

        // 9. Production Records (Loom-wise with Leakage / Waste Calculation)
        Production::create([
            'production_date' => now()->subDays(2)->toDateString(),
            'shift' => 'Day',
            'loom_no' => 'Loom-01',
            'operator_name' => 'Tariq Mehmood',
            'sizing_beam_id' => $beam1->id,
            'fabric_quality' => 'Twill 2/1 108x56 / 40x40',
            'start_meter' => 1200.00,
            'end_meter' => 1520.00,
            'net_meters_produced' => 320.00,
            'beam_meters_consumed' => 332.00,
            'leakage_waste_meters' => 12.00,
            'leakage_percentage' => 3.61,
            'picks_per_inch' => 56,
            'rpm' => 360,
            'efficiency_percentage' => 88.50,
            'remarks' => 'Normal running, minor weft stop',
        ]);

        Production::create([
            'production_date' => now()->subDays(2)->toDateString(),
            'shift' => 'Night',
            'loom_no' => 'Loom-01',
            'operator_name' => 'Bilal Hassan',
            'sizing_beam_id' => $beam1->id,
            'fabric_quality' => 'Twill 2/1 108x56 / 40x40',
            'start_meter' => 1520.00,
            'end_meter' => 1835.00,
            'net_meters_produced' => 315.00,
            'beam_meters_consumed' => 326.00,
            'leakage_waste_meters' => 11.00,
            'leakage_percentage' => 3.37,
            'picks_per_inch' => 56,
            'rpm' => 360,
            'efficiency_percentage' => 87.20,
            'remarks' => 'Smooth night shift',
        ]);

        Production::create([
            'production_date' => now()->subDay()->toDateString(),
            'shift' => 'Day',
            'loom_no' => 'Loom-02',
            'operator_name' => 'Zubair Akram',
            'sizing_beam_id' => $beam2->id,
            'fabric_quality' => 'Cotton Sheeting 68x68 / 30x30',
            'start_meter' => 2100.00,
            'end_meter' => 2445.00,
            'net_meters_produced' => 345.00,
            'beam_meters_consumed' => 358.00,
            'leakage_waste_meters' => 13.00,
            'leakage_percentage' => 3.63,
            'picks_per_inch' => 68,
            'rpm' => 350,
            'efficiency_percentage' => 89.10,
            'remarks' => 'High speed plain weave run',
        ]);

        Production::create([
            'production_date' => now()->subDay()->toDateString(),
            'shift' => 'Day',
            'loom_no' => 'Loom-03',
            'operator_name' => 'Nawaz Sharif',
            'sizing_beam_id' => $beam5->id,
            'fabric_quality' => 'Cotton Sheeting 68x68 / 30x30',
            'start_meter' => 0.00,
            'end_meter' => 340.00,
            'net_meters_produced' => 340.00,
            'beam_meters_consumed' => 352.00,
            'leakage_waste_meters' => 12.00,
            'leakage_percentage' => 3.41,
            'picks_per_inch' => 68,
            'rpm' => 345,
            'efficiency_percentage' => 86.40,
            'remarks' => 'New beam BM-105 mounted smoothly',
        ]);

        // 10. Ledgers
        Ledger::create([
            'party_name' => 'Al-Karam Spinning Mills',
            'party_type' => 'yarn_supplier',
            'transaction_date' => now()->subDays(12)->toDateString(),
            'voucher_no' => 'CH-8821',
            'description' => 'Yarn Purchase 100 bags 30/1 PC Combed',
            'debit' => 0.00,
            'credit' => 1450000.00,
            'balance' => -1450000.00,
            'notes' => 'Challan CH-8821',
        ]);

        Ledger::create([
            'party_name' => 'National Sizing Industries',
            'party_type' => 'sizing_mill',
            'transaction_date' => now()->subDays(9)->toDateString(),
            'voucher_no' => 'SZ-2026-081',
            'description' => 'Sizing labor charges for 4 beams (18,000m)',
            'debit' => 0.00,
            'credit' => 144000.00,
            'balance' => -144000.00,
            'notes' => 'Slip #SZ-2026-081',
        ]);

        Ledger::create([
            'party_name' => 'Apex Dyeing & Finishing Mills',
            'customer_id' => $apexCustomer->id,
            'party_type' => 'fabric_buyer',
            'transaction_date' => now()->subDays(5)->toDateString(),
            'voucher_no' => 'SLIP-9011',
            'description' => 'Fabric dispatch 3,500m under Contract CNT-2026-041',
            'debit' => 820000.00,
            'credit' => 0.00,
            'balance' => 820000.00,
            'notes' => 'Invoice SLIP-9011',
        ]);

        Ledger::create([
            'party_name' => 'Apex Dyeing & Finishing Mills',
            'customer_id' => $apexCustomer->id,
            'party_type' => 'fabric_buyer',
            'transaction_date' => now()->subDays(3)->toDateString(),
            'voucher_no' => 'RV-502',
            'description' => 'Bank payment received via Meezan Bank',
            'debit' => 0.00,
            'credit' => 820000.00,
            'balance' => 0.00,
            'notes' => 'Cleared invoice SLIP-9011',
        ]);
    }
}
