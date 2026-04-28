<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'default_interval_km',
    ];
    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }
}
