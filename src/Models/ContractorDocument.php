<?php

/**
 * Created by Reliese Model.
 */

 namespace Odisse\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ContractorDocument
 *
 * @property int $id_contractor_document
 * @property int $id_contractor
 * @property string $document_name
 * @property string $document_address
 * @property string $document_extention
 * @property string $description
 * @property int $contractor_document_active

 * @package App\Models
 */
class ContractorDocument extends Model
{
    use HasFactory;

	protected $table = 'contractor_document';
    protected $primaryKey = 'id_contractor_document';
	public $timestamps = false;

      // Disable Laravel's mass assignment protection
      protected $guarded = [];

	protected $casts = [
		'id_contractor' => 'int',
		'contractor_document_active' => 'int',
	];

	/*protected $dates = [
		'completed_at'
	];*/

	protected $fillable = [
		'id_contractor',
		'document_name',
		'document_address',
		'document_extention',
		'description',
		'contractor_document_active',
	];


}
