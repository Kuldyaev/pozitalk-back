<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademySubscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academy_course_id',
        'is_active',
        'end_date',
    ];
}
