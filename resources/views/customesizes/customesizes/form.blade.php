<div class="form-group {{ $errors->has('size_id') ? 'has-error' : ''}}">
    <label for="size_id" class="col-md-4 control-label">{{ 'Size Id' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="size_id" type="number" id="size_id" value="{{ $customesize->size_id or ''}}" >
        {!! $errors->first('size_id', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('customer_id') ? 'has-error' : ''}}">
    <label for="customer_id" class="col-md-4 control-label">{{ 'Customer Id' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="customer_id" type="number" id="customer_id" value="{{ $customesize->customer_id or ''}}" >
        {!! $errors->first('customer_id', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('division_id') ? 'has-error' : ''}}">
    <label for="division_id" class="col-md-4 control-label">{{ 'Division Id' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="division_id" type="number" id="division_id" value="{{ $customesize->division_id or ''}}" >
        {!! $errors->first('division_id', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('size_name') ? 'has-error' : ''}}">
    <label for="size_name" class="col-md-4 control-label">{{ 'Size Name' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="size_name" type="text" id="size_name" value="{{ $customesize->size_name or ''}}" >
        {!! $errors->first('size_name', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-4 col-md-4">
        <input class="btn btn-primary" type="submit" value="{{ $submitButtonText or 'Create' }}">
    </div>
</div>
