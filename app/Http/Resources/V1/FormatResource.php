<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'header_time' => floatval($this->header_time),
            'user_time' => floatval($this->body_time),
            'environment' => $this->environment_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'sds_id' => $this->sds_id
        ];
    }
}
