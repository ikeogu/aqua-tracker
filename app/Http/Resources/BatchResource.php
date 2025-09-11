<?php

namespace App\Http\Resources;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
/** @mixin \App\Models\Batch */
class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $totalInventories = $this->inventories()->sum('amount');
        $totalExpenses = 0;

        // Loop through the expenses and sum the amount for the specific batch_id
        foreach ($this->expenses() as $expense) {
            $splittedForBatch = is_string($expense->splitted_for_batch) ?
                json_decode($expense->splitted_for_batch, true) : $expense->splitted_for_batch;

            foreach ($splittedForBatch as $split) {
                if ($split['batch_id'] === $this->id) {
                    $totalExpenses += $split['amount'];
                }
            }
        }
        $totalCapital = $this->amount_spent + $totalExpenses + $totalInventories;

        $totalHarvest = $this->harvests()->pluck('id')->toArray();
        $totalSales = Purchase::whereIn('harvest_id', $totalHarvest)->sum('amount');

        $totalProfit = $totalSales - $totalCapital;
        return [
            'id' => $this->id,
            'type' => 'batch',
            'attributes' => [
                'name' => $this->name,
                'unit_purchase' => $this->unit_purchase,
                'price_per_unit' => intval($this->price_per_unit),
                'amount_spent' => intval($this->amount_spent),
                'fish_specie' => $this->fish_specie,
                'fish_type' => $this->fish_type,
                'vendor' => $this->vendor,
                'status' => $this->status,
                'date_purchased' => $this->date_purchased
            ],

            'other_details' => [
                'total_capital' => $totalCapital,
                'total_expenses' => $totalExpenses,
                'total_profit' => $totalProfit,
                'total_kg' => ceil(Purchase::whereIn('harvest_id', $totalHarvest)->sum('size')),
                'total_pc' => ceil(Purchase::whereIn('harvest_id', $totalHarvest)->sum('pieces')),
                'total_feed' => $this->inventories()->sum('quantity'),
                'inventories' => $this->inventories->map(function ($inventory) {
                    return [
                        'id' => $inventory->id,
                        'amount' => $inventory->amount,
                        'date' => $inventory->created_at,
                        'brand' => $inventory->name,
                        'quantity' => $inventory->quantity,
                        'size' => $inventory->size,
                    ];
                }),
            ],
            'farm' => [
                'id' => $this->farm->id,
                'name' => $this->farm->name
            ]
        ];
    }
}
