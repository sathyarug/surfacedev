<div class="row">
	<div class="col-md-12">
		<div class="text-right">
			<button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn_new_payment_method"><b><i class="icon-plus3"></i></b> New</button>
		</div>
	</div>
</dev>

<div class="row">
	 <div class="col-md-12">
		<table class="table display compact" id="tbl_payment_method">
			<thead>
				<tr>
					<th class="text-center">Action</th>
					<th>Payment Method Code</th>
					<th>Payment Method Description</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<div id="model_payment_method" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
		<form class="form-horizontal form-validate-jquery" action="#" id="payment_method_form">


                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Payment Method</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}

					<input type="hidden" name="payment_method_id" id="payment_method_id" value="0">

						<fieldset class="content-group">
							<label>Payment Method Code <span class="text-danger">*</span> :</label>
							<input type="text" name="payment_method_code" id="payment_method_code" class="form-control input-xxs" placeholder="Enter payment code" >
						</fieldset>
						<fieldset class="content-group">
							<label>Payment Method Description <span class="text-danger">*</span> :</label>
							<input type="text" name="payment_method_description" id="payment_method_description" class="form-control input-xxs" placeholder="Enter payment description" >
						</fieldset>


					</div>

        <div class="modal-footer">
            <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal" id=""><b><i class="icon-cross"></i></b>Close</button>
            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn_save_payment_method"><b><i class="icon-floppy-disk"></i></b> Save</button>

            </div>
        </form>
    </div>
</div>
</div>
</div>
