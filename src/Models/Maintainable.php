<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Maintainable
 *
 * @property int $id
 * @property int $id_maintenance_job
 * @property int $maintenable_id
 * @property string $maintenable_type
 *
 *
 * @package App\Models
 */
class Maintainable extends Model
{
    use HasFactory;

	protected $table = 'maintainable';
    protected $primaryKey = 'id';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
    protected $guarded = [];

	protected $casts = [
		'id_maintenance_job' => 'int',
		'maintenable_id' => 'int',
		'maintainable_active'=>'int'

	];



	protected $fillable = [
		'id_maintenance_job',
		'maintenable_id',
		'maintenable_type',
		'maintainable_active',

	];


}
