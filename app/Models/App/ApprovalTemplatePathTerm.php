<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;


//class ApprovalTemplateStageTerm extends Model
class ApprovalTemplatePathTerm extends Model
{
    protected $table='app_approval_template_path_terms';
    protected $primaryKey='path_term_id';
    public $timestamps = false;

    protected $rules = array(
        'term_id' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

}
