<script>

    $(function () {

        $('#permission-field').select2();


        var validator = app_form_validator('#role_form', {

            submitHandler: function () {
                try {
<<<<<<< HEAD
                    edit_role();
                    validator.resetForm();
=======
                   // edit_role();
                   add_edit_role();
>>>>>>> origin/master
                } catch (e) {
                    console.log(e);
                    return false;
                }
                return false;
            },

        });

    });

<<<<<<< HEAD
    function edit_role() {
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
                    role_tbl.ajax.reload(); // reload datatabe

                } else {
                    app_alert('error', res.message);
                }

            }
        });
    }

</script>
=======
 </script>
>>>>>>> origin/master

{!! Form::model($role, [
'method' => 'POST',
'url' => ['/admin/role', $role->id],
'class' => 'form-horizontal',
'id'=>'role_form'
]) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Edit Role</h5>
</div>

@include ('admin.role.form', ['submitButtonText' => 'Update'])

{!! Form::close() !!}
