<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobPriorityHistory
 *
 * @property int $id_maintenance_job_priority_history
 * @property int $id_maintenance_job
 * @property int $id_maintenance_job_priority_ref
 * @property Carbon|null $priority_start_date_time
 * @property Carbon|null $priority_end_date_time
 * @property int $maintenance_job_priority_history_active

 * @package App\Models
 */
class MaintenanceJobPriorityHistory extends Model
{

    use HasFactory;

	protected $table = 'maintenance_job_priority_history';
    protected $primaryKey = 'id_maintenance_job_priority_history';
	public $timestamps = false;

          // Disable Laravel's mass assignment protection
          protected $guarded = [];

	protected $casts = [
		'id_maintenance_job' => 'int',
		'id_maintenance_job_priority_ref' => 'int',
		'maintenance_job_priority_history_active' => 'int',
	];



	protected $fillable = [
		'id_maintenance_job',
		'id_maintenance_job_priority_ref',
		'priority_start_date_time',
		'priority_end_date_time',
		'maintenance_job_priority_history_active',
	];


}
