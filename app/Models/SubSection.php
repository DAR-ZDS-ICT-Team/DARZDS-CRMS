<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    use HasFactory;

    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function sub_section_types(){
        return $this->hasMany(SubSectionType::class);
    }

}
