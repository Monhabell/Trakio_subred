<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\V1\FormatResource;

class ReceptionResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'file_number' => $this->file_number,
            'environment' => $this->environment_file->entorno,
            'delivered_by' => $this->user->name." ".$this->user->last_name,
            'relationships' => [
                'format' => new FormatResource($this->bases),
                'package' => $this->packages
            ]
        ];
    }
}
