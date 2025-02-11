<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOtherAttributeIndication extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'indication',
    ];
}
