<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionSubSection extends Model
{
    use HasFactory;
    protected $fillable = [
        'section_id',
        'sub_section_id',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function sub_section()
    {
        return $this->belongsTo(SubSection::class);
    }
}
