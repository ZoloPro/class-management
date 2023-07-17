<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Lecturer extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'lecturer';

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
        'onboardingDate'
    ];

    protected $hidden = [
        'password'
    ];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'lecturerId');
    }
}
