
@extends('layout.main')
@section('title') Supplier @endsection
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
                    <h6 class="panel-title">Supplier</h6>

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
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" onclick="addEditSupplier(0)"><b><i class="icon-plus3"></i></b>Add New</button>
                                        {{--<button type="button"  class="btn bg-teal-400 btn-labeled btn-primary btn-xs"--}}
                                                {{--id="add_data"><b><i class="icon-plus3"></i></b>Add New</button>--}}
                                    </div>


                                    <table class="table datatable-basic" id="supplier_tbl">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Action</th>
                                            <th>Supplier Name</th>
                                            <th>Supplier Code</th>
                                            <th>Supplier City</th>
                                            <th>Supplier Phone</th>
                                            <th>Supplier Email</th>
                                            <th>Status</th>

                                            <!-- <th class="text-center">Actions</th> -->
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>


                                    </table>


                                </div>

                                <!-- popup -->
                                <div id="show_supplier" class="modal fade">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
<<<<<<< HEAD

=======
                                            <form class="form-horizontal form-validate-jquery" action="#" id="frm_supplier">
                                                <input type="hidden" value="0" name="supplier_hid" id="supplier_hid" class="form-control input-xxs">

                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">New Supplier</h5>
                                                </div>

                                                <div class="modal-body">
                                                    {{csrf_field()}}
                                                    <fieldset class="content-group">

                                                        <div class=" col-md-12">
                                                        <div class=" col-md-4">

                                                            <label>Supplier Code<span class="text-danger">*</span>:</label>
                                                            {{ Form::text('supplier_code', null, ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="supplier-code" id="Supplier_code">--}}

                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Supplier Name <span class="text-danger">*</span>:</label>
                                                            {{ Form::text('supplier_name', null, ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="supplier-name" id="supplier_name">--}}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Supplier Country <span class="text-danger">*</span>:</label>
                                                            {{ Form::select('supplier_country_id',$loc, null, ['class' => 'form-control input-xxs', 'required' => 'required'])  }}
                                                            {{--<select class="select-search input-xxs" name="supplier-country" id="def_curr">--}}
                                                                {{--<option value="">Select One ...</option>--}}
                                                            {{--</select>--}}
                                                            {{--<input type="text" class="form-control input-xxs" name="supplier-country" id="supplier_country">--}}
                                                        </div>
                                                        </div>
                                                        <div class=" col-md-12">
                                                        <div class=" col-md-4">
                                                            <label>Supplier City <span class="text-danger">*</span>:</label>
                                                            {{ Form::text('supplier_city', null, ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="supplier-city" id="supplier_city">--}}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Supplier Address 1 <span class="text-danger">*</span>:</label>
                                                            {{--<input type="text" class="form-control input-xxs" name="Supplier-ad1" id="Supplier_ad1">--}}
                                                            {{ Form::text('supplier_address1', null, ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Supplier Address 2 :</label>
                                                            {{ Form::text('supplier_address2', null, ['class' => 'form-control input-xxs']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="Supplier-ad2" id="Supplier_ad2">--}}
                                                        </div>
                                                        </div>
                                                        <div class=" col-md-12">
                                                        <div class=" col-md-4">
                                                            <label>Supplier Phone <span class="text-danger">*</span>:</label>
                                                            {{ Form::text('supplier_phone', null, ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="company-city" id="supplier_phone">--}}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Supplier Fax :</label>
                                                            {{ Form::text('supplier_fax', null, ['class' => 'form-control input-xxs']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="supplier-fax" id="supplier_fax">--}}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Supplier Email <span class="text-danger">*</span>:</label>
                                                            {{ Form::email('supplier_email', null, ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                                                            {{--<input type="text" class="form-control input-xxs" name="supplier-email" id="supplier_email">--}}
                                                        </div>
                                                        </div>
                                                        <div class=" col-md-12">
                                                        <div class=" col-md-4">
                                                            <label>Payment Mode<span class="text-danger">*</span></label>
                                                            {{ Form::select('payment_method_id',$method, null, ['class' => 'form-control input-xxs', 'required' => 'required'])  }}
                                                            {{--<input type="text" class="form-control input-xxs" name="payment-mode" id="payment_mode">--}}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Payment terms<span class="text-danger">*</span></label>
                                                            {{ Form::select('payment_code',$terms, null, ['class' => 'form-control input-xxs', 'required' => 'required'])  }}
                                                            {{--<input type="text" class="form-control input-xxs" name="payment-code" id="payment_code">--}}
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <label>Default currency code<span class="text-danger">*</span></label>
                                                            {{ Form::select('default_currency_code',$currency, null, ['class' => 'form-control input-xxs', 'required' => 'required'])  }}

                                                        </div>
                                                        </div>


                                                        {{--<div class=" col-md-4">--}}
                                                            {{--<label>Default Currency<span class="text-danger">*</span>:</label>--}}
                                                            {{--<select class="select-search input-xxs" name="def-curr" id="def_curr">--}}
                                                                {{--<option value="">Select One ...</option>--}}
                                                            {{--</select>--}}
                                                        {{--</div>--}}
                                                        {{--<div class=" col-md-4">--}}
                                                            {{--<label>Finance Month<span class="text-danger">*</span>:</label>--}}
                                                            {{--<select class="select-search input-xxs" name="fin-month" id="fin_month">--}}
                                                                {{--<option value="">Select One ...</option>--}}
                                                            {{--</select>--}}
                                                        {{--</div>--}}
                                                        {{--<div class=" col-md-4">--}}
                                                            {{--<label>Vat Registration Number<span class="text-danger">*</span>:</label>--}}
                                                            {{--<input type="text" class="form-control input-xxs" name="vat-regnum" id="vat_regnum">--}}
                                                        {{--</div>--}}
                                                        {{--<div class=" col-md-4">--}}
                                                            {{--<label>Tax Code<span class="text-danger">*</span>:</label>--}}
                                                            {{--<input type="text" class="form-control input-xxs" name="tax-code" id="tax_code">--}}
                                                        {{--</div>--}}


                                                    </fieldset>


                                                </div>

                                                <div class="modal-footer">

                                                    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cancel</button>
                                                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-3">
                                                        <b><i class="icon-floppy-disk"></i></b> Save</button>

                                                </div>
                                            </form>
>>>>>>> origin/master
                                        </div>
                                    </div>
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


    <script type="text/javascript" src="{{ URL::asset('js/supplier/supplier.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/application.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/pages/datatables_basic.js') }}"></script>



<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>


@endsection
