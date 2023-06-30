<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

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
        'enrollmentDate'
    ];

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'registerClassroom',
            'studentId',
            'classroomId',
        )->as('registerClassroom');
    }
}
