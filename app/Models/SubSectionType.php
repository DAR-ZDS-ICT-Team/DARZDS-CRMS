<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSectionType extends Model
{
    use HasFactory;
    protected $fillable = [
        'sub_section_id',
        'type_name',
    ];

    

}
