<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Support\Facades\DB;

class LedgerPostingService
{
    /**
     * @param  array<int, array{role: string, party_name: string, party_type: string, transaction_date: mixed, voucher_no: ?string, description: string, debit: float, credit: float, notes: ?string}>  $lines
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
                $affectedParties[$existingLine->party_type.'|'.$existingLine->party_name] = [
                    'party_type' => $existingLine->party_type,
                    'party_name' => $existingLine->party_name,
                ];
            }

            foreach ($lines as $line) {
                Ledger::updateOrCreate(
                    [
                        'source_type' => $sourceType,
                        'source_id' => $sourceId,
                        'source_role' => $line['role'],
                    ],
                    [
                        'party_name' => $line['party_name'],
                        'party_type' => $line['party_type'],
                        'transaction_date' => $line['transaction_date'],
                        'voucher_no' => $line['voucher_no'],
                        'description' => $line['description'],
                        'debit' => round($line['debit'], 2),
                        'credit' => round($line['credit'], 2),
                        'notes' => $line['notes'],
                    ]
                );
                $affectedParties[$line['party_type'].'|'.$line['party_name']] = [
                    'party_type' => $line['party_type'],
                    'party_name' => $line['party_name'],
                ];
            }

            foreach ($affectedParties as $party) {
                $this->recalculateBalance($party['party_name'], $party['party_type']);
            }
        });
    }

    private function recalculateBalance(string $partyName, string $partyType): void
    {
        $balance = 0.0;
        Ledger::where('party_name', $partyName)
            ->where('party_type', $partyType)
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
