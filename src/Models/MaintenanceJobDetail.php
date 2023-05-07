<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobDetail
 *
 * @property int $id_maintenance_job
 * @property int $id_saas_client_business
 * @property int $id_parent_job
* @property int $id_staff
 * @property int $id_saas_staff_reporter
 * @property int $id_maintenance_job_category
 * @property int $id_maintenance_job_priority
 * @property int $id_maintenance_job_status
 * @property int $id_resident_reporter
 * @property string $maintenance_job_title
 * @property text $maintenance_job_description
 * @property Carbon|null $job_report_date_time
 *
 * @property User $user
 *
 * @package App\Models
 */
class MaintenanceJobDetail extends Model
{
    use HasFactory;

	protected $table = 'maintenance_job_detail';
    protected $primaryKey = 'id_maintenance_job_detail';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
      protected $guarded = [];
      
	protected $casts = [
		'id_maintenance_job' => 'int',
		'maintenance_job_detail_active' => 'int',
		'id_staff' => 'int',

	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'id_maintenance_job',
		'maintenance_job_detail_date_time',
		'id_staff',
		'job_detail_note',
		'maintenance_job_detail_active',
	];


}
