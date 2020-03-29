<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
        id="add_company"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 



    <table class="table datatable-basic" id="location_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
<<<<<<< HEAD
                
=======
                <th>Status</th>
<<<<<<< HEAD
                <th>Section</th>
>>>>>>> origin/master
=======
>>>>>>> origin/master
                <th>Cluster Code</th> 
                <th>Company Code</th>
                <th>Company Name</th> 
                <th>Address 01</th> 
                <th>Address 02</th>
                <th>City</th>
                <th>Country</th>
                <th>Com Reg Num</th>
                <th>Cotact 01</th>
                <th>Cotact 02</th>
                <th>Fax</th>
                <th>Email</th>
                <th>Web</th>
                <th>Remarks</th>  
                <th>Def.Currency</th>
                <th>Finance Month</th>
                <th>Vat Reg Num</th>
                <th>Tax Code</th>    
                <th>Company Logo</th>                                        
<<<<<<< HEAD
                <th>Status</th>
=======
                
>>>>>>> origin/master
                <!-- <th class="text-center">Actions</th> -->
            </tr>
        </thead>


        <tbody>

        </tbody>


    </table>

</div>

<!-- popup -->

<div id="show_location" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="location_form">


                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Main Company</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}
                    <input type="hidden" value="0" name="location_hid" id="location_hid" class="form-control input-xxs">
                    <fieldset class="content-group">

                        <div class=" col-md-12">
<<<<<<< HEAD
                         <div class=" col-md-4">
=======
                           <div class=" col-md-4">
>>>>>>> origin/master

                            <label>Select Main Cluster <span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="group_id" id="main_cluster">
                                <option value="">Select One ...</option>
                            </select>

                        </div>

                        <div class=" col-md-4">
                            <label>Company code <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_code" id="company_code">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Name <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_name" id="company_name">
                        </div>

                    </div> 
                    <div class=" col-md-12">                 

                        <div class=" col-md-4">
                            <label>Company Address 1 <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_address_1" id="company_ad1">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Address 2:</label>
                            <input type="text" class="form-control input-xxs" name="company_address_2" id="company_ad2">
                        </div>

                        <div class=" col-md-4">
                            <label>City <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="city" id="company_city">
                        </div>
                    </div> 
                    <div class=" col-md-12"> 
                        <div class=" col-md-4">
                            <label>Country Code <span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="country_code" id="ctry_code">
                                <option value="">Select One ...</option>
                            </select>
                        </div>

                        <div class=" col-md-4">
                            <label>Company Registration Number <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_reg_no" id="com_regnum">
                        </div>

                        <div class=" col-md-4">
                            <label>Contact 1<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_contact_1" id="contact_1">
                        </div>
                    </div> 
                    <div class=" col-md-12"> 
                        <div class=" col-md-4">
                            <label>Contact 2</label>
                            <input type="text" class="form-control input-xxs" name="company_contact_2" id="contact_2">
                        </div>

                        <div class=" col-md-4">
                            <label>Fax</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_fax" id="con_fax">
                        </div>

                        <div class=" col-md-4">
                            <label>Email<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company_email" id="com_email">
                        </div>
                    </div> 
                    <div class=" col-md-12"> 
                        <div class=" col-md-4">
                            <label>Web</label>
                            <input type="text" class="form-control input-xxs" name="company_web" id="com_web">
                        </div>

                        <div class=" col-md-4">
                            <label>Remarks</label>
                            <input type="text" class="form-control input-xxs" name="company_remarks" id="com_remak">
                        </div>
                        <div class=" col-md-4">
                            <label>Vat Registration Number<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="vat_reg_no" id="vat_regnum">
                        </div>
                    </div> 
                    <div class=" col-md-12">                   
                        <div class=" col-md-4">
                            <label>Tax Code<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="tax_code" id="tax_code">
                        </div>

                        <div class=" col-md-4">
                            <label>Default Currency<span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="default_currency" id="def_curr">
                                <option value="">Select One ...</option>
                            </select>
                        </div>
                        <div class=" col-md-4">
                            <label>Finance Month<span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="finance_month" id="fin_month">
                                <option value="">[ Select ]</option>
                                <option value="Jan to Dec">Jan to Dec</option>
                                <option value="March to April">March to April</option>
                            </select>
                        </div>
<<<<<<< HEAD

                        <div class=" col-md-12">
<<<<<<< HEAD
                           <label>Company Logo :</label>
                           <input type="file" class="form-control file-styled" name="company_logo" id="company_logo">

                       </div>


                   </fieldset>


               </div>

               <div class="modal-footer">
=======
=======
                    </div>
<div class=" col-md-12">    
                        <div class=" col-md-6">
>>>>>>> origin/master
                         <label>Company Logo :</label>
                         <input type="file" class="form-control file-styled input-xxs" name="company_logo" id="company_logo">

                        </div>

                        <div class=" col-md-6">
                         <label>Select Section :</label>
                         <select multiple="multiple" class="select-multiple input-xxs"  name="sec_mulname[]" id="sec_mulname">
                       </select>

                        </div></div>

                        <div class=" col-md-12"><div class=" col-md-12">    
                         <label>Select Department :</label>
                         <select multiple="multiple" class="select-multiple input-xxs"  name="sel_depart[]" id="sel_depart">
                       </select>

                        </div></div>


                 </fieldset>


             </div>

             <div class="modal-footer">
>>>>>>> origin/master
                <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Close</button>            
                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-3">
                    <b><i class="icon-floppy-disk"></i></b> Save</button>

                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
</div> 
=======
</div>


<!-- popup -->
<!-- <div id="show_section" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="section_form">

                <div class="modal-header  bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Main section</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}
                    
                    <input type="hidden" name="company_id_d" id="company_id_d" class="form-control input-xxs">

                    <div class=" col-md-12">

                    <fieldset class="content-group">
                       <label>Multiple select <span class="text-danger">*</span> :</label>
                       <select multiple="multiple" class="select-multiple"  name="sec_mulname[]" id="sec_mulname">
                       </select>

                    </fieldset>

                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Close</button>                                
                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-5">
                        <b><i class="icon-floppy-disk"></i></b> Save</button>

                    </div>
                </form>
            </div>
        </div>
<<<<<<< HEAD
    </div>
>>>>>>> origin/master
=======
    </div> -->
>>>>>>> origin/master
