<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecturer extends Model
{
    use HasFactory;

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

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

}
