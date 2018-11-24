@extends('l.home')

@section('title')
    银行卡管理
@parent
@stop

@section('main')
<div class="nav-bg">
            <div class="title-normal">
                银行卡管理
            </div>
        </div>

        <div class="content">
            <div class="prompt-text">
                一个游戏账户最多绑定 4 张银行卡， 您目前绑定了1张卡，还可以绑定3张。<br />
                银行卡信息锁定后，不能增加新卡绑定，已绑定的银行卡信息不能进行修改和删除。<br />
                为了您的账户资金安全，银行卡“新增”和“修改”将在操作完成2小时0分后，新卡才能发起“向平台提现”。
            </div>
            <table class="table">
                <tr>
                    <th>银行名称</th>
                    <th>卡号</th>
                    <th>绑定时间</th>
                    <th>银行卡状态</th>
                    <th>操作</th>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
                <tr>
                    <td>中国银行</td>
                    <td>**** **** **** **** 999</td>
                    <td>2014-05-27 16:45:56</td>
                    <td>尚未生效</td>
                    <td><a href="#">修改</a>  |  <a href="#">删除</a></td>
                </tr>
            </table>
            <br>
            <div class="prompt-text">
                提示：银行卡已锁定，解除锁定请联系客服。
            </div>


            <div class="alert alert-error alert-noresult">
                <i></i>
                <div class="txt">
                    <h4>您还没有绑定银行卡，<a href="#" class="btn">立即绑定</a></h4>
                </div>
            </div>


            <div class="area-search" style="margin-top:10px;">
                <p class="row">&nbsp;&nbsp;&nbsp;<a href="#" class="btn">增加绑定</a>  <a href="#" class="btn">锁定银行卡</a></p>
            </div>
        </div>
@stop