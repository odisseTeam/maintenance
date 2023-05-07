<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\SLP\Formatter\SystemDateFormats;

/**
 * Class MaintenanceJob
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

 *
 * @property User $user
 *
 * @package App\Models
 */
class MaintenanceJob extends Model
{
    use HasFactory;

	protected $table = 'maintenance_job';
    protected $primaryKey = 'id_maintenance_job';
	public $timestamps = false;

          // Disable Laravel's mass assignment protection
          protected $guarded = [];

	protected $casts = [
		'id_saas_client_business' => 'int',
		'id_parent_job' => 'int',
		'id_saas_staff_reporter' => 'int',
		'id_maintenance_job_category' => 'int',
		'id_maintenance_job_priority' => 'int',
		'id_maintenance_job_status' => 'int',
		'id_resident_reporter' => 'int',
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
		'job_finished_date_time'
	];



	public function getJobReportDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setJobReportDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['job_report_date_time'] = null;
        else
            $this->attributes['job_report_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }


    public function getJobStartDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setJobStartDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['job_start_date_time'] = null;
        else
            $this->attributes['job_start_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }


	public function getJobFinishedDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setJobFinishedDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['job_finished_date_time'] = null;
        else
            $this->attributes['job_finished_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }

}
