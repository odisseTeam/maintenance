<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContractorAgent
 *
 * @property int $id_contractor_agent

 * @property int $id_contractor
 * @property int $id_user
 * @property int $contractor_agent_active
 *
 *
 * @package App\Models
 */
class ContractorAgent extends Model
{

    use HasFactory;

	protected $table = 'contractor_agent';
    protected $primaryKey = 'id_contractor_agent';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
    // protected $guarded = [];

	protected $casts = [

	'id_contractor' => 'int',
	'id_user' => 'int',
	'contractor_agent_active' => 'int',
	];



	protected $fillable = [
        'id_contractor',
        'id_user',
		'contractor_agent_active',
	];


}
