@extends('layout.main')

@section('title') Item Property Assign @endsection

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

<div class="container">
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Items Property Assign</h6>

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
                                {{csrf_field()}}
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-8">
                                    <div class="col-md-2"><label>Main Category <span class="text-danger">*</span> :</label></div>
                                    <div class="col-md-3"><select name="category_code" id="category_code" class="select-search input-xxs">
                                    <option value="-1">..................</option>
                                    <?php foreach ($categories as $category) {
                                            echo '<option value="'.$category['category_id'].'">'.$category['category_name'].'</option>';
                                    } ?>
                                </select></div>
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-2"><label>Sub Category <span class="text-danger">*</span> :</label></div>
                                    <div class="col-md-3">{{Form::select('subcategory',["-1"=>".................."],null,['id'=>'subcategory','class'=>'select-search input-xs', 'required'=>'required'])}}</div>
                                   
                                </div>
                                <div class="col-md-2">&nbsp;</div>                               
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-12">&nbsp;</div> 
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-8">
                                    <form class="form-horizontal form-validate-jquery" action="#" id="itemproperty_assign_form">
                                        <div class="row">
                                           <div class="col-md-5">
                                               <select name="from[]" id="multiselect1" class="form-control lisbox" size="8" multiple="multiple">                                           
                                               </select>
                                           </div>                                    
                                           <div class="col-md-2">
                                               <div class="row">
                                                   <div class="col-md-12">&nbsp;</div>
                                                   <div class="col-md-12">&nbsp;</div><div class="col-md-12">&nbsp;</div><div class="col-md-12">&nbsp;</div>
                                                   <div class="col-md-12 col-md-offset-2">
                                                      <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="multiselect1_rightSelected"><b><i class=" icon-arrow-right16"></i></b> Right</button>
                                                   </div>
                                                   <div class="col-md-12">&nbsp;</div>
                                                   <div class="col-md-12 col-md-offset-2">
                                                       <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="multiselect1_leftSelected" style="width: 70px;"><b><i class=" icon-arrow-left16"></i></b> Left </button>
                                                   </div>                                                 
                                               </div>
                                           </div>
                                           <div class="col-md-5">
                                               <select name="to[]" id="multiselect1_to" class="form-control lisbox" size="8" multiple="multiple"></select>
                                           </div> 
                                        </div>
                                    </form>        
                                </div>
                                <div class="col-md-2">
                                    <div class="col-md-12">&nbsp;</div>
                                    <div class="col-md-12">&nbsp;</div>
                                    <div class="col-md-12">&nbsp;</div>
                                    <div class="col-md-12"><button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="multiselect_up" style="width: 72px;"><b><i class=" icon-arrow-up16"></i></b> Up</button></div>
                                    <div class="col-md-12">&nbsp;</div>
                                    <div class="col-md-12"><button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="multiselect_down"><b><i class="icon-arrow-down16"></i></b> Down</button></div>
                                </div>  
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-8">
                                     <div class="text-right col-md-5"><button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="add_data"><b><i class="icon-plus3"></i></b>Add New</button></div>
                                     <div class="col-md-2">&nbsp;</div>
                                     <div class="text-right col-md-5"><button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-property-assign"><b><i class="icon-floppy-disk"></i></b> Save</button></div>
                                </div>
                                <div class="col-md-2">&nbsp;</div>
                            </div>
                            
                            <!-- Pop Up -->
                            <div id="show_itemproperty" class="modal fade">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <form class="form-horizontal form-validate-jquery" action="#" id="itemproperty_form">

                                                <div class="modal-header bg-teal-300">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Add Custome Sizes</h5>
                                                </div>

                                                <div class="modal-body">
                                                    {{csrf_field()}}                                                   
                                                    <div class=" col-source_hidmd-12">

                                                        <fieldset class="content-group">                                                            
                                                            <div class="form-group">
                                                                <label>Property Name<span class="text-danger">*</span> :</label>
                                                                {{Form::text('propertyname',null,['id'=>'propertyname','class'=>'form-control input-xs', 'required'=>'required'])}}
                                                            </div>
                                                        </fieldset>

                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id="">
                                                        <b><i class="icon-cross"></i></b>Close</button> 

                                                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-property">
                                                        <b><i class="icon-floppy-disk"></i></b> Save</button>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Pop Up -->
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
        
    
</div>
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
<script type="text/javascript" src="assets/js/plugins/multiselect/multiselect.js"></script>



<script type="text/javascript" src="js/application.js"></script>
<script type="text/javascript" src="js/itemproperty/itemproperty.js"></script>
<script type="text/javascript" src="js/finance/item/sub_category.js"></script>


<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/pages/form_select2.js"></script>
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
@endsection
