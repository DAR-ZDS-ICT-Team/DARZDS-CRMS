<?php

namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Dimension extends JsonResource
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
            'slug' => $this->slug,    
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => Carbon::parse($this->created_at)->format("mm/dd/YY"), 
            'updated_at' => Carbon::parse($this->updated_at)->format("mm/dd/YY"),
        ];
    }
}
