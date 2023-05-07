<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TagRef
 *
 * @property int $id_tag_ref
 * @property int $tag_ref_active
 * @property string $tag_code
 * @property string $tag_name

 *
 *
 * @package App\Models
 */
class TagRef extends Model
{
    use HasFactory;

	protected $table = 'tag_ref';
    protected $primaryKey = 'id_tag_ref';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
    protected $guarded = [];

	protected $casts = [
		'tag_ref_active' => 'int',

	];



	protected $fillable = [
		'tag_code',
		'tag_name',
		'tag_ref_active',

	];


}
