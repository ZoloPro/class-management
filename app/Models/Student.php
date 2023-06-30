<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'student';

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'famMidName',
        'name',
        'gender',
        'birthdate',
        'birthdate',
        'phone',
        'email',
        'enrollmentDate'
    ];
}
