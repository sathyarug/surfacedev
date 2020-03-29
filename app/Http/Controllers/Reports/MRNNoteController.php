<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class MRNNoteController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        if ($type == 'auto') {
            $search = $request->search;
            return response($this->load_mrn_no($search));
        }
        
    }

    //search mrn no for autocomplete
    private function load_mrn_no($search)
    {
        $mrn_no_list = DB::table('store_mrn_header')
            ->select(
                'mrn_id',
                'mrn_no'
            )
            ->where([['mrn_no', 'like', '%' . $search . '%'],])
            ->get();
        return $mrn_no_list;
    }

    public function getMrnNote(Request $request)
    {
        $mrn_no = $request->bi;
        $mrn_id = $request->ci;
        $load_list = [];

        $query = DB::table('store_mrn_header')
            ->join('org_location', 'org_location.loc_id', '=', 'store_mrn_header.user_loc_id')
            ->join('org_section', 'org_section.section_id', '=', 'store_mrn_header.section_id')
            ->join('org_request_type', 'org_request_type.request_type_id', '=', 'store_mrn_header.request_type_id')
            ->join('style_creation', 'style_creation.style_id', '=', 'store_mrn_header.style_id')
            ->join('usr_profile', 'usr_profile.user_id', '=', 'store_mrn_header.created_by')
            ->join('org_company', 'org_company.company_id', '=', 'org_location.company_id')
            ->join('org_country', 'org_country.country_id', '=', 'org_location.country_code')
            ->select(
                'store_mrn_header.mrn_id',
                'org_location.loc_name',
                'store_mrn_header.mrn_no',
                'org_section.section_name',
                'store_mrn_header.line_no',
                'org_request_type.request_type',
                'style_creation.style_no',
                'store_mrn_header.cut_qty',
                'store_mrn_header.created_date',
                'usr_profile.first_name',
                'usr_profile.last_name',
                'org_company.company_name',
                'org_location.loc_name',
                'org_location.loc_address_1',
                'org_location.loc_address_2',
                'org_country.country_description',
                'org_location.loc_phone',
                'org_location.loc_fax',
                'org_location.loc_email',
                'org_location.loc_web'
            );

        if ($mrn_no != null || $mrn_no != "") {
            $query->where('store_mrn_header.mrn_no', $mrn_no);
        }

        $load_list['main'] = $query->distinct()->get();

        $query2 = DB::table('store_mrn_detail')
            ->join('item_master', 'item_master.master_id', '=', 'store_mrn_detail.item_id')
            ->join('org_uom', 'org_uom.uom_id', '=', 'store_mrn_detail.uom')
            ->select(
                'store_mrn_detail.mrn_id',
                'item_master.master_code',
                'item_master.master_description',
                'org_uom.uom_code',
                'store_mrn_detail.requested_qty'
            );

        if ($mrn_id != null || $mrn_id != "") {
            $query2->where('store_mrn_detail.mrn_id', $mrn_id);
        }

        $load_list['details'] = $query2->distinct()->get();

        $pdf = PDF::loadView('reports/mrn-note', $load_list)
            ->stream('MRN Note - ' . $mrn_no . '.pdf');
        return $pdf;
    }
}
