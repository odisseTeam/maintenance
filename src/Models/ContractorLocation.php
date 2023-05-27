<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContractorLocation
 *
 * @property int $id_contractor_location

 * @property int $id_contractor_location_ref
 * @property int $id_contractor
 * @property int $contractor_location_active
 *
 *
 * @package App\Models
 */
class ContractorLocation extends Model
{

    use HasFactory;

	protected $table = 'contractor_location';
    protected $primaryKey = 'id_contractor_location';
	public $timestamps = false;


	protected $casts = [

	'contractor_location_active' => 'int',
	];



	protected $fillable = [
        'id_contractor_location_ref',
        'id_contractor',
		'contractor_location_active',
	];


}
