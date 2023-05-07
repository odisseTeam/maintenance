<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobCategoryRef
 *
 * @property int $id_maintenance_job_category_ref
 * @property string $job_category_code
 * @property string $job_category_name
 * @property string $job_category_icon

 * @property int $maintenance_job_category_ref_active

 *
 * @property User $user
 *
 * @package App\Models
 */
class MaintenanceJobCategoryRef extends Model
{

    use HasFactory;

	protected $table = 'maintenance_job_category_ref';
    protected $primaryKey = 'id_maintenance_job_category_ref';
	public $timestamps = false;

          // Disable Laravel's mass assignment protection
          protected $guarded = [];
	protected $casts = [
		'maintenance_job_category_ref_active' => 'int',

	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'job_category_code',
		'job_category_name',
		'job_category_icon',
		'maintenance_job_category_ref_active'
    ];



}
