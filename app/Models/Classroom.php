<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classroom';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'termId',
        'lecturerId',
        'startDate',
        'endDate',
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

    public function checkedInStudents(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'checkin',
            'classroomId',
            'studentId',
        )->withTimestamps()->withPivot('type')->as('checkin');
    }

    public function hasGrades(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'grade',
            'classroomId',
            'studentId'
        )->as('grade')->withPivot(['attendanceGrade', 'examGrade', 'finalGrade']);
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

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class, 'classroomId');
    }

    public function checkinHistory(): HasMany
    {
        return $this->hasMany(CheckinHistory::class, 'classroomId');
    }

    public function wifiInfo(): HasOne
    {
        return $this->hasOne(WifiInfo::class, 'classroomId');
    }
}
