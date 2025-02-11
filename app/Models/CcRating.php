<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'name',
        'answer',
        'created_at',
        'updated_at',
    ];
}
