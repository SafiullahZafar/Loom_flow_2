<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'party_name' => 'nullable|string|max:150',
            'customer_id' => 'nullable|exists:customers,id',
            'party_type' => 'nullable|string|max:40',
            'ledger' => 'nullable|in:parties,expenses,cash_bank,inventory,income,equity',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);
        $ledgerTypes = [
            'parties' => ['yarn_supplier', 'sizing_mill', 'fabric_buyer'],
            'expenses' => ['expense'],
            'cash_bank' => ['cash_bank'],
            'inventory' => ['inventory'],
            'income' => ['income'],
            'equity' => ['equity'],
        ];
        $query = Ledger::with('customer');

        $query
            ->when($filters['party_name'] ?? null, fn ($builder, string $partyName) => $builder->where('party_name', $partyName))
            ->when($filters['customer_id'] ?? null, fn ($builder, int $customerId) => $builder->where('customer_id', $customerId))
            ->when($filters['party_type'] ?? null, fn ($builder, string $partyType) => $builder->where('party_type', $partyType))
            ->when($filters['ledger'] ?? null, function ($builder, string $ledger) use ($ledgerTypes): void {
                $builder->whereIn('party_type', $ledgerTypes[$ledger]);
            })
            ->when($filters['from'] ?? null, fn ($builder, string $date) => $builder->whereDate('transaction_date', '>=', $date))
            ->when($filters['to'] ?? null, fn ($builder, string $date) => $builder->whereDate('transaction_date', '<=', $date));

        $ledgers = $query->latest('transaction_date')->latest('id')->paginate(20)->withQueryString();

        $parties = Ledger::query()
            ->when($filters['ledger'] ?? null, function ($builder, string $ledger) use ($ledgerTypes): void {
                $builder->whereIn('party_type', $ledgerTypes[$ledger]);
            })
            ->select('party_name', 'party_type')->distinct()->orderBy('party_name')->get();

        $summaryQuery = Ledger::query()
            ->when($filters['ledger'] ?? null, function ($builder, string $ledger) use ($ledgerTypes): void {
                $builder->whereIn('party_type', $ledgerTypes[$ledger]);
            })
            ->when($filters['party_name'] ?? null, fn ($builder, string $partyName) => $builder->where('party_name', $partyName))
            ->when($filters['customer_id'] ?? null, fn ($builder, int $customerId) => $builder->where('customer_id', $customerId))
            ->when($filters['party_type'] ?? null, fn ($builder, string $partyType) => $builder->where('party_type', $partyType))
            ->when($filters['from'] ?? null, fn ($builder, string $date) => $builder->whereDate('transaction_date', '>=', $date))
            ->when($filters['to'] ?? null, fn ($builder, string $date) => $builder->whereDate('transaction_date', '<=', $date));
        $totalDebit = (clone $summaryQuery)->sum('debit');
        $totalCredit = (clone $summaryQuery)->sum('credit');
        $ledgerDifference = $totalDebit - $totalCredit;
        $activeLedger = $filters['ledger'] ?? 'all';
        $ledgerTitles = [
            'all' => 'General Ledger',
            'parties' => 'Party Ledgers',
            'expenses' => 'Expense Ledger',
            'cash_bank' => 'Cash & Bank Ledger',
            'inventory' => 'Inventory Ledger',
            'income' => 'Income Ledger',
            'equity' => 'Equity Ledger',
        ];
        $activeLedgerTitle = $ledgerTitles[$activeLedger];
        $customers = Customer::whereHas('ledgers')->orderBy('name')->get();

        return view('ledgers.index', compact(
            'ledgers',
            'parties',
            'customers',
            'filters',
            'totalDebit',
            'totalCredit',
            'ledgerDifference',
            'activeLedger',
            'activeLedgerTitle'
        ));
    }
}
