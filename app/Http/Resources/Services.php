<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Services extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'division_id' => $this->division_id,
        'section_id' => $this->section_id,
        'service_name' => $this->service_name,
        // Other fields...
    ];
}
}
