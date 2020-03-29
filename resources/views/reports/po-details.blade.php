@extends('reports.common.template')

@section('title')
PO Details
@stop

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-offset-2 form-holder">


            <div class="col-md-12">
                <div class="logo">
                    <img src="{{ URL::asset('assets\images\logo.jpg') }}" />
                </div>
                <div class="address">
                    @foreach ($company as $row)
                    <div class="company-name">{{ $row->company_name }}</div>
                    <div class="company-address">{{ $row->loc_name }}</div>
                    <div class="company-address">{{ $row->loc_address_1 }},{{ $row->loc_address_2 }},{{ $row->country_description }}</div>
                    <div class="company-contact"><strong>Tel :</strong> {{ $row->loc_phone }} / <strong>Fax :</strong> {{ $row->loc_fax }}</div>
                    <div class="company-contact"><strong>Email :</strong> {{ $row->loc_email }} / <strong>Web :</strong> {{ $row->loc_web }}</div>
                    @endforeach
                </div>
            </div>

            @foreach ($headers as $header)
            <div class="col-md-12">
                <div class="title">PO DETAILS (PO NO : {{ $header->po_number }})</div>
            </div>


            <div class="col-md-12">
                <div class="style-details">
                    <table class="table table-borderless">
                        <tr>
                            <th width="17%">PO No</th>
                            <th width="2%">:</th>
                            <td width="31%">{{ $header->po_number }}</td>
                            <th width="17%">PO Created Date</th>
                            <th width="2%">:</th>
                            <td width="31%">{{ $header->created_date }}</td>
                        </tr>

                        <tr>
                            <th>Division</th>
                            <th>:</th>
                            <td>{{ $header->division_description }}</td>
                            <th>Created By</th>
                            <th>:</th>
                            <td>{{ $header->first_name }}</td>
                        </tr>

                        <tr>
                            <th>Currency</th>
                            <th>:</th>
                            <td>{{ $header->currency_code }}</td>
                            <th>Supplier name</th>
                            <th>:</th>
                            <td>{{ $header->supplier_name }}</td>
                        </tr>

                        <tr>
                            <th>Deliver to</th>
                            <th>:</th>
                            <td>{{ $header->loc_name }}</td>
                            <th>Invoice To</th>
                            <th>:</th>
                            <td>{{ $header->company_name }}</td>
                        </tr>

                        <tr>
                            <th>PO Status</th>
                            <th>:</th>
                            <td>{{ $header->po_status }}</td>
                        </tr>

                    </table>
                </div>

            </div>
            @endforeach


            <div class="col-md-12">
                <table class="table table-bordered details">
                    <tr class="green-row">
                        <th>Line No</th>
                        <th>RM In date</th>
                        <th>Revice RM In Date</th>
                        <th>Sales Order No</th>
                        <th>Sales Order Line No</th>
                        <th>Customer PO</th>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Standard Price</th>
                        <th>Purchase Price</th>
                        <th>Inventory UOM</th>
                        <th>Purchase UOM</th>
                        <th>Purchase Qty</th>
                        <th>Value</th>
                        <th>Costing ID</th>
                        <th>Costing EPM</th>
                        <th>Costing NP</th>
                        <th>BOM ID</th>
                        <th>FNG#</th>
                        <th>SFG#</th>
                        <th>Style</th>
                        <th>BOM EPM</th>
                        <th>BOM NP</th>
                    </tr>
                    @foreach ($details as $detail)
                    <tr>
                        <td>{{ $detail->line_no }}</td>
                        <td>{{ $detail->rm_in_date }}</td>
                        <td>{{ $detail->pcd }}</td>
                        <td>{{ $detail->order_code }}</td>
                        <td>{{ $detail->line_no }}</td>
                        <td>{{ $detail->po_no }}</td>
                        <td>{{ $detail->item_code }}</td>
                        <td>{{ $detail->item_description }}</td>
                        <td>{{ $detail->standard_price }}</td>
                        <td>{{ $detail->purchase_price }}</td>
                        <td>{{ $detail->Inventory_UOM }}</td>
                        <td>{{ $detail->purchase_UOM }}</td>
                        <td>{{ $detail->po_qty }}</td>
                        <td>{{ $detail->value}}</td>
                        <td>{{ $detail->costing_ID }}</td>
                        <td>{{ $detail->cos_epm }}</td>
                        <td>{{ $detail->cos_np }}</td>
                        <td>{{ $detail->bom_id }}</td>
                        <td>{{ $detail->FNG_NO}}</td>
                        <td>{{ $detail->SFG_NO }}</td>
                        <td>{{ $detail->style_no }}</td>
                        <td>{{ $detail->bom_epm }}</td>
                        <td>{{ $detail->bom_np }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>


        </div>
    </div>
</div>

@stop