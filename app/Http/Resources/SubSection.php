<?php

namespace App\Http\Resources;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubSection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'sub_section_name' => $this->sub_section_name, 
            'sub_section_types'=> $this->sub_section_types ? SubSectionType::collection($this->sub_section_types) : [],
        ];
    }
}
