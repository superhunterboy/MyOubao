<div class="form-group">
    <label for="name" class="col-sm-3 control-label">*{{ __('Role Name') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="name" id="name" value="{{ Input::old('name', isset($data) ? $data->name : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('name', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="rights" class="col-sm-3 control-label">*{{ __('Rights') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="rights" id="rights" value="{{ Input::old('rights', isset($data) ? $data->rights : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('rights', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">*{{ __('Description') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="description" id="description" value="{{ Input::old('description', isset($data) ? $data->description : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('description', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>


<div class="form-group">
    <label for="priority" class="col-sm-3 control-label">*{{ __('Priority') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="priority" id="priority" value="{{ Input::old('priority', isset($data) ? $data->priority : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('priority', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="is_system" class="col-sm-3 control-label">*{{ __('Is System') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="is_system" id="is_system" value="1"
                {{ Input::old('is_system', (isset($data) ? $data->is_system : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('is_system', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="right_settable" class="col-sm-3 control-label">*{{ __('Right Settable') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="right_settable" id="right_settable" value="1"
                {{ Input::old('right_settable', (isset($data) ? $data->right_settable : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('right_settable', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="user_settable" class="col-sm-3 control-label">*{{ __('User Settable') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="user_settable" id="user_settable" value="1"
                {{ Input::old('user_settable', (isset($data) ? $data->user_settable : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('user_settable', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="disabled" class="col-sm-3 control-label">*{{ __('Disabled') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="disabled" id="disabled" value="1"
                {{ Input::old('disabled', (isset($data) ? $data->disabled : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('disabled', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="sequence" class="col-sm-3 control-label">{{ __('Sequence') }}</label>
    <div class="col-sm-5">
       <input class="form-control" type="text" name="sequence" id="sequence" value="{{ Input::old('sequence', (isset($data) ? $data->sequence : '' )) }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('sequence', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>