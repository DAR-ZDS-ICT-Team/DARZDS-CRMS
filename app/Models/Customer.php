<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'client_type',
        'sex',
        'age_group',
        'pwd',
        'pregnant',
        'senior_citizen',
        'signature_path',
        'created_at',
        'updated_at',
    ];
}
