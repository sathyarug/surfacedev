<div class="form-group {{ $errors->has('master_id') ? 'has-error' : ''}}">
    <label for="master_id" class="col-md-4 control-label">{{ 'Master Id' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="master_id" type="number" id="master_id" value="{{ $itemcreation->master_id or ''}}" >
        {!! $errors->first('master_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-4 col-md-4">
        <input class="btn btn-primary" type="submit" value="{{ $submitButtonText or 'Create' }}">
    </div>
</div>
