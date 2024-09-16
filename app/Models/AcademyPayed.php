<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademyPayed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academy_course_id'
    ];
    protected $table='academy_payed';
}
