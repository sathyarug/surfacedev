<?php

namespace App\Exceptions;

use Exception;



/**
 *
 */
trait ExceptionTrait
{

  public function apiException($request,$exception)
  {

    $error = [
      /*"file" => $exception->getFile(),
      "line" => $exception->getLine(),
      "exceptionFull" => (new \ReflectionClass($exception)),
      "exception" => (new \ReflectionClass($exception))->getShortName(),
      "code" => $exception->getCode(),
      "message" => $exception->getMessage()*/
    ];
    $userMessage = 'ss';
    $status = 400;

    /*if(true){

      $userMessage = 'Requested model not found';
      $status = 404;
    }
    else if($this->isNotFoundHttpException($exception)){

      $userMessage = 'Requested page not found';
      $status = 404;
    }
    else{

      $userMessage = 'Bad request';
      $status = 400;
    }*/

  //  echo json_encode($error);

    /*$error['userMessage'] = $userMessage;
    $error['status'] = $status;*/
    return response()->json([
          "errors" => "555"
    ]);
  }


  /*protected function isModelNotFoundException(Exception $e)
  {
    echo '888';
     return $e instanceof ModelNotFoundException;
  }


  protected function isNotFoundHttpException(Exception $e)
  {
     return $e instanceof NotFoundHttpException;
  }*/

}


 ?>
