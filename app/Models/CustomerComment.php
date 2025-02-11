<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  CustomerComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'comment',
        'is_complaint',
        'created_at',
        'updated_at',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id' ,'id');
    }
}
