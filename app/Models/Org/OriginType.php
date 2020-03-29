<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class OriginType extends BaseValidator
{
    protected $table = 'org_origin_type';
    protected $primaryKey = 'origin_type_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['origin_type','origin_type_id'];

    /*protected $rules = array(
        'origin_type' => 'required'
    );*/

   public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setOriginTypeAttribute($value) {
        $this->attributes['origin_type'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'origin_type' => [
            'required',
            'unique:org_origin_type,origin_type,'.$data['origin_type_id'].',origin_type_id',
          ]
      ];
    }

    //other.....................................................................

    public function isUsed($id){
      $is_exists = DB::table('item_master')->where('uom_id', $id)->exists();
    }

}
