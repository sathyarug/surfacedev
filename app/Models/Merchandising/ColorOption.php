<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/10/2018
 * Time: 4:55 PM
 */

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;



class ColorOption extends BaseValidator {

    protected $table = 'merc_color_options';
    protected $primaryKey = 'col_opt_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['color_option','color_type_code'];

    // protected $rules = array(
    //     'color_option' => 'required'
    // );


    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'color_option' => [
            'required',
            'unique:merc_color_options,color_option,'.$data['col_opt_id'].',col_opt_id',
          ],
      ];
    }


    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

}
