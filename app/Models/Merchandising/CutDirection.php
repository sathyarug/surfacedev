<?php
namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class CutDirection extends BaseValidator{


  protected $table = 'merc_cut_direction';
  protected $primaryKey = 'cut_dir_id';

  const UPDATED_AT = 'updated_date';
  const CREATED_AT = 'created_date';

  protected $fillable = ['cut_dir_description','cd_acronyms'];

  //    protected $rules = array(
  //        'pack_type_description' => 'required'
  //
  //    );

  //Validation Functions
  /**
  *unique:table,column,except,idColumn
  *The field under validation must not exist within the given database table
  **/
  protected function getValidationRules($data) {
    return [
        'cut_dir_description' => [
          'required',
          'unique:merc_cut_direction,cut_dir_description,'.$data['cut_dir_id'].',cut_dir_id',
        ],
        'cd_acronyms' => 'required'
    ];
  }

  public function __construct() {
      parent::__construct();
      $this->attributes = array(
          'updated_by' => 2//Session::get("user_id")
      );
  }





}
