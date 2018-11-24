jQuery(document).ready(function($) {
    //下拉框组件
    var selectDays = new bomao.Select({realDom:'#J-select-link-valid',cls:'w-2'});
    var selectChannel = new bomao.Select({realDom:'#J-select-channel-name',cls:'w-3'});
    var defaultPrizeGroup = aDefaultPrizeGroups[0];
    var defaultMaxPrizeGroup = aDefaultMaxPrizeGroups[0];
    var lotteryPrizeGroupCache = {},
        seriesPrizeGroupCache  = {};
    $('#J-input-custom-bonus-value').val(defaultPrizeGroup['classic_prize']);

    //开户类型切换
    var switchHandles = $('#J-user-type-switch-panel').find('a');
    switchHandles.click(function(e){
        var index = switchHandles.index(this),userTypeId = $.trim($(this).attr('data-userTypeId'));
        e.preventDefault();
        switchHandles.removeClass('current');
        switchHandles.eq(index).addClass('current');
        $('#J-input-userType').val(userTypeId);
        $('#J-group-gametype-panel').find('li:not(.item-all)').css('display', +userTypeId ? 'none' : '');
        defaultPrizeGroup = aDefaultPrizeGroups[+userTypeId];
        defaultMaxPrizeGroup = aDefaultMaxPrizeGroups[+userTypeId];
        // $('#J-input-custom-bonus-value').val(defaultPrizeGroup['classic_prize']);
        // loadGroupDataByUserTypeId(userTypeId);
    });
    // 初始化根据当前开户类型渲染彩种列表
    var iDefaultUserTypeId = $('#J-user-type-switch-panel').find('a.current').attr('data-userTypeId');
    $('.bonusgroup-game-type').css('display', +iDefaultUserTypeId ? 'none' : '');




    //固定奖金组和自定义奖金组切换
    var tab = new bomao.Tab({par:'#J-panel-cont', triggers:'.tab-title > li', panels:'.tab-panels > li', eventType:'click'});
    tab.addEvent('afterSwitch', function(e, index){
        $('#J-input-group-type').val(index + 1);
    });


    selectChannel.addEvent('change', function(e, value, text){
        if(value == '0'){
            $('#J-input-custom').show();
        }else{
            $('#J-input-custom').hide();
            $('#J-input-custom').val(value);
        }
    });

    //查看奖金组详情
    $('#J-link-bonusgroup-detail').click(function(e){
        var el       = $(this),
            path     = el.attr('data-path'),
            value    = $.trim($('#J-input-custom-bonus-value').val()),
            gameType = $('#J-input-custom-type').val(),
            gameId   = $('#J-input-custom-id').val(),
            url      = path + '?gametype=' + gameType + '&gameid=' + gameId + '&bonus=' + value;
        el.attr('href', url);
    });

    //选择某个奖金组套餐
    $('#J-panel-group').on('click', '.button-selectGroup', function(){
        var el = $(this),groupid = $.trim(el.attr('data-groupid'));
        $('#J-input-groupid').val(groupid);
        $('#J-panel-group').find('li').removeClass('current');
        $('#J-panel-group').find('input[type="button"]').val('选 择')
        el.parent().addClass('current');
        el.val('已选择');
    });

    //游戏彩种选择
    $('#J-group-gametype-panel').on('click', '.item-game', function(e){
        var el = $(this),
            type = $.trim(el.attr('data-itemtype')),
            id = $.trim(el.attr('data-id'));
        selectGameConfig(type, id);
        $('#J-group-gametype-panel').find('li').removeClass('current');
        el.parent().addClass('current');
        e.preventDefault();
    });
    //选择某一个游戏或者彩系进行设置
    var selectGameConfig = function(type, id){
        var typeDom = $('#J-input-custom-type'),idDom = $('#J-input-custom-id'),feedback = [];
        // cacheLotteryPrizeGroup();
            //选择的是彩种
        if(type == 'all'){
            typeDom.val(id);
            idDom.val('');
        }else{
            //选择的是游戏
            typeDom.val('');
            idDom.val(id);
        }

        //生成拖动slider参数
        slider.reSet({
            'minBound': +(defaultPrizeGroup['classic_prize']),
            'maxBound': Math.min(defaultMaxPrizeGroup['classic_prize'], currentPrize),
            'step'    : 1,
            'value'   : +(defaultPrizeGroup['classic_prize'])
        });

        // feedback = getFeedback(sliderCfg['proxyBonus'], sliderCfg['bonus'], sliderCfg['minMethodBonus'], sliderCfg['maxMethodBonus']);
        // $('#J-custom-feedback-value').text(feedback[0] + '% - ' + feedback[1] + '%');
    };
    //根据参数计算返点率
    var getFeedback = function(proxyBonus, playerBonus, minMethod, maxMethod){
        var arr = [];
        arr.push(((proxyBonus - playerBonus)/maxMethod).toFixed(2));
        arr.push(((proxyBonus - playerBonus)/minMethod).toFixed(2));
        return arr;
    };

    var cacheLotteryPrizeGroup = function () {
        var lotteryId = $('#J-input-custom-id').val(),
            seriesId  = $('#J-input-custom-type').val();
            // key = lotteryId,
            // lotteryPre = 'lotteryPre_',
            // seriesPre  = 'seriesPre_',
            // key = !!lotteryId ? lotteryPre + lotteryId : (!!seriesId ? seriesPre + seriesId : '');
        if (!seriesId && !lotteryId) return false;
        if (seriesId) {
            seriesPrizeGroupCache[seriesId]  = +(slider.getValue());
        }
        if (lotteryId) {
            lotteryPrizeGroupCache[lotteryId] = +(slider.getValue());
        }
    }

    //自定义奖金组设置组件
    var slider = new bomao.SliderBar({
        'minDom'   :'#J-slider-minDom',
        'maxDom'   :'#J-slider-maxDom',
        'contDom'  :'#J-slider-cont',
        'handleDom':'#J-slider-handle',
        'innerDom' :'#J-slider-innerbg',
        'minNumDom':'#J-slider-num-min',
        'maxNumDom':'#J-slider-num-max',
        'isUpOnly' :true,
        'step'     : 1,
        'minBound' : +(defaultPrizeGroup['classic_prize']),
        'maxBound' : Math.min(defaultMaxPrizeGroup['classic_prize'], currentPrize),
        'value'    : +(defaultPrizeGroup['classic_prize'])
    });
    slider.addEvent('change', function(){
        var me = this,feedback,typeDom = $('#J-input-custom-type'),idDom = $('#J-input-custom-id'),feedback = [];
        $('#J-input-custom-bonus-value').val(me.getValue());
        cacheLotteryPrizeGroup();

        // feedback = getFeedback(sliderCfg['proxyBonus'], me.getValue(), sliderCfg['minMethodBonus'], sliderCfg['maxMethodBonus']);
        // $('#J-custom-feedback-value').text(feedback[0] + '% - ' + feedback[1] + '%');
    });


    $('#J-input-custom-bonus-value').blur(function(){
        var v = $.trim(this.value),mul = 1,step = 1;
        v = v.replace(/[^\d]/g, '');
        v = Number(v);
        mul = Math.ceil(v/step);
        v = mul * step;
        this.value = v;
        slider.setValue(v);
    }).keyup(function(){
        this.value = this.value.replace(/[^\d]/g, '');
    });


    //表单提交
    $('#J-button-submit').click(function(){
        var userType = $.trim($('#J-input-userType').val()),
            validDays = $.trim(selectDays.getValue()),
            spreadChannel = $.trim(selectChannel.getValue()),
            spreadChannelValue = $.trim($('#J-input-custom').val()),
            //套餐还是自定义
            groupType = $.trim($('#J-input-group-type').val());
        var lotteriesJsonData = JSON.stringify(lotteryPrizeGroupCache),
            seriesJsonData    = JSON.stringify(seriesPrizeGroupCache);
            if (lotteriesJsonData != '{}') $('#J-input-lottery-json').val(lotteriesJsonData);
            if (seriesJsonData    != '{}') $('#J-input-series-json').val(seriesJsonData);
            // return false;
            if(validDays == ''){
                alert('请选择链接有效期');
                return false;
            }
            if(spreadChannel == ''){
                alert('请选择推广渠道');
                return false;
            }
            if(spreadChannel == '0' && spreadChannelValue == ''){
                alert('自定义推广渠道，请填写渠道名称');
                return false;
            }
            //套餐
            if(groupType == '1'){
                if($.trim($('#J-input-groupid').val()) == ''){
                    alert('请选择一个奖金组套餐');
                    return false;
                }
            }else{
                if($.trim($('#J-input-custom-type').val()) == '' && $.trim($('#J-input-custom-id').val()) == ''){
                    alert('请选择一个游戏或者彩种进行设置');
                    return false;
                }
            }
            return true;
    });

    $('#J-button-goback').click(function(e){
        history.back(-1);
        e.preventDefault();
    });

});