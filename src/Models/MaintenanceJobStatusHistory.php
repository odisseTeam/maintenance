<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobStatusHistory
 *
 * @property int $id_maintenance_job_status_history
 * @property int $id_maintenance_job
 * @property int $id_maintenance_staff
 * @property int $id_maintenance_job_status
 * @property Carbon|null $maintenance_status_start_date
 * @property Carbon|null $maintenance_status_end_date
 * @property int $maintenance_job_status_history_active

 * @package App\Models
 */
class MaintenanceJobStatusHistory extends Model
{
    use HasFactory;

	protected $table = 'maintenance_job_status_history';
    protected $primaryKey = 'id_maintenance_job_status_history';
	public $timestamps = false;

          // Disable Laravel's mass assignment protection
          protected $guarded = [];


	protected $casts = [
		'id_maintenance_job' => 'int',
		'id_maintenance_staff' => 'int',
		'id_maintenance_job_status' => 'int',
		'maintenance_job_status_history_active' => 'int',
	];



	protected $fillable = [
		'id_maintenance_job',
		'id_maintenance_staff',
		'id_maintenance_job_status',
		'maintenance_status_start_date',
		'maintenance_status_end_date',
		'maintenance_job_status_history_active',
	];


}
