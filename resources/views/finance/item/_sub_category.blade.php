<div class="row">
	<div class="col-md-12">
		<div class="text-right">
			<button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn_new_sub_category"><b><i class="icon-plus3"></i></b> New</button>
		</div>
	</div>
</dev>

<div class="row">
	 <div class="col-md-12">
		<table class="table display compact" id="tbl_sub_category">
			<thead>
				<tr>
					<th class="text-center">Action</th>
					<th>Sub Category Code</th>
					<th>Sub Category Name</th>
					<th>Category</th>
					<th>Inspectiion Allowed</th>
					<th>Display</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<div id="model_sub_category" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
		<form class="form-horizontal form-validate-jquery" action="#" id="sub_category_form">

                <div class="modal-header bg-teal-300">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Sub Category</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}

					<input type="hidden" name="subcategory_id" id="subcategory_id" value="0">

						<fieldset class="content-group">
							<label>Sub Category Code<span class="text-danger">*</span> :</label>
							<input type="text" name="subcategory_code" id="subcategory_code" class="form-control input-xxs" placeholder="Enter sub category code" >
						</fieldset>
						<fieldset class="content-group">
							<label>Sub Category Name<span class="text-danger">*</span> :</label>
							<input type="text" name="subcategory_name" id="subcategory_name" class="form-control input-xxs" placeholder="Enter payment description" >
						</fieldset>
						<fieldset class="content-group">
							<label>Category<span class="text-danger">*</span> :</label>
							<select name="category_code" id="category_code" class="form-control input-xxs">
								<option value="">[select]</option>
								<?php foreach ($categories as $category) {
									echo '<option value="'.$category['category_code'].'">'.$category['category_name'].'</option>';
								} ?>
							</select>
						</fieldset>
						<fieldset class="content-group">
							<input type="checkbox" name="is_display" id="is_display"  value="1">
							<label>Display </label>
						</fieldset>
						<fieldset class="content-group">
							<input type="checkbox" name="is_inspectiion_allowed" id="is_inspectiion_allowed" value="1">
							<label>Inspectiion Allowed</label>
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
