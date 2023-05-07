<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MaintenanceJobDocument
 *
 * @property int $id_maintenance_job_document
 * @property int $id_maintenance_job
 * @property string $document_name
 * @property string $document_address
 * @property string $document_extention
 * @property string $description
 * @property int $maintenance_job_document_active

 * @package App\Models
 */
class MaintenanceJobDocument extends Model
{
    use HasFactory;

	protected $table = 'maintenance_job_document';
    protected $primaryKey = 'id_maintenance_job_document';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
      protected $guarded = [];

	protected $casts = [
		'id_maintenance_job' => 'int',
		'maintenance_job_document_active' => 'int',
	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'id_maintenance_job',
		'document_name',
		'document_address',
		'document_extention',
		'description',
		'maintenance_job_document_active',
	];


}
