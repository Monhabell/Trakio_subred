<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'document' => $this->document,
            'phone' => $this->phone,
            'address' => $this->address,
            'rh' => $this->rh,
            'contract' => $this->contract,
            'birthdate' => formatDMY($this->birthdate),
            'sex' => $this->sex,
            'ethnicity' => $this->ethnicity,
            'eps' => $this->eps,
            'afp' => $this->afp,
            'arl' => $this->arl,
            'caja' => $this->caja,
            'url_img' => $this->url_img
        ];
    }
}