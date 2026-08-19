<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PackageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($package) {
            return [
                'id' => $package->id,
                'number' => $package->num_package,
                'status' => $package->status,
                'return_digitator' => formatDMY($package->return_digitator),
                'accept_return_digitator' => formatDMY($package->accept_return_digitator),
                'environment_id' => $package->environment_id,
                'observations' => $package->observations,
                'count_receptions' => count($package->receptions)
            ];
        })->toArray();        
    }
}
