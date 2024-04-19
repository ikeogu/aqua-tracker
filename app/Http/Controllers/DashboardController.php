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

        $batches_id =$farm->batches()->where('status', Status::INSTOCK->value)->pluck('id');
        $farmDetails = [
            'total_units' => $farm->ponds()->whereIn('batch_id', $batches_id)->sum('unit'),
            'batch' => $farm->batches()->where('status', Status::INSTOCK->value)->count(),
            'feed_available' => $farm->inventories()->where('status', Status::INSTOCK->value)->sum('quantity') + $farm->inventories()->where('status', Status::SOLDOUT->value)->sum('left_over'),
            'ponds' => $farm->ponds()->whereIn('batch_id', $batches_id)->count(),
            'mortality_rate' => $farm->ponds()->whereIn('batch_id', $batches_id)->sum('mortality_rate'),
        ];

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

            $capitalPerMonth[] = [

                'name' => $month,
                'capital' => $inventoriesPerMonth[$month]->sum('amount') + $batchesPerMonth[$month]->sum('amount_spent'),
                'net_profit' => $farm->purchases()->whereMonth('created_at', $month)->sum('amount') - $inventoriesPerMonth[$month]->sum('amount') - $batchesPerMonth[$month]->sum('amount_spent'),
                'total_expense' => $farm->expenses()->whereMonth('created_at', $month)->sum('total_amount'),

            ];
        }

        return $capitalPerMonth;
    }
}
