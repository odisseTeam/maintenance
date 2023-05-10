<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Contractor
 *
 * @property int $id_maintenance_job_sla_ref

 * @property int $id_client
  * @property int $id_maintenance_job_priority
 * @property int $id_saas_client_business
 * @property integer $tel_number2
 * @property integer $address_line1
 * @property Carbon|null $maximum_expected_seen_minutes
 * @property Carbon|null $expected_target_minutes

 * @property int $maintenance_job_sla_ref_active
 *
 *
 * @package App\Models
 */
class MaintenanceJobSlaRef extends Model
{

    use HasFactory;

	protected $table = 'maintenance_job_sla_ref';
    protected $primaryKey = 'id_maintenance_job_sla_ref';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
    protected $guarded = [];

	protected $casts = [
		
	'id_client' => 'int',
	'maintenance_job_sla_ref_active' => 'int',
	'id_maintenance_job_priority' => 'int',
	'id_saas_client_business' => 'int',
	
	];



	protected $fillable = [
        'maximum_expected_seen_minutes',
		'expected_target_minutes',
		'id_client',
		'maintenance_job_sla_ref_active',
		'id_maintenance_job_priority',
		'id_saas_client_business',
		
	];


}
