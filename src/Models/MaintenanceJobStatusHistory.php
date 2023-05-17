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
 * Class MaintenanceJobStatusHistory
 *
 * @property int $id_maintenance_job_status_history
 * @property int $id_maintenance_job
 * @property int $id_maintenance_staff
 * @property int $id_maintenance_job_status
 * @property Carbon|null $maintenance_status_start_date_time
 * @property Carbon|null $maintenance_status_end_date_time
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
		'maintenance_status_start_date_time',
		'maintenance_status_end_date_time',
		'maintenance_job_status_history_active',
	];


	public function getMaintenanceStatusStartDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setMaintenanceStatusStartDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['maintenance_status_start_date_time'] = null;
        else
            $this->attributes['maintenance_status_start_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }

	public function getMaintenanceStatusEndDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setMaintenanceStatusEndDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['maintenance_status_end_date_time'] = null;
        else
            $this->attributes['maintenance_status_end_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }

}
