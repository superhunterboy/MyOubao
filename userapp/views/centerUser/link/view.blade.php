@extends('l.home')

@section('title')
    链接开户
@stop

@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
@stop



@section ('container')
    
    @include('w.header')

    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>



    <div class="page-content">
        <div class="g_main clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                <ul class="list clearfix">
                    <li><a href="{{ route('user-links.create') }}#list">返 回</a></li>
                    <li class="active"><a href="">开户链接详情</a></li>
                </ul>
            </div>


            <div class="page-content-inner">



    <div class="row-tip" style="background:#EEE;">
        <p class="row">
            开户类型：<b style="color:#F60">{{ $data->{$aListColumnMaps['is_agent']} }}</b>
            &nbsp;&nbsp;链接状态：<b style="color:#F60">{{ $data->{$aListColumnMaps['status']} }}</b>
        
            &nbsp;&nbsp;&nbsp;&nbsp;链接地址：
        <a style="text-decoration: underline;" target="_blank" href="{{ (strpos($data->url, 'http') === false ? 'http://' : '') . $data->url }}">{{ $data->url }}</a>
        &nbsp;&nbsp;
        <button id="J-button-copy" class="btn" data-url="{{ (strpos($data->url, 'http') === false ? 'http://' : '') . $data->url }}">复 制</button>
        </p>
        
    </div>

    <?php
    $iPrizeGroup = UserUser::find($data->user_id)->prize_group;
    $aPrizeGroupSets = json_decode(($data->prize_group_sets));
    $iSubPrizeGroup = $aPrizeGroupSets[0]->prize_group;
    $water = ($iPrizeGroup - $iSubPrizeGroup)/2000*100;
    ?>

    @foreach ($aSeriesLotteries as $aSeries)
    <div class="row-title" style="padding:20px;">
        {{ $aSeries['name'] }}奖金组详情：
    </div>
    <table width="100%" class="table table-toggle">
        <thead>
            <tr>
                <th>彩种类型/名称</th>
                <th>奖金组</th>
                @if ($data->is_agent)
                <th>返点</th>
                @endif
            </tr>
        </thead>
        <tbody>

        @if (isset($aSeries['children']) && $aSeries['children'])
            @foreach ($aSeries['children'] as $aLottery)
            <?php
                // pr($data->prize_group_sets_json);exit;
                //$aPres = ['lottery_id_', 'series_id_'];
                //$sPre  = $aPres[$data->is_agent];
                //$sPre .= $data->is_agent ? $aSeries['id'] : $aLottery['id'];
            //$aPrizeGroup = $data->prize_group_sets_json;
            ?>
{{--            @if ($aPrizeGroup = (isset( $data->prize_group_sets_json[$sPre] ) ? $data->prize_group_sets_json[$sPre] : ''))--}}
            <tr>
                <td>{{ $aLottery['name'] }}</td>
                <td>{{ $iSubPrizeGroup }}</td>
                @if ($data->is_agent)
                <td>{{ $water }}%</td>
                @endif
            </tr>
            {{--@endif--}}
            @endforeach
        @endif
            <!-- <tr>
                <td>重庆时时彩</td>
                <td>1950</td>
                <td>2.7%</td>
            </tr>
            <tr>
                <td>重庆时时彩</td>
                <td>1950</td>
                <td>2.7%</td>
            </tr> -->
        </tbody>
    </table>
    @endforeach
    <br /><br />




            </div>
        </div>
    </div>


    @include('w.footer')
@stop



@section('end')
@parent
{{ script('ZeroClipboard')}}
<script>

(function($){
  ZeroClipboard.setMoviePath('/assets/js/ZeroClipboard.swf');
  var tip = new bomao.Tip({cls:'j-ui-tip-r j-ui-tip-success'});
  var clip_link,timer,
    fn = function(client){
      var el = $(client.domElement),value = el.attr('data-url');
      client.setText(value);
      tip.setText('已复制');
      tip.show(-70, -3, el);
      
      clearTimeout(timer);
      timer = setTimeout(function(){
        tip.hide();
      }, 1000);

    };


   clip_link = new ZeroClipboard.Client();
   clip_link.setCSSEffects( true );
   clip_link.addEventListener( "mouseUp", fn);
   clip_link.glue('J-button-copy');
  





})(jQuery);
</script>
@stop


