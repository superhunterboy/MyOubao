@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])
    <table class="table table-bordered table-striped">
        <tbody>


    @if(!empty($sParentTitle))
        <tr>
            <th  class="text-right col-xs-2">{{ __('_basic.parent',null,2) }}</th>
            <td>{{{ $sParentTitle }}}</td>
        </tr>
    @endif
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'id', null, 2) }}</th>
            <td>{{{ $data->id }}}</td>
        </tr>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'qq', null, 2) }}</th>
            <td>{{{ $data->qq }}}</td>
        </tr>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'platform', null, 2) }}</th>
            <td>{{{ $data->platform }}}</td>
        </tr>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'sale', null, 2) }}</th>
            <td>{{{ ${$aColumnSettings['sale']['options']}[ $data->sale] }}}</td>
        </tr>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'sale_screenshot_path', null, 2) }}</th>
            <td><img src="http://{{ $data->sale_screenshot_path }}"></td>
        </tr>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'created_at', null, 2) }}</th>
            <td>{{{ $data->created_at }}}</td>
        </tr>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . 'updated_at', null, 2) }}</th>
            <td>{{{ $data->updated_at }}}</td>
        </tr>


        {{--<tr>--}}
            {{--<th  class="text-right col-xs-2">{{ __($sLangPrev . $sColumn, null, 2) }}</th>--}}
            {{--<td>{{{ $sDisplayValue }}}</td>--}}
        {{--</tr>--}}

    </tbody>
    </table>
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
