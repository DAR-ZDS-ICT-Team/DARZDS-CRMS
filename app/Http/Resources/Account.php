<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Account extends JsonResource
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
            'name' => $this->name,
            'designation' => $this->designation,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'office'=> $this->office,
            'division'=> $this->division,
            'section'=> $this->section, 
            'account_type' => $this->account_type,
        ];
    }
}
