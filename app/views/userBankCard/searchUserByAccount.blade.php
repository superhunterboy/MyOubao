@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $resourceName . __('Management') }}
@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')

    <div class="row">
      <div class="col-xs-3">
        <div class="h2" style="line-height: 32px;">{{ __('_function.search by bank card') }} </div>
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
                @foreach( $aColumnForList as $sColumn )
                    <th>{{ (__($sLangPrev . $sColumn, null, 3)) }} {{ order_by($sColumn) }}</th>
                @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td>{{ $data->username }}</td>
                    <td>{{ $data->parent_username }}</td>
                    <td>{{ $data->bank }}</td>
                    <td>{{ $data->province }}</td>
                    <td>{{ $data->city }}</td>
                    <td>{{ yes_no($data->is_blocked) }}</td>
                    <td>{{ $data->blocked_type }}</td>
                    <td>{{ yes_no($data->is_tester) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
    </div>
@stop



