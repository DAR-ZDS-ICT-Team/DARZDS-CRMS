<?php

namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
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
            'name' => $this->name,    
            'email' => $this->email,
            'sex' => $this->sex,
            'age_group' => $this->age_group,
            'client_type' => $this->client_type,
            'digital_literacy' => $this->digital_literacy,
            'pwd' => $this->pwd,
            'pregnant' => $this->pregnant,
            'senior_citizen' => $this->senior_citizen,
            'pwd' => $this->pwd,
            'created_at' => Carbon::parse($this->created_at)->format("mm/dd/YY"), 
            'updated_at' => Carbon::parse($this->updated_at)->format("mm/dd/YY"),
        ];
    }
}