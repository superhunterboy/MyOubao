@extends('l.home')

@section('title')
    输入新银行卡信息 -- 增加绑定
@parent
@stop

@section('scripts')
@parent
    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
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
                            <td class="clicked"><div class="con"><i>1</i>验证老银行卡</div></td>
                            <td class="current"><div class="tri"><div class="con"><i>2</i>输入新银行卡信息</div></div></td>
                            <td><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                            <td><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <form action="card-add-bind-3.php" method="post" id="J-form">
            <table width="100%" class="table-field">
                <tr>
                    <td align="right">开户银行：</td>
                    <td>
                        <select id="J-select-bank-name" style="display:none;">
                            <option value="0" selected="selected">请选择开户银行</option>
                            <option value="1">招商银行</option>
                            <option value="2">中国银行</option>
                            <option value="3">建设银行</option>
                            <option value="4">浦发银行</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right">开户银行区域：</td>
                    <td>
                        <select id="J-select-province" style="display:none;">
                            <option value="0" selected="selected">请选择省份</option>
                            <option value="北京">北京市</option>
                            <option value="浙江省">浙江省</option>
                            <option value="天津市">天津市</option>
                            <option value="安徽省">安徽省</option>
                            <option value="上海市">上海市</option>
                                                  <option value="福建省">福建省</option>
                                                  <option value="重庆市">重庆市</option>
                                                  <option value="江西省">江西省</option>
                                                  <option value="山东省">山东省</option>
                                                  <option value="河南省">河南省</option>
                                                  <option value="湖北省">湖北省</option>
                                                  <option value="湖南省">湖南省</option>
                                                  <option value="广东省">广东省</option>
                                                  <option value="海南省">海南省</option>
                                                  <option value="山西省">山西省</option>
                                                  <option value="青海省">青海省</option>
                                                  <option value="江苏省">江苏省</option>
                                                  <option value="辽宁省">辽宁省</option>
                                                  <option value="吉林省">吉林省</option>
                                                  <option value="台湾省">台湾省</option>
                                                  <option value="河北省">河北省</option>
                                                  <option value="贵州省">贵州省</option>
                                                  <option value="四川省">四川省</option>
                                                  <option value="云南省">云南省</option>
                                                  <option value="陕西省">陕西省</option>
                                                  <option value="甘肃省">甘肃省</option>
                                                  <option value="黑龙江省">黑龙江省</option>
                                                  <option value="香港特别行政区">香港</option>
                                                  <option value="澳门特别行政区">澳门</option>
                                                  <option value="广西壮族自治区">广西</option>
                                                  <option value="宁夏回族自治区">宁夏</option>
                                                  <option value="新疆维吾尔自治区">新疆</option>
                                                  <option value="内蒙古自治区">内蒙古区</option>
                                                  <option value="西藏自治区">西藏</option>
                        </select>
                        &nbsp;
                        <select id="J-select-city" style="display:none;">
                            <option value="0" selected="selected">请选择城市</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right">支行名称：</td>
                    <td>
                        <input type="text" class="input w-3" id="J-input-bankname">
                        <span class="ui-text-prompt">由1至20个字符或汉字组成，不能使用特殊字符</span>
                    </td>
                </tr>
                <tr>
                    <td align="right">开户人姓名：</td>
                    <td>
                        <input type="text" class="input w-3" id="J-input-name">
                        <span class="ui-text-prompt">由1至20个字符或汉字组成，不能使用特殊字符</span>
                    </td>
                </tr>
                <tr>
                    <td align="right">银行账号：</td>
                    <td>
                        <input type="text" class="input w-3" id="J-input-card-number">
                        <span class="ui-text-prompt">银行卡卡号由16位或19位数字组成</span>
                    </td>
                </tr>
                <tr>
                    <td align="right">确认银行账号：</td>
                    <td>
                        <input type="text" class="input w-3" id="J-input-card-number2">
                        <span class="ui-text-prompt">银行账号只能手动输入，不能粘贴</span>
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td>
                        <input type="submit" value="下一步" class="btn" id="J-submit">
                    </td>
                </tr>
            </table>
            </form>

        </div>
@stop

@srction('end')
<script>
    (function($){
        var tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
            cardInput = $('#J-input-card-number, #J-input-card-number2'),
            bankSelect = new bomao.Select({realDom:'#J-select-bank-name',cls:'w-3'}),
            provinceSelect = new bomao.Select({realDom:'#J-select-province',cls:'w-3'}),
            citySelect = new bomao.Select({realDom:'#J-select-city',cls:'w-3'}),
            cityCache = {},
            makeBigNumber;


        provinceSelect.addEvent('change', function(e, value, text){
            var id = $.trim(value);
            if(id == '0'){
                citySelect.reBuildSelect([{value:0, text:'请选择城市',checked:true}]);
                return;
            }
            if(cityCache[id]){
                citySelect.reBuildSelect(cityCache[id]);
            }else{
                $.ajax({
                    url:'../data/city.php?id=' + id,
                    timeout:30000,
                    dataType:'json',
                    beforeSend:function(){

                    },
                    success:function(data){
                        if(Number(data['isSuccess']) == 1){
                            var list = [];
                            $.each(data['data'], function(i){
                                list[i] = {value:this['id'], text:this['name'], checked: this['isdefault'] ? true : false};
                            });
                            cityCache[id] = list;
                            citySelect.reBuildSelect(cityCache[id]);
                        }else{
                            alert(data['msg']);
                        }
                    },
                    error:function(){
                        alert('网络请求失败，请稍后重试');
                    }
                });
            }
        });


        cardInput.keyup(function(e){
            var el = $(this),v = this.value.replace(/^\s*/g, ''),arr = [],code = e.keyCode;
            if(code == 37 || code == 39){
                return;
            }
            v = v.replace(/[^\d|\s]/g, '').replace(/\s{2}/g, ' ');
            this.value = v;
            if(v == ''){
                v = '&nbsp;';
            }else{
                v = makeBigNumber(v);
                this.value = v;
            }
            tip.setText(v);
            tip.getDom().css({left:el.offset().left + el.width()/2 - tip.getDom().width()/2});
            if(v != '&nbsp;'){
                tip.show(el.width()/2 - tip.getDom().width()/2, tip.getDom().height() * -1 - 20, this);
            }else{
                tip.hide();
            }
        });
        cardInput.focus(function(){
            var el = $(this),v = $.trim(this.value);
            if(v == ''){
                v = '&nbsp;';
            }else{
                v = makeBigNumber(v);
            }
            tip.setText(v);
            if(v != '&nbsp;'){
                tip.show(el.width()/2 - tip.getDom().width()/2, tip.getDom().height() * -1 - 20, this);
            }else{
                tip.hide();
            }
        });
        cardInput.blur(function(){
            this.value = makeBigNumber(this.value);
            tip.hide();
        });
        cardInput.keydown(function(e){
            if(e.ctrlKey && e.keyCode == 86){
                return false;
            }
        });
        cardInput.bind("contextmenu",function(e){
            return false;
        });
        //每4位数字增加一个空格显示
        makeBigNumber = function(str){
            var str = str.replace(/\s/g, '').split(''),len = str.length,i = 0,newArr = [];
            for(;i < len;i++){
                if(i%4 == 0 && i != 0){
                    newArr.push(' ');
                    newArr.push(str[i]);
                }else{
                    newArr.push(str[i]);
                }
            }
            return newArr.join('');
        };


        $('#J-submit').click(function(){
            var bank = $('#J-select-bank-name'),
                province = $('#J-select-province'),
                city = $('#J-select-city'),
                bankname = $('#J-input-bankname'),
                name = $('#J-input-name'),
                cardnumber = $('#J-input-card-number'),
                cardnumber2 = $('#J-input-card-number2');

            if($.trim(bank.val()) == ''){
                alert('请选择开户银行');
                return false;
            }
            if($.trim(province.val()) == '0'){
                alert('请选择开户银行省份');
                return false;
            }
            if($.trim(city.val()) == '0'){
                alert('请选择开户银行城市');
                return false;
            }
            if($.trim(bankname.val()) == ''){
                alert('请填写支行名称');
                bankname.focus();
                return false;
            }
            if($.trim(name.val()) == ''){
                alert('请填写开户人姓名');
                name.focus();
                return false;
            }
            if($.trim(cardnumber.val()) == ''){
                alert('请填写银行账号');
                cardnumber.focus();
                return false;
            }
            if($.trim(cardnumber2.val()) == ''){
                alert('请填写确认银行账号');
                cardnumber2.focus();
                return false;
            }
            if($.trim(cardnumber.val()) != $.trim(cardnumber2.val())){
                alert('两次填写的银行账号不一致');
                cardnumber2.focus();
                return false;
            }

            return true;
        });



    })(jQuery);
    </script>
@stop