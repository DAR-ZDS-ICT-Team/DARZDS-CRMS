<?php

namespace App\Http\Resources;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAttributeRatings extends JsonResource
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
            'rate_score' => $this->rate_score,    
            'importance_rate_score' => $this->importance_rate_score,
            'created_at' => Carbon::parse($this->created_at)->format("mm/dd/YY"), 
            'updated_at' => Carbon::parse($this->updated_at)->format("mm/dd/YY"),
            'dimension'=> $this->dimension ? new Dimension( $this->dimension) : null,
            'customer'=> $this->customer ? new Customer( $this->customer) : null,

        ];
    }
}
