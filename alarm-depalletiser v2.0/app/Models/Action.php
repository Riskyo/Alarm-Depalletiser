<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['alarm_id','action_text'];

    public function alarm() {
        return $this->belongsTo(Alarm::class);
    }

    public function sensors() {
        return $this->hasMany(Sensor::class);
    }
}
