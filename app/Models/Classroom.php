<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function hasMarks(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'mark',
            'classroomId',
            'studentId'
        )->as('mark')->withPivot('mark');
    }

    public function lecturer(): BelongsTo
    {
        return $this->BelongsTo(Lecturer::class, 'lecturerId');
    }

    public function term(): BelongsTo
    {
        return $this->BelongsTo(Term::class, 'termId');
    }
}
