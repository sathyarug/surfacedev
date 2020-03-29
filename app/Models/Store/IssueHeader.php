<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 5/1/2019
 * Time: 11:16 AM
 */

namespace App\Models\Store;
use App\BaseValidator;


class IssueHeader extends BaseValidator
{
    protected $table='store_issue_header';
    protected $primaryKey='issue_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['issue_no','so_no', 'mrn_no','location','store', 'sub_store', 'status' ];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function issueDetails(){
        return $this->hasMany('App\Models\Store\IssueDetails', 'issue_id', 'issue_id');
    }

    public function __construct() {
        parent::__construct();
    }


}