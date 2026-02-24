<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;
    protected $fillable = [
        'office_id',
        'division_id',
        'section_id',
        'service_name',
        'service_type',
        'service_url',
        'is_disabled',
        'slug',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subservices()
    {
        return $this->hasMany(SubServices::class);
    }
}
