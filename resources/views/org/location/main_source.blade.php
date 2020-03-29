<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
        id="add_data"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 


    <table class="table datatable-basic" id="source_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
<<<<<<< HEAD
                <th>Source Code</th>
                <th>Source Name</th>
                <th>Status</th>                                                
=======
                <th>Status</th> 
                <th>Source Code</th>
                <th>Source Name</th>
                
                                                               
>>>>>>> origin/master

                <!-- <th class="text-center">Actions</th> -->
            </tr>
        </thead>
        <tbody>

        </tbody>


    </table>


</div>

<!-- popup -->
 <div id="show_source" class="modal fade">
<<<<<<< HEAD
=======

>>>>>>> origin/master
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal form-validate-jquery" action="#" id="source_form">

                    <div class="modal-header  bg-teal-300">
<<<<<<< HEAD
=======

>>>>>>> origin/master
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add Main Sourse</h5>
                    </div>

                    <div class="modal-body">
                        {{csrf_field()}}
                        <input type="hidden" value="0" name="source_hid" id="source_hid" class="form-control input-xxs">
                        <div class=" col-md-12">

                            <fieldset class="content-group">

                                <label>Main Source Code <span class="text-danger">*</span> :</label>

                                <input type="text" name="source_code" id="source-code" class="form-control input-xxs" >



                                <label>Main Source Name <span class="text-danger">*</span> :</label>

                                <input type="text" name="source_name" id="source-name" class="form-control input-xxs" >

                            </fieldset>

                        </div>
                    </div>

                    <div class="modal-footer">
<<<<<<< HEAD
         
                        <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Close</button>                                
=======

         
                        <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Close</button>                                

>>>>>>> origin/master
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save">
                            <b><i class="icon-floppy-disk"></i></b> Save</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

