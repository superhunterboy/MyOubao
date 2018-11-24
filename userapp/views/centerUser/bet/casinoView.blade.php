@extends('l.home')

@section('title')
注单详情
@parent
@stop


@section ('main')



<div class="nav-inner clearfix">
    <ul class="list">
        <li><a href="{{ route('projects.index') }}?jc_type=casino&mode=single">返回列表</a></li>
        <li class="active"><a href="">游戏详情</a></li>
    </ul>

</div>


<div class="content">
    <div class="area-search" style="background:#FFF;">


        <div class="openball-result">
            <?php
                $Cards = [];
                if(!empty($data->player_number))
                    $Cards = explode(',',$data->player_number);
            ?>

            @foreach($Cards as $card)

                <?php $cardnum = str_split($card);$index = ($cardnum[0]-1)*13+(int)($cardnum[1].$cardnum[2]);?>
            <span class="item item-bjl item-bjl-{{$index}}">{{$cardnum[1].$cardnum[2]}}</span>
            @endforeach

            <span class="item item-bjl item-bjl-0">|</span>
                <?php
                $Cards = [];
                if(!empty($data->player_number))
                $Cards = explode(',',$oManProject->banker_number);
                ?>

                @foreach($Cards as $card)
                    <?php $cardnum = str_split($card);$index = ($cardnum[0]-1)*13+(int)($cardnum[1].$cardnum[2]);?>
                    <span class="item item-bjl item-bjl-{{$index}}">{{$cardnum[1].$cardnum[2]}}</span>
                @endforeach

        </div>

        <table width="100%" class="table-detail">
            <tr>
                <td width="400" align="right">游戏：</td>
                <td><span class="value">{{ $data->game_title }}</span></td>
                <td align="right">注单编号：</td>
                <td><span class="value">{{ $data->serial_number }}</span></td>
            </tr>
            <tr>
                <td align="right">玩法：</td>
                <td><span class="value"> {{ $data->method_title }}</span></td>
                <td align="right">投注时间：</td>
                <td><span class="value">{{ $data->created_at }}</span></td>
            </tr>


            <tr>
                <td align="right">状态：</td>
                <td><span class="value">{{ $data->formatted_status }}</span></td>
                <td align="right">奖金：</td>
                <td>
                    <span class="value"> {{ $data->prize_formatted }}</span>
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td></td>
                <td align="right">投注额：</td>
                <td>
                    <span class="value"> {{ $data->amount_formatted }}</span>
                </td>
            </tr>


        </table>


        <div class="detail-row-cont">
            <div class="title">投注内容

            </div>
            <textarea disabled="disabled" class="textarea-lotterys-detail input">{{ $data->way_title }}</textarea>
        </div>




        <p class="row align-center">
            <a class="btn goback" href="{{ route('projects.index') }}?jc_type=casino&mode=single">返回</a>


        </p>



    </div>

</div>
@stop

@section('end')
@parent
<script>
    (function($){
        // $('#cancelProject').click(function(event) {
        //     if (confirm('')) {
        //         location.href = "{{ URL::route('projects.drop',['id' => $data->id]) }}";
        //     }
        // });
        $('#cancelProject').click(function(event) {
            var popWindowNew = bomao.Message.getInstance();
            var data = {
                title          : '确认',
                content        : "<i class=\"ico-waring\"></i><p class=\"pop-text\">您确认撤单么？</p>",
                isShowMask     : true,
                cancelIsShow   : true,
                confirmIsShow  : true,
                cancelButtonText: '取消',
                confirmButtonText: '确认',
                confirmFun     : function() {
                    location.href = "{{ route('projects.drop',['id' => $data->id]) }}";
                },
                cancelFun      : function() {
                    this.hide();
                }
            };
            popWindowNew.show(data);
        });
        // debugger;

//   $('#J-button-goback , .goback').click(function(e){
//     history.back(-1);
//     e.preventDefault();
//   });
    })(jQuery);

</script>
@stop