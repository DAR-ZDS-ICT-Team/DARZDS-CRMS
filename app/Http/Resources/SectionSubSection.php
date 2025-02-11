<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionSubSection extends JsonResource
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
            'section'=> $this->section ? new Section( $this->section) : null,
            'sub_section'=> $this->sub_section ? new SubSection( $this->sub_section) : null,
        ];
    }
}
