<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationDetail extends Model
{
    use HasFactory;

    protected $table = 'notification_detail';

    public $timestamps = false;

    protected $fillable = [
        'notificationId',
        'title',
        'type',
        'module',
        'body',
        'status',
        'time',
        'user',
        'userId',
    ];

    public function nofification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notificationId');
    }
}
