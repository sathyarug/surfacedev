<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
        id="add_company"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 



    <table class="table datatable-basic" id="location_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
                <th>Company Logo</th>
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
                <th>Status</th>
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


                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">Add Main Company</h5>
                        </div>

                        <div class="modal-body">
                            {{csrf_field()}}
                            <fieldset class="content-group">


                               <div class=" col-md-4">

                                <label>Select Main Cluster <span class="text-danger">*</span>:</label>
                                <select class="select-search input-xxs" name="main-cluster" id="main_cluster">
                                <option value="">Select One ...</option>
                                </select>

                            </div>

                         <div class=" col-md-4">
                            <label>Company code <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company-code" id="company_code">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Name <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company-name" id="company_name">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Logo <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company-logo" id="company_logo">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Address 1 <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company-ad1" id="company_ad1">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Address 2 <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company-ad2" id="company_ad2">
                        </div>

                        <div class=" col-md-4">
                            <label>City <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="company-city" id="company_city">
                        </div>

                        <div class=" col-md-4">
                            <label>Country Code <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="country-code" id="country_code">
                        </div>

                        <div class=" col-md-4">
                            <label>Company Registration Number <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="com-regnum" id="com_regnum">
                        </div>

                        <div class=" col-md-4">
                            <label>Contact 1<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="contact-1" id="contact_1">
                        </div>

                        <div class=" col-md-4">
                            <label>Contact 2</label>
                            <input type="text" class="form-control input-xxs" name="contact-2" id="contact_2">
                        </div>

                        <div class=" col-md-4">
                            <label>Fax</span>:</label>
                            <input type="text" class="form-control input-xxs" name="com-fax" id="con_fax">
                        </div>

                        <div class=" col-md-4">
                            <label>Email<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="com-email" id="com_email">
                        </div>

                        <div class=" col-md-4">
                            <label>Web</label>
                            <input type="text" class="form-control input-xxs" name="com-web" id="com_web">
                        </div>

                        <div class=" col-md-4">
                            <label>Remarks</label>
                            <input type="text" class="form-control input-xxs" name="com-remak" id="com_remak">
                        </div>

                        <div class=" col-md-4">
                            <label>Default Currency<span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="def-curr" id="def_curr">
                                <option value="">Select One ...</option>
                                </select>
                        </div>
                        <div class=" col-md-4">
                            <label>Finance Month<span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="fin-month" id="fin_month">
                                <option value="">Select One ...</option>
                                </select>
                        </div>
                        <div class=" col-md-4">
                            <label>Vat Registration Number<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="vat-regnum" id="vat_regnum">
                        </div>
                        <div class=" col-md-4">
                            <label>Tax Code<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="tax-code" id="tax_code">
                        </div>


                    </fieldset>


                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal" id="">Close</button>             
                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-3">
                            <b><i class="icon-floppy-disk"></i></b> Save</button>

                </div>
            </form>
        </div>
    </div>
</div> 