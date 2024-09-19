<?php

namespace App\Http\Resources;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $totalExpenses = array_sum(array_filter($this->expenses(), function ($item) {
            return is_numeric($item);
        }));
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
                'total_kg' => $this->inventories()->sum('size'),
                'total_pc' => 0,
                'total_feed' => $this->inventories()->sum('quantity'),
                'inventories' => $this->inventories->map(function ($inventory) {
                    return [
                        'id' => $inventory->id,
                        'amount' => $inventory->amount,
                        'date' => $inventory->date,
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
