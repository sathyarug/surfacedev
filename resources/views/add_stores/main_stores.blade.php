<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
        id="add_data"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 


    <table class="table datatable-basic" id="stores_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
                <th>Location Name</th>
                <th>Stores Name</th>
                <th>store address</th>
                <th>store T.P. #</th> 
                 <th>store fax</th>
                <th>store email</th>
                <th>Status</th>



                <!-- <th class="text-center">Actions</th> ---->
            </tr>
        </thead>
        <tbody>

        </tbody>


    </table>


</div>

<!-- popup -->

<div id="show_stores" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form class="form-horizontal form-validate-jquery" action="#" id="stores_form">


                        <div class="modal-header bg-teal-300">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">Add Stores</h5>
                        </div>

                        <div class="modal-body">
                            {{csrf_field()}}
                            <input type="hidden" value="0" name="stores_hid" id="stores_hid" class="form-control input-xxs">
                           
                        <fieldset class="content-group">

                        <div class=" col-md-12">    
                            <div class=" col-md-4">
                                <label>Select Factory Location <span class="text-danger">*</span>:</label>
                                <select class="select-search input-xxs" name="loc_id" id="loc_id">
                                <option value="">Select One ...</option>
                                </select>
                            </div>

                            <div class=" col-md-4">
                                <label>Select Factory Section <span class="text-danger">*</span>:</label>
                                <select class="select-search input-xxs" name="fac_section" id="fac_section">
                                <option value="">Select One ...</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Stores Name <span class="text-danger">*</span>:</label>
                                <input type="text" class="form-control input-xxs" name="store_name" id="store_name">
                            </div>
                        </div>
                            
                        <div class=" col-md-12">           
                            <div class=" col-md-4">
                                <label>Stores Address <span class="text-danger">*</span>:</label>
                                <input type="text" class="form-control input-xxs" name="store_address" id="store_address">
                            </div>

                            <div class=" col-md-4">
                                <label>T.P No :</label>
                                <input type="text" class="form-control input-xxs" name="store_phone" id="store_phone">
                            </div>

                            <div class=" col-md-4">
                                <label>Fax No :</label>
                                <input type="text" class="form-control input-xxs" name="store_fax" id="store_fax">
                            </div>
                        </div>
                        
                        
                        <div class=" col-md-12"> 
                            <div class=" col-md-4">
                                <label>Email:</label>
                                <input type="text" class="form-control input-xxs" name="store_email" id="store_email">
                            </div>
                        </div>

                                



                    </fieldset>


                </div>

                <div class="modal-footer">
<!--                <button type="button" class="btn btn-link" data-dismiss="modal" id="">Close</button>  -->
                <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id="">
                        <b><i class="icon-cross"></i></b>Close</button>

                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save">
                            <b><i class="icon-floppy-disk"></i></b> Save</button>

                </div>
            </form>
        </div>
    </div>
</div> 

