<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $fillable = [
        'division_id',  
        'section_name',
    ];
    public function division(){
        return $this->belongsTo(Division::class);
    }

    public function sub_sections(){
        return $this->hasMany(SubSection::class);
    }
}
