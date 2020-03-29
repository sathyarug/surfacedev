<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CompanySection extends BaseValidator
{

		protected $table = 'org_company_sections';
		protected $primaryKey = 'id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';



    	public function __construct()
    	{
        parent::__construct();
    	}
}
