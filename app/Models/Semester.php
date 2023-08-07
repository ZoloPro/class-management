<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'semesterName',
        'startDate',
        'endDate',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'semesterId', 'id');
    }
}
