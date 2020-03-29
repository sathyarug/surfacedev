<!-- Content area -->
<div class="content">
    <div class="col-md-12">
        <!-- Basic layout-->
        <form class="form-horizontal form-validate-jquery" action="#" id="customer_form">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="row" >
                        <div class="col-md-12">

                            <div class="col-md-12" style="padding: 10px;border-radius:10px; ">
                                {{csrf_field()}}
                                <input type="hidden" value="0" name="customer_hid" id="customer_hid" class="form-control input-xxs">
                                <div class="row" >
                                    <div class="col-md-1">
                                        <label>Code<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_code" name="customer_code">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Customer Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_name" name="customer_name">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Company Code<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="company_code" name="company_code">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Account No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_acc_no" name="bank_acc_no">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Short Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_short_name" name="customer_short_name">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Operations Start Date<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon input-xxs"><i class="icon-calendar5"></i></span>
                                            <input type="text" class="form-control pickadate-accessibility  input-xxs" id="operation_start_date" name="operation_start_date" placeholder="Select Date&hellip;">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Bank Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_name" name="bank_name">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Type Of Good/Service<span class="text-danger"></span></label>
                                        <select class="select-search input-xxs" id="type_of_service" name="type_of_service" >
                                            <option value="Bulk">Bulk</option>cus_type_service
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Order Destination/s<span class="text-danger">*</span></label>
                                        <select class="select-search input-xxs" id="order_destination" name="order_destination">
                                            <option value="Bulk">Bulk</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Branch<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_branch" name="bank_branch">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Business Registration No 1<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="business_reg_no" name="business_reg_no">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Currency<span class="text-danger">*</span></label>
                                        <select class="select-search input-xxs" id="currency" name="currency">
                                            <option value="">Select One ...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Code<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_code" name="bank_code">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Business Registration Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon  input-xxs"><i class="icon-calendar5"></i></span>
                                            <input type="text" class="form-control pickadate-accessibility  input-xxs" id="business_reg_date" name="business_reg_date" placeholder="Select Date&hellip;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>BOI Register No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="boi_reg_no" name="boi_reg_no">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Swift/Sort Code<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_swift" name="bank_swift">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Address 1<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_address1" name="customer_address1">
                                    </div>
                                    <div class="col-md-4">
                                        <label>BOI Register Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon  input-xxs"><i class="icon-calendar5"></i></span>
                                            <input type="text" class="form-control pickadate-accessibility  input-xxs" id="boi_reg_date" name="boi_reg_date" placeholder="Select Date&hellip;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>IBAN<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_iban" name="bank_iban">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Address 2(Lane/Road)<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_address2" name="customer_address2">
                                    </div>
                                    <div class="col-md-4">
                                        <label>VAT Register No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="vat_reg_no" name="vat_reg_no">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Contact No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="bank_contact" name="bank_contact">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>City/Province<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_city" name="customer_city">
                                    </div>
                                    <div class="col-md-4">
                                        <label>SVAT No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="svat_no" name="svat_no">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Intermediary Bank Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="intermediary_bank_name" name="intermediary_bank_name">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Postal Code<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_postal_code" name="customer_postal_code">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Managing Director Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="managing_director_name" name="managing_director_name">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Intermediary Bank Address<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="intermediary_bank_address" name="intermediary_bank_address">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>State/Territory<span class="text-danger"></span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_state" name="customer_state">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Managing Director E-mail<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="managing_director_email" name="managing_director_email">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Intermediary Bank Contact No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="intermediary_bank_contact" name="intermediary_bank_contact">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Country<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_county" name="customer_county">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Finance Director Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="finance_director_name" name="finance_director_name">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Buyer Posting Group<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="buyer_posting_group" name="buyer_posting_group">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Contact No 1<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_contact1" name="customer_contact1">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Finance Director E-mail<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="finance_director_email" name="finance_director_email">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Business Posting Group<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="business_posting_group" name="business_posting_group">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Contact No 2<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_contact2" name="customer_contact2">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Finance Director's Contact No<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="finance_director_contact" name="finance_director_contact">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Approved By<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="approved_by" name="approved_by">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Contact No 3<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_contact3" name="customer_contact3">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Additional Comments / Remarks<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="additional_comments" name="additional_comments">
                                    </div>
                                    <div class="col-md-4">
                                        <label>System Updated By<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="system_updated_by" name="system_updated_by">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>E-mail Address Book<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_email" name="customer_email">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Ship Terms Agreed To<span class="text-danger">*</span></label>
                                        <select class="select-search input-xxs" id="ship_terms_agreed" name="ship_terms_agreed">
                                            <option value="USD">USD</option>
                                            <option value="Rs">Rs</option>
                                            <option value="Pound">Pound</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Buyer Creation Form<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_creation_form" name="customer_creation_form">
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Google Maps Location<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_map_location" name="customer_map_location">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Payment Terms<span class="text-danger">*</span></label>
                                        <select class="select-search input-xxs" id="payemnt_terms" name="payemnt_terms">
                                            <option value="">Select One ...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-md-4">
                                        <label>Buyer Web Site<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control input-xxs" id="customer_website" name="customer_website">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Payment Mode<span class="text-danger">*</span></label>
                                        <select class="select-search input-xxs" id="payment_mode" name="payment_mode">
                                            <option value="USD">USD</option>
                                            <option value="Rs">Rs</option>
                                            <option value="Pound">Pound</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>	
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="btn-new"><b><i class="icon-plus3"></i></b> New</button>                       
                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save">
                    <b><i class="icon-floppy-disk"></i></b> Save</button>

            </div>
        </form>

    </div>

</div>
