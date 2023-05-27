<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContractorSkillRef
 *
 * @property int $id_contractor_skill_ref

 * @property string $skill_name
 * @property int $contractor_skill_ref_active
 *
 *
 * @package App\Models
 */
class ContractorSkillRef extends Model
{

    use HasFactory;

	protected $table = 'contractor_skill_ref';
    protected $primaryKey = 'id_contractor_skill_ref';
	public $timestamps = false;



	protected $fillable = [
        'skill_name',
		'contractor_skill_ref_active',
	];


}
