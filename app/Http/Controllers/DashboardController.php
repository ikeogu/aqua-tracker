<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Resources\TaskResource;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function __invoke(Request $request, Farm $farm) : JsonResponse
    {
        $overview = [
            'capital' => $farm->inventories()->sum('amount') + $farm->batches()->sum('amount_spent'),
            'net_profit' => $farm->purchases()->sum('amount') - $farm->inventories()->sum('amount') - $farm->batches()->sum('amount_spent'),
            'total_expense' => $farm->expenses()->sum('total_amount'),

        ];


        $batchesId = $farm->batches()->where('status', Status::INSTOCK->value)->pluck('id')->toArray();
        $farmDetails = [
            'total_units' => (int) $farm->ponds()->whereIn('batch_id', $batchesId)->sum('unit'),
            'batch' => $farm->batches()->where('status', Status::INSTOCK->value)->count(),
            'feed_available' => $farm->inventories()->where('status', Status::INSTOCK->value)->sum('quantity') + $farm->inventories()->where('status', Status::SOLDOUT->value)->sum('left_over'),
            'ponds' => $farm->ponds()->whereIn('batch_id', $batchesId)->count(),
            'mortality_rate' => (int) $farm->ponds()->whereIn('batch_id', $batchesId)->sum('mortality_rate'),
        ];

        //


        $farmDetails = $this->pieChartData($farmDetails);

        return $this->success(
            message: 'Dashboard overview',
            data: [
                'overview' => $overview,
                'farm_details' => $farmDetails,
                'graph_data' => $this->linearGraphPerMonth($request, $farm),

            ],
            code: 200
        );

    }

    private function pieChartData(array $data) : array
    {

        // Calculate the total sum of all values
        $totalSum = array_sum($data);
        // Calculate the percentages
        $pieData = [];
        foreach ($data as $key => $value) {
            $pieData[] = [
                'name' => $key,
                'value' => ($totalSum) ? round((($value / $totalSum) * 100),2) : 0
            ];

        }

        // Return the percentages
        return [
            'data' => $data,
            'pie_data' => $pieData
        ];
    }


    private function linearGraphPerMonth(Request $request, Farm $farm) : array
    {
       $months = [
           'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
       ];

       $year = $request->year ?? now()->year;

       $inventoriesPerMonth = $batchesPerMonth =[];
       foreach ($months as $month) {
            $inventoriesPerMonth[$month] = $farm->inventories()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', date('m', strtotime($month)))
                ->get()->groupBy(function($inventory) {
                    return $inventory->created_at->format('M');
                });


            $batchesPerMonth[$month] = $farm->batches()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', date('m', strtotime($month)))
                ->get()->groupBy(function($batch) {
                return $batch->created_at->format('M');
                });

        }


        $capitalPerMonth =  [];

        foreach ($months as $month) {
            $capital = 0;
            $net_profit = 0;
            $total_expense = 0;

            if (isset($inventoriesPerMonth[$month][$month], $batchesPerMonth[$month][$month])) {
                $capital = $inventoriesPerMonth[$month][$month]->sum('amount') + $batchesPerMonth[$month][$month]->sum('amount_spent');
                $net_profit = $farm->purchases()->whereMonth('created_at', $month)->sum('amount') - $inventoriesPerMonth[$month][$month]->sum('amount') - $batchesPerMonth[$month][$month]->sum('amount_spent');
            }

            $expenseQuery = $farm->expenses()->whereMonth('created_at', $month);
            if ($expenseQuery->exists()) {
                $total_expense = $expenseQuery->sum('total_amount');
            }

            $capitalPerMonth[] = [
                'name' => $month,
                'capital' => $capital,
                'net_profit' => $net_profit,
                'total_expense' => $total_expense,
            ];
        }

        return $capitalPerMonth;
    }
}