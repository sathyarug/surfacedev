@extends('layout.main')

@section('title') Costing @endsection

@section('load_css') <link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/theme-default.min.css"
    rel="stylesheet" type="text/css" /> @endsection


@section('m_currency') class = 'active' @endsection

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
               
                <h5 class="panel-title">Currency</h5>
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
                        <form class="form-horizontal form-validate-jquery" action="#" id="currency-form">
                            <input type="hidden" name="currency_id" id="cur-id" value="0">
                            <div class="col-md-3">
                            <fieldset class="content-group">
                                <label>Currency Code <span class="text-danger">*</span> :</label>
                                <input type="text" name="currency_code" id="cur-code" class="form-control input-xxs" placeholder="Enter currency code" 
                                       data-validation="length" data-validation-length="min1">
                            </fieldset>
                            </div>
                            <div class="col-md-4">
                            <fieldset class="content-group">
                                <label>Currency Description <span class="text-danger">*</span> :</label>
                                <input type="text" name="currency_description" id="cur-description" class="form-control input-xxs" placeholder="Enter currency description" 
                                       data-validation="length" data-validation-length="min2">
                            </fieldset>
                            </div>
                            
                        
                    </div>
                    <div class="col-md-7">
                        <div class="text-right">
                            <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn-new"><b><i class="icon-plus3"></i></b> New</button>
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save"><b><i class="icon-floppy-disk"></i></b> Save</button>
                        </div> 
                    </div>
                    </form>
                </div>
                
                
                
                
                
                
                  <div class="tab-content">
                            <div class="tab-pane active" id="highlighted-justified-tab1">

                                <!-- <div class=" col-md-12">
                                        <fieldset class="content-group">
                                                <div class=" col-md-3">

                                                        <label>Main Sourse Name * :</label>
                                                        <input type="text" class="form-control input-xxs" >
                                                </div>
                                        </fieldset>
                                </div>-->


                                <div class="col-md-7">


                                 



                                    <table class="table display compact" id="tbl">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Action</th>
                                                <th>Currency Code</th>                                                
                                                <th>Currency Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                      
                                           


                                        </tbody></table>





                                </div>




                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab2">
                                <!-- <div class=" col-md-12">
                                        <fieldset class="content-group">

                                                <div class=" col-md-3">

                                                        <label>Select Main Sourse *:</label>
                                                        <select class="select-search input-xxs" >

                                                                <option value="">Select One ...</option>

                                                        </select>
                                                </div>

                                                <div class=" col-md-3">

                                                        <label>Cluster Name * :</label>
                                                        <input type="text" class="form-control input-xxs" >
                                                </div>
                                        </fieldset>
                                </div>


                                <div class="text-right">
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-floppy-disk"></i></b> Save</button>
                                </div> -->
                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab3">
                                DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg whatever.
                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab4">
                                Aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthet.
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
        <script type="text/javascript" src="js/currency/currency.js"></script>
        <script type="text/javascript" src="js/application.js"></script>
					<!-- /Select with search -->

					@endsection