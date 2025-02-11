<?php

namespace App\Http\Resources;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;


class Division extends JsonResource
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
            'division_name' => $this->division_name,    
            'sections'=> $this->sections ? Section::collection($this->sections) : [],
        ];
    }
}
