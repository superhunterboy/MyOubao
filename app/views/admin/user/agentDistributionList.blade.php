@extends('l.admin', ['active' => $resource])

@section('title')
@parent
@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')

    <div class="row">
      <div class="col-xs-3">
        <div class="h2" style="line-height: 32px;">{{ __('_function.agent distribution') }} </div>
      </div>
      <div class="col-xs-9">
          @include('w.page_link')
      </div>
    </div>
    <hr>
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <div class="col-xs-12">

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __('_userprofit.prize group') }} {{ order_by('prize_group') }}</th>
                    <th>{{ __('_user.agent-count') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td>{{ $data->prize_group }}</td>
                    <td>{{ $data->num }}</td>
                    <td>
                    @foreach ($buttons['itemButtons'] as $element)
                        @if ($element->isAvailable($data))
                        <a  href="{{ $element->route_name ? route($element->route_name, [$element->para_name => $data->prize_group]) : 'javascript:void(0);'
        }}" class="btn btn-xs btn-embossed btn-default" > {{ __( $element->label) }}</a>
                        @endif
                    @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@stop