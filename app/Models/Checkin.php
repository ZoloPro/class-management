<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Checkin extends Pivot
{
    protected $table = 'checkin';

    protected $primaryKey = 'id';

    protected $fillable = [
        'classroomId',
        'studentId',
        'date',
    ];
}
