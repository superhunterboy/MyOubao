<div class="form-group">
    <label for="description" class="col-sm-3 control-label">{{ __('Description') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="description" id="description" value="{{ Input::old('description', isset($data) ? $data->description : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('description', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
<div class="form-group">
    <label for="controller" class="col-sm-3 control-label">*{{ __('Controller') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="controller" id="controller" value="{{ Input::old('controller', isset($data) ? $data->controller : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('controller', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
<div class="form-group">
    <label for="action" class="col-sm-3 control-label">*{{ __('Action') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="action" id="action" value="{{ Input::old('action', isset($data) ? $data->action : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('action', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="refresh_cycle" class="col-sm-3 control-label">{{ __('Refresh Cycle') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="refresh_cycle" id="refresh_cycle" value="{{ Input::old('refresh_cycle', isset($data) ? $data->refresh_cycle : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('refresh_cycle', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
<div class="form-group">
    <label for="menu" class="col-sm-3 control-label">*{{ __('Menu') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="menu" id="menu" value="1"
                {{ Input::old('menu', (isset($data) ? $data->menu : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('menu', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
    </div>
</div>

<div class="form-group">
    <label for="need_curd" class="col-sm-3 control-label">*CURD</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="need_curd" id="need_curd" value="1"
                {{ Input::old('need_curd', (isset($data) ? $data->need_curd : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('need_curd', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
    </div>
</div>

<div class="form-group">
    <label for="need_search" class="col-sm-3 control-label">*{{ __('Search') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="need_search" id="need_search" value="1"
                {{ Input::old('need_search', (isset($data) ? $data->need_search : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('need_search', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
    </div>
</div>

<div class="form-group">
    <label for="need_log" class="col-sm-3 control-label">*{{ __('Log') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="need_log" id="need_log" value="1"
                {{ Input::old('need_log', (isset($data) ? $data->need_log : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('need_log', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
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
        {{ $errors->first('disabled', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
    </div>
</div>

<div class="form-group">
    <label for="realm" class="col-sm-3 control-label">{{ __('Realm') }}</label>
    <div class="col-sm-5">
        <select class="form-control" name="realm" id="realm" >
            @foreach ($realmDesc as $id => $desc)
                <option value="{{ $id }}" {{ Input::old('realm', (isset($data) ? $data->realm : '')) == $id ? 'selected' : '' }}>{{ __($desc) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('realm', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
<div class="form-group">
    <label for="sequence" class="col-sm-3 control-label">{{ __('Sequence') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="sequence" id="sequence" value="{{ Input::old('sequence', isset($data) ? $data->sequence : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('sequence', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
