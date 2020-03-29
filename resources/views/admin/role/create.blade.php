<script>

    $(function () {

        $('#permission-field').select2();


        var validator = app_form_validator('#role_form', {

            submitHandler: function () {
                try {
<<<<<<< HEAD
                    save_role();
                    $("#role_form :input").val('');
                    validator.resetForm();
=======
                    //save_role();
                    add_edit_role();
>>>>>>> origin/master
                } catch (e) {
                    console.log(e);
                    return false;
                }
                return false;
            },

            rules: {

                "name": {
                    required: true,
                    remote: {
                        type: "get",
                        url: "/admin/role/checkName",
                        data: {
                            name: function () {
                                return $("#role-name").val();
                            },
                            id: function () {
                                return $("#role_id").val();
                            }
                        },
                        dataFilter: function (data) {
                            if (data == 'true') {
                                return "\"" + "This role name already exists." + "\"";
                                ;
                            } else {
                                return 'true';
                            }
                        }
                    },

                },

            }
        });

    });

<<<<<<< HEAD
    function save_role() {
=======
   /* function save_role() {
>>>>>>> origin/master
        $.ajax({
            url: $("#role_form").attr('action'),
            async: false,
            type: "POST",
            data: $("#role_form").serialize(),
            dataType: "json",
            success: function (res)
            {
                if (res.status === 'success')
                {
                    app_alert('success', res.message);
                    $('#show_role').modal('toggle');
<<<<<<< HEAD
                    role_tbl.ajax.reload(); // reload datatabe
=======
                    role_tbl.ajax.reload(null, false); // reload datatabe
>>>>>>> origin/master

                } else {
                    app_alert('error', res.message);
                }
            }
        });
<<<<<<< HEAD
    }
=======
    }*/
>>>>>>> origin/master

</script>

{!! Form::open(['url' => ['/admin/role'], 'class' => 'form-horizontal', 'id'=>'role_form']) !!}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Add Role</h5>
</div>

@include ('admin.role.form')

{!! Form::close() !!}


