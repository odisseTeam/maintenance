<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobStaffHistory
 *
 * @property int $id_maintenance_job_staff_history
 * @property int $id_maintenance_job
 * @property int $id_maintenance_staff
 * @property Carbon|null $staff_assign_date_time
 * @property Carbon|null $staff_start_date_time
 * @property Carbon|null $staff_end_date_time
 * @property int $maintenance_job_staff_history_active

 * @package App\Models
 */
class MaintenanceJobStaffHistory extends Model
{

    use HasFactory;

	protected $table = 'maintenance_job_staff_history';
    protected $primaryKey = 'id_maintenance_job_staff_history';
	public $timestamps = false;

       // Disable Laravel's mass assignment protection
       protected $guarded = [];

	protected $casts = [
		'id_maintenance_job' => 'int',
		'id_maintenance_staff' => 'int',
		'maintenance_job_staff_history_active' => 'int',
	];



	protected $fillable = [
		'id_maintenance_job',
		'id_maintenance_staff',
		'staff_assign_date_time',
		'staff_start_date_time',
		'staff_end_date_time',
		'priority_end_date_time',
		'maintenance_job_staff_history_active',
	];


}
