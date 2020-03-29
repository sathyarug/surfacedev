@extends('layout.main')

@section('title')Customer Master @endsection
@section('m_customer') class='active' @endsection

@section('body')
<!-- Page Header -->
<div class="page-header page-header-default">
    
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
                    <li><a href="#"><i class="icon-gear"></i> All settings </a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /Page Header -->

<!-- Content Area -->
<div class="content">
    <div class="col-md-1">&nbsp;</div>
    <div class="col-md-10">
        <form action="#">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">CUSTOMER MASTER</h5>
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
                        <div class="col-md-9">
                            <div class="col-md-12" style="background:#F5F5F5;padding: 10px;border-radius:10px; ">
                                <div class="col-md-12">
                                    &nbsp;<label>Search</label>                                
                                </div>
                                <div class="col-md-10">
                                    <select class="select-search input-xs" >
                                        <option value="">Select One ...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-3">&nbsp;Customer Code</div>
                                <div class="col-md-1">&nbsp;</div>
                                <div class="col-md-8">&nbsp;Customer Name</div>
                                <div class="col-md-3">
                                    {{Form::text('Customer_Code',"",array('class'=>'form-control'))}}                          
                                </div>
                                <div class="col-md-1">&nbsp;</div>
                                <div class="col-md-6">
                                    {{Form::text('Customer_Name',"",array('class'=>'form-control'))}}
                                </div>
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-3">&nbsp;Document Address</div>
                                <div class="col-md-1">&nbsp;</div>
                                <div class="col-md-8">&nbsp;Delivery Address</div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <!-- <input type="text" class="form-control" readonly="readonly" placeholder=""/> -->
                                        {{Form::text('doc_address',"",array('class'=>'form-control', 'readonly'=>'true'))}}
                                        <span class="input-group-btn">
                                            <!-- <button class="btn btn-default" type="button">...</button>-->
                                            {{Form::button('...',array('class'=>'btn btn-default'))}}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-1">&nbsp;</div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <!-- <input type="text" class="form-control" readonly="readonly" placeholder=""/> -->
                                        {{Form::text('delivery_address',"",array('class'=>'form-control','readonly'=>'true'))}}
                                        <span class="input-group-btn">
                                            {{Form::button('...',array('class'=>'btn btn-default','data-toggle'=>'modal', 'data-target'=>'#frmDeliveryAddress'))}}
                                        </span>
                                    </div>
                                </div>
                                <!--<div class="col-md-3">&nbsp;{{Form::text('doc_address',"",array('class'=>'form-control'))}}</div>
                                <div class="col-md-1">&nbsp;</div>
                                <div class="col-md-6">&nbsp;{{Form::text('del_address',"",array('class'=>'form-control'))}}</div>
                                <div class="col-md-2">&nbsp;</div> -->
                                <!-- Test 1 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>        
    </div>
    <div class="col-md-1">&nbsp;</div>
</div>
<!-- /Content Area -->

<!-- Modal -->
<div class="modal fade" id="frmDeliveryAddress" tabindex="-1" role="dialog" aria-labelledby="deliveryAddressLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delivery Address</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-md-1 table">#</div>
              <div class="col-md-4">Address</div>
              <div class="col-md-3">Street</div>
              <div class="col-md-2">City</div>
              <div class="col-md-2">Country</div>
              <div class="col-md-12">&nbsp;</div>
              <div class="col-md-1">1</div>
              <div class="col-md-4">{{Form::text('deliaddress1',"",array('class'=>'form-control'))}}</div>
              <div class="col-md-3">Street</div>
              <div class="col-md-2">City</div>
              <div class="col-md-2">Country</div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


@endsection
@section('javascripy') 


<!-- Select with search -->
<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/pages/form_select2.js"></script>

<!-- Theme JS files -->
<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>

<script type="text/javascript" src="assets/js/pages/datatables_basic.js"></script>
<!-- /theme JS files -->
<!-- ###Select with search -->


<!-- Test Merge 1 -->
<!-- Test Merge 2 -->
<!-- Test Merge 3 -->
<!-- Test Merge 4 -->

@endsection