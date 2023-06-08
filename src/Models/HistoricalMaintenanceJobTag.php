<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobTag
 *
 * @property int $id_maintenance_job
 * @property int $id_saas_client_business
 * @property int $id_parent_job
 * @property int $id_saas_staff_reporter
 * @property int $id_maintenance_job_category
 * @property int $id_maintenance_job_priority
 * @property int $id_maintenance_job_status
 * @property int $id_resident_reporter
 * @property string $maintenance_job_title
 * @property text $maintenance_job_description
 * @property Carbon|null $job_report_date_time
 * @property Carbon|null $job_start_date_time
 * @property Carbon|null $job_finished_date_time
 * @property Carbon|null $history_valid_from
 * @property Carbon|null $history_valid_to
 * @property int $edited_by
 *
 * @property User $user
 *
 * @package App\Models
 */
class HistoricalMaintenanceJobTag extends Model
{
    use HasFactory;

	protected $table = 'historical_maintenance_job_tag';
    protected $primaryKey = 'id_historical_maintenance_job_tag';
	public $timestamps = false;

          // Disable Laravel's mass assignment protection
          protected $guarded = [];

	protected $casts = [
		'id_tag_ref' => 'int',
		'id_first_job' => 'int',
		'id_second_job' => 'int',
		'maintenance_job_tag_active' => 'int',
		'edited_by' => 'int',
		'id_maintenance_job_tag' => 'int',

	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'id_tag_ref',
		'id_first_job',
		'id_second_job',
		'maintenance_job_tag_active',
		'edited_by',
        'history_valid_from',
        'history_valid_to',
		'id_maintenance_job_tag',

	];

	

}
