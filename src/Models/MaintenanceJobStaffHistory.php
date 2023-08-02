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


	protected $guarded = [];

	protected $casts = [
		'id_maintenance_job' => 'int',
		'id_maintenance_staff' => 'int',
		'id_maintenance_assignee' => 'int',
		'maintenance_job_staff_history_active' => 'int',

	];



	protected $fillable = [
		'id_maintenance_job',
		'id_maintenance_staff',
		'id_maintenance_assignee',
		'staff_assign_date_time',
		'staff_start_date_time',
		'staff_end_date_time',
		'is_last_one',
		'maintenance_job_staff_history_active',
	];




    // accessors for convert date formats
    public function getStaffAssignDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setStaffAssignDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['staff_assign_date_time'] = null;
        else
            $this->attributes['staff_assign_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }



    // accessors for convert date formats
    public function getStaffStartDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setStaffStartDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['staff_start_date_time'] = null;
        else
            $this->attributes['staff_start_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }




    // accessors for convert date formats
    public function getStaffEndDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setStaffEndDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['staff_end_date_time'] = null;
        else
            $this->attributes['staff_end_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }



}
