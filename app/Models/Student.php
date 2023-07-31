<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function checkinClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'checkin',
            'studentId',
            'classroomId',
        )->withTimestamps()->withPivot('type')->as('checkin');
    }

    public function hasGrades(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'grade',
            'studentId',
            'classroomId'
        )->as('grade')->withPivot(['attendanceGrade', 'examGrade', 'finalGrade']);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class, 'studentId');
    }
}
