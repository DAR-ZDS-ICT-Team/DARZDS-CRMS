<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicesSubServices extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'sub_service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class);
    }

    public function sub_service()
    {
        return $this->belongsTo(SubServices::class);
    }
}
