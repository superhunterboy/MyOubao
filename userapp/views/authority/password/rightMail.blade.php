<!-- 邮件发送成功开始 -->
			<div class="panel-tip panel-tip-right">
				<div class="title">
					找回成功
				</div>
				<div class="text">
					&nbsp;已经向您的邮箱：<span class="text-email">tere*******@qq.com</span>发送了一封确认邮件，请前往查看并按提示完成找回密码流程！
					<div class="text-gray">（您的激活链接在24小时内有效）
					</div>
				</div>
				<div class="control">
					<a id="J-button-resend" href="#" class="btn btn-disable"><span id="J-resend-time">60</span> 秒后可重新发送</a>
				</div>
			</div>
					<script>
					(function(){
						var timer,doTimer,timeNum = 5;
						doTimer = function(){
							var dom = $('#J-resend-time'),
								num = timeNum;
							timer = setInterval(function(){
								num--;
								if(num <= 0){
									clearInterval(timer);
									dom.parent().text('没收到邮件？重新发送').removeClass('btn-disable');
								}else{
									dom.text(num);
								}
							}, 1000);
						};
						$('#J-button-resend').click(function(e){
							var el = $(this);
							e.preventDefault();
							if(el.hasClass('btn-disable')){
								return;
							}
							$.ajax({
								url:'?',
								dataType:'json',
								success:function(data){
									if(Number(data['isSuccess']) == 1){
										alert('重新发送验证邮件成功');
										el.html('<span id="J-resend-time">60</span> 秒后可重新发送').addClass('btn-disable');
										doTimer();
									}else{
										alert(data['msg'] || '发送失败，请重试');
									}
								},
								error:function(xhr, type){
									alert('发送失败，请重试');
								}
							});
						});
						doTimer();
					})();
					</script>
		<!-- 邮件发送成功结束 -->