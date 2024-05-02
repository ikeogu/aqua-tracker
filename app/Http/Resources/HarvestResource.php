<?php

namespace App\Http\Resources;

use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HarvestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        $totalHarvest = Purchase::where('harvest_id', $this->id)->sum('amount');
        $inventories = Inventory::where('batch_id', $this->batch_id)->sum('amount');
        $expenses = Expense::where('splitted_for_batch->batch_id', $this->batch_id)->sum('splitted_for_batch->amount');


        $data = [
            'total_harvest' => number_format($totalHarvest,2),
            'total_capital' => number_format($inventories + $expenses,2),
             'total_profit' => number_format($totalHarvest - ($inventories + $expenses),2),
             'expenses' => number_format($expenses,2),

        ];

        $details = [
            'id' => $this->id,
            'type' => 'harvest',
            'attributes' => [
                'name' => $this->name,
                'consultant' => $this->consultant,
                'created_at' => $this->created_at,
            ],

            'relationships' => [
                'farm' => [
                    'data' => [
                        'id' => $this->farm_id,
                        'type' => 'farm'
                    ]
                ],
                'batch' => [
                    'data' => [
                        'id' => $this->batch_id,
                        'type' => 'batch',
                        'name' => $this->batch->name,
                    ]
                ],

                'customers' => CustomerResource::collection($this->customers),

            ],
        ];

        return [
            'card_data' => $data,
            'details' => $details
        ];
    }
}
