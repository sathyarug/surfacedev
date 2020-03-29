<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemController extends Controller
{
     public function index(){
        return view('org.item_creation.item_creation');
    }
}
