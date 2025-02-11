<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ShowDateCSFForm extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'is_displayed' => $this->is_displayed,    
        ];
    }
}
