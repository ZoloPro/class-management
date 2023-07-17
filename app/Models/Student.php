<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasFactory, hasApiTokens;

    protected $table = 'student';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'famMidName',
        'name',
        'gender',
        'birthdate',
        'birthdate',
        'phone',
        'email',
        'enrollmentDate',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function registeredClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'registerClassroom',
            'studentId',
            'classroomId',
        )->as('registerClassroom');
    }

    public function attendedClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'attendance',
            'studentId',
            'classroomId',
        )->as('attendance');
    }

    public function hasMarks(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'mark',
            'studentId',
            'classroomId'
        )->as('mark')->withPivot('mark');
    }
}
