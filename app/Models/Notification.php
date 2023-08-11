<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'type',
        'time',
    ];

    public function notificationDetails(): HasMany
    {
        return $this->hasMany(NotificationDetail::class, 'noficationId');
    }
}
