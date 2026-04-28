<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'driver_id',
        'plate_number',
        'model',
        'vehicle_type',
        'year',
        'color',
        'current_odometer_km',
        'photo',
        'status',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    public function isInShop(): bool
    {
        return $this->status === 'in_shop';
    }
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}
