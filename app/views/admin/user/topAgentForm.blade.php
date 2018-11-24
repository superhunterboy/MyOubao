<div class="form-group">
    <label for="username" class="col-sm-3 control-label">*{{ __('_user.username') }}</label>
    <div class="col-sm-5">
      <input type="text" class="form-control" id="username" name="username" value="{{ Input::old('username', isset($data) ? $data->username : '') }}"/>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('username', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

{{--<div class="form-group">--}}
    {{--<label for="nickname" class="col-sm-3 control-label">*{{ __('_user.nickname') }}</label>--}}
    {{--<div class="col-sm-5">--}}
        {{--<input class="form-control" type="text" name="nickname" id="nickname" value="{{ Input::old('nickname', isset($data) ? $data->nickname : '') }}" />--}}
    {{--</div>--}}
    {{--<div class="col-sm-4">--}}
        {{--{{ $errors->first('nickname', '<label class="text-danger control-label">:message</label>') }}--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div class="form-group">--}}
    {{--<label for="email" class="col-sm-3 control-label">*{{ __('_user.email') }}</label>--}}
    {{--<div class="col-sm-5">--}}
        {{--<input class="form-control" type="text" name="email" id="email" value="{{ Input::old('email', isset($data) ? $data->email : '') }}" />--}}
    {{--</div>--}}
    {{--<div class="col-sm-4">--}}
        {{--{{ $errors->first('email', '<label class="text-danger control-label">:message</label>') }}--}}
    {{--</div>--}}
{{--</div>--}}
<div class="form-group">
    <label for="prize_group" class="col-sm-3 control-label">*{{ __('_user.prize_group') }}</label>
    <div class="col-sm-5">
        <select name="prize_group" id="prize_group" class="form-control">
            @foreach($aTopAgentPrizeGroups as $iPrizeGroup)
            <option value="{{ $iPrizeGroup }}">{{ $iPrizeGroup }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('prize_group', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

@foreach($oSeriesSets as $oSeriesSet)
    <div class="form-group">
        <label for="commission_rate[]"  class="col-sm-3 control-label">{{ $oSeriesSet->name }}--返点</label>
        <input type="hidden" name="series_set_id[]" value="{{ $oSeriesSet->id }}" />
        <div class="col-sm-5">
        <input type="text" class="form-control" id="commission_rate[]" name="commission_rate[]" value=""/>
        </div>
    </div>
@endforeach

@if (!$isEdit)
<div class="form-group">
    <label for="password"  class="col-sm-3 control-label">*{{ __('_user.password') }}</label>

    <div class="col-sm-5">
        <input class="form-control"type="password" name="password" id="password" value="" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('password', '<label class="text-danger control-label">:message</label>') }}
    </div>

</div>

<div class="form-group">
    <label for="password_confirmation"  class="col-sm-3 control-label">*{{ __('_user.password_confirmation') }}</label>

    <div class="col-sm-5">
        <input class="form-control"type="password" name="password_confirmation" id="password_confirmation" value="" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('password_confirmation', '<label class="text-danger">:message</label>') }}
    </div>
</div>
@endif
<div class="form-group">
    <label for="channel" class="col-sm-3 control-label">{{ __('_user.is_tester') }}</label>
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

