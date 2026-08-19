<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'number' => $this->num_package,
            'status' => $this->status,
            'return_digitator' => $this->return_digitator,
            'accept_return_digitator' => $this->accept_return_digitator,
            'environment_id' => $this->environment_id,
            'observations' => $this->observations,
            'receptions' => new ReceptionCollection($this->receptions)
        ];
    }
}
