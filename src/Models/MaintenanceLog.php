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
 * Class MaintenanceLog
 *
 * @property int $id_maintenance_log
 * @property int $id_maintenance_job
 * @property int $id_staff
 * @property text $log_note
 * @property Carbon|null $log_date_time
 *
 * @property User $user
 *
 * @package App\Models
 */
class MaintenanceLog extends Model
{
    use HasFactory;

	protected $table = 'maintenance_log';
    protected $primaryKey = 'id_maintenance_log';
	public $timestamps = false;

     // Disable Laravel's mass assignment protection
     protected $guarded = [];

	protected $casts = [
		'id_maintenance_job' => 'int',
		'id_staff' => 'int',

	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'id_maintenance_job',
		'id_staff',
		'id_maintenance_job_priority',
		'log_date_time',
		'log_note',

	];


	public function getLogDateTimeAttribute($value)
    {
        if( $value == null )
            return null;
        else
            return Carbon::parse($value)->format(SystemDateFormats::getDateTimeFormat());
    }

    public function setLogDateTimeAttribute($value)
    {
        if( $value == null )
            $this->attributes['log_date_time'] = null;
        else
            $this->attributes['log_date_time'] = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $value)->format('Y-m-d H:i:s');
    }



}
