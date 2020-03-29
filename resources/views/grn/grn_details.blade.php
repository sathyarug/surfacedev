@extends('layout.main')

@section('title') GRN Details @endsection

@section('load_css') <link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/theme-default.min.css"
                           rel="stylesheet" type="text/css" /> @endsection


@section('m_costing') class = 'active' @endsection

@section('body')

<!-- Page header -->
<div class="page-header page-header-default ">
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="index.html"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">@yield('title')</li>
        </ul>
        <ul class="breadcrumb-elements">
            <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-gear position-left"></i>
                    Settings
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
                    <li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
                    <li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
                    <li class="divider"></li>
                    <li><a href="#"><i class="icon-gear"></i> All settings</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div> <!-- /page header -->

<!-- Content area -->
<div class="content">
    <div class="col-md-12">
        <div class="panel panel-flat">
            <div class="panel-heading">

                <h5 class="panel-title">GRN Details</h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="alert alert-danger" style="display: none">
                            error
                        </div>
                        {!! Form::open(array('url' => 'insertCountry','method','post','id'=>'country-form')) !!}
                        <input type="hidden" id="country_id" name="country_id">
                        <div class="col-md-2">
                            <fieldset class="content-group">
                                <label>Origin <span class="text-danger">*</span> :</label>
                                {!!Form::select('size', ['S' => 'Small','L' => 'Large'], 'S',['class'=>'form-control input-xxs','style'=>'']) !!}
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="content-group">
                                <label>Supplier<span class="text-danger">*</span> :</label>
                                {!!Form::select('size', ['S' => 'Small','L' => 'Large'], 'S',['class'=>'form-control input-xxs','style'=>'']) !!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="content-group">
                                {!!Form::checkbox('name', 'value',['class'=>'form-control input-xxs'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="content-group">
                                <label>PO Date From <span class="text-danger">*</span> :</label>
                                {!!Form::text('country_description',null,['class'=>'form-control input-xxs','id'=>'country_description','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="content-group">
                                <label>To <span class="text-danger">*</span> :</label>
                                {!!Form::text('country_description',null,['class'=>'form-control input-xxs','id'=>'country_description','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>

                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-12">
                        <div class="col-md-1">
                            <fieldset class="content-group">
                                <label>GRN No<span class="text-danger">*</span> :</label>
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-1">
                            <label></label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <label>Date</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <label>GRN Value</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <label>Invoice Value</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label>PO No*</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <label>Remarks</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <label>Supplier Advice No<span class="text-danger">*</span> </label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <label>Cusdec No</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <label>Invoice No<span class="text-danger">*</span> </label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <label>Entry No</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <label>Advice Date</label>
                            <fieldset class="content-group">
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="content-group">
                                <label>Main Stores <span class="text-danger">*</span> </label>
                                {!!Form::select('size', ['S' => 'Small','L' => 'Large'], 'S',['class'=>'form-control input-xxs','style'=>'']) !!}
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="content-group">
                                <label>Sub Stores<span class="text-danger">*</span></label>
                                {!!Form::select('size', ['S' => 'Small','L' => 'Large'], 'S',['class'=>'form-control input-xxs','style'=>'']) !!}
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-6 btn pull-right">
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" 
                                id="btn-close"><b><i class="icon-floppy-disk"></i></b> Auto BIN</button>
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" 
                                id="btn-close"><b><i class="icon-floppy-disk"></i></b> ADD NEW</button>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="highlighted-justified-tab1">
                        <div class="col-md-12">
                            <table class="table display compact" id="tbl">
                                <thead>
                                    <tr>
                                        <th>Del</th>
                                        <th>Style - Order No</th>
                                        <th>SC No</th>
                                        <th>Buyer PO</th>
                                        <th>Description</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Unit</th>
                                        <th>PO Rate</th>
                                        <th>Invoice Rate</th>
                                        <th>Pending Qty</th>
                                        <th>Rec Qty</th>
                                        <th>Location</th>
                                        <th>Excess</th>
                                        <th>Balance</th>
                                        <th>Value</th>
                                        <th>Mat ID</th>
                                        <th>Year</th>
                                        <th>PO No</th>
                                        <th>PO Qty</th>
                                        <th>Total GRN Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="data">

                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab2">
                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab3">
                        DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg whatever.
                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab4">
                        Aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthet.
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="text-right">
                        <button class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn-new"><b><i class="icon-plus3"></i></b> New</button>
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" 
                                id="btn-save"><b><i class="icon-floppy-disk"></i></b> Save</button>
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" 
                                id="btn-close"><b><i class="icon-floppy-disk"></i></b> Close</button>
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs"
                                id="btn-update" style="display: none"><b><i class="icon-floppy-disk"></i></b> Update</button>
                    </div> 
                </div>
                {!! Form::close() !!}
            </div>  </div>
    </div>            
</div>



@endsection




@section('javascripy') 


<!-- Select with search -->
<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>


<!-- Theme JS files -->
<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>


<!-- /theme JS files -->


<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="js/grn/grn.js"></script>
<script type="text/javascript" src="js/application.js"></script>
<!-- /Select with search -->
<!-- <script type="text/javascript">
    $(document).ready(function(){
         getAll();
    });
</script> -->
@endsection