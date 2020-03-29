<div class="row">
	<div class="col-md-12">
		<div class="text-right">
			<button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn_new_cost_center"><b><i class="icon-plus3"></i></b> New</button>
		</div>
	</div>
</dev>

<div class="row">
	<div class="col-md-12">
		<table class="table display compact" id="tbl_cost_center">
			<thead>
				<tr>
					<th class="text-center">Action</th>
					<th>Cost center Code</th>
					<th>Cost center name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			</table>
	</div>
</div>

<div id="model_cost_center" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="cost_center_form">


                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Cost Center</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}
					<input type="hidden" name="cost_center_id" id="cost_center_id" value="0">

					<fieldset class="content-group">
						<label>Cost Center Code <span class="text-danger">*</span> :</label>
						<input type="text" name="cost_center_code" id="cost_center_code" class="form-control input-xxs" placeholder="Enter cost center code">
					</fieldset>


					<fieldset class="content-group">
						<label>Cost Center Name <span class="text-danger">*</span> :</label>
						<input type="text" name="cost_center_name" id="cost_center_name" class="form-control input-xxs" placeholder="Enter cost center name">
					</fieldset>

				</div>

        <div class="modal-footer">
            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id=""><b><i class="icon-cross"></i></b>Close</button>
            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-2">
                <b><i class="icon-floppy-disk"></i></b> Save</button>

            </div>
        </form>
    </div>
</div>
</div>
</div>
