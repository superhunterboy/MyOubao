@if ($message = Session::get('success'))
<div class="pop" id="popWindow" style="display:none;">
    <div class="pop-hd">
        <!-- <a href="#" class="pop-close"></a> -->
        <button type="button" class="pop-close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h3 class="pop-title">标题</h3>
    </div>
    <div class="pop-bd">
        <div class="pop-content">
            <i class="ico-success"></i>
            <p class="pop-text">{{ $message }}</p>
        </div>
        <div class="pop-btn">
            <input type="button" value="确 定" class="btn">
            <input type="button" value="取 消" class="btn btn-normal">
        </div>
    </div>
</div>
@endif

@if ($message = Session::get('error'))
<div class="pop" id="popWindow" style="display:none;">
    <div class="pop-hd">
        <a href="#" class="pop-close"></a>
        <h3 class="pop-title">标题</h3>
    </div>
    <div class="pop-bd">
        <div class="pop-content">
            <i class="ico-error"></i>
            <p class="pop-text">{{ $message }}</p>
        </div>
        <div class="pop-btn">
            <input type="button" value="确 定" class="btn">
            <input type="button" value="取 消" class="btn btn-normal">
        </div>
    </div>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="pop" id="popWindow" style="display:none;">
    <div class="pop-hd">
        <a href="#" class="pop-close"></a>
        <h3 class="pop-title">标题</h3>
    </div>
    <div class="pop-bd">
        <div class="pop-content">
            <i class="ico-waring"></i>
            <p class="pop-text">{{ $message }}</p>
        </div>
        <div class="pop-btn">
            <input type="button" value="确 定" class="btn">
            <input type="button" value="取 消" class="btn btn-normal">
        </div>
    </div>
</div>
@endif


@if ($message = Session::get('info'))
<div class="pop w-13" id="popWindow" style="display:none;">
    <div class="pop-hd">
        <a href="#" class="pop-close"></a>
        <h3 class="pop-title">信息确认</h3>
    </div>
    <div class="pop-bd">
        <div class="pop-content">
            <p class="pop-text">该用户的具体信息如下，是否立即开户？</p>
            <div class="bonusgroup-title">
                <table width="100%">
                    <tbody><tr>
                        <td>Terence2014<br><span class="tip">用户名称</span></td>
                        <td>特伦苏<br><span class="tip">用户昵称</span></td>
                        <td>代理<br><span class="tip">用户类型</span></td>
                        <td>66,888,888.00 元<br><span class="tip">可用余额</span></td>
                        <td class="last">66,888,888.00 元<br><span class="tip">奖金限额</span></td>
                    </tr>
                </tbody></table>
            </div>
        </div>
        <div class="pop-btn">
            <input type="button" value="确 定" class="btn">
            <input type="button" value="取 消" class="btn btn-normal">
        </div>
    </div>
</div>
@endif


<script type="text/javascript">
    (function(){
        if ($('#popWindow').length) {
            var popWindow = new bomao.Message();
            var data = {
                            title          : '提示',
                            content        : $('#popWindow').find('.pop-bd > .pop-content').html(),
                            isShowMask     : true,
                            closeIsShow    : true,
                            closeButtonText: '关闭',
                            closeFun       : function() {
                                this.hide();
                            }
            };
            popWindow.show(data);
        }
    })();
</script>

