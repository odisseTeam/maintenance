<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobPriorityRef
 *
 * @property int $id_maintenance_job_priority_ref
 * @property string $priority_code
 * @property string $priority_name
 * @property string $priority_icon
 * @property int $maintenance_job_priority_ref_active

 * @package App\Models
 */
class MaintenanceJobPriorityRef extends Model
{

    use HasFactory;

	protected $table = 'maintenance_job_priority_ref';
    protected $primaryKey = 'id_maintenance_job_priority_ref';
	public $timestamps = false;

          // Disable Laravel's mass assignment protection
          protected $guarded = [];

	protected $casts = [

		'maintenance_job_priority_ref_active' => 'int',
	];



	protected $fillable = [
		'priority_code',
		'priority_name',
		'priority_icon',
		'maintenance_job_priority_ref_active',
	];


}
