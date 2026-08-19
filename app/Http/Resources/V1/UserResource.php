<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\ProfileResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'is_active' => $this->is_active,
            'terms_accepted' => $this->terms_accepted,
            'environment' => $this->entorno->entorno,
            'subnet' => $this->subnet->name,
            'role' => $this->role->name,
            'relationships' => [
               'profile' => new ProfileResource($this->dataUser)
            ],
        ];
    }
}