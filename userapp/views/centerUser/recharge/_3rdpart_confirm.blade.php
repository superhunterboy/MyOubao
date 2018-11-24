<div class="nav-inner">
	    <div class="title-normal">
	         快捷充值
	    </div>
	</div>
<div class="content">
    <div class="prompt" style="background-position:10px 10px;padding:60px 40px;">
        
请在新打开的银行支付页面进行充值操作。
<br />
如果您的浏览器未弹出新的银行支付页面，请取消浏览器对弹出页面的阻拦，并选择允许（信任）网站的弹窗。
<br />
		<a href="{{$oUserDeposit->break_url}}" target="_blank" class="btn">点击前往快捷支付</a>
        <a href="/" class="btn">前往首页</a>
    </div>
</div>

<script>
var win = window.open('{{$oUserDeposit->break_url}}', 'deposit_win', 'height=600, width=1000, toolbar=no, menubar=no, location=no, status=no, scrollbars=yes');
</script>