<?php

namespace App\Http\Resources;

use App\Models\Batch;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

/** @mixin \App\Models\Harvest */
class HarvestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        $totalHarvest = $this->purchases()->sum('amount');
        $inventories = Inventory::where('batch_id', $this->batch_id)->sum('amount');

        $expenses =  DB::table('expenses')
            ->selectRaw('SUM(j.amount) as total')
            ->fromRaw("expenses, JSON_TABLE(expenses.splitted_for_batch, '$[*]'
                    COLUMNS (
                        batch_id VARCHAR(255) PATH '$.batch_id',
                        amount DECIMAL(10,2) PATH '$.amount'
                    )
                ) as j")
            ->where('j.batch_id', $this->batch_id)
            ->value('total');

        $batch = Batch::find($this->batch->id)->amount_spent;


        $data = [
            'total_harvest' => $totalHarvest,
            'total_capital' => $inventories + $batch + $expenses,
            'total_profit' => $totalHarvest - ($inventories + $batch + $expenses),
            'expenses' => $expenses,

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
                'total_pieces' => $this->purchases->sum('pieces'),
                'total_amount' => $this->purchases->sum('amount'),
                'total_size' => $this->purchases->sum('size'),
                'total_sales' => number_format($this->purchases->sum('amount'), 2),
            ],
        ];

        return [
            'card_data' => $data,
            'details' => $details
        ];
    }
}
