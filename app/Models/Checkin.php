<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Checkin extends Pivot
{
    protected $table = 'checkin';

    protected $primaryKey = 'id';

    protected $fillable = [
        'classroomId',
        'studentId',
        'type',
        'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'studentId');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroomId');
    }
}
