<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReceptionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($reception) {
            return [
                'file_number' => $reception->file_number,
                'environment' => $reception->environment_file->entorno,
                'delivered_by' => $reception->user->name." ".$reception->user->last_name,
                'package_id' => $reception->package_id,
                'relationships' => [
                    'format' => new FormatResource($reception->bases),
                ],
            ];
        })->toArray();
    }
}
