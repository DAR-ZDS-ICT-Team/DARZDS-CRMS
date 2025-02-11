<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAttributeRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',  
        'dimension_id',
        'rate_score',
        'importance_rate_score',
        'created_at',
        'updated_at',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id' ,'id');
    }

    public function dimension(){
        return $this->belongsTo(Dimension::class, 'dimension_id', 'id');
    }

    
}