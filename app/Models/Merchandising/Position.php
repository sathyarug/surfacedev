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



class Position extends BaseValidator {

    protected $table = 'merc_position';
    protected $primaryKey = 'position_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['position'];

    public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setPositionAttribute($value) {
        $this->attributes['position'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'position' => [
            'required',
            'unique:merc_position,position,'.$data['position_id'].',position_id',
          ]
      ];
    }

}
