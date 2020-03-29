
@extends('layout.main')

@section('title') Stores Configuration @endsection
@section('load_css')

@endsection

@section('add_stores') class = 'active' @endsection

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
                    <h6 class="panel-title">Stores Configuration</h6>

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
                        <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                            <li class="active"><a href="#highlighted-justified-tab1" data-toggle="tab">Stores</a></li>
                            <li><a href="#highlighted-justified-tab2" data-toggle="tab">Stores Location</a></li>
                            <li><a href="#highlighted-justified-tab3" data-toggle="tab">Bin</a></li>

                        </ul>

                        <div class="tab-content">
                           <div class="tab-pane active" id="highlighted-justified-tab1">

                                @include('add_stores.main_stores')

                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab2">

                                @include('add_stores.stores_location')

                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab3">

                                @include('add_stores.stores_bins')

                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab4">
                                #
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

<script type="text/javascript" src="js/add_stores_js/add_stores_js.js"></script>
<script type="text/javascript" src="js/application.js"></script>

<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>

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
<script type="text/javascript">
    $('.select-search').select2();
</script>


@endsection
