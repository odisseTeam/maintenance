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
  * @property int $id_historical_contractor
 * @property int $id_contractor

 * @property int $id_saas_client_business
 * @property string $name
 * @property string $short_name
 * @property string $vat_number
 * @property string $tel_number1
 * @property string $tel_number2
 * @property string $address_line1
 * @property string $address_line2
 * @property string $address_line3
 * @property int $contractor_active
 * @property Carbon|null $history_valid_from
 * @property Carbon|null $history_valid_to
 * @property int $edited_by

 *
 * @package App\Models
 */
class HistoricalContractor extends Model
{

    use HasFactory;

	protected $table = 'historical_contractor';
    protected $primaryKey = 'id_historical_contractor';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
    // protected $guarded = [];

	protected $casts = [

	'contractor_active' => 'int',
    'id_contractor' => 'int',
    'edited_by' => 'int',

	];



	protected $fillable = [
        'id_saas_client_business',
        'id_contractor',
        'name',
        'short_name',
        'vat_number',
        'tel_number1',
        'tel_number2',
        'address_line1',
        'address_line2',
        'address_line3',
		'contractor_active',
        'edited_by',
        'history_valid_from',
        'history_valid_to',

	];

   

}
