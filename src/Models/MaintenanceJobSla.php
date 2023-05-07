<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobSla
 *
 * @property int $id_maintenance_job_sla
 * @property int $id_maintenance_job
 * @property int $paused
 * @property int $pause_duration
 * @property int $finished
* @property Carbon|null $origin_date
 * @property Carbon|null $maximum_expected_seen_date
 * @property Carbon|null $expected_target_date
 * @property Carbon|null $actual_target_date




 * @package App\Models
 */
class MaintenanceJobSla extends Model
{

    use HasFactory;

	protected $table = 'maintenance_job_sla';
    protected $primaryKey = 'id_maintenance_job_sla';
	public $timestamps = false;

       // Disable Laravel's mass assignment protection
       protected $guarded = [];

	protected $casts = [

		'id_maintenance_job',
		'paused',
		'pause_duration',
		'finished',

	];



	protected $fillable = [
		'id_maintenance_job',
		'paused',
		'pause_duration',
		'finished',
		'origin_date',
		'maximum_expected_seen_date',
		'expected_target_date',
		'actual_target_date',

	];


}
