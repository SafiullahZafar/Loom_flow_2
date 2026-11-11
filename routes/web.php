<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BagController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FabricStockController;
use App\Http\Controllers\FactoryCheckingController;
use App\Http\Controllers\FactoryOrderController;
use App\Http\Controllers\FactoryParchiController;
use App\Http\Controllers\FactoryReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SizingController;
use App\Http\Controllers\YarnQualityController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/quick-login', [AuthController::class, 'quickLogin'])->name('quick-login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Integrated grey-fabric factory operations: Parta, orders, looms, checking and parchi.
    Route::get('/factory/reports', [FactoryReportController::class, 'index'])->name('factory.reports');
    Route::post('/factory/wages', [FactoryReportController::class, 'wage'])->name('factory.wage.store');
    Route::post('/factory/stoppages', [FactoryReportController::class, 'stoppage'])->name('factory.stoppage.store');
    Route::get('/factory/orders', [FactoryOrderController::class, 'index'])->name('factory.orders');
    Route::post('/factory/parta', [FactoryOrderController::class, 'parta'])->name('factory.parta');
    Route::post('/factory/orders', [FactoryOrderController::class, 'store'])->name('factory.order.store');
    Route::post('/factory/looms/assign', [FactoryOrderController::class, 'assign'])->name('factory.loom.assign');
    Route::post('/factory/beam', [FactoryOrderController::class, 'beam'])->name('factory.beam');
    Route::post('/factory/orders/{order}/close', [FactoryOrderController::class, 'close'])->name('factory.order.close');
    Route::get('/factory/orders/{order}/closing', [FactoryOrderController::class, 'closing'])->name('factory.order.closing');
    Route::get('/factory/checking', [FactoryCheckingController::class, 'index'])->name('factory.checking');
    Route::post('/factory/checking', [FactoryCheckingController::class, 'store'])->name('factory.checking.store');
    Route::get('/factory/parchis', [FactoryParchiController::class, 'index'])->name('factory.parchis');
    Route::post('/factory/kachi', [FactoryParchiController::class, 'dispatch'])->name('factory.kachi.store');
    Route::post('/factory/pakki', [FactoryParchiController::class, 'pakki'])->name('factory.pakki.store');
    Route::post('/factory/returns', [FactoryParchiController::class, 'returnThaan'])->name('factory.returns.store');
    Route::post('/factory/disposition', [FactoryParchiController::class, 'disposition'])->name('factory.disposition.store');
    Route::get('/factory/parchis/{kachi}/print', [FactoryParchiController::class, 'printKachi'])->name('factory.kachi.print');
    Route::get('/factory/pakki/{pakki}/print', [FactoryParchiController::class, 'printPakki'])->name('factory.pakki.print');
    Route::post('/factory/own-sale', [FactoryParchiController::class, 'ownSale'])->name('factory.own-sale.store');

    Route::post('/yarn-qualities', [YarnQualityController::class, 'store'])->name('yarn-qualities.store');

    // Expenses (fixed schedules and variable payments)
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/fixed', [ExpenseController::class, 'storeFixedExpense'])->name('expenses.fixed.store');
    Route::post('/expenses/fixed/{fixedExpense}/record', [ExpenseController::class, 'recordFixedExpense'])->name('expenses.fixed.record');
    Route::patch('/expenses/fixed/{fixedExpense}/status', [ExpenseController::class, 'toggleFixedExpense'])->name('expenses.fixed.status');
    Route::post('/expenses/variable', [ExpenseController::class, 'storeVariableExpense'])->name('expenses.variable.store');

    // 1. Bags (Yarn management, purchase split, store stock & sizing history)
    Route::get('/bags', [BagController::class, 'index'])->name('bags.index');
    Route::get('/bags/purchases', [BagController::class, 'purchases'])->name('bags.purchases');
    Route::get('/bags/ledger', [BagController::class, 'ledger'])->name('bags.ledger');
    Route::get('/bags/sizing-usage', [BagController::class, 'sizingUsage'])->name('bags.sizing-usage');
    Route::post('/bags/purchase', [BagController::class, 'storePurchase'])->name('bags.purchase');
    Route::post('/bags/customer-receipts', [BagController::class, 'receiveCustomerBags'])->name('bags.customer-receipts.store');
    Route::post('/bags/issue', [BagController::class, 'issueStock'])->name('bags.issue');
    Route::post('/bags/return', [BagController::class, 'returnStock'])->name('bags.return');

    // 2. Sizing (Sizing slip entry, beam tracking, bags used & returned)
    Route::get('/sizing', [SizingController::class, 'index'])->name('sizing.index');
    Route::get('/sizing/beams', [SizingController::class, 'beams'])->name('sizing.beams');
    Route::get('/sizing/slips', [SizingController::class, 'slips'])->name('sizing.slips');
    Route::get('/sizing/balance', [SizingController::class, 'balance'])->name('sizing.balance');
    Route::post('/sizing/slip', [SizingController::class, 'storeSlip'])->name('sizing.slip.store');
    Route::post('/sizing/beams/{beam}/assign', [SizingController::class, 'assignBeam'])->name('sizing.beam.assign');
    Route::post('/sizing/beams/{beam}/exhaust', [SizingController::class, 'exhaustBeam'])->name('sizing.beam.exhaust');

    // 3. Parts (Parts store, gripper/accessories inventory, factory issuance)
    Route::get('/parts', [PartController::class, 'index'])->name('parts.index');
    Route::get('/parts/inventory', [PartController::class, 'inventory'])->name('parts.inventory');
    Route::get('/parts/issuances', [PartController::class, 'issuances'])->name('parts.issuances');
    Route::post('/parts', [PartController::class, 'storePart'])->name('parts.store');
    Route::post('/parts/issue', [PartController::class, 'issuePart'])->name('parts.issue');

    // 4. Fabric Stock (Finished goods, open stock vs contract commitment)
    Route::get('/fabric-stock', [FabricStockController::class, 'index'])->name('fabric.index');
    Route::get('/fabric-stock/inventory', [FabricStockController::class, 'inventory'])->name('fabric.inventory');
    Route::get('/fabric-stock/contracts', [FabricStockController::class, 'contracts'])->name('fabric.contracts');
    Route::post('/fabric-stock', [FabricStockController::class, 'storeStock'])->name('fabric.store');
    Route::post('/fabric-stock/loom-quality', [FabricStockController::class, 'updateLoomFabricQuality'])->name('fabric.loom-quality');

    // 5. Sales (Sales slips, open market sales, multi-quality contracts)
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/slips', [SalesController::class, 'slips'])->name('sales.slips');
    Route::get('/sales/contracts', [SalesController::class, 'contracts'])->name('sales.contracts');
    Route::post('/sales', [SalesController::class, 'storeSale'])->name('sales.store');
    Route::post('/sales/contract', [SalesController::class, 'storeContract'])->name('sales.contract.store');

    // 8. Invoices (customer billing, receipts, and cash/bank account balances)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/records', [InvoiceController::class, 'invoices'])->name('invoices.invoices');
    Route::get('/invoices/customers', [InvoiceController::class, 'customers'])->name('invoices.customers');
    Route::get('/invoices/payments', [InvoiceController::class, 'payments'])->name('invoices.payments');
    Route::get('/invoices/accounts', [InvoiceController::class, 'accounts'])->name('invoices.accounts');
    Route::post('/invoices/customers', [InvoiceController::class, 'storeCustomer'])->name('invoices.customer.store');
    Route::post('/invoices', [InvoiceController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/invoices/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payment.store');
    Route::post('/invoices/accounts', [InvoiceController::class, 'storeAccount'])->name('invoices.account.store');
    Route::post('/invoices/accounts/transactions', [InvoiceController::class, 'storeAccountTransaction'])->name('invoices.account-transaction.store');

    // 6. Production (Loom-wise daily production, beam length consumption, leakage/loss calculation)
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/loom-summary', [ProductionController::class, 'loomSummary'])->name('production.loom-summary');
    Route::get('/production/leakage', [ProductionController::class, 'leakage'])->name('production.leakage');
    Route::get('/production/history', [ProductionController::class, 'history'])->name('production.history');
    Route::post('/production', [ProductionController::class, 'storeProduction'])->name('production.store');

    // 7. Ledgers (Party ledgers & audit trail)
    Route::get('/ledgers', [LedgerController::class, 'index'])->name('ledgers.index');
});
