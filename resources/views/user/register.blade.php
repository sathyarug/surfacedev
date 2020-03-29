
@extends('layout.main')

@section('title') Register User @endsection
@section('m_icon') class = 'active' @endsection

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

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">User Profile</h5>
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
                            <li class="active"><a href="#highlighted-justified-tab1" data-toggle="tab">User Information</a></li>
                            <li><a href="#highlighted-justified-tab3" data-toggle="tab">User Permation</a></li>

                        </ul>



                        <div class="tab-content">
                            <div class="tab-pane active" id="highlighted-justified-tab1">

                                <form action="{{ url('register-user') }}" method="POST" id="signup-form">

                                <div class="col-md-12">
                                    <div class="panel-body">
                                        <div class="row">
                                            <legend class="text-bold">Personal Details</legend>

                                            <div class="form-group  col-md-4">
                                                {{ Form::label('first_name', 'First Name', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::text('first_name','', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('last_name', 'Last Name', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::text('last_name','', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                <div>&nbsp;</div>
                                            </div>
                                            <br clear="all">

                                            <div class="form-group col-md-4">
                                                {{ Form::label('date_of_birth', 'Date of Birth', array('class' => 'control-label text-semibold pickadate-year')) }}
                                                {{ Form::text('date_of_birth','', array('class' => 'form-control input-xxs pickadate'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('nic_no', 'NIC No', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::text('nic_no','', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('gender', 'Gender', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::select('gender', array('M' => 'Male', 'F' => 'Female'), null, array('class' => 'form-control input-xxs select'))  }}
                                            </div>

                                            <br clear="all">
                                            <div class="form-group col-md-4">
                                                {{ Form::label('civil_status', 'Civil Status', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::select('civil_status', array('MARRIED' => 'Married', 'UNMARRIED' => 'Un Married'), null, array('class' => 'form-control input-xxs select'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('mobile_no', 'Mobile Number', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::text('mobile_no','', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('extension_no', 'Extention Number', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::text('extension_no','', array('class' => 'form-control input-xxs'))  }}
                                            </div>
                                            <br clear="all">


                                            <legend class="text-bold">Official Details</legend>


                                            <div class="form-group col-md-4">
                                                {{ Form::label('email', 'Email', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::text('email', '', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('emp_number', 'Employee Number', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::text('emp_number', '', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('joined_date', 'Date of Joined', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::text('joined_date','', array('class' => 'form-control input-xxs pickadate'))  }}
                                            </div>

                                            <br clear="all">
                                            <div class="form-group col-md-4">

                                                {{ Form::label('loc_id', 'Location', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
<<<<<<< HEAD
                                                {{ Form::select('loc_id',array('' => 'Please Select') + $data['location'], null,  array('class' => 'form-control input-xxs select'))  }}
=======
                                                {{ Form::select('loc_id',array('' => '[Select]') + $data['location'], null,  array('class' => 'form-control input-xxs select'))  }}
>>>>>>> origin/master
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('dept_id', 'Department', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
<<<<<<< HEAD
                                                {{ Form::select('dept_id', array('' => 'Please Select') + $data['dept'], null, array('class' => 'form-control input-xxs select'))  }}
=======
                                                {{ Form::select('dept_id', array('' => '[Select]') + $data['dept'], null, array('class' => 'form-control input-xxs select'))  }}
>>>>>>> origin/master
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('cost_center_id', 'Cost Center', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
<<<<<<< HEAD
                                                {{ Form::select('cost_center_id', array('' => 'Please Select') + $data['costCtr'], null, array('class' => 'form-control input-xxs select'))  }}
=======
                                                {{ Form::select('cost_center_id', array('' => '[Select]') + $data['costCtr'], null, array('class' => 'form-control input-xxs select'))  }}
>>>>>>> origin/master
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('desig_id', 'Designation', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
<<<<<<< HEAD
                                                {{ Form::select('desig_id', array('' => 'Please Select') + $data['desg'], null, array('class' => 'form-control input-xxs select'))  }}
=======
                                                {{ Form::select('desig_id', array('' => '[Select]') + $data['desg'], null, array('class' => 'form-control input-xxs select'))  }}
>>>>>>> origin/master
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('resign_date', 'Resign Date', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::text('resign_date','', array('class' => 'form-control input-xxs pickadate'))  }}
                                            </div>
                                            <br clear="all">


                                            <legend class="text-bold">Report Levels</legend>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('immediate', 'Immediate Report', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::select('immediate', array('' => '[Search and Select]'), null, array('class' => 'form-control input-xxs select'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('alternative', 'Alternative Report', array('class' => 'control-label text-semibold')) }}
                                                {{ Form::select('alternative', array('' => '[Search and Select]'), null, array('class' => 'form-control input-xxs select'))  }}
                                            </div>
                                            <br clear="all">

                                            <legend class="text-bold">User Login</legend>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('user_name', 'User Name', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::text('user_name','', array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('password', 'Password', array('class' => 'control-label text-semibold')) }}
                                                <span class="text-danger">*</span>
                                                {{ Form::password('password',  array('class' => 'form-control input-xxs'))  }}
                                            </div>

                                            <br clear="all">

                                            <div class="text-right">
                                                <button type="reset" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"><b><i class="icon-plus3"></i></b> New</button>
                                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-floppy-disk"></i></b> Save</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                    {{ csrf_field() }}
                                </form>
                            <!-- /basic layout -->

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
                    </div>
                </div>
                <!-- Basic layout-->


            </div>

        </div>




    </div>
    <!-- /latest posts -->


@endsection




@section('javascripy')

    <!-- Select with search -->
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/pages/form_select2.js') }}"></script>


    <!-- picker_date -->
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/pickadate/legacy.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/pages/picker_date.js') }}"></script>

    <!-- /picker_date -->

    <!-- Content loading -->
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/loaders/progressbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/pages/components_loaders.js') }}"></script>
    <!-- /Content loading -->

<<<<<<< HEAD
    <script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
    <script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
    <script type="text/javascript" src="assets/js/pages/picker_date.js"></script>
    <script type="text/javascript" src="js/application.js"></script>
    <script type="text/javascript" src="js/admin/user.js"></script>


    <script>

        $(function() {
            /*$('.pickadate-accessibility').pickadate({
                // Escape any “rule” characters with an exclamation mark (!).
                format: 'yyyy/mm/dd',
                formatSubmit: 'yyyy/mm/dd',
                hiddenPrefix: 'prefix__',
                hiddenSuffix: '__suffix'
            })*/

           /* $('#date_of_birth').pickadate({
                format: 'yyyy/mm/dd',
                formatSubmit: 'yyyy/mm/dd',
                hiddenPrefix: 'prefix__',
                hiddenSuffix: '__suffix',
                picker.set('min', true)

        });*/
        });


    </script>
=======
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/application.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/admin/user.js') }}"></script>
>>>>>>> origin/master

@endsection



