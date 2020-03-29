<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
        id="add_sublocation"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 



    <table class="table datatable-basic" id="sub_location_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
<<<<<<< HEAD
                
=======
                <th>Status</th>
>>>>>>> origin/master
                <th>Location Code</th> 
                <th>Company Code</th>
                <th>Location Name</th> 
                <th>Is Manufacturing</th> 
                <th>Address 01</th> 
                <th>Address 02</th>
                <th>City</th>
                <th>Country</th>
                <th>Phone #</th>
                <th>Fax #</th>
                <th>Email #</th>
                <th>Web #</th>
                <th>Time zone</th>   
                <th>Currency Code</th>                     
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

<div id="show_sub_location" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="sub_location_form">


                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Main Location</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}
                    <input type="hidden" value="0" name="sub_location_hid" id="sub_location_hid" class="form-control input-xxs">
                    <fieldset class="content-group">

                        <div class=" col-md-12">
                         <div class=" col-md-3">

                            <label>Select Main Company <span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="company_id" id="company_id">
                                <option value="">Select One ...</option>
                            </select>

                        </div>

                        <div class=" col-md-3">
                            <label>Type of Location <span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="type_of_loc" id="type_of_loc">
                                <option value="">Select One ...</option>
                            </select>
                        </div>

                        <div class=" col-md-3">
                            <label>Location code <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="loc_code" id="loc_code">
                        </div>

                        <div class=" col-md-3">
                            <label>Location Name <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="loc_name" id="loc_name">
                        </div>

                    </div><div class=" col-md-12">        

                        <div class=" col-md-3">
                            <label>Is Manufacturing Plant ? <span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="loc_type" id="loc_type">
                                <option value="" disabled selected>[ Select ]</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class=" col-md-3">
                            <label>Location Address 1 <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="loc_address_1" id="loc_address_1">
                        </div>

                        <div class=" col-md-3">
                            <label>Location Address 2:</label>
                            <input type="text" class="form-control input-xxs" name="loc_address_2" id="loc_address_2">
                        </div>

                        <div class=" col-md-3">
                            <label>City <span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="city" id="city">
                        </div>
                    </div><div class=" col-md-12">  
                        <div class=" col-md-3">
                            <label>Postal code :</label>
                            <input type="text" class="form-control input-xxs" name="postal_code" id="postal_code">
                        </div>

                        <div class=" col-md-3">
                            <label>State / Territory :</label>
                            <input type="text" class="form-control input-xxs" name="state_Territory" id="state_Territory">
                        </div>

                        <div class=" col-md-3">
                            <label>Country Code <span class="text-danger">*</span>:</label>
                            <select class="select-search input-xxs" name="country_code" id="country_code">
                                <option value="">Select One ...</option>
                            </select>
                        </div>

                        <div class=" col-md-3">
                            <label>Contact #<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="loc_phone" id="loc_phone">
                        </div>
                    </div><div class=" col-md-12">  
                        <div class=" col-md-3">
                            <label>Fax</span>:</label>
                            <input type="text" class="form-control input-xxs" name="loc_fax" id="loc_fax">
                        </div>

                        <div class=" col-md-3">
                            <label>Email<span class="text-danger">*</span>:</label>
                            <input type="text" class="form-control input-xxs" name="loc_email" id="loc_email">
                        </div>

                        <div class=" col-md-3">
                            <label>Web :</label>
                            <input type="text" class="form-control input-xxs" name="loc_web" id="loc_web">
                        </div>


                        <div class=" col-md-3">
                            <label>Google maps location Link:</label>
                            <input type="text" class="form-control input-xxs" name="loc_google" id="loc_google">
                        </div>
                    </div><div class=" col-md-12">  
                        <div class=" col-md-3">
                            <label>Land acres :</label>
                            <input type="text" class="form-control input-xxs" name="land_acres" id="land_acres">
                        </div>

                        <div class=" col-md-3">
                            <label>Type of Property :</label>
                            <select class="select-search input-xxs" name="type_property" id="type_property">
                                <option value="">Select One ...</option>
                            </select>
                        </div>

                   <!--  <div class=" col-md-3">
                        <label>Fixed asset list :</label>
                        <select class="select-search input-xxs" name="fix_asset" id="fix_asset">
                            <option value="">Select One ...</option>
                        </select>
                    </div> -->

                    <div class=" col-md-3">
                        <label>Operations start date <span class="text-danger">*</span>:</label>

                        <div class="input-group">
                                <span class="input-group-addon  input-xxs"><i class="icon-calendar5"></i></span>
                                <input type="text" class="form-control pickadate-accessibility  input-xxs" name="opr_start_date" id="opr_start_date" placeholder="[ Select Date ]">
                            </div>


                        <!-- <input type="text" class="form-control input-xxs pickadate" name="opr_start_date" id="opr_start_date" placeholder="[ Select Date ]"> -->

                    </div>

                    <div class=" col-md-3">
                        <label>Latitude :</label>
                        <input type="text" class="form-control input-xxs" name="latitude" id="latitude">
                    </div>
                </div><div class=" col-md-12">  
                    <div class=" col-md-3">
                        <label>Longitude :</label>
                        <input type="text" class="form-control input-xxs" name="longitude" id="longitude">
                        
                    </div>

                    <div class=" col-md-3">
                        <label>Time Zone <span class="text-danger">*</span>:</label>
                        <input type="text" class="form-control input-xxs" name="time_zone" id="time_zone">
                    </div>

                    <div class=" col-md-3">
                        <label>Default Currency<span class="text-danger">*</span>:</label>
                        <select class="select-search input-xxs" name="currency_code" id="currency_code">
                            <option value="">Select One ...</option>
                        </select>
                    </div>
                </div>
                 <div class=" col-md-12"><div class=" col-md-12">
                    <label>List of Cost Centers :</label>
                    <select multiple="multiple" class="select-multiple"  name="type_center[]" id="type_center">
                    </select>
                </div> 

            </div>



            </fieldset>


        </div>

        <div class="modal-footer">
            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Close</button>            
            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-4">
                <b><i class="icon-floppy-disk"></i></b> Save</button>

            </div>
        </form>
    </div>
</div>
</div> 