@extends('l.home')

@section('title')
开户链接管理
@parent
@stop




@section('main')
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">开户链接管理</div>
    <div class="title-info">
        <a href="{{ route('users.accurate-create') }}" class="btn">精准开户</a>
        <a href="{{ route('user-links.create') }}" class="btn">链接开户</a>
    </div>
    <ul class="tab-title clearfix">
        
                    <li><a href="{{ route('user-profits.index') }}">团队盈亏</a></li>
                    <li><a href="{{ route('user-transactions.mycommission',Session::get('user_id')) }}">佣金报表</a></li>
                    <li><a href="{{ route('user-profits.bonus') }}">分红报表</a></li>
                    <li><a href="{{ route('users.index') }}">团队管理</a></li>
                    <li><a href="{{ route('users.accurate-create') }}">下级开户</a></li>
                     @if(Session::get('show_overlimit'))
                    <li><a href="{{ route('my-overlimit-quotas.index') }}">高点配额</a></li>
                    @endif

    </ul>
</div>

<div class="content">
    
    @include('centerUser.link_agent._list')
    {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
</div>
@stop

@section('end')
@parent
<script>
(function($){
    var table = $('#J-table');
    table.find('.agent-link-name').click(function(e){
        var el = $(this),
            id = $.trim(el.attr('data-id')),
            ico = el.find('i');
        if(ico.hasClass('ico-fold')){
            table.find('.ico-unfold').removeClass('ico-unfold').addClass('ico-fold');
            table.find('.table-tr-item').addClass('table-tr-hidden');
            table.find('.table-tr-pid-' + id).removeClass('table-tr-hidden');
            //table.find('.ico-fold').replaceClass('ico-unfold', 'ico-fold');
            ico.removeClass('ico-fold').addClass('ico-unfold');
        }else{
            ico.removeClass('ico-unfold').addClass('ico-fold');
            table.find('.table-tr-pid-' + id).addClass('table-tr-hidden');
        }
        e.preventDefault();
    });
    $('.confirmDelete').click(function(event) {
        var url = $(this).attr('url');
        if (confirm('确定关闭该开户链接？')) {
            location.href = url;
        }
    });
})(jQuery);
</script>
@stop