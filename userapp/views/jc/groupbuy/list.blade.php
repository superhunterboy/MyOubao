@extends('l.sports')


@section ('container')
@include('jc.header')




<div class="layout-main">
    <div class="container">
        <div class="inner">

            <div class="line-list-top"></div>


            <div class="list-tab">
                <ul class="list clearfix">
                    <li @if(!isset($sMethodGroupKey))class="active"@endif><a href="{{{ route('jc.groupbuy', [$sLotteryKey]) }}}">全部方案</a></li>
                    @foreach($aMethodGroup as $oMethodGroup)
                    <li @if($sMethodGroupKey == $oMethodGroup->identifier)class="active"@endif><a href="{{{ route('jc.groupbuy', [$sLotteryKey, $oMethodGroup->identifier]) }}}">{{{ $oMethodGroup->name }}}</a></li>
                    @endforeach

                </ul>
            </div>



            <div class="bet-confirm">
                <div class="search">
                    <form>
                    搜索发起人：
                    <input type="text" name="nickname" class="input" value="{{{ Input::get('nickname') }}}" placeholder="" />
                    &nbsp;&nbsp;
                    状态：
                   <select name="status" id="J-select-filter-status">
                        <option value="">全部</option>
                        @foreach($aStatus as $key => $val)
                        <option @if(Input::get('status') !== null && Input::get('status') !== '' && Input::get('status') == $key)selected="selected"@endif value="{{{ $key }}}">{{{ $val }}}</option>
                        @endforeach
                    </select>
                    &nbsp;&nbsp;
                    日期：
                    <select name='searchDate' id="J-select-filter-date">
                        @foreach($aDates as $sKey => $sDate)
                        <option @if (Input::get('searchDate') == $sKey)selected="selected"@endif value="{{{ $sKey }}}">{{{ $sDate }}}</option>
                        @endforeach
                    </select>
                    &nbsp;
                    <input type="submit" value=" 搜索 " class="button" />

                    </form>
                </div>
                <div class="uptable-tip c-yellow">【注】合买进度达到90%，网站自动保底 &nbsp;&nbsp;<i id="J-groupbuy-info-tip" class="fa fa-info-circle"></i></div>
                <table class="table table-group" id="J-table-list">
                    <tr>
                        <th>排序</th>
                        <th>发起人</th>
                        <th>战绩</th>
                        <th>玩法</th>
                        <th>
                            {{ custom_order_by('amount', '方案金额 <i class="fa fa-sort"></i><i class="fa fa-sort-desc"></i><i class="fa fa-sort-asc"></i>')}}
                        </th>
                        <th>{{ custom_order_by('progress', '<span class="c-green">方案进度</span> + 保底 <i class="fa fa-sort"></i><i class="fa fa-sort-desc"></i><i class="fa fa-sort-asc"></i>')}}</th>
                        <th>{{ custom_order_by('fee_rate', '提成 <i class="fa fa-sort"></i><i class="fa fa-sort-desc"></i><i class="fa fa-sort-asc"></i>')}}</th>
                        <th>奖金</th>
                        <th>状态</th>
                        <th>详情</th>
                    </tr>
                    <?php $index = $datas->getFrom(); ?>
                    @foreach($datas as $data)
                    <tr class="tr-status-{{{ $data->status }}}">
                        <td>
                            @if ($data->sequence > 0)
                            <span class="text-ding">顶</span>
                            @else
                            {{{ $index }}}
                            @endif
                            <?php $index++; ?>
                        </td>
                        <td>{{{ $data->display_nickname }}}</td>
                        <td>
                            @if (isset($aUserGrowth[$data->user_id]))
                            <a class="ct-rank-detail" href="{{{ route('jc.zj', [$oJcLottery->identifier, $data->user_id]) }}}">
                                @include('jc.groupbuy.star', ['oUserGrowth' => $aUserGrowth[$data->user_id]])
                            </a>
                            @endif
                        </td>
                        <td>
                            @if (isset($aMethodGroup[$data->method_group_id]))
                            {{{ $aMethodGroup[$data->method_group_id]->name }}}
                            @endif
                        </td>
                        <td>
                            <span class="c-yellow">{{{ number_format($data->amount, 2) }}}</span>
                        </td>
                        <td class="cell-bar">
                            <span class="bar">
                                <span class="bar-inner" style="width:{{{ $data->buy_percent }}};"></span>
                            </span>
                            <span class="text-pre">{{{ $data->buy_percent }}}</span>
                            @if(intval($data->guarantee_percent) > 0)
                                <span> + {{{ $data->guarantee_percent }}}</span>
                                <span class="text-baodi">保</span>
                            @endif
                            {{-- <span class="text-baodi">保底 {{ number_format($data->guarantee_amount, 2) }}元 ({{{ $data->guarantee_percent}}})</span> --}}
                            {{-- <span class="text-pre">{{{ $data->schedule }}}</span> --}}
                        </td>
                        <td>
                            {{ number_format($data->fee_rate * 100) }}%
                        </td>
                        <td>
                            @if ($data->userBet)
                            <span class="c-yellow">{{{ $data->userBet->prize > 0 ? number_format($data->userBet->prize, 4) : '' }}}</span>
                            @else
                            <span class="c-yellow">{{{ $data->prize > 0 ? number_format($data->prize, 4) : '' }}}</span>
                            @endif
                        </td>
                        <td>
                            {{{ $data->formatted_status }}}
                        </td>
                        <td class="cell-ct">
                            <a class="ct-link" target="_blank" href="{{{ route('jc.follow', $data->id) }}}">查看</a>
                            <!--
                            @if ($data->user_id == Session::get('user_id'))<a class="ct-link" href="{{ route('jc.drop', $data->id) }}">撤单</a>@endif
                            -->
                        </td>
                    </tr>
                    @endforeach
                </table>
                
                <span class="rules-tips">合买撤单、置顶规则&nbsp;&nbsp;<i id="rules-tips-content" class="fa fa-info-circle"></i></span>

                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
            </div>

            <div class="rules-explanation">
                <div class="rules-logo">
                    <span class="rules-lab">战绩规则说明</span>
                    <ul class="rules-group gold-group">
                        <li>
                            <span>金皇冠:</span>
                            <span class="logo-img gold-1"></span>
                        </li>
                        <li>
                            <span>金冠:</span>
                            <span class="logo-img gold-2"></span>
                        </li>
                        <li>
                            <span>金钻:</span>
                            <span class="logo-img gold-3"></span>
                        </li>
                        <li>
                            <span>金星:</span>
                            <span class="logo-img gold-4"></span>
                        </li>
                    </ul>

                    <ul class="rules-group silver-group">
                        <li>
                            <span>银皇冠:</span>
                            <span class="logo-img silver-1"></span>
                        </li>
                        <li>
                            <span>银冠:</span>
                            <span class="logo-img silver-2"></span>
                        </li>
                        <li>
                            <span>银钻:</span>
                            <span class="logo-img silver-3"></span>
                        </li>
                        <li>
                            <span>银星:</span>
                            <span class="logo-img silver-4"></span>
                        </li>
                    </ul>
                </div>
                <div class="program-type">
                    <div class="explantion-box">
                        <span class="explantion-title">成功方案</span>
                        <span class="explantion-content">
                            <span class="content-lab">1金皇冠=10金冠，1金冠=10金钻，1金钻=10金星</span>
                            <span class="content-lab">1金星：单个成功方案盈利≥500元或回报超过10倍</span>
                        </span>
                    </div>
                    <div class="explantion-box">
                        <span class="explantion-title">流产方案</span>
                        <span class="explantion-content">
                            <span class="content-lab">1银皇冠=10银冠，1银冠=10银钻，1银钻=10银星</span>
                            <span class="content-lab">1银星：单个成功方案盈利≥1000元或回报超过10倍</span>
                        </span>
                    </div>
                    <div class="explantion-box">
                        <span class="explantion-title">流单方案</span>
                        <span class="explantion-content">
                            <span class="content-lab">每个用户每期（竞彩每日）流产方案不超过3个才有</span>
                            <span class="content-lab">机会获得银星，最多获得一个银星，按最高级别评定</span>
                        </span>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>





@include('jc.rank-detail')


@include('w.footer')
@stop


@section('end')
@parent
<script>
(function($, host){
    var selectStatus = new host.Select({realDom:'#J-select-filter-status', cls:'w-2'}),
        selectDate = new host.Select({realDom:'#J-select-filter-date', cls:'w-2'}),
        TIP = host.Tip.getInstance();

    
    $('#J-groupbuy-info-tip').hover(function(){
        TIP.setText('享受保底的方案，保底部分的中奖提成将不计算在内');
        TIP.show(25, TIP.dom.height()/2*-1, this);
    },function(){
        TIP.hide();
    });

    $('#rules-tips-content').hover(function(){
        var tip_str = '一、注单撤单规则</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'1. 进度<50%，发起人可对整个方案进行撤单，跟单人也可撤销自己的认购；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'2. 进度≥50%后，无论是发起人撤整个方案，还是跟单人撤自己的认购皆不能撤单；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'3. 撤销整个方案后，认购金额将原数返还到参与该方案的会员的帐户中；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'4. 撤销自己的认购，认购金额将原数返还到该会员的帐户中。</br>'+
                      '二、合买置顶规则</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'所谓置顶，就是把其方案排在所有合买方案的前列，置顶的方案以“顶”为标识显示。</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'1. 置顶方案总数最多为10个；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'2. 若同时满足置顶条件的方案>10个，将根据方案金额及进度、战绩及等情况，推荐其中10个方案优先排序；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'3. 置顶方案中每个用户每期不能超过3个未满员方案；排序规则按照进度、方案金额大小进行排序；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'4. 置顶申请条件：合买方案金额≥800元，且（进度+保底）≥50%；</br>'+
                          '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+'5. 如需置顶，请与客服联系。';
        TIP.setText(tip_str);
        TIP.show(25, TIP.dom.height()/2*-1, this);
    },function(){
        TIP.hide();
    });


})(jQuery, bomao);
</script>
@stop










