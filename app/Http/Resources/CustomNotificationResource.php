<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'type' => 'notifications',
            "attributes" => [
                'title' => $this->data['title'],
                'body' => $this->data['message'] ?? $this->data['body'] ,
                'readAt' => !empty($this->read_at) ? true : false,
                'created_at' => $this->created_at
            ]
        ];
    }
}
