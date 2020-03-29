@extends('layout.main')

@section('title') Costing @endsection

@section('load_css') <link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/theme-default.min.css"
    rel="stylesheet" type="text/css" /> @endsection


@section('m_orgtype') class = 'active' @endsection

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

                <h5 class="panel-title">Origin Type</h5>
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
			<button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn_new_origin_type"><b><i class="icon-plus3"></i></b> New</button>
		</div>
	</div>
</dev>

<div class="row">
	<div class="col-md-12">
		<table class="table display compact" id="tbl_origin_type">
			<thead>
				<tr>
					<th class="text-center">Action</th>
					<th>Origin Type</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			</table>
	</div>
</div>

<div id="model_origin_type" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="origin_type_form">


                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Origin Type</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}
					<input type="hidden" name="origin_type_id" id="origin_type_id" value="0">

					<fieldset class="content-group">
						<label>Origin Type <span class="text-danger">*</span> :</label>
						<input type="text" name="origin_type" id="origin_type" class="form-control input-xxs" placeholder="Enter cost center code">
					</fieldset>

				</div>

        <div class="modal-footer">
            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id=""><b><i class="icon-cross"></i></b>Close</button>
            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn_save_origin_type">
                <b><i class="icon-floppy-disk"></i></b> Save</button>

            </div>
        </form>
    </div>
</div>
</div>
</div>

</div>
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
	<script type="text/javascript" src="js/org/origin_type/origin_type.js"></script>
	<script type="text/javascript" src="js/application.js"></script>
		<!-- /Select with search -->

		@endsection
