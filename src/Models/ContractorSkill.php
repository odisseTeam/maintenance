<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContractorSkill
 *
 * @property int $id_contractor_skill

 * @property int $id_contractor_skill_ref
 * @property int $id_contractor
 * @property int $contractor_skill_active
 *
 *
 * @package App\Models
 */
class ContractorSkill extends Model
{

    use HasFactory;

	protected $table = 'contractor_skill';
    protected $primaryKey = 'id_contractor_skill';
	public $timestamps = false;


	protected $casts = [

	'contractor_skill_active' => 'int',
	];



	protected $fillable = [
        'id_contractor_skill_ref',
        'id_contractor',
		'contractor_skill_active',
	];


}
