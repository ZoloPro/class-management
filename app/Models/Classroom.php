<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classroom';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'termId',
        'lecturerId',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'registerClassroom',
            'classroomId',
            'studentId',
        )->as('registerClassroom');
    }

    public function registeredStudents(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'registerClassroom',
            'classroomId',
            'studentId',
        )->as('registerClassroom');
    }

    public function attendedStudents(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'attendance',
            'classroomId',
            'studentId',
        )->as('attendance');
    }

    public function hasGrades(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'grade',
            'classroomId',
            'studentId'
        )->as('grade')->withPivot('grade');
    }

    public function lecturer(): BelongsTo
    {
        return $this->BelongsTo(Lecturer::class, 'lecturerId');
    }

    public function term(): BelongsTo
    {
        return $this->BelongsTo(Term::class, 'termId');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'classroomId');
    }

    public function attendanceHistory(): HasMany
    {
        return $this->hasMany(AttendanceHistory::class, 'classroomId');
    }
}
