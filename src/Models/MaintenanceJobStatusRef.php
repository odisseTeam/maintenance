<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobStatusRef
 *
 * @property int $id_maintenance_job_status_ref
 * @property string $job_status_code
 * @property string $job_status_name
 * @property string $job_status_icon
 * @property int $maintenance_job_status_ref_active

 * @package App\Models
 */
class MaintenanceJobStatusRef extends Model
{


    use HasFactory;

	protected $table = 'maintenance_job_status_ref';
    protected $primaryKey = 'id_maintenance_job_status_ref';
	public $timestamps = false;

     protected $guarded = [];

	protected $casts = [

		'maintenance_job_status_ref_active' => 'int',
	];



	protected $fillable = [
		'job_status_code',
		'job_status_name',
		'job_status_icon',
		'maintenance_job_status_ref_active',

	];


}
