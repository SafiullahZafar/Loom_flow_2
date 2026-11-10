<?php

namespace App\Services;

use App\Models\FabricStock;
use App\Models\FabricStockMovement;
use App\Models\SalesContract;
use App\Models\ThaanRoll;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FactoryStockService
{
    /** Increment checked/returned stock or decrement dispatched/disposed stock once. */
    public function move(FabricStock $fabric, float $meters, int $rollDelta, string $date, string $note): void
    {
        $fabric = FabricStock::whereKey($fabric->id)->lockForUpdate()->firstOrFail();
        $newBalance = round((float) $fabric->current_stock_meters + $meters, 2);
        if ($newBalance < -0.001) {
            throw ValidationException::withMessages(['roll_ids' => 'Not enough fabric stock for this dispatch.']);
        }
        $fabric->current_stock_meters = max(0, $newBalance);
        $fabric->total_rolls_in_stock = max(0, (int) $fabric->total_rolls_in_stock + $rollDelta);
        if ($meters > 0) {
            $fabric->daily_received_meters = (float) $fabric->daily_received_meters + $meters;
        }
        $fabric->save();
        $movement = FabricStockMovement::where('fabric_stock_id', $fabric->id)->whereDate('movement_date', $date)->lockForUpdate()->first();
        if (! $movement) {
            $movement = new FabricStockMovement(['fabric_stock_id' => $fabric->id, 'movement_date' => $date, 'previous_stock_meters' => max(0, $newBalance - $meters)]);
        }
        $movement->received_meters = (float) ($movement->received_meters ?? 0) + max(0, $meters);
        $movement->dispatch_meters = (float) ($movement->dispatch_meters ?? 0) + max(0, -$meters);
        $movement->rolls_received = (int) ($movement->rolls_received ?? 0) + max(0, $rollDelta);
        $movement->closing_stock_meters = $fabric->current_stock_meters;
        $movement->remarks = trim(($movement->remarks ? $movement->remarks.'; ' : '').$note);
        $movement->save();
        $this->refreshAvailability($fabric);
    }

    public function refreshAvailability(FabricStock $fabric): void
    {
        $committed = (float) SalesContract::where('fabric_stock_id', $fabric->id)
            ->where('status', 'active')->where('contract_type', 'Grey Sale')->sum('remaining_meters');
        $customerStock = (float) ThaanRoll::where('fabric_stock_id', $fabric->id)
            ->where('owner_type', 'customer')->whereIn('status', ['ready', 'returned_ready'])->sum('length_meters');
        $fabric->committed_contract_meters = min((float) $fabric->current_stock_meters, $committed);
        $fabric->available_open_meters = max(0, (float) $fabric->current_stock_meters - $customerStock - (float) $fabric->committed_contract_meters);
        $fabric->save();
    }
}
