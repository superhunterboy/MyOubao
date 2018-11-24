<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>投注记录</title>
        @section ('styles')
            {{ style('global')}}
            <style type="text/css">
            .table{border-radius: 0px;
                background-image:-webkit-linear-gradient(top,rgba(0,0,0,0),rgba(0,0,0,0.1));
                background-image:-moz-linear-gradient(top,rgba(0,0,0,0),rgba(0,0,0,0.1));
                background-image:-o-linear-gradient(top,rgba(0,0,0,0),rgba(0,0,0,0.1));
                background-image:linear-gradient(top,rgba(0,0,0,0),rgba(0,0,0,0.1));
            }
            .table th{ background-color:#F3F3F3;color: #5A5757;border-bottom: 1px solid #D3D3D3;border-right-color: #D3D3D3}
            .table th:first-child,.table th:last-child,.table tfoot td:first-child,.table tfoot td:last-child{border-radius:0;}
            .table tbody{}
            .table thead{ position: fixed;}
            </style>
        @show

        @section('scripts')
            {{ script('jquery-1.9.1') }}
            {{ script('bomao.base') }}
            {{ script('bomao.Select') }}
            {{ script('bomao.Tip') }}
        @show
    </head>

    <body>
            <table width="100%" class="table" id="J-table">
    <thead>
        <tr>
            <th>投注时间2</th>
            <th>游戏</th>
            <th>注单编号</th>
            <th>奖期</th>
            <th>投注内容</th>
            <th>金额</th>
            <th>中奖金额</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>投注时间</th>
            <th>游戏</th>
            <th>注单编号</th>
            <th>奖期</th>
            <th>投注内容</th>
            <th>金额</th>
            <th>中奖金额</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        @foreach ($datas as $data)
        <tr>
            <td> {{ $data->created_at }} </td>
            <td> {{ $aLotteries[$data->lottery_id] }} </td>
            <td>
                <a class="view-detail" href="{{route('projects.view', $data->id)}}" target="_blank">{{ $data->serial_number_short }}</a><textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
            </td>
            <td> {{ $data->issue }} </td>
            <td>
                @if ( strlen( $data->display_bet_number) > 10 )
                    <a class="view-detail" href="javascript:void(0);">详细号码</a><textarea class="data-textarea" style="display:none;">{{ $data->display_bet_number }} </textarea>
                @else
                    {{ $data->display_bet_number }}
                @endif
            </td>
            <td>{{ $data->amount_formatted }} </td>
            <td>{{ $data->prize_formatted ? $data->prize_formatted : '--' }} </td>
            <td>{{ $data->formatted_status }} </td>
            <td>
                @if ($data->status == Project::STATUS_NORMAL)
                    <a id="cancelProject" href="javascript:void(0);">撤单</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@section('end')
<script>
(function($){
    var table = $('#J-table'),
        details = table.find('.view-detail'),
        tip = new bomao.Tip({cls:'j-ui-tip-l j-ui-tip-page-records'});


    details.hover(function(e){
        var el = $(this),
            text = el.parent().find('.data-textarea').val();
        tip.setText(text);
        tip.show(50, -(tip.getDom().height()/2), el);

        e.preventDefault();
    },function(){
        tip.hide();
    });

    //tabel is width []
    table.find('tbody tr:first').find('th').each(function(i){
        table.find('thead tr:first').find(' th').eq(i).width($(this).width());
    })

})(jQuery);
</script>
@show

    </body>
</html>

