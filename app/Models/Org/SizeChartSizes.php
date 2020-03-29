<?php

namespace App\Models\Org;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SizeChartSizes extends BaseValidator
{

    protected $table = 'org_size_chart_sizes';
    protected $primaryKey = 'size_chart_size_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['size_chart_id','size_id'];
    
    protected $rules=array(
        'size_id'=>'required',
        'size_chart_id'=>'required'
    );

    public function __construct()
    {
        parent::__construct();
    }


}
