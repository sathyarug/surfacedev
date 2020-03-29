@extends('layout.main')
@section('title') Role @endsection
@section('m_role') class = 'active' @endsection
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
<div class="content">
    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-flat">

                <div class="panel-heading">
                    <h5 class="panel-title">Role</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-right">
                                <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" onclick="addEditRole(0)"><b><i class="icon-plus3"></i></b>Add New</button>
                            </div> 

                            <table class="table datatable-basic" id="role_tbl">
                                <thead>
                                    <tr>
                                        <th class="text-center">Action</th>
                                        <th>Role Name</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<div id="show_role" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <!--<form class="form-horizontal form-validate-jquery" action="#" id="role_form">-->
<<<<<<< HEAD

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Add Role</h5>
            </div>



            <div class="modal-body"> </div>

=======

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Add Role</h5>
            </div>



            <div class="modal-body"> </div>

>>>>>>> origin/master
            <div class="modal-footer">
                <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cancel</button>                               
                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save" ><b><i class="icon-floppy-disk"></i></b> Save</button>
            </div>
            <!--</form>-->
        </div>
    </div>
</div>
@endsection

@section('javascripy') 
<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>

<script type="text/javascript" src="{{ URL::asset('js/admin/role.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/application.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/js/pages/datatables_basic.js') }}"></script>

<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>

@endsection