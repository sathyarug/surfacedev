<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Exception;

use App\Models\App\Bookmark;

class BookmarkController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify'/*, ['except' => ['index']]*/);
    }

    //get user bookmark list
    public function index(Request $request)
    {
        $user = auth()->user();
        $bookmarks = Bookmark::where('user', '=', $user->user_id)->get();
        return response([
          'data' => $bookmarks
        ]);
    }

    //create a new bookmark
    public function store(Request $request)
    {
      $bookmark = new Bookmark();
      if($bookmark->validate($request->all()))
      {
        $user = auth()->user();
        $bookmarks = Bookmark::where('user', '=', $user->user_id)->get();
        if(sizeof($bookmarks) >= 10){
          Bookmark::destroy($bookmarks[0]->id);
        }
        $bookmark->fill($request->all());
        $bookmark->user = $user->user_id;
        $bookmark->save();

        return response([ 'data' => [
          'message' => 'Page was bookmarked successfully',
          'bookmark' => $bookmark
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $bookmark->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


}
