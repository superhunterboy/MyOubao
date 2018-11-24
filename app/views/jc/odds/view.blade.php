@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @foreach($aOddses as $method => $aOdds)
    <div class="col-xs-12 J-tab-chart">
        <table class="table table-striped table-hover table-bordered text-center">
            <thead>
            <tr>
                <th class="text-center" colspan="{{count($aOdds['code'])}}">{{$method}}</th>
            </tr>
            </thead>
            <tr>
                @foreach($aOdds['code'] as $v)
                <th class="text-center">{{$v}}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($aOdds['odds'] as $v)
                <td>{{$v}}</td>
                @endforeach

            </tr>

        </table>
    </div>
    @endforeach
@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
    </script>
@stop
