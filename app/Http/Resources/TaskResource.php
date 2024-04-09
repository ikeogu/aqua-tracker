<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->due_date);
        return [
            'id' => $this->id,
            'type' => 'task',
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'end_date' => $this->due_date,
                'start_date' => $this->start_date,
                'repeat' => $this->repeat,
                'time_left' => $startDate->diffAsCarbonInterval($endDate)->forHumans(),
                'created_at' => $this->created_at,


            ],
        ];
    }
}
