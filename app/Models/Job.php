<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'vehicle_jobs';
    protected $fillable = [
        'vehicle_id',
        'mechanic_id',
        'report_id',
        'maintenance_sched_id',
        'job_type',
        'priority',
        'status',
        'scheduled_at',
        'mechanic_notes',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];


    // relationship
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_sched_id');
    }

    // helper
    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    /**
     * Mark job as done. Optionally resets the linked maintenance schedule.
     */
    public function markDone(string $notes = null, int $completionOdometer = null): void
    {
        $this->update([
            'status'         => 'done',
            'mechanic_notes' => $notes,
            'completed_at'   => now(),
        ]);

        // Auto-reset the PMS schedule if this job came from one
        if ($this->maintenanceSchedule && $completionOdometer) {
            $this->maintenanceSchedule->recordService($completionOdometer);
        }
    }
}
