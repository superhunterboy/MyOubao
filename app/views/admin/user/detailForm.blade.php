<div class="form-group">
    <label for="parent_id" class="col-sm-3 control-label">{{ __('Parent') }}</label>
    <div class="col-sm-5">
        <select class="form-control" name="parent_id" id="parent_id" >
            <option value=""></option>
            @foreach ($aParentIds as $id => $title)
                <option value="{{ $id }}" {{ Input::old('parent_id', isset($data) ? $data->parent_id : '') == $id ? 'selected' : ''}}>{{ $title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('parent_id', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="username" class="col-sm-3 control-label">*{{ __('User Name') }}</label>
    <div class="col-sm-5">
      <input type="text" class="form-control" id="username" name="username" value="{{ Input::old('username', isset($data) ? $data->username : '') }}"/>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('username', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="nickname" class="col-sm-3 control-label">*{{ __('Nick Name') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="nickname" id="nickname" value="{{ Input::old('nickname', isset($data) ? $data->nickname : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('nickname', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
<div class="form-group">
    <label for="email" class="col-sm-3 control-label">{{ __('Email') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="email" id="email" value="{{ Input::old('email', isset($data) ? $data->email : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('email', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
@if (!$isEdit)
<div class="form-group">
    <label for="password"  class="col-sm-3 control-label">*{{ __('Password') }}</label>

    <div class="col-sm-5">
        <input class="form-control"type="password" name="password" id="password" value="" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('password', '<label class="text-danger control-label">:message</label>') }}
    </div>

</div>

<div class="form-group">
    <label for="password_confirmation"  class="col-sm-3 control-label">*{{ __('Password Confirmation') }}</label>

    <div class="col-sm-5">
        <input class="form-control"type="password" name="password_confirmation" id="password_confirmation" value="" />
    </div>
    <!-- <div class="col-sm-4">
        {{ $errors->first('password', '<label class="text-danger">:message</label>') }}
    </div> -->
</div>

<div class="form-group">
    <label for="fund_password"  class="col-sm-3 control-label">*{{ __('Fund Password') }}</label>

    <div class="col-sm-5">
        <input class="form-control"type="password" name="fund_password" id="fund_password" value="" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('fund_password', '<label class="text-danger control-label">:message</label>') }}
    </div>

</div>
<div class="form-group">
    <label for="fund_password_confirmation"  class="col-sm-3 control-label">*{{ __('Fund Password Confirmation') }}</label>

    <div class="col-sm-5">
        <input class="form-control"type="password" name="fund_password_confirmation" id="fund_password_confirmation" value="" />
    </div>
    <!-- <div class="col-sm-4">
        {{ $errors->first('password', '<label class="text-danger">:message</label>') }}
    </div> -->
</div>
@endif
<div class="form-group">
    <label for="is_agent" class="col-sm-3 control-label">{{ __('Is Agent') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="is_agent" id="is_agent" value="1"
                {{ Input::old('is_agent', (isset($data) ? $data->is_agent : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('is_agent', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
    </div>
</div>
<div class="form-group">
    <label for="channel" class="col-sm-3 control-label">{{ __('Is Tester') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="is_tester" id="is_tester" value="1"
                {{ Input::old('is_tester', (isset($data) ? $data->is_tester : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('is_tester', '<span style="color:#c7254e;margin:0 1em;">:message</span>') }}
    </div>
</div>

