<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LedgerPostingService
{
    /**
     * @param  array<int, array{role: string, party_name: string, party_type: string, transaction_date: mixed, voucher_no: ?string, description: string, debit: float, credit: float, notes: ?string, customer_id?: ?int}>  $lines
     */
    public function post(string $sourceType, int $sourceId, array $lines): void
    {
        DB::transaction(function () use ($sourceType, $sourceId, $lines): void {
            $existingLines = Ledger::where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->lockForUpdate()
                ->get();
            $affectedParties = [];

            foreach ($existingLines as $existingLine) {
                $affectedParties[$this->balanceKey($existingLine->party_type, $existingLine->customer_id, $existingLine->party_name)] = [
                    'party_type' => $existingLine->party_type,
                    'customer_id' => $existingLine->customer_id,
                    'party_name' => $existingLine->party_name,
                ];
            }

            foreach ($lines as $line) {
                $customerId = $line['customer_id'] ?? null;
                if ($line['party_type'] === 'fabric_buyer' && ! $customerId) {
                    $customers = Customer::query()
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($line['party_name']))])
                        ->get();
                    if ($customers->count() !== 1) {
                        throw ValidationException::withMessages([
                            'customer_id' => 'Select a registered customer for this Ledger entry; the typed party name is missing or ambiguous.',
                        ]);
                    }
                    $customer = $customers->first();
                    $customerId = $customer->id;
                }
                $partyName = $customerId ? Customer::query()->findOrFail($customerId)->name : $line['party_name'];

                Ledger::updateOrCreate(
                    [
                        'source_type' => $sourceType,
                        'source_id' => $sourceId,
                        'source_role' => $line['role'],
                    ],
                    [
                        'party_name' => $partyName,
                        'customer_id' => $customerId,
                        'party_type' => $line['party_type'],
                        'transaction_date' => $line['transaction_date'],
                        'voucher_no' => $line['voucher_no'],
                        'description' => $line['description'],
                        'debit' => round($line['debit'], 2),
                        'credit' => round($line['credit'], 2),
                        'notes' => $line['notes'],
                    ]
                );
                $affectedParties[$this->balanceKey($line['party_type'], $customerId, $partyName)] = [
                    'party_type' => $line['party_type'],
                    'customer_id' => $customerId,
                    'party_name' => $partyName,
                ];
            }

            foreach ($affectedParties as $party) {
                $this->recalculateBalance($party['party_name'], $party['party_type'], $party['customer_id']);
            }
        });
    }

    private function balanceKey(string $partyType, ?int $customerId, string $partyName): string
    {
        return $partyType === 'fabric_buyer' && $customerId
            ? $partyType.'|customer:'.$customerId
            : $partyType.'|name:'.$partyName;
    }

    private function recalculateBalance(string $partyName, string $partyType, ?int $customerId): void
    {
        $balance = 0.0;
        Ledger::where('party_type', $partyType)
            ->when($partyType === 'fabric_buyer' && $customerId, fn ($query) => $query->where('customer_id', $customerId), function ($query) use ($partyName): void {
                $query->where('party_name', $partyName);
            })
            ->lockForUpdate()
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get()
            ->each(function (Ledger $ledger) use (&$balance): void {
                $balance = round($balance + (float) $ledger->debit - (float) $ledger->credit, 2);
                if ((float) $ledger->balance !== $balance) {
                    $ledger->update(['balance' => $balance]);
                }
            });
    }
}
