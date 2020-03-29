
@extends('layout.main')

@section('title') Form Details @endsection
@section('m_icon') class = 'active' @endsection

@section('body')
<!-- Page header -->
<div class="page-header page-header-default ">
    <!-- <div class="page-header-content">
            <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
            </div>

            <div class="heading-elements">
                    <div class="heading-btn-group">
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
                    </div>
            </div>
    </div> -->

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
        <form action="#">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">Sample Form</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">

                    <!-- moodel start -->

                    <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" data-toggle="modal" data-target="#show_source"><b><i class="icon-plus3"></i></b>Show model</button>
                    <div id="show_source" class="modal fade">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <form class="form-horizontal form-validate-jquery" action="#" id="source_form">

                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h5 class="modal-title">Add Main Sourse</h5>
                                    </div>

                                    <div class="modal-body">
                                        {{csrf_field()}}
                                        <input type="hidden" value="0" name="source_hid" id="source_hid" class="form-control input-xxs">
                                        <div class=" col-md-12">

                                            <fieldset class="content-group">

                                                <div class="form-group">

                                                    <label>Main Source Code <span class="text-danger">*</span> :</label>

                                                    <input type="text" name="source_code" id="source-code" class="form-control input-xxs" >

                                                </div>

                                                <div class="form-group">

                                                    <label>Main Source Name <span class="text-danger">*</span> :</label>

                                                    <input type="text" name="source_name" id="source-name" class="form-control input-xxs" >
                                                </div>
                                            </fieldset>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cancel</button>                               
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-success btn-xs" data-dismiss="modal"><b><i class="icon-floppy-disk"></i></b> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- moodel END -->
                    
                    
                    
                    
                    <div class="row">



                        <legend class="text-bold">Validation states</legend>

                        <div class="form-group has-warning has-feedback col-md-4">
                            <label class="control-label text-semibold">Text Box:</label>
                            <input type="text" class="form-control" placeholder=".has-warning">
                            <div class="form-control-feedback">
                                <i class="icon-notification2"></i>
                            </div>
                            <span class="help-block">Warning input helper</span>
                        </div>

                        <div class="form-group has-success has-feedback col-md-4">
                            <label class="control-label text-semibold">Success with icon:</label>
                            <input type="text" class="form-control" placeholder=".has-warning">
                            <div class="form-control-feedback">
                                <i class="icon-notification2"></i>
                            </div>
                            <span class="help-block">Success input helper</span>
                        </div>

                        <div class="form-group has-error has-feedback col-md-4">
                            <label class="control-label text-semibold">Error with icon:</label>
                            <input type="text" class="form-control" placeholder=".has-warning">
                            <div class="form-control-feedback">
                                <i class="icon-notification2"></i>
                            </div>
                            <span class="help-block">Error input helper</span>
                        </div>


                        <legend class="text-bold">Sample Field</legend>

                        <div class="form-group col-md-4">
                            <label>Text Box:</label>
                            <input type="text" class="form-control input-xxs" placeholder="Eugene Kopyov">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Select with search:</label>
                            <select class="select-search">

                                <option value="AZ">Arizona</option>
                                <option value="CO">Colorado</option>
                                <option value="ID">Idaho</option>
                                <optgroup label="Eastern Time Zone">
                                    <option value="CT">Connecticut</option>
                                    <option value="FL">Florida</option>

                                </optgroup>
                            </select>
                        </div>


                        <div class="content-group-lg col-md-4">

                            <label>Accessibility Date:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-calendar5"></i></span>
                                <input type="text" class="form-control pickadate-accessibility" placeholder="Try me&hellip;">
                            </div>
                        </div>




                        <legend class="text-bold">Buttons</legend>


                        <div class="form-group col-md-12">
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class="icon-plus3"></i></b> New</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-pencil"></i></b> Edit</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-floppy-disk"></i></b> Save</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-warning btn-xs"><b><i class="icon-checkmark"></i></b> Confirm</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-info btn-xs"><b><i class="icon-printer2"></i></b> Print</button>


                            
                           

                        </div>

                        <div class="form-group col-md-12">

                             <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class=" icon-arrow-left16"></i></b> Left</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class=" icon-arrow-right16"></i></b> Right</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class=" icon-arrow-up16"></i></b> Up</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class="icon-arrow-down16"></i></b> Down</button>

                        </div>


                        <div class="form-group col-md-12">

                            <div class="media-left media-middle">
                            <a href="#" class="btn bg-teal-400 btn-rounded btn-icon btn-primary btn-xs">
                            <span class="icon-arrow-left16"></span>
                            </a>
                            </div>

                            <div class="media-left media-middle">
                            <a href="#" class="btn bg-teal-400 btn-rounded btn-icon btn-primary btn-xs">
                            <span class="icon-arrow-right16"></span>
                            </a>
                            </div>

                            <div class="media-left media-middle">
                            <a href="#" class="btn bg-teal-400 btn-rounded btn-icon btn-primary btn-xs">
                            <span class="icon-arrow-up16"></span>
                            </a>
                            </div>

                            <div class="media-left media-middle">
                            <a href="#" class="btn bg-teal-400 btn-rounded btn-icon btn-primary btn-xs">
                            <span class="icon-arrow-down16"></span>
                            </a>
                            </div>


                        </div>

                        <div class="form-group col-md-12">
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class="icon-copy"></i></b> Copy To</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs"><b><i class="icon-cross"></i></b> Cancel</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs"><b><i class="icon-bin"></i></b> Delete</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-warning btn-xs"><b><i class="icon-mail-read"></i></b> Send Mail</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-warning btn-xs"><b><i class="icon-share2"></i></b> Sent to Approval</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-hammer2"></i></b> Approve</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs"><b><i class="icon-blocked"></i></b> Reject</button>
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class="icon-clipboard2"></i></b> Report</button>

                        </div>





                        <div class="col-md-4">
                            <legend class="text-bold">Highlighted tabs</legend>

                            <div class="panel-body">
                                <div class="tabbable">
                                    <ul class="nav nav-tabs nav-tabs-highlight">
                                        <li class="active"><a href="#highlight-tab1" data-toggle="tab">Active</a></li>
                                        <li><a href="#highlighted-tab2" data-toggle="tab">Inactive</a></li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="#highlighted-tab3" data-toggle="tab">Dropdown tab</a></li>
                                                <li><a href="#highlighted-tab4" data-toggle="tab">Another tab</a></li>
                                            </ul>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="highlighted-tab1">
                                            Highlight top border of the active tab by adding <code>.nav-tabs-highlight</code> class.
                                        </div>

                                        <div class="tab-pane" id="highlighted-tab2">
                                            Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid laeggin.
                                        </div>

                                        <div class="tab-pane" id="highlighted-tab3">
                                            DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg whatever.
                                        </div>

                                        <div class="tab-pane" id="highlighted-tab4">
                                            Aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthet.
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <legend class="text-bold">Content loading spinner 6</legend>



                            <button type="button" class="btn btn-default" id="spinner-light-6"><i class="icon-spinner9 spinner position-left"></i> Light overlay</button>
                            <button type="button" class="btn bg-teal-400" id="spinner-dark-6"><i class="icon-spinner9 spinner position-left"></i> Dark overlay</button>

                        </div>



                        <div class="col-md-4">

                            <legend class="text-bold">Accordion Item</legend>

                            <div class="panel-group panel-group-control panel-group-control-right content-group-lg" id="accordion-control-right">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group1">Accordion Item #1</a>
                                        </h6>
                                    </div>
                                    <div id="accordion-control-right-group1" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch.
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group2">Accordion Item #2</a>
                                        </h6>
                                    </div>
                                    <div id="accordion-control-right-group2" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            Тon cupidatat skateboard dolor brunch. Тesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda.
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h6 class="panel-title">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group3">Accordion Item #3</a>
                                        </h6>
                                    </div>
                                    <div id="accordion-control-right-group3" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /accordion with right control button -->

                        </div>


















                    </div>	



                </div>
            </div>
        </form>
        <!-- /basic layout -->





    </div>






</div>
<!-- /latest posts -->


@endsection




@section('javascripy') 

<!-- Select with search -->
<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/pages/form_select2.js"></script>
<!-- /Select with search -->

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

<!-- Content loading -->
<script type="text/javascript" src="assets/js/plugins/loaders/progressbar.min.js"></script>
<script type="text/javascript" src="assets/js/pages/components_loaders.js"></script>
<!-- /Content loading -->


@endsection
