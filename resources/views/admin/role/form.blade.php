<div class="modal-body">
<<<<<<< HEAD
    
    <!--<input type="hidden"  name="id" id="role_id" class="form-control">-->
    <div class=" col-md-12">

        <div class="form-group">
            <label>Role Name <span class="text-danger">*</span> :</label>
            {!! Form::text('name', isset($role)?$role->name:'' , 
            isset($role)?['class' => 'form-control input-xxs', 'disabled' => 'disabled','id'=>'role-name' ]:['class' => 'form-control input-xxs', 'required' => 'required','id'=>'role-name' ]
            ) !!}
        </div>

        <div class="form-group">
            <label>Permissions <span class="text-danger">*</span> :</label>
            {!! Form::select('permissions[]',$permissions, old('permissions')??isset($role)?$role->permissions->pluck('name','name'):null, ['class' => 'form-control', 'required' => 'required','multiple','id'=>'permission-field'] ) !!}
        </div>

=======

    <div class="row">
        <div class=" col-md-12">
            <div class="form-group">
                <label>Role Name <span class="text-danger">*</span> :</label>
                {!! Form::text('name', isset($role)?$role->name:'' , 
                isset($role)?['class' => 'form-control input-xxs', 'disabled' => 'disabled','id'=>'role-name' ]:['class' => 'form-control input-xxs', 'required' => 'required','id'=>'role-name' ]
                ) !!}
            </div>

            <div class="form-group">
                <label>Permissions <span class="text-danger">*</span> :</label>
                {!! Form::select('permissions[]',$permissions, old('permissions')??isset($role)?$role->permissions->pluck('name','name'):null, ['class' => 'form-control', 'required' => 'required','multiple','id'=>'permission-field'] ) !!}
            </div>
        </div>
>>>>>>> origin/master
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn bg-teal-400 btn-labeled btn-danger btn-xs" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cancel</button>                               
    <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save" ><b><i class="icon-floppy-disk"></i></b> Save</button>
</div>