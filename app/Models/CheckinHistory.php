<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckinHistory extends Model
{
    use HasFactory;

    protected $table = 'checkin_history';

    protected $primaryKey = 'id';

    protected $fillable = [
        'classroomId',
        'date',
        'time',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroomId');
    }
}
