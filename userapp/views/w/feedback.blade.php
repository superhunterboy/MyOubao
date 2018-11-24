@if (SysConfig::readValue('sys_use_suggestion') == 1)
<div id="J-global-panel-feedback" class="global-panel-feedback">
    <i class="fa fa-comments"></i> 用户反馈
</div>
<input type="hidden" name="_token" id="J-global-token-value" value="{{ csrf_token() }}" />
<script type="text/template" id="J-template-feedback-text">
    <div class="global-feedback-window-cont">
        <div class="title-text">
            请提出您对于欧豹娱乐的意见和建议：
            <br />
            <span class="gray">(包括用户体验、界面、网络、活动政策等任何建议，1000字以内)</span>
        </div>
        <div>
            <textarea maxlength="1000" class="textarea feedback-textarea" id="J-text-feedback-value"></textarea>
            <div class="tip-text-length"><span id="J-text-feedback-length">0</span>/1000</div>
        </div>
    </div>
</script>
@endif