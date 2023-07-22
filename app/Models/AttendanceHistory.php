<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceHistory extends Model
{
    use HasFactory;

    protected $table = 'attendanceHistory';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'classroomId',
        'date',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroomId');
    }
}
