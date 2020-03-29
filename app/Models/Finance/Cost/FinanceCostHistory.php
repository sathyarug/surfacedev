<?php

namespace App\Models\Finance\Cost;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;

class FinanceCostHistory extends BaseValidator
{
    protected $table = 'fin_fin_cost_his';
    protected $primaryKey = 'fin_cost_his_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['fin_cost_his_id','finance_cost','cpmfront_end','cpum','effective_from','effective_to','fin_cost_id'];

    protected $rules = array(
        'cpmfront_end' => 'required',
        'finance_cost'  => 'required',
        'cpum'  => 'required',
    );

    public function setEffectiveFromAttribute($value)
		{
    	$this->attributes['effective_from'] = date('Y-m-d', strtotime($value));
    }

    public function setEffectiveToAttribute($value)
		{
    	$this->attributes['effective_to'] = date('Y-m-d', strtotime($value));
    }

   public function __construct()
    {
        parent::__construct();
    }


}
