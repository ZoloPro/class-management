<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'departmentId',
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
        )->as('grade')->withPivot([
            'attendance',
            'coefficient1Exam1',
            'coefficient1Exam2',
            'coefficient1Exam3',
            'coefficient2Exam1',
            'coefficient2Exam2',
            'exam',
            'final',]);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class, 'studentId');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'studentId');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }
}
