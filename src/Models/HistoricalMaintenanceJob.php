<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJob
 *
 * @property int $id_historical_maintenance_job
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
 * @property Carbon|null $job_finish_date_time
 * @property int $maintenance_job_active
 * @property int $edited_by
 * @property Carbon|null $history_valid_from
 * @property Carbon|null $history_valid_to

 *
 * @property User $user
 *
 * @package App\Models
 */
class HistoricalMaintenanceJob extends Model
{
    use HasFactory;

	protected $table = 'historical_maintenance_job';
    protected $primaryKey = 'id_historical_maintenance_job';
	public $timestamps = false;


	protected $casts = [
		'id_saas_client_business' => 'int',
		'id_parent_job' => 'int',
		'id_saas_staff_reporter' => 'int',
		'id_maintenance_job_category' => 'int',
		'id_maintenance_job_priority' => 'int',
		'id_maintenance_job_status' => 'int',
		'id_resident_reporter' => 'int',
		'edited_by' => 'int',
		'id_maintenance_job' => 'int',

        
	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'id_saas_client_business',
		'id_parent_job',
		'id_saas_staff_reporter',
		'id_maintenance_job_category',
		'id_maintenance_job_priority',
		'id_maintenance_job_status',
		'maintenance_job_title',
		'maintenance_job_description',
		'id_resident_reporter',
		'job_start_date_time',
		'job_report_date_time',
		'job_finish_date_time',
        'maintenance_job_active',
        'id_maintenance_job',
        'edited_by',
        'history_valid_from',
        'history_valid_to'

	];


   
}
