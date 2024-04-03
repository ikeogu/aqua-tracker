<?php

namespace App\Http\Controllers;

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
            'capital' => 100,
            'net_profit' => 200,
            'total_expense' => 300,
            'total_balance' => 400

        ];

        $farmDetails = [
            'total_units' => 100,
            'batch' => $farm->batches()->count(),
            'feed_available' => 300,
            'ponds' => $farm->ponds()->count(),
            'mortality_rate' => 500,
        ];

        $farmDetails = $this->pieChartData($farmDetails);

        $tasks = TaskResource::collection($farm->tasks()->latest()->paginate($request->per_page ?? 10))->response()->getData();

        return $this->success(
            message: 'Dashboard overview',
            data: [
                'overview' => $overview,
                'farm_details' => $farmDetails,
                'tasks' => $tasks
            ],
            code: 200
        );

    }

    private function pieChartData(array $data) : array
    {
        // Calculate the total sum of all values
        $totalSum = array_sum($data);

        // Calculate the percentages
        $percentages = [];
        foreach ($data as $key => $value) {
            $percentages[$key] = round((($value / $totalSum) * 100),2);
        }

        // Return the percentages
        return [
            'data' => $data,
            'percentages' => $percentages,
            'labels' => array_keys($data)
        ];
    }

}
