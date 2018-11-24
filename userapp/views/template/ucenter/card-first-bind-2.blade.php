@extends('l.home')

@section('title')
    确认新银行卡信息 -- 增加绑定
@parent
@stop

@section('scripts')
@parent
    {{ script('tip')}}
@stop

@section('main')
<div class="nav-bg">
            <div class="title-normal">
                银行卡绑定
            </div>
        </div>

        <div class="content">


            <div class="step">
                <table class="step-table">
                    <tbody>
                        <tr>
                            <td class="current"><div class="con"><i>1</i>输入银行卡信息</div></td>
                            <td class="current"><div class="tri"><div class="con"><i>2</i>确认银行卡信息</div></div></td>
                            <td><div class="tri"><div class="con"><i>3</i>绑定结果</div></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <form action="card-first-bind-3.php">
            <table width="100%" class="table-field">
                <tr>
                    <td align="right">开户银行：</td>
                    <td>中国农业银行</td>
                </tr>
                <tr>
                    <td align="right">开户银行区域：</td>
                    <td>广西&nbsp;&nbsp;北海</td>
                </tr>
                <tr>
                    <td align="right">支行名称：</td>
                    <td>中华街支行</td>
                </tr>
                <tr>
                    <td align="right">开户人姓名：</td>
                    <td>张麻子</td>
                </tr>
                <tr>
                    <td align="right">银行账号：</td>
                    <td>6225758303225555</td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td>
                        <input type="submit" value="确认提交" class="btn" id="J-submit">
                        <input type="button" value="返回上一步" class="btn btn-normal" id="J-button-back">
                    </td>
                </tr>
            </table>
            </form>


        </div>
@stop
@section('end')
    <script>
    (function($){

        $('#J-button-back').click(function(){
            history.back(-1);
        });

    })(jQuery);
</script>
@stop