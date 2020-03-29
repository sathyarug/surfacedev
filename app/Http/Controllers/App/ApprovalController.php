<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;

use App\Models\App\Process;
use App\Models\App\ApprovalTemplate;
use App\Models\App\ApprovalTemplateStage;
use App\Models\App\ProcessApproval;
use App\Models\App\ProcessApprovalStage;
use App\Models\App\ProcessApprovalStageUser;
use App\Models\Admin\ApprovalStage;
use App\Models\App\ApprovalStageUser;
use App\Models\Admin\UsrProfile;
use App\Models\Merchandising\ShopOrderDetail;

use App\Jobs\ApprovalMailSendJob;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGood;

use App\Libraries\Approval;
use App\Services\Merchandising\Costing\CostingService;

class ApprovalController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index', 'approve','generate_costing_bom','remove_costing_data','start']]);
    }

    //get Color list
    public function index(Request $request)
    {
        auth()->payload()->get('loc_id');
    }


   public function start(Request $request){
     
   }


   public function approve(Request $request){
      /*$costingService = new CostingService();
      $costingService->genarate_bom(26);*/
      $approval = new Approval();
      $approval->readMail();
   }


   public function generate_costing_bom(Request $request){
     $costing_id = $request->costing_id;
     $costing = Costing::find($costing_id);
     if($costing != null && $costing->status == 'APPROVED'){
       $costingService = new CostingService();
       $res = $costingService->genarate_bom($costing_id);
       echo json_encode($res);
     }
   }

   public function remove_costing_data(Request $request){
     try {
         DB::beginTransaction();

         $costing_id = $request->costing_id;

         DB::delete("delete from bom_details where costing_id = ?", [$costing_id]);
         DB::delete("delete from bom_header where costing_id = ?", [$costing_id]);

         $fng_items = DB::table('costing_fng_item')->where('costing_id', '=', $costing_id)->pluck('fng_id');
         $sfg_items = DB::table('costing_sfg_item')->where('costing_id', '=', $costing_id)->pluck('sfg_id');

         DB::table('item_master')->whereIn('master_id', $fng_items)->delete();
         DB::table('item_master')->whereIn('master_id', $sfg_items)->delete();

         DB::delete("delete from costing_sfg_item where costing_id = ?", [$costing_id]);
         DB::delete("delete from costing_fng_item where costing_id = ?", [$costing_id]);

         $process_ids = DB::table('app_process_approval')->where('document_id', '=', $costing_id)->pluck('id');
         $stage_ids = DB::table('app_process_approval_stages')->whereIn('approval_id', $process_ids)->pluck('id');

         DB::table('app_process_approval_stage_users')->whereIn('approval_stage_id', $stage_ids)->delete();
         DB::table('app_process_approval_stages')->whereIn('id', $stage_ids)->delete();
         DB::table('app_process_approval')->whereIn('id', $process_ids)->delete();

         DB::update("update costing set status = 'CREATE' where id = ?", [$costing_id]);

         DB::commit();// Commit Transaction
         echo 'done';
     }
     catch(\Exception $e){
       // Rollback Transaction
       DB::rollback();
       echo 'error';
     }


   }

}
