<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = ['action_id','sensor_name','komponen','plc_io'];

    public function action() {
        return $this->belongsTo(Action::class);
    }
}
