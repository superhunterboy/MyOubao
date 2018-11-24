@extends('l.home')

@section('title')
   链接详情
@parent
@stop

@section('scripts')
@parent
    {{ script('jscroll')}}
    {{ script('datePicker')}}
@stop

@section('main')
<div class="nav-bg nav-bg-tab">
            <div class="title-normal">开户链接管理</div>
            <ul class="tab-title clearfix">
                <li><a href="agent-user-management.php"><span>用户管理</span></a></li>
                <li class="current"><a href="agent-link-management.php"><span>开户链接管理</span></a></li>
            </ul>
        </div>

        <div class="content">

            <div class="prompt">
                您当前查看的注册链接详情如下：<br />
                <a href="#">http://www.bomao.com/link/askdfjkasjf.html?/sjflksjlfkja22-1-10111421414214</a>
            </div>
            <div class="area-search">
                <p class="row">
                    开户类型：代理&nbsp;&nbsp;链接状态：已过期
                </p>
            </div>

            <div class="row-title">
                数字型奖金组详情：
            </div>
            <table width="100%" class="table table-toggle">
                <thead>
                    <tr>
                        <th>彩种类型/名称</th>
                        <th>奖金组</th>
                        <th>返点</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>重庆时时彩</td>
                        <td>1950</td>
                        <td>2.7%</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td>1950</td>
                        <td>2.7%</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td>1950</td>
                        <td>2.7%</td>
                    </tr>
                </tbody>
            </table>

            <div class="row-title">
                乐透型奖金组详情：
            </div>
            <table width="100%" class="table table-toggle">
                <thead>
                    <tr>
                        <th>彩种类型/名称</th>
                        <th>奖金组</th>
                        <th>返点</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>山东11选5</td>
                        <td>1950</td>
                        <td>2.7%</td>
                    </tr>
                    <tr>
                        <td>江西11选5</td>
                        <td>1950</td>
                        <td>2.7%</td>
                    </tr>
                    <tr>
                        <td>广东11选5</td>
                        <td>1950</td>
                        <td>2.7%</td>
                    </tr>
                </tbody>
            </table>



        </div>
@stop
