<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
                id="add_data"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 


    <table class="table datatable-basic" id="source_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
                <th>Country Code</th>
                <th>Description</th>
                <th>Status</th>                                                

                <!-- <th class="text-center">Actions</th> -->
            </tr>
        </thead>
        <tbody>

        </tbody>


    </table>


</div>

<!-- popup -->
<div id="show_country" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="country_form">

                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Country</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}
                    <input type="hidden" value="0" name="country_hid" id="country_hid" class="form-control input-xxs">
                    <div class=" col-source_hidmd-12">

                        <fieldset class="content-group">
                            <div class="form-group">
                                <label>Country Code <span class="text-danger">*</span> :</label>

                                <input type="text" name="country_code" id="country_code" class="form-control input-xxs" >

                            </div>
                            <div class="form-group">

                                <label>Description<span class="text-danger">*</span> :</label>

                                <input type="text" name="country_description" id="country_description" class="form-control input-xxs" >
                            </div>
                        </fieldset>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id=""><b><i class="icon-cross"></i></b>Close</button>                                 
                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save">
                        <b><i class="icon-floppy-disk"></i></b> Save</button>

                </div>
            </form>
        </div>
    </div>
</div>

