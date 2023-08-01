<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grade';

    protected $primaryKey = 'id';

    protected $fillable = [
        'classroomId',
        'studentId',
        'attendance',
        'coefficient1Exam1',
        'coefficient1Exam2',
        'coefficient1Exam3',
        'coefficient2Exam1',
        'coefficient2Exam2',
        'exam',
    ];
}
