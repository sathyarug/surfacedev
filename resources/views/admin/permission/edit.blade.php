<script>

    $(function () {

        $('#permission-field').select2();


        var validator = app_form_validator('#permission_form', {

            submitHandler: function () {
                try {
                    add_edit_permission();
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
                        url: "/admin/permission/checkName",
                        data: {
                            name: function () {
                                return $("#permission-name").val();
                            },
                            id: function () {
                                return $("#permission_id").val();
                            }
                        },
                        dataFilter: function (data) {
                            if (data == 'true') {
                                return "\"" + "This permission name already exists." + "\"";
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

    

</script>
@if(isset($permission))
{!! Form::model($permission, [
'method' => 'POST',
'url' => ['/admin/permission', $permission->id],
'class' => 'form-horizontal',
'id'=>'permission_form'
]) !!}
@else
{!! Form::open(['url' => ['/admin/permission'], 'class' => 'form-horizontal', 'id'=>'permission_form']) !!}
@endif
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Edit Role</h5>
</div>

<div class="modal-body">
    <div class="row">
        <div class=" col-md-12">
            <div class="form-group">
                {!! Form::hidden('id', isset($permission)?$permission->id:'',['id'=>'permission_id' ]) !!}
                <label>Role Name <span class="text-danger">*</span> :</label>
                {!! Form::text('name', isset($permission)?$permission->name:'' , 
                ['class' => 'form-control input-xxs', 'required' => 'required','id'=>'permission-name' ]
                ) !!}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cancel</button>                               
    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save" ><b><i class="icon-floppy-disk"></i></b> Save</button>
</div>

{!! Form::close() !!}
