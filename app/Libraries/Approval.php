<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Exception;

use App\Models\App\Process;
use App\Models\App\ApprovalTerm;
use App\Models\App\ApprovalTemplate;
use App\Models\App\ApprovalTemplateStage;
use App\Models\App\ApprovalTemplateStageTerm;
use App\Models\App\ProcessApproval;
use App\Models\App\ProcessApprovalStage;
use App\Models\App\ProcessApprovalStageUser;
use App\Models\App\ApprovalStage;
use App\Models\App\ApprovalStageUser;
use App\Models\App\ApprovalTemplatePath;
use App\Models\App\ApprovalTemplatePathTerm;
use App\Models\Admin\UsrProfile;

use App\Jobs\ApprovalMailSendJob;

use App\Services\Merchandising\Costing\CostingService;
use App\Services\Merchandising\Bom\BomService;

//will be remove in later developments
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGood;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;

use App\Models\Merchandising\PoOrderApproval;

use Webklex\IMAP\Facades\Client;



class Approval
{

  public function start($process_name, $document_id, $document_created_by) {
    try {

      DB::beginTransaction();
      $date = date("Y-m-d H:i:s");
      $process = Process::find($process_name);
      $template = ApprovalTemplate::find($process->approval_template);
      $user_id = auth()->user()->user_id;
      //get initial template stage
      $initial_stage = ApprovalStage::find($template->initial_stage_id);
      if($initial_stage != null){ //has initial stage
        //create new process approval
        $process_approval = new ProcessApproval();
        $process_approval->process = $process_name;
        $process_approval->template_id = $template->template_id;
        $process_approval->document_id = $document_id;
        $process_approval->current_stage_id = $initial_stage->stage_id;
        $process_approval->document_created_by = $document_created_by;
        $process_approval->created_date = $date;
        $process_approval->created_by = $user_id;
        $process_approval->updated_date = $date;
        $process_approval->updated_by = $user_id;
        $process_approval->status = 'PENDING';
        $process_approval->save();
        //process initial stage
        $this->process_stage($template, $process_approval, $initial_stage->stage_id, $process_name, $document_id);
        //commit all transactions
        DB::commit();
        return true;
      }
      else {//no initial stage
        return false;
      }
    }
    catch(Exception $e){
      DB::rollback();
      echo json_encode($e);
      return false;
    }
  }


  //process a process approval stage
  private function process_stage($template, $process_approval, $stage_id, $process_name, $document_id){

    $date = date("Y-m-d H:i:s");
    $stage = ApprovalStage::find($stage_id);
    //create new stage
    $process_approval_stage = new ProcessApprovalStage();
    $process_approval_stage->approval_id = $process_approval->id;
    $process_approval_stage->request_date = $date;
    $process_approval_stage->request_remark = '';
    $process_approval_stage->status = 'PENDING';
    //$process_approval_stage->template_stage_id = $first_template_stages->template_stage_id;
    $process_approval_stage->stage_id = $stage->stage_id;
    //$process_approval_stage->stage_order = $first_template_stages->stage_order;
    $process_approval_stage->created_date = $date;
    $process_approval_stage->updated_date = $date;
    $process_approval_stage->save();

    if($stage->interaction_type == 'MANUAL'){ //na human interaction
      $stage_users = ApprovalStageUser::where('stage_id', '=', $stage->stage_id)->get();
      //get users which can have permissions to work with current stage
      $to = [];
      for($x = 0 ; $x < sizeof($stage_users) ; $x++){
        $process_approval_user = new ProcessApprovalStageUser();
        $process_approval_user->approval_stage_id = $process_approval_stage->id;
        $process_approval_user->user_type = $stage_users[$x]['type'];
        $process_approval_user->user_position = $stage_users[$x]['user_position'];
        $process_approval_user->user_id = $stage_users[$x]['user_id'];
        $process_approval_user->status = 'PENDING';
        $process_approval_user->created_date = $date;
        $process_approval_user->updated_date = $date;

        if($process_approval_user->user_type == 'USER'){ //exact user
          $user = UsrProfile::find($process_approval_user->user_id);
          array_push($to, ['email' => $user->email]);
        }
        else if($process_approval_user->user_type == 'REPORTING_LEVEL'){//user reporting level
          $reporting_level = $process_approval_user->user_id; //user id = reporting level
          $created_user =  UsrProfile::find($process_approval->document_created_by); //get document created user
          $report_user = null;
          if($reporting_level == 1) {
            $report_user = UsrProfile::find($created_user->reporting_level_1); //get reporting level 1 user
          }
          else if($reporting_level == 2) {
            $report_user = UsrProfile::find($created_user->reporting_level_2); //get reporting level 2 user
          }
          $process_approval_user->user_id = $report_user->user_id;
          array_push($to, ['email' => $report_user->email]);
        }
        else if($process_approval_user->user_type == 'DESIGNATION'){//department designation

        }

        $process_approval_user->save();
        //get document data
        $data = $this->get_data($process_name, $document_id);
        $data['approval_id'] = $process_approval_user->id;
        $data['request_remark'] = $process_approval_stage->request_remark;
        $mail_subject = 'APPROVAL PENDING ' . $process_name . '-'.$document_id.' #'.$process_approval_user->id.'#';
        $job = new ApprovalMailSendJob($process_name, $mail_subject, $data, $to);//dispatch mail to stage users
        dispatch($job);
      }
    }
    else {//no human interaction (AUTO)
        $process_approval_stage->status = 'APPROVED';
        $process_approval_stage->save();
        //get template stage paths
        $paths = ApprovalTemplatePath::where('template_id', '=', $template->template_id)->where('start_stage_id', '=', $stage_id)->get();
        if(sizeof($paths) > 0){//has next stage
          foreach ($paths as $path) {
            $term_status = $this->validate_terms($path, $document_id);
            if($term_status == true){ //validation pass and can with this path
              $this->process_stage($template, $process_approval, $path->end_stage_id, $process_name, $document_id);
            }
            else {
              //can not go in this path
            }
          }
       }
    }
  }


  //validate path terms and quiries
  private function validate_terms($path, $document_id){
    $terms = ApprovalTemplatePathTerm::where('path_id', '=', $path->path_id)->get();
    if(sizeof($terms) > 0) {
      //get all stage rules and execute one by one
      foreach($terms as $term){
        $approval_term = ApprovalTerm::find($term->term_id);
        $query = $approval_term->query();
        $query = str_replace("{document_id}", $document_id, $query);
        $query = str_replace("{ratio}", $approval_term->ratio, $query);
        $query = str_replace("{value}", $approval_term->value, $query);

        if($approval_term->execute_query($query) == false) { //run query and did not pass the term
          return false;
        }
      }
    }
    //chek path quiries
    $queries = DB::table('app_approval_template_path_queries')
    ->join('app_approval_queries', 'app_approval_queries.query_id', '=', 'app_approval_template_path_queries.query_id')
    ->where('app_approval_template_path_queries.path_id', '=', $path->path_id)
    ->select('app_approval_queries.*')->get();

    if(sizeof($queries) > 0){
      foreach($queries as $query) {
        $query->query = str_replace("{document_id}", $document_id, $query->query);
        $query_result = DB::select($query->query);
        if($query_result == null || $query_result[0]->term_result == 0){
          return false;
        }
      }
    }
    return true;
  }



  public function approve($id, $status, $approval_remark, $user_id) {
    try {
      $date = date("Y-m-d H:i:s");
      //check approval status
      if($status == 'A' || $status == 'a' || $status == 'APPROVED'){
        $status = 'APPROVED';
      }
      else if($status == 'R' || $status == 'r' || $status == 'REJECTED'){
        $status = 'REJECTED';
      }

      $process_approval_stage_user = ProcessApprovalStageUser::find($id);
      if($process_approval_stage_user != null && $process_approval_stage_user->status == 'PENDING') { //not approved or reject
        //
        $process_approval_stage_user->status = $status;
        $process_approval_stage_user->approval_date = $date;
        //$process_approval_stage_user->approval_user = $user_id;
        $process_approval_stage_user->approval_remark = $approval_remark;
        $process_approval_stage_user->save();
        //get process approval stage
        $process_approval_stage = ProcessApprovalStage::find($process_approval_stage_user->approval_stage_id);
        $process_approval_stage->status = $status;
        $process_approval_stage->save();

        $process_approval = ProcessApproval::find($process_approval_stage->approval_id);//get the process approval header

        if($status == 'REJECTED') { //stop the approval process if rejected
          $process_approval->status = $status;
          $process_approval->save();
          //update document status
          $this->update_document_status($process_approval->process, $process_approval->document_id, $status);
          return true;
        }

        $paths = ApprovalTemplatePath::where('template_id', '=', $process_approval->template_id)->where('start_stage_id', '=', $process_approval_stage->stage_id)->get();
        if(sizeof($paths) > 0){//has next stage
          $condition_success_count = 0;
          foreach ($paths as $path) {
            $term_status = $this->validate_terms($path, $process_approval->document_id);
            if($term_status == true){ //validation pass and can with this path
              $condition_success_count++;
              $template = ApprovalTemplate::find($process_approval->template_id);
              $this->process_stage($template, $process_approval, $path->end_stage_id, $process_approval->process, $process_approval->document_id);
            }
            else {
              //can not go in this path
            }
          }
          //has paths, but no conditins pass
          if($condition_success_count == 0) {
            $this->update_document_status($process_approval->process, $process_approval->document_id, 'APPROVED');
            $process_approval->status = $status;
            $process_approval->save();
          }
          return true;
        }
        else {
          $this->update_document_status($process_approval->process, $process_approval->document_id, 'APPROVED');
          $process_approval->status = $status;
          $process_approval->save();
          return true;
        }
      }
      else { //already approved or rejected
        //send response
        return false;
      }
    }
    catch(Exception $e){
      echo json_encode($e);die();
    }
  }


  public function readMail(){
    try {
      $oClient = Client::account('default');
      $oClient->connect();

      $oFolder = $oClient->getFolder('INBOX');  // get the read inbox
      //$aMessage = $oFolder->search()->text('TEST')->get();//subject('TEST')->limit(20, 1)->get();
      $aMessage = $oFolder->query()->get();
      //echo json_encode($aMessage);
      foreach($aMessage as $message){
      //  echo $message->getSubject();
        if(strpos($message->getSubject(), "APPROVAL PENDING") > 0) {
          $supject_parts = explode ("#", $message->getSubject());
          //$process_name = $supject_parts[1];

          $approval_id = $supject_parts[1];
          $status = null;
          $approval_remark = null;
          if(sizeof($supject_parts) > 2) {
            $status = $supject_parts[2];
          }
          if(sizeof($supject_parts) > 3){
            $approval_remark = $supject_parts[3];
          }

          $approval_stage_user = ProcessApprovalStageUser::find($approval_id);
          $user_profile = UsrProfile::where('email', '=', $message->getFrom()[0]->mail)->first();
          if($approval_stage_user == null || $user_profile == null){
            continue;
          }
          //echo json_encode($approval_id);die();
          //chek email replied user is same as approval user
          if($user_profile != null && $user_profile->user_id == $approval_stage_user->user_id) {
            $response = $this->approve($approval_id, $status, $approval_remark, $user_profile->user_id);
            if($response == true){
              $message->delete();
              echo 'Document approved successfully';
            }
            else {
              echo 'Error occured while approving document';
            }
          }
        }
      }
    }
    catch(Exception $e){
      echo json_encode($e);
    }
  }

  //***************************************************************************

  private function get_data($process, $document_id){
    $response_data = [];
    if($process == 'COSTING') {
      $response_data = [
        'costing' => Costing::find($document_id)
      ];
    }
    else if($process == 'CUSTOMER_ORDER'){

    }
    else if($process == 'PO'){
      $response_data = [
        'po' => PoOrderApproval::find($document_id)
      ];
    }
    else if($process == 'BOM'){
      $response_data = [
        'bom' => BomHeader::find($document_id)
      ];
    }
    return $response_data;
  }


  public function update_document_status($process_name, $document_id, $status){
    if($process_name == 'COSTING') {

      DB::table('costing')->where('id', $document_id)->update(['status' => $status]);
      //send status to document created user
      $data = [
        'costing' => Costing::find($document_id)
      ];

      $costingService = new CostingService();
      $costingService->genarate_bom($document_id);
      //$this->generate_bom_for_costing($document_id);
      //echo json_encode('6666');die();
      $mail_subject = 'COSTING ' . $status . ' - '.$document_id;
      $created_user = UsrProfile::find($data['costing']->created_by);
      if($created_user != null){//send response to costing created user
        $to = [['email' => $created_user->email]];
        $job = new ApprovalMailSendJob($process_name.'_CONFIRM', $mail_subject, $data, $to);
        dispatch($job);
      }
    }
    if($process_name == 'BOM') {

      if($status == 'APPROVED'){ //change status to released
        $status = 'RELEASED';
      }

      DB::table('bom_header')->where('bom_id', $document_id)->update(['status' => $status]);
      $bom_service = new BomService();
      $bom_service->create_shop_order_items($document_id);//create new shop order items

      //send status to document created user
      $data = [ 'bom' => BomHeader::find($document_id) ];

      $mail_subject = 'BOM ' . $status . ' - '.$document_id;
      $created_user = UsrProfile::find($data['bom']->created_by);
      if($created_user != null){//send response to costing created user
        $to = [['email' => $created_user->email]];
        $job = new ApprovalMailSendJob($process_name.'_CONFIRM', $mail_subject, $data, $to);
        dispatch($job);
      }
    }

    if($process_name == 'PO') {

      if($status == 'APPROVED'){

        $poApp = PoOrderApproval::find($document_id);
        $data = [ 'po' => $poApp ];

        $header = json_decode($poApp->po_header);
        $details = json_decode($poApp->po_details);

        $po_id= $header->po_id;
        $deli_date = explode("T",$header->delivery_date);
        $deliverto = $header->deliverto->loc_id;
        $invoiceto = $header->invoiceto->company_id;

        DB::table('merc_po_order_header')
          ->where('po_id', $po_id)
          ->update(['delivery_date' => $deli_date[0],
                    'po_deli_loc' => $deliverto,
                    'invoice_to' => $invoiceto ]);

        for($x = 0 ; $x < sizeof($details) ; $x++){

          DB::table('merc_po_order_details')
            ->where('id', $details[$x]->id)
            ->update(['req_qty' => $details[$x]->tra_qty,
                      'tot_qty' => $details[$x]->value_sum]);

        }

        DB::table('merc_po_order_header')
            ->where('po_id', $poApp->po_id)
            ->update([ 'approval_status' => $status  ]);

        $mail_subject = 'PO ' . $status . ' - '.$document_id;
        $created_user = UsrProfile::find($poApp->created_by);
        $to = [['email' => $created_user->email]];
        $job = new ApprovalMailSendJob($process_name.'_CONFIRM', $mail_subject, $data, $to);
        dispatch($job);

      }
      else {
        $poApp = PoOrderApproval::find($document_id);
        $data = [ 'po' => $poApp ];

        DB::table('merc_po_order_header')
            ->where('po_id', $poApp->po_id)
            ->update([ 'approval_status' => $status  ]);

        $mail_subject = 'PO ' . $status . ' - '.$document_id;
        $created_user = UsrProfile::find($poApp->created_by);
        $to = [['email' => $created_user->email]];
        $job = new ApprovalMailSendJob($process_name.'_CONFIRM', $mail_subject, $data, $to);
        dispatch($job);
      }


    }
  }




  //privae functions, these functions will remove in future developments.
  //those are use temporally


  /*private function generate_bom_for_costing($costing_id) {
    $deliveries = CustomerOrderDetails::where('costing_id', '=', $costing_id)->get();
    $costing = Costing::find($costing_id);
    for($y = 0; $y < sizeof($deliveries); $y++) {
      $bom = new BOMHeader();
      $bom->costing_id = $deliveries[$y]->costing_id;
      $bom->delivery_id = $deliveries[$y]->details_id;
      $bom->sc_no = $costing->sc_no;
      $bom->status = 1;
      $bom->save();

      $components = CostingFinishGoodComponent::where('fg_id', '=', $deliveries[$y]->fg_id)->get()->pluck('id');
      $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
      $items = json_decode(json_encode($items), true); //conver to array
      for($x = 0 ; $x < sizeof($items); $x++) {
        $items[$x]['bom_id'] = $bom->bom_id;
        $items[$x]['costing_item_id'] = $items[$x]['id'];
        $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
        $items[$x]['bom_unit_price'] = $items[$x]['unit_price'];
        $items[$x]['order_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
        $items[$x]['required_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
        $items[$x]['total_cost'] = (($items[$x]['unit_price'] * $items[$x]['gross_consumption'] * $deliveries[$y]->order_qty) + $items[$x]['freight_charges'] + $items[$x]['surcharge']);
        $items[$x]['created_date'] = null;
        $items[$x]['created_by'] = null;
        $items[$x]['updated_date'] = null;
        $items[$x]['updated_by'] = null;
      }
      DB::table('bom_details')->insert($items);
    }
  }*/

}
