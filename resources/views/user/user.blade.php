
@extends('layout.main')
@section('title') User @endsection
@section('supplier') class = 'active' @endsection
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
                        <h6 class="panel-title">Users</h6>

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
                                            <a href="register" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class="icon-plus3"></i></b>Add New</a>
                                        </div>


                                        <table class="table datatable-basic" id="user-tbl">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Action</th>
                                                    <th>First Name</th>
                                                    <th>Last Name</th>
                                                    <th>Employee Number</th>
                                                    <th>Email</th>
                                                    <th>Location</th>
                                                    <th>Department</th>
                                                    <th>Designation</th>


                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>

                                        </table>

                                    </div>
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

    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>


    <script type="text/javascript" src="{{ URL::asset('js/admin/user.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/application.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/pages/datatables_basic.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>


@endsection
