@extends('l.home')

@section('title')
   个人资料
@parent
@stop



@section('main')
<div class="nav-bg">
    <div class="title-normal">个人资料</div>
</div>

<div class="content">
    <form action="{{ route('users.personal') }}" method="post" id="J-form-login">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="_method" value="PUT" />
        <div class="title-field">

        </div>
        <table width="100%" class="table-field">
            <tr>
                <td align="right" style="width:150px;">用户名：</td>
                <td>
                     {{ Session::get('username') }}
                </td>
            </tr>
            <!-- <tr>
                <td align="right">邮箱：</td>
                <td>
                    {{ $data->email }} &nbsp;&nbsp;
                @if ( $data->isActivated())
                     <span class="c-green">已绑定</span>
                @else
                     <a href="{{ route('users.bind-email') }}" title="点击绑定邮箱">未绑定</a>
                @endif
                </td>
            </tr> -->
            <tr>
                <td align="right">昵称：</td>
                <td>
                    <input id="J-input-nickname" type="text" class="input w-2" name="nickname" value="{{ $data->nickname }}">
                    &nbsp;&nbsp;
                    <span class="tip">由2至16个字符组成</span>
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td>
                    <input id="J-button-submit" type="submit" value="保存" class="btn" />
                </td>
            </tr>
        </table>
    </form>

</div>
@stop

@section('end')
@parent
<script>
(function($){

    $('#J-button-submit').click(function(){
        var v = $.trim($('#J-input-nickname').val());
        if(v.length < 2 || v.length > 16){
            alert('昵称必须由2至6个字符组成，请重新输入');
            $('#J-input-nickname').focus();
            return false;
        }
        return true;
    });

})(jQuery);
</script>
@stop