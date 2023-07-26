<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckinHistory extends Model
{
    use HasFactory;

    protected $table = 'checkin_history';

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
