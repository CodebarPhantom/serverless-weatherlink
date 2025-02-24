<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    use HasFactory;

    protected $fillable = ['master_station_id','unix_epoch_time','rain_rate_hi_mm','is_send'];
}
