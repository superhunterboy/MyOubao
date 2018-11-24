@extends('l.home')

@section('title')
    用户管理
@parent
@stop

@section('scripts')
@parent
    {{ script('jscroll')}}
    {{ script('datePicker')}}
@stop

@section('main')
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">用户管理</div>
    <div class="title-info">
        <a href="agent-account-accurate.php" class="btn">精准开户</a>
        <a href="agent-account-link.php" class="btn">链接开户</a>
    </div>
    <ul class="tab-title clearfix">
        <li class="current"><a href="agent-user-management.php"><span>用户管理</span></a></li>
        <li><a href="agent-link-management.php"><span>开户链接管理</span></a></li>
    </ul>
</div>

<div class="content">
    <div class="area-search">
        <p class="row">
            用户组：<select id="J-select-user-groups" style="display:none;">
                      <option selected="selected" value="">全部用户</option>
                      <option value="1">代理用户</option>
                      <option value="2">玩家用户</option>
                </select>
            &nbsp;
            用户名：<input class="input w-2" type="text" value="" />
            &nbsp;&nbsp;
            用户余额：<input class="input w-1" type="text" value="" /> - <input class="input w-1" type="text" value="" /> 元
            &nbsp;&nbsp;
            <input class="btn" type="button" value=" 搜 索 " />
        </p>
    </div>

    <table width="100%" class="table table-toggle">
        <thead>
            <tr>
                <th><a href="#">用户名<i class="ico-up-down"></i></a></th>
                <th><a href="#">所属用户组</a></th>
                <th><a href="#">下级人数<i class="ico-up-current"></i></a></th>
                <th><a href="#">团队余额<i class="ico-down-current"></i></a></th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td>0</td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td><a href="#">10</a></td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td>0</td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td><a href="#">10</a></td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td>0</td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td><a href="#">10</a></td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td>0</td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
            <tr>
                <td><a href="#">个梵蒂冈和吃蛋黄</a></td>
                <td>代理</td>
                <td><a href="#">10</a></td>
                <td>0.00</td>
                <td>
                    <a href="#">账变列表</a>
                </td>
            </tr>
        </tbody>
    </table>

    @include('w.pages')

</div>
@stop

@section('end')
<script>
(function($){

    new bomao.Select({realDom:'#J-select-user-groups',cls:'w-2'});

})(jQuery);
</script>
@stop