
@extends('layout.main')

@section('title') Season Details @endsection
@section('load_css')

@endsection

@section('m_add_season') class = 'active' @endsection

@section('body')
<!-- Page header -->
<div class="page-header page-header-default ">
    <style>
        .button_big {
            position: relative;
            background-color: #4CAF50;
            border: none;
            font-size: 12px;
            color: #FFFFFF;
            padding: 6px;
            width: 90px;
            text-align: center;
            -webkit-transition-duration: 0.4s; /* Safari */
            transition-duration: 0.4s;
            text-decoration: none;
            overflow: hidden;
            cursor: pointer;
        }

        .button_big:after {
            content: "";
            background: #f1f1f1;
            display: block;
            position: absolute;
            padding-top: 300%;
            padding-left: 350%;
            margin-left: -20px !important;
            margin-top: -120%;
            opacity: 0;
            transition: all 0.8s
        }

        .button_big:active:after {
            padding: 0;
            margin: 0;
            opacity: 1;
            transition: 0s
        }
    </style>

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
</div>
<!-- /page header -->


<!-- Content area -->
<div class="content">


    <div class="col-md-12">

        <!-- Basic layout-->

        <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">DESIGN SHEET & STYLE BRIEF</h6>

                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">

                    <div class="tabbable">
                        <div class="tab-content">
                            <div class="tab-pane active" id="highlighted-justified-tab1">
                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Customer<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Season<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Upload file<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control file-styled input-xxs" name="company_logo" id="company_logo">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Stage<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Style No<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Upload file name<span class="text-danger"></span></label>
                                            <input type="text" class="form-control input-xxs" name="vat_reg_no" id="vat_regnum">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label><span class="text-danger"></span></label>
                                             <input type="text" class="form-control input-xxs" name="vat_reg_no" id="vat_regnum">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Description<span class="text-danger">*</span></label>
                                             <input type="text" class="form-control input-xxs "  name="vat_reg_no" id="vat_regnum">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Upload File Description<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control input-xxs"  name="vat_reg_no" id="vat_regnum">
                                        </div>
                                    </div>
                                    <div class="row"  style="margin-top: 10px">
                                        <div class="col-md-8">
                                            <label><span class="text-danger"></span></label>
                                            <canvas class="text-danger" id="myCanvas" style="border:1px solid #000000; min-width: 100%;height: auto;">

                                            </canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="col-md-12">
                                            <div class="row" style="margin-top: 10px">
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>BOM<br>
                                                        <br/>
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>Tech Pack<br/>
                                                        <br>
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>Activities<br/>
                                                        <br>
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>
                                                        Construction <br>Breakdown
                                                    </button>
                                                </div>
                                            </div>
                                            </div>
                                             <div class="col-md-12">   
                                            <div class="row" style="margin-top: 10px">
                                                <div class="col-md-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>Patterns<br>
                                                        <br/>
                                                    </button>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>Markers<br/>
                                                        <br>
                                                    </button>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>
                                                        RM Inspection <br>Reports
                                                    </button>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn button_big">
                                                        <i class="fa fa-user fa-5x"></i>
                                                        Revision<br>Table
                                                    </button>
                                                </div>
                                            </div>
                                             </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label></label>
                                                    <div class="p-5 mb-2 bg-brown text-white">Comment Sheet</div>
                                                    <textarea class="form-control" style="border-color: black; "rows="11" id="comment"></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- popup -->

                            </div>
                            <div class="tab-pane" id="highlighted-justified-tab4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- /basic layout -->


    </div>


</div>
<!-- /latest posts -->

<!-- /mini modal -->


@endsection




@section('javascripy') 



<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>


<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript" src="js/season/season.js"></script>
<script type="text/javascript" src="js/application.js"></script>

<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/pages/form_select2.js"></script>
<!-- //Select with search -->

<!-- picker_date -->
<script type="text/javascript" src="assets/js/plugins/notifications/jgrowl.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="assets/js/pages/picker_date.js"></script>
<!-- /picker_date -->

<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>


@endsection
