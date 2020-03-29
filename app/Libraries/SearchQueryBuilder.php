<?php

namespace App\Libraries;

//use Illuminate\Support\Facades\DB;

use App\BaseValidator;

class SearchQueryBuilder
{

  public function generateQuery(/*BaseValidator */$queryObject, $fields)
  {
    foreach ($fields as $row) {
      $rules = explode(';', $row->query);
      if(sizeof($rules) > 0)
      {
        $arr = [];
        $queryObject = $queryObject->where(function($query) use ($row, $rules){

          foreach($rules as $rule){

            $operator = $this->get_operator($rule);
            $rules2 = explode($operator, $rule);

            if($operator == '...')
            {
              $rules3 = explode(',' , $rules2[1]);
              $query->orWhere([ [$row->field , '>', $rules3[0]], [$row->field , '<', $rules3[1]] ]);
            }
            else if($operator == '%')
            {
              $query->orWhere($row->field, 'like', $rules2[1].'%');
            }
            else if($operator == '%%'){
              $query->orWhere($row->field, 'like', '%'.$rules2[1].'%');
            }
            else
            {
              $rules3 = explode(',' , $rules2[1]);
              if(sizeof($rules3) <= 1){
                $query->orWhere($row->field, $operator, $rules2[1]);
              }
              else{
                $query->whereIn($row->field, $rules3);
              }
            }

          }

        });

      }
    }
    return $queryObject;
  }



  private function get_operator($rule){
    if(preg_match('/=[A-Za-z0-9_-]{1,}/', $rule)){
      return '=';
    }
    if(preg_match('/!=[A-Za-z0-9_-]{1,}/', $rule)){
      return '!=';
    }
    else if(preg_match('/>=[A-Za-z0-9_-]{1,}/', $rule)){
      return '>=';
    }
    else if(preg_match('/>[A-Za-z0-9_-]{1,}/', $rule)){
      return '>';
    }
    else if(preg_match('/<=[A-Za-z0-9_-]{1,}/', $rule)){
      return '<=';
    }
    else if(preg_match('/<[A-Za-z0-9_-]{1,}/', $rule)){
      return '<';
    }
    else if(preg_match('/%%[A-Za-z0-9_-]{1,}/', $rule)){
      return '%%';
    }
    else if(preg_match('/%[A-Za-z0-9_-]{1,}/', $rule)){
      return '%';
    }
    else if(preg_match('/...[A-Za-z0-9_-]{1,}/', $rule)){
      return '...';
    }

  }



}
