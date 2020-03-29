<script>

    $(function () {


        var validator = app_form_validator('#frm_supplier', {

            submitHandler: function () {
                try {
                    save_supplier();
                    // $("#frm_supplier :input").val('');
                    validator.resetForm();
                } catch (e) {
                    console.log(e);
                    return false;
                }
                return false;
            },

            rules: {

                rules: {
                    phoneNumber: {
                        matches: "[0-9]+",  // <-- no such method called "matches"!
                        minlength:10,
                        maxlength:10
                    }
                }
            },
            messages: {

            }
        });


        $('select').select2();






        $('#supplier_tbl').on('click','i',function(){
            var ele = $(this);
            if(ele.attr('data-action') === 'EDIT'){
                supplier_edit(ele.attr('data-id'));
            }
            else if(ele.attr('data-action') === 'DELETE'){
                supplier_delete(ele.attr('data-id'));
            }
        });


        function supplier_edit(_id){

            $('#show_supplier').modal('show');
            $('#frm_supplier')[0].reset();
            validator.resetForm();

            $.ajax({
                url : 'supplier/edit',
                type : 'get',
                data : {'id' : _id},
                success : function(res){
                    var data = JSON.parse(res);
                    //alert(data);
                    $('#supplier_hid').val(data['supplier_id']);
                    $("input[name~='supplier_code']").val(data['supplier_code']);
                    $("input[name~='supplier_name']").val(data['supplier_name']);
                    $("input[name~='supplier_city']").val(data['supplier_city']);
                    $("input[name~='supplier_address1']").val(data['supplier_address1']);
                    $("input[name~='supplier_address2']").val(data['supplier_address2']);
                    $("input[name~='supplier_phone']").val(data['supplier_phone']);
                    $("input[name~='supplier_fax']").val(data['supplier_fax']);
                    $("input[name~='supplier_email']").val(data['supplier_email']);

                    // $("select[name='supplier_country_id']").select2("val", data['supplier_country_id']);
                    // $('#source-name').val(data['source_name']);
                    $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
                }
            });

        }





    });

    function save_supplier() {

        var data = app_serialize_form_to_json('#frm_supplier');
        data['_token'] = X_CSRF_TOKEN;
        console.log(data);
        $.ajax({
            url: "/supplier/save",
            async: false,
            type: "POST",
            data: data,
            dataType: "json",
            success: function (res)
            {
                //var json_res = JSON.parse(res);
                if (res.status === 'success')
                {
                    app_alert('success', res.message);
                    var tbl = $('#supplier_tbl').dataTable();
                    tbl.fnClearTable();
                    tbl.fnDraw();

                    $('#frm_supplier')[0].reset();
                    $('#show_supplier').modal('toggle');
                    validator.resetForm();

                } else {
                    app_alert('error', res.message);
                }


            }})


    }
</script>
<form class="form-horizontal form-validate-jquery" action="#" id="frm_supplier">
    <input type="hidden" value="{{ (isset($Supplier->supplier_id)) ? $Supplier->supplier_id : 0 }}" name="supplier_hid" id="supplier_hid" class="form-control input-xxs">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        @if(isset($Supplier->supplier_id))
            <h5 class="modal-title">Edit Supplier</h5>
        @else
            <h5 class="modal-title">New Supplier</h5>
        @endif

    </div>

    <div class="modal-body">
        {{csrf_field()}}
        <fieldset class="content-group">

            <div class=" col-md-12">
                <div class=" col-md-4">

                    <label>Supplier Code<span class="text-danger">*</span>:</label>
                    {{ Form::text('supplier_code',  ($Supplier->supplier_code) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                    {{--<input type="text" class="form-control input-xxs" name="supplier-code" id="Supplier_code">--}}

                </div>

                <div class=" col-md-4">
                    <label>Supplier Name <span class="text-danger">*</span>:</label>
                    {{ Form::text('supplier_name', ($Supplier->supplier_name) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                    {{--<input type="text" class="form-control input-xxs" name="supplier-name" id="supplier_name">--}}
                </div>

                <div class=" col-md-4">
                    <label>Supplier Country <span class="text-danger">*</span>:</label>
                    {{ Form::select('supplier_country_id',$loc,($Supplier->supplier_country_id) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}
                    {{--<select class="select-search input-xxs" name="supplier-country" id="def_curr">--}}
                    {{--<option value="">Select One ...</option>--}}
                    {{--</select>--}}
                    {{--<input type="text" class="form-control input-xxs" name="supplier-country" id="supplier_country">--}}
                </div>
            </div>
            <div class=" col-md-12">
                <div class=" col-md-4">
                    <label>Supplier City <span class="text-danger">*</span>:</label>
                    {{ Form::text('supplier_city',($Supplier->supplier_city) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                    {{--<input type="text" class="form-control input-xxs" name="supplier-city" id="supplier_city">--}}
                </div>

                <div class=" col-md-4">
                    <label>Supplier Address 1 <span class="text-danger">*</span>:</label>
                    {{--<input type="text" class="form-control input-xxs" name="Supplier-ad1" id="Supplier_ad1">--}}
                    {{ Form::text('supplier_address1', ($Supplier->supplier_address1) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                </div>

                <div class=" col-md-4">
                    <label>Supplier Address 2 :</label>
                    {{ Form::text('supplier_address2', ($Supplier->supplier_address2) ?? '', ['class' => 'form-control input-xxs']) }}
                    {{--<input type="text" class="form-control input-xxs" name="Supplier-ad2" id="Supplier_ad2">--}}
                </div>
            </div>
            <div class=" col-md-12">
                <div class=" col-md-4">
                    <label>Supplier Phone <span class="text-danger">*</span>:</label>
                    {{ Form::text('supplier_phone', ($Supplier->supplier_phone) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                    {{--<input type="text" class="form-control input-xxs" name="company-city" id="supplier_phone">--}}
                </div>

                <div class=" col-md-4">
                    <label>Supplier Fax :</label>
                    {{ Form::text('supplier_fax', ($Supplier->supplier_fax) ?? '', ['class' => 'form-control input-xxs']) }}
                    {{--<input type="text" class="form-control input-xxs" name="supplier-fax" id="supplier_fax">--}}
                </div>

                <div class=" col-md-4">
                    <label>Supplier Email <span class="text-danger">*</span>:</label>
                    {{ Form::email('supplier_email', ($Supplier->supplier_email) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required']) }}
                    {{--<input type="text" class="form-control input-xxs" name="supplier-email" id="supplier_email">--}}
                </div>
            </div>
            <div class=" col-md-12">
                <div class=" col-md-4">
                    <label>Payment Mode<span class="text-danger">*</span></label>
                    {{ Form::select('payment_mode_id',$method, ($Supplier->payment_mode_id) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}
                    {{--<input type="text" class="form-control input-xxs" name="payment-mode" id="payment_mode">--}}
                </div>

                <div class=" col-md-4">
                    <label>Payment terms<span class="text-danger">*</span></label>
                    {{ Form::select('payment_code',$terms, ($Supplier->payment_code) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}
                    {{--<input type="text" class="form-control input-xxs" name="payment-code" id="payment_code">--}}
                </div>

                <div class=" col-md-4">
                    <label>Default currency code<span class="text-danger">*</span></label>
                    {{ Form::select('default_currency_code',$currency, ($Supplier->default_currency_code) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}

                </div>
            </div>
            <div class=" col-md-12">
                <div class=" col-md-4">
                    <label>vat reg no<span class="text-danger">*</span></label>
                    {{ Form::text('vat_reg_no',($Supplier->vat_reg_no) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}

                </div>

                <div class=" col-md-4">
                    <label>Origin<span class="text-danger">*</span></label>
                    {{ Form::select('payment_code1',$origin, ($Supplier->origin) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}

                </div>

                <div class=" col-md-4">
                    <label>Default currency code<span class="text-danger">*</span></label>
                    {{ Form::select('default_currency_code1',$currency, ($Supplier->default_currency_code) ?? '', ['class' => 'form-control input-xxs', 'required' => 'required'])  }}

                </div>
            </div>


            {{--<div class=" col-md-4">--}}
            {{--<label>Default Currency<span class="text-danger">*</span>:</label>--}}
            {{--<select class="select-search input-xxs" name="def-curr" id="def_curr">--}}
            {{--<option value="">Select One ...</option>--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--<div class=" col-md-4">--}}
            {{--<label>Finance Month<span class="text-danger">*</span>:</label>--}}
            {{--<select class="select-search input-xxs" name="fin-month" id="fin_month">--}}
            {{--<option value="">Select One ...</option>--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--<div class=" col-md-4">--}}
            {{--<label>Vat Registration Number<span class="text-danger">*</span>:</label>--}}
            {{--<input type="text" class="form-control input-xxs" name="vat-regnum" id="vat_regnum">--}}
            {{--</div>--}}
            {{--<div class=" col-md-4">--}}
            {{--<label>Tax Code<span class="text-danger">*</span>:</label>--}}
            {{--<input type="text" class="form-control input-xxs" name="tax-code" id="tax_code">--}}
            {{--</div>--}}


        </fieldset>


    </div>

    <div class="modal-footer">

        <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cancel</button>
        <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-3">
            <b><i class="icon-floppy-disk"></i></b> Save</button>

    </div>
</form>

