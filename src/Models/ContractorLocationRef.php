<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContractorLocationRef
 *
 * @property int $id_contractor_location_ref

 * @property string $location
 * @property int $contractor_location_ref_active
 *
 *
 * @package App\Models
 */
class ContractorLocationRef extends Model
{

    use HasFactory;

	protected $table = 'contractor_location_ref';
    protected $primaryKey = 'id_contractor_location_ref';
	public $timestamps = false;



	protected $fillable = [
        'location',
		'contractor_location_ref_active',
	];


}
