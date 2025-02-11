<?php

namespace App\Http\Resources;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Section extends JsonResource
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
            'section_name' => $this->section_name,    
            'sub_sections'=> $this->sub_sections ? SubSection::collection($this->sub_sections) : [],
        ];
    }
}
