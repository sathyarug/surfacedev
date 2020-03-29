<?php

namespace App\Models\Finance\Cost;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;

class FinanceCost extends BaseValidator
{
    protected $table = 'fin_fin_cost';
    protected $primaryKey = 'fin_cost_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['fin_cost_id','finance_cost','cpmfront_end','cpum','effective_from','effective_to'];

    /*protected $rules = array(
        'cpmfront_end' => 'required',
        'finance_cost'  => 'required',
        'cpum'  => 'required',
        'effective_from' => 'required'
    );*/

    public function __construct() {
         parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setEffectiveFromAttribute($value)
		{
    	$this->attributes['effective_from'] = date('Y-m-d', strtotime($value));
    }

    public function setEffectiveToAttribute($value)
		{
    	$this->attributes['effective_to'] = date('Y-m-d', strtotime($value));
    }

    // public function getEffectiveFromAttribute($value){
    // $this->attributes['effective_from'] = date('m-d-Y', strtotime($value));
    // return $this->attributes['effective_from'];
    // }

    // public function getEffectiveToAttribute($value){
    // $this->attributes['effective_to'] = date('m-d-Y', strtotime($value));
    // return $this->attributes['effective_to'];
    // }

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'cpmfront_end' => 'required',
        'finance_cost'  => 'required',
        'cpum'  => 'required',
        'effective_from' => 'required'
      ];
    }

    //Relationships...........................................................

    public function history()
		{
			 return $this->belongsTo('App\Models\Finance\Cost\FinanceCostHistory' , 'fin_cost_his_id')->select(['fin_cost_his_id']);
		}




}
