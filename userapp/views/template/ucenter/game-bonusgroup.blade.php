@extends('l.home')

@section('title')
    我的奖金
@parent
@stop

@section('main')
<div class="nav-bg">
            <div class="title-normal">
                我的奖金
            </div>
        </div>

        <div class="content">
            <div class="bonusgroup-title">
                <table width="100%">
                    <tr>
                        <td>Terence2014<br /><span class="tip">用户名称</span></td>
                        <td>特伦苏<br /><span class="tip">用户昵称</span></td>
                        <td>代理<br /><span class="tip">用户类型</span></td>
                        <td>66,888,888.00 元<br /><span class="tip">可用余额</span></td>
                        <td class="last">66,888,888.00 元<br /><span class="tip">奖金限额</span></td>
                    </tr>
                </table>
            </div>

            <div class="row-title">
                奖金组
            </div>
            <div class="bonusgroup-game-type">
                            <ul class="clearfix gametype-row">
                                <li class="item-all current"><a class="item-game" href="#" data-id="1" data-itemtype="all"><span class="name">全部时时彩</span></a></li>
                                <li>
                                    <a href="#" class="item-game" data-id="1" data-itemtype="game"><span class="name">重庆时时彩</span><span class="group">1800</span></a>
                                </li>

                                <li>
                                    <a href="#" class="item-game" data-id="2" data-itemtype="game"><span class="name">天津时时彩</span><span class="group">1800</span></a>
                                </li>

                                <li>
                                    <a href="#" class="item-game" data-id="3" data-itemtype="game"><span class="name">上海时时彩</span><span class="group">1800</span></a>
                                </li>

                                <li>
                                    <a href="#" class="item-game" data-id="4" data-itemtype="game"><span class="name">黑龙江时时彩</span><span class="group">1800</span></a>
                                </li>

                                <li>
                                    <a href="#" class="item-game" data-id="5" data-itemtype="game"><span class="name">江西时时彩</span><span class="group">1800</span></a>
                                </li>

                            </ul>


                            <ul class="clearfix gametype-row">
                            <li class="item-all"><a class="item-game" href="#" data-id="2" data-itemtype="all"><span class="name">全部11选5</span></a></li>
                            <li>
                                <a href="#" class="item-game" data-id="6" data-itemtype="game"><span class="name">上海11选5</span><span class="group">1800</span></a>
                            </li>

                            <li>
                                <a href="#" class="item-game" data-id="7" data-itemtype="game"><span class="name">山东11选5</span><span class="group">1800</span></a>
                            </li>

                            </ul>




            </div>
            <div class="bonusgroup-title">
                <table width="100%">
                    <tr>
                        <td>1950<br /><span class="tip">&nbsp;&nbsp;&nbsp;当前奖金&nbsp;&nbsp;&nbsp;</span></td>
                    </tr>
                </table>
            </div>
            <div class="row-title">
                玩法奖金详情
            </div>

            <table class="table table-rowspan" width="100%" style="margin-top:10px;">
                <thead>
                    <tr>
                        <th>玩法群</th>
                        <th>玩法组</th>
                        <th>奖级</th>
                        <th>奖金</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="15">五星</td>
                        <td rowspan="2">直选</td>
                        <td>复式</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>单式</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td rowspan="6">组选</td>
                        <td>组选120（杂牌）</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组选60（对子）</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组选30（两对）</td>
                        <td>70</td>
                    </tr>
                    <tr>
                        <td>组选20（三条）</td>
                        <td>50</td>
                    </tr>
                    <tr>
                        <td>组选10（葫芦）</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td>组选10（葫芦）</td>
                        <td>10</td>
                    </tr>
                    <tr>
                        <td rowspan="3">不定位</td>
                        <td>一码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>二码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>三码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td rowspan="4">趣味</td>
                        <td>一帆风顺</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>好事成双</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>三星报喜</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>四季发财</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td rowspan="8">四星</td>
                        <td rowspan="2">直选</td>
                        <td>复式</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td>单式</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td rowspan="4">组选</td>
                        <td>组选24</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组选12</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组选6</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组选4</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td rowspan="2">不定位</td>
                        <td>一码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>二码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td rowspan="15">后三</td>
                        <td rowspan="4">直选</td>
                        <td>复式</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>单式</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>和值</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>跨度</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td rowspan="7">组选</td>
                        <td>组三</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组六</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组三单式</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>组六单式</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>混合组选</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>和值</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>包胆</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td rowspan="4">不定位</td>
                        <td>一码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>二码不定位</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>和值</td>
                        <td>无限制</td>
                    </tr>
                    <tr>
                        <td>包胆</td>
                        <td>无限制</td>
                    </tr>
                </tbody>
            </table>
        </div>
@stop