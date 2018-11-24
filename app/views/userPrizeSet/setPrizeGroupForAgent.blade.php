@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Modify User Prize Group') }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.page_link')
    @include('w.notification')
    <div class="row">
      <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('Modify User Prize Group') }}</div>
      </div>
    </div>
    <hr>

    <form class="form-horizontal" method="post" action="{{ route($resource . '.set-agent-prize-group', $id) }}" autocomplete="off">

        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="form-group">
            <label for="username" class="col-sm-3 control-label">{{ __('User Name') }}</label>
            <div class="col-sm-5">
                {{ isset($data) ? $data->username : '' }}
            </div>
        </div>
        <div class="form-group">
            <label for="prize_group" class="col-sm-3 control-label">{{ __('Exist Prize Group') }}</label>
            <div class="col-sm-5">
                {{ isset($data) ? $data->prize_group : '' }}
            </div>
        </div>
        <div class="form-group">
            <label for="prize_group" class="col-sm-3 control-label">{{ __('New Prize Group') }}</label>
            <div class="col-sm-5">
                <select class="form-control" name="prize_group" id="prize_group" >
                    <option value="">{{ __('Please Select') }}</option>
                    @foreach ($aLimitPrizeGroups as $iPrizeGroup)
                        <option value="{{ $iPrizeGroup }}" >{{ $iPrizeGroup }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                {{ $errors->first('prize_group', '<label class="text-danger control-label">:message</label>') }}
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-3 control-label">{{ __('Description') }}</label>
            <div class="col-sm-5">
                <input type="text" name="description" style="width:578px;" value=" {{ isset($data) ? $data->description : '' }} " >
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
              <a class="btn btn-default" href="{{ route('users.agent-prize-group', ['is_agent' => 1]) }}">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>

@stop