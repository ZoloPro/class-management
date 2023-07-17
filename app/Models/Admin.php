<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'admin';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'password',
    ];

    protected $hidden = [
        'password'
    ];
}
