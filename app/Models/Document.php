<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'document';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'classroomId',
        'fileName',
        'url',
    ];

}