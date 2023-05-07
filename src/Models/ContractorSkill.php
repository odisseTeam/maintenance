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

 * @property string $skill_name
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

      // Disable Laravel's mass assignment protection
    protected $guarded = [];

	protected $casts = [

	'contractor_skill_active' => 'int',
	];



	protected $fillable = [
        'skill_name',
		'contractor_skill_active',
	];


}
