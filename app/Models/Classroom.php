<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classroom';

    protected $primaryKey = 'id';

    protected $fillable = [
        'moduleCode',
        'lectureCode',
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

    public function lecturer(): BelongsTo
    {
        return $this->BelongsTo(Lecturer::class, 'lecturerId');
    }

    public  function  module(): BelongsTo
    {
        return $this->BelongsTo(Module::class, 'moduleId');
    }
}
