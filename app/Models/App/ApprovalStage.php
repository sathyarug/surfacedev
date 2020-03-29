<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Models\Admin\UsrProfile;

class ApprovalStage extends BaseValidator
{
    protected $table='app_approval_stage';
    protected $primaryKey='stage_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['stage_name','stage_id'];

    protected $rules = array(
        'stage_name' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
    }

  /*  public function approval_users()
    {
        return $this->belongsToMany(UsrProfile::class, 'app_approval_stage_users','stage_id','user_id')
        ->select(['user_id','first_name','last_name'])->withPivot('approval_order');
    }*/

}
