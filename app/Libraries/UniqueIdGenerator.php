<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class UniqueIdGenerator
{

  public static function generateUniqueId($type , $company_id)
  {
      $unque_id = DB::transaction(function () use ($type , $company_id){
        
          DB::table('unique_id_generator')
          ->where([ ['process_type' , '=' , $type] , ['company' , '=' , $company_id] ])
          ->increment('unque_id', 1);

          $id = DB::table('unique_id_generator')->where([ ['process_type' , '=' , $type] , ['company' , '=' , $company_id] ])
          ->sharedLock()
          ->value('unque_id');
          return $id;
      });
      return $unque_id;
  }

}
