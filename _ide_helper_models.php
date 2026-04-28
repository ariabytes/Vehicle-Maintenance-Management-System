<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $vehicle_id
 * @property int $mechanic_id
 * @property int|null $report_id
 * @property int|null $maintenance_sched_id
 * @property string $job_type
 * @property string $priority
 * @property string $status
 * @property string $scheduled_at
 * @property string|null $mechanic_notes
 * @property string|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MaintenanceSchedule|null $maintenanceSchedule
 * @property-read \App\Models\User $mechanic
 * @property-read \App\Models\Report|null $report
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereJobType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereMaintenanceSchedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereMechanicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereMechanicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereVehicleId($value)
 */
	class Job extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vehicle_id
 * @property int $maintenance_type_id
 * @property \Illuminate\Support\Carbon|null $last_service_date
 * @property int $last_service_odo
 * @property int $interval_km
 * @property int $next_due_odo
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $remaining_km
 * @property-read string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $jobs
 * @property-read int|null $jobs_count
 * @property-read \App\Models\MaintenanceType $maintenanceType
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereIntervalKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereLastServiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereLastServiceOdo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereMaintenanceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereNextDueOdo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceSchedule whereVehicleId($value)
 */
	class MaintenanceSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $default_interval_km
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaintenanceSchedule> $schedules
 * @property-read int|null $schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType whereDefaultIntervalKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceType whereName($value)
 */
	class MaintenanceType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vehicle_id
 * @property int $reported_by
 * @property int|null $reviewed_by
 * @property string $issue_type
 * @property string|null $description
 * @property string $status
 * @property string|null $admin_notes
 * @property string|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Job|null $job
 * @property-read \App\Models\User $reporter
 * @property-read \App\Models\User|null $reviewer
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereIssueType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereVehicleId($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string $status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $assignedJobs
 * @property-read int|null $assigned_jobs_count
 * @property-read string $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reviewedReports
 * @property-read int|null $reviewed_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $submittedReports
 * @property-read int|null $submitted_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $driver_id
 * @property string $plate_number
 * @property string $model
 * @property string $vehicle_type
 * @property string $year
 * @property string|null $color
 * @property int $current_odometer_km
 * @property string|null $photo
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $driver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $jobs
 * @property-read int|null $jobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaintenanceSchedule> $maintenanceSchedules
 * @property-read int|null $maintenance_schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCurrentOdometerKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVehicleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYear($value)
 */
	class Vehicle extends \Eloquent {}
}

