<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'vehicle_id',
        'maintenance_type_id',
        'last_service_date',
        'last_service_odo',
        'interval_km',
        'next_due_odo',
        'notes',
    ];

    protected $casts = [
        'last_service_date' => 'date',
    ];

    // Relationships

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceType()
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'maintenance_sched_id');
    }

    //  Computed Helpers

    // Returns: overdue | due_soon | ok
    public function getStatusAttribute(): string
    {
        $remaining = $this->remaining_km;

        if ($remaining < 0) {
            return 'overdue';
        }

        if ($remaining <= 500) {
            return 'due_soon';
        }

        return 'ok';
    }

    // Positive = km left
    // Zero = due now
    // Negative = overdue
    public function getRemainingKmAttribute(): int
    {
        return $this->next_due_odo - $this->vehicle->current_odometer_km;
    }


    // Reset Schedule After Service Completion
    public function recordService(int $odometer, string $date = null): void
    {
        $this->update([
            'last_service_odo'  => $odometer,
            'last_service_date' => $date ?? now()->toDateString(),
            'next_due_odo'      => $odometer + $this->interval_km,
        ]);
    }
}
