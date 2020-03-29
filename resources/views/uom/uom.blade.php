
@extends('layout.main')

@section('title') UOM Details @endsection
@section('load_css')

@endsection

@section('m_add_uom') class = 'active' @endsection

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
</div>
<!-- /page header -->


<!-- Content area -->
<div class="content">


    <div class="col-md-12">

        <!-- Basic layout-->

        <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Add Unit of Measure</h6>

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

                                    <div class="text-right">
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
                                                id="add_data"><b><i class="icon-plus3"></i></b>Add New</button>
                                    </div> 


                                    <table class="table datatable-basic" id="uom_tbl">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Action</th>
                                                <th>Unit Code</th>
                                                <th>Description</th>
                                                <th>Factor</th>
                                                <th>Base Unit</th>
                                                <th>Unit Type</th>
                                                <th>Status</th>  
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>


                                    </table>


                                </div>

                                <!-- popup -->
                                <div id="show_uom" class="modal fade">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <form class="form-horizontal form-validate-jquery" action="#" id="uom_form">

                                                <div class="modal-header bg-teal-300">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Add Unit Of Measure</h5>
                                                </div>

                                                <div class="modal-body">
                                                    {{csrf_field()}}
                                                    <input type="hidden" value="0" name="uom_hid" id="uom_hid" class="form-control input-xxs">
                                                    <div class=" col-source_hidmd-12">

                                                        <fieldset class="content-group">
                                                            <div class="form-group">
                                                                <label>Unit Code <span class="text-danger">*</span> :</label>
                                                                <input type="text" name="uom_code" id="uom_code" class="form-control input-xxs" >
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Description<span class="text-danger">*</span> :</label>
                                                                <input type="text" name="uom_description" id="uom_description" class="form-control input-xxs" >
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Factor<span class="text-danger">*</span> :</label>
                                                                <input type="text" name="uom_factor" id="uom_factor" class="form-control input-xxs" >
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Base Unit<span class="text-danger">*</span> :</label>
                                                                <input type="text" name="uom_base_unit" id="uom_base_unit" class="form-control input-xxs" >
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Unit Type<span class="text-danger">*</span> :</label>
                                                                <select class="select-search" id="unit_type" name="unit_type">
                                                                    <option value="Not Used">Not Used</option>
                                                                    <option value="Weight">Weight</option>
                                                                    <option value="Volume">Volume</option>
                                                                    <option value="Length">Length</option>
                                                                    <option value="Temperature">Temperature</option>
                                                                    <option value="Discrete">Discrete</option>
                                                                    <option value="Density">Density</option>
                                                                </select>
                                                            </div>
                                                        </fieldset>

                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id="">
                                                        <b><i class="icon-cross"></i></b>Close</button> 

                                                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save">
                                                        <b><i class="icon-floppy-disk"></i></b> Save</button>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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

<script type="text/javascript" src="js/uom/uom.js"></script>
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
