
@extends('layout.main')

@section('title') Item Creation @endsection
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
                    <h6 class="panel-title">ITEM CREATION</h6>
                    <div class="col-md-12 p-5 bg-blue-800 text-white text-center">ITEM CREATION</div>

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
                                            <label>Main Category<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Sub Category<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>UOM<span class="text-danger">*</span></label>
                                            <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                <option value="">USD</option>
                                                <option value="">Rs</option>
                                                <option value="">Pound</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Item Code<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control input-xxs"  name="vat_reg_no" id="vat_regnum">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Item Description<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control input-xxs"  name="vat_reg_no" id="vat_regnum">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Upload spec sheet<span class="text-danger"></span></label>
                                            <input type="file" class="form-control file-styled input-xxs border-grey" name="company_logo" id="company_logo">
                                        </div>
                                    </div>

                                    <div class="row"  style="margin-top: 10px">
                                        <div class="p-5 bg-grey-600 text-white text-center">Properties</div>
                                        <div class="col-md-12">
                                            <div class="row">
                                            <div class="col-md-4">
                                                <label>Brand of thread<span class="text-danger">*</span></label>
                                                <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                    <option value="">USD</option>
                                                    <option value="">Rs</option>
                                                    <option value="">Pound</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Composition<span class="text-danger">*</span></label>
                                                <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                    <option value="">USD</option>
                                                    <option value="">Rs</option>
                                                    <option value="">Pound</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>TKT<span class="text-danger">*</span></label>
                                                <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                    <option value="">USD</option>
                                                    <option value="">Rs</option>
                                                    <option value="">Pound</option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                <label>Length<span class="text-danger">*</span></label>
                                                <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                    <option value="">USD</option>
                                                    <option value="">Rs</option>
                                                    <option value="">Pound</option>
                                                </select>
                                            </div>
                                                <div class="col-md-4">
                                                <label>Tex<span class="text-danger">*</span></label>
                                                <select class="select-search input-xxs" id="cus_currency" name="cus_currency">
                                                    <option value="">USD</option>
                                                    <option value="">Rs</option>
                                                    <option value="">Pound</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
<!--                                        <canvas class="text-danger" id="myCanvas" style="border:1px solid #000000; min-width: 100%;height: auto;">

                                        </canvas>-->
                                    </div>
                                    <div class="row"  style="margin-top: 10px">
                                        <div class="text-right">

                                        </div>
                                        <div class="p-5 bg-grey-600 text-white text-center">Item Match Screen</div>
                                        <div class="tab-pane active" id="highlighted-justified-tab1">
                                            <div class="col-md-12">

                                                <table class="table datatable-basic" id="sample_tbl">
                                                    <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Description</th>
                                                            <th>Article no</th>
                                                            <th>Date of creation</th>    
                                                            <th>Created by</th>    
                                                            <th>Approved by</th>    
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>aaa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>  
                                                        </tr>
                                                        <tr>
                                                            <td>aaa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>
                                                            <td>aa</td>  
                                                        </tr>
                                                    </tbody>


                                                </table>


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
    <div class="modal-footer">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Close</button>                       
        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-2">
            <b><i class="icon-floppy-disk"></i></b> Save</button>

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
