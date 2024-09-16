<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPayed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_record_id'
    ];

    protected $table='lesson_payed';
}
