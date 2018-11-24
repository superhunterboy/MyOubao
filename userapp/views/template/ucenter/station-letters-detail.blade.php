@extends('l.home')

@section('title')
   站内信
@parent
@stop

@section('main')
<div class="nav-bg">
    <div class="title-normal">站内信情页</div>
    <a id="J-button-goback" class="button-goback" href="#">返回</a>
</div>

<div class="content">
    <div class="article-page">
        <div class="article-page-title">
            <p>站内信标题</p>
            <p class="article-page-time">2014-05-26 14:01</p>
        </div>
        <div class="article-page-content">
            尊敬的客户您好：<br />
            好消息！好消息！<br />
            凤凰娱乐平台提现金额变更咯！最低限额由300元调整为100元，无最高提款限额，欢迎您来体验哦！<br />
            1.每个账户每天可提款的最高次数为3次，同时每张银行卡累计提款最高次数为3次。<br />
2.为了您的资金安全，建议您一次性将款项提走或者不同的账户绑定不同的银行卡，频繁提款不利于您的账户安全。<br />

3.如果您提款次数高于3次，平台将会退回您的提款至您的游戏账户内，您需要隔天重新发起提款申请。<br />

4.您充值成功后，需要参与游戏的金额达到充值金额的20%以上才能提款成功。<br />

5.到账时间：自您发起提款后的30分钟处理完毕，如因遇到网银系统问题或者其他不可抗力因素影响，到帐时间将会延迟。
        </div>
    </div>
</div>
@stop

@section('end')
@parent
<script>
(function($){
    $('#J-button-goback').click(function(e){
        history.back(-1);
        e.preventDefault();
    });
})(jQuery);
</script>
@stop