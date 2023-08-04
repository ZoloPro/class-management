<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VertifyEmail extends Model
{
    use HasFactory;

    protected $table = 'verify_email_tokens';

    public $timestamps = false;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'email',
        'token',
        'phone',
        'email',
        'created_at'
    ];

}
