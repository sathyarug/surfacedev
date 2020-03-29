@extends('reports.common.template')

@section('title')
MRN Note
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
                    @foreach ($main as $row)
                    <div class="company-name">{{ $row->company_name }}</div>
                    <div class="company-address">{{ $row->loc_name }}</div>
                    <div class="company-address">{{ $row->loc_address_1 }},{{ $row->loc_address_2 }},{{ $row->country_description }}</div>
                    <div class="company-contact"><strong>Tel :</strong> {{ $row->loc_phone }} / <strong>Fax :</strong> {{ $row->loc_fax }}</div>
                    <div class="company-contact"><strong>Email :</strong> {{ $row->loc_email }} / <strong>Web :</strong> {{ $row->loc_web }}</div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="title">MRN NOTE</div>
            </div>

            <div class="col-md-12">
                <div class="style-details">
                    <table class="table table-borderless">
                        <tr>
                            <th width="17%">Location</th>
                            <th width="2%">:</th>
                            <td width="31%">{{ $row->loc_name }}</td>
                            <th width="17%">MRN No</th>
                            <th width="2%">:</th>
                            <td width="31%">{{ $row->mrn_no }}</td>
                        </tr>

                        <tr>
                            <th>Section</th>
                            <th>:</th>
                            <td>{{ $row->section_name }}</td>
                            <th>Line No</th>
                            <th>:</th>
                            <td>{{ $row->line_no }}</td>
                        </tr>

                        <tr>
                            <th>Request Type</th>
                            <th>:</th>
                            <td>{{ $row->request_type }}</td>
                            <th>Style No</th>
                            <th>:</th>
                            <td>{{ $row->style_no }}</td>
                        </tr>

                        <tr>
                            <th>Cut Quantity</th>
                            <th>:</th>
                            <td>{{ $row->cut_qty }}</td>
                        </tr>

                    </table>
                </div>

                <div class="style-image">
                    <div class="text-right">
                    </div>
                </div>

            </div>


            <div class="col-md-12">
                <table class="table table-bordered details">
                    <tr class="green-row">
                        <th width="15%">Item Code</th>
                        <th width="45%">Item Description</th>
                        <th width="10%">UOM</th>
                        <th width="15%">Quantity</th>
                    </tr>
                    @foreach ($details as $detail)
                    <!-- @if($row->mrn_id == $detail->mrn_id) -->
                    <tr>
                        <td>{{ $detail->master_code }}</td>
                        <td>{{ $detail->master_description }}</td>
                        <td>{{ $detail->uom_code }}</td>
                        <td>{{ $detail->requested_qty }}</td>
                    </tr>
                    <!-- @endif -->
                    @endforeach
                </table>
            </div>

            <div class="col-md-12">
                <div class="style-details">
                    <table class="table table-borderless">
                        <tr>
                            <th width="17%">Request By</th>
                            <th width="2%">:</th>
                            <td width="31%">{{ $row->first_name }} {{$row->last_name}}</td>
                            <th width="17%">Date and Time</th>
                            <th width="2%">:</th>
                            <td width="31%">{{ date('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="style-image">
                    <div class="text-right">
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="style-details">
                    <table class="table table-borderless">
                        <tr>
                            <th width="17%">Signature</th>
                        </tr>
                    </table>
                </div>
                <div class="style-image">
                    <div class="text-right">
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</div>

@stop
