@extends('l.admin', ['active' => $resource])
@section('title')
    @parent
    {{ $sPageTitle }}
@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')
    @include('w._function_title')


    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <table class="table table-striped table-hover">
        <tbody>
        @foreach ($datas as $data)
            @if($data->commission_type == 1)
                <tr>
                    <td>
                        下级每天首次充值金额<input type="text" value="{{$data->amount}}"  style="width:70px;">元以上，

                        并且达到<input type="text" value="{{$data->multiple}}" style="width:40px;"> 倍投注量，

                        上级送<input type="text" value="{{$data->return_money_1}}" style="width:40px;">元佣金，

                        上上级送<input type="text" value="{{$data->return_money_2}}" style="width:40px;">元佣金，

                        上上上级送<input type="text" value="{{$data->return_money_3}}" style="width:40px;">元佣金
                    </td>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>


    <table class="table table-striped table-hover">
        <tbody>
        @foreach ($datas as $data)
            @if($data->commission_type == 2)
                <tr>
                    <td>
                        下级每天消费达<input type="text" value="{{$data->amount}}"  style="width:70px;">元以上，

                        上级送<input type="text" value="{{$data->return_money_1}}" style="width:40px;">元佣金，

                        上上级送<input type="text" value="{{$data->return_money_2}}" style="width:40px;">元佣金，

                        上上上级送<input type="text" value="{{$data->return_money_3}}" style="width:40px;">元佣金
                    </td>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    <table class="table table-striped table-hover">
        <tbody>
        @foreach ($datas as $data)
            @if($data->commission_type == 3)
                <tr>
                    <td>
                        下级每天亏损达<input type="text" value="{{$data->amount}}"  style="width:70px;">元以上，

                        上级送<input type="text" value="{{$data->return_money_1}}" style="width:40px;">元佣金，

                        上上级送<input type="text" value="{{$data->return_money_2}}" style="width:40px;">元佣金，

                        上上上级送<input type="text" value="{{$data->return_money_3}}" style="width:40px;">元佣金
                    </td>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>


@stop



