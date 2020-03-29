@extends('layout.main')

@section('title') Costing @endsection

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
               
                <h5 class="panel-title">Payment Term</h5>
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
                        <form class="form-horizontal form-validate-jquery" action="#" id="payment_form">
                            <input type="hidden" name="payment_id" id="payment_id" value="0">
                            <div class="col-md-3">
                            <fieldset class="content-group">
                                <label>Payment Code <span class="text-danger">*</span> :</label>
                                <input type="text" name="payment_code" id="payment_code" class="form-control input-xxs" placeholder="Enter payment code" >
                            </fieldset>
                            </div>
                            <div class="col-md-4">
                            <fieldset class="content-group">
                                <label>Payment Description <span class="text-danger">*</span> :</label>
                                <input type="text" name="payment_description" id="payment_description" class="form-control input-xxs" placeholder="Enter payment description" >
                            </fieldset>
                            </div>
                            
                        
                    </div>
                    <div class="col-md-7">
                        <div class="text-right">
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn_new"><b><i class="icon-plus3"></i></b> New</button>
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn_save"><b><i class="icon-floppy-disk"></i></b> Save</button>
                        </div> 
                    </div>
                    </form>
                </div>
                
                
                
                
                
                
                <div class="tab-content">
                    <div class="tab-pane active" id="highlighted-justified-tab1"> 
                        <div class="col-md-7">
                            <table class="table display compact" id="tbl_payment_term">
                                <thead>
                                    <tr>
                                        <th class="text-center">Action</th>
                                        <th>Payment Code</th>                                                
                                        <th>Payment Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
        <script type="text/javascript" src="js/payment_term/payment_term.js"></script>
        <script type="text/javascript" src="js/application.js"></script>
					<!-- /Select with search -->

					@endsection