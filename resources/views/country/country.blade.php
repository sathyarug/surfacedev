@extends('layout.main')

@section('title') Country @endsection

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

                <h5 class="panel-title">Country</h5>
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
                        <div class="alert alert-danger" style="display: none">
                            error
                        </div>
                        {!! Form::open(array('url' => 'insertCountry','method','post','id'=>'country-form')) !!}
                        <input type="hidden" id="country_id" name="country_id">
                        <div class="col-md-3">
                            <fieldset class="content-group">
                                <label>Country Code <span class="text-danger">*</span> :</label>
                                {!!Form::text('country_code',null,['class'=>'form-control input-xxs','id'=>'country_code','placeholder'=>'Enter Country code','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="content-group">
                                <label>Country Description <span class="text-danger">*</span> :</label>
                                {!!Form::text('country_description',null,['class'=>'form-control input-xxs','id'=>'country_description','placeholder'=>'Enter Country description','data-validation'=>'length','data-validation-length'=>'min1'])!!}
                            <input type="hidden" name="id" value="" id="country_id"/>
                            </fieldset>
                        </div>

                    </div>
                    <div class="col-md-7">
                        <div class="text-right">
                            <button class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn-new"><b><i class="icon-plus3"></i></b> New</button>
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" 
                            id="btn-save"><b><i class="icon-floppy-disk"></i></b> Save</button>
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs"
                                    id="btn-update" style="display: none"><b><i class="icon-floppy-disk"></i></b> Update</button>
                        </div> 
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="highlighted-justified-tab1">
                        <div class="col-md-6">
                            <table class="table display compact" id="tbl">
                                <thead>
                                    <tr>
                                        <th>Country ID</th>
                                        <th>Country Code</th>
                                        <th>Country Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="data">

                                </tbody>
                            </table>
                        </div>




                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab2">
                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab3">
                        DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg whatever.
                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab4">
                        Aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthet.
                    </div>
                </div>      
            </div>  </div>
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
    <script type="text/javascript" src="js/country.js"></script>
   <script type="text/javascript" src="js/application.js"></script>
    <!-- /Select with search -->
    <!-- <script type="text/javascript">
        $(document).ready(function(){
             getAll();
        });
    </script> -->
    @endsection