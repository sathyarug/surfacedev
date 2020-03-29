<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class StyleListController extends Controller
{
    public function index(Request $request)
    { }

    public function getStyles(Request $request)
    {
        $type = $request->type;
        $load_list = [];
        
        if ($type == 'click') {
            $division = $request->division_description['division_id'];
            $style = $request->style_no['style_no'];

            $query = DB::table('style_creation')
                ->select(
                    'style_creation.style_no',
                    'style_creation.style_description',
                    'style_creation.image'
                );

            if ($style != null || $style != '') {
                $query->where('style_creation.style_no', $style);
            }

            if ($division != null || $division != '') {
                $query->where('style_creation.division_id', $division);
            }

            $load_list = $query->get();
        } else if ($type == 'auto') {

            $searchStyle = $request->search;
            $div = $request->div['division_id'];

            $query = DB::table('style_creation')
                ->select(
                    'style_creation.style_no',
                    'style_creation.style_description',
                    'style_creation.image'
                );

            if ($searchStyle != null || $searchStyle != '') {
                $query->where('style_creation.style_no', 'LIKE', '%' . $searchStyle . '%');
            }

            if ($div != null || $div != '') {
                $query->where('style_creation.division_id', $div);
            }

            $load_list = $query->get();
        }

        echo json_encode([
            "data" => $load_list
        ]);
    }
}
