<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiInfo extends Model
{
    use HasFactory;

    protected $table = 'wifiInfo';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'wifiName',
        'wifiBSSID',
        'wifiIP'
    ];
}
