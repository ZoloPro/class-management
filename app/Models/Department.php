<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $table = "department";

    public $timestamps = false;

    protected $fillable = [
        'departmentName',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
