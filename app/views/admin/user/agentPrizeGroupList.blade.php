@extends('l.admin', ['active' => $resource])
@section('title')
@parent

@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')

    <div class="row">
      <div class="col-xs-3">
        <div class="h2" style="line-height: 32px;">{{ __('_function.agent prize groups') }} </div>
      </div>
    </div>

    <hr>
<?php
//pr($functionality_id);
//exit;
?>
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <div class="col-xs-12">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __('_user.username') }} {{ order_by('username') }}</th>
                    <th>{{ __('_user.is_agent') }}</th>
                    <th>{{ __('_userprofit.prize group') }} {{ order_by('prize_group') }}</th>
                    <th>{{ __('Actions') }} </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td>{{ $data->username }}</td>
                    <td>{{ $data->user_type_formatted }}</td>
                    <td>{{ $data->prize_group }}</td>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
    </div>
@stop



