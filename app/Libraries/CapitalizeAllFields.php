<?php
namespace App\Libraries;

 class CapitalizeAllFields{


public  static function setCapitalAll($object){
$arr=json_decode(json_encode($object), true);
foreach ($arr as $key => $value) {
  // code...
  $object->$key=strtoupper($value);
}

  return $object;
}

}
