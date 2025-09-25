<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    protected $fillable = ['description_alarm','step'];

    public function actions() {
        return $this->hasMany(Action::class);
    }
}

