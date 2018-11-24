jQuery(document).ready(function($) {
    //下拉框组件
    var selectDays = $('#J-select-link-valid');
    // var selectChannel = $('#J-select-channel-name');
    defaultPrizeGroup = +defaultPrizeGroup;
    var lotteryPrizeGroupCache = {},
        seriesPrizeGroupCache  = {};
    // currentPrizeGroup = JSON.parse(currentPrizeGroup);
    var curPrizeGroups = {};
    if (isEdit) {
        for (var i = 0, l = currentPrizeGroup.length; i < l; i++) {
            var item = currentPrizeGroup[i];
            curPrizeGroups[item.series_id] = item.prize_group;
            curPrizeGroups['all_lotteries'] = item.prize_group;
        }
    }

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
        $('#J-panel-group').find('a').removeClass('btn-success');
        $('#J-panel-group').find('a').text('选 择');
        el.addClass('btn-success');
        el.text('已选择');
    });

    //游戏彩种选择
    $('#J-group-gametype-panel').on('click', '.item-game', function(e){
        var el = $(this),
            type = $.trim(el.attr('data-itemtype')),
            id = $.trim(el.attr('data-id'));
        selectGameConfig(type, id);
        $('#J-group-gametype-panel').find('a.item-game').removeClass('btn-success');
        el.addClass('btn-success');
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
        iCurPrizeGroup = curPrizeGroups[id] || '';
        sliderContainer.slider({
            min: sliderCfg['min'],
            max: sliderCfg['max'],
            step: sliderCfg['step'],
            value: isEdit ? currentUserPrize : iCurPrizeGroup
        });
        $('#slider-min').text(sliderCfg['min']);
        $('#slider-max').text(sliderCfg['max']);
        $('#J-input-custom-bonus-value').val(isEdit ? currentUserPrize : defaultPrizeGroup);

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
        if (!seriesId && !lotteryId) return false;
        if (seriesId) {
            seriesPrizeGroupCache[seriesId]  = sliderContainer.slider('value');
        }
        if (lotteryId) {
            lotteryPrizeGroupCache[lotteryId] = sliderContainer.slider('value');
        }
    }

    //自定义奖金组设置组件
    var sliderContainer = $("#slider");
    if (sliderContainer.length > 0) {
        sliderContainer.slider({
            min: sliderCfg['min'],
            max: sliderCfg['max'],
            step: sliderCfg['step'],
            value: currentUserPrize || defaultPrizeGroup,
            orientation: "horizontal",
            range: "min",
            stop: function (event, ui) {
                if (currentUserPrize && ui.value < currentUserPrize) sliderContainer.slider({value: currentUserPrize});
            },
            slide: function( event, ui ) {
                $( "#J-input-custom-bonus-value" ).val( ui.value );
                // sliderContainer.slider({value: ui.value});
            },
            change: function(event, ui) {
                if (currentUserPrize && ui.value < currentUserPrize) {
                    sliderContainer.slider({value: currentUserPrize});
                    ui.value = currentUserPrize;
                }
                $( "#J-input-custom-bonus-value" ).val( ui.value );
                // sliderContainer.slider({value: ui.value});
                cacheLotteryPrizeGroup();
            }
        });
      // .addSliderSegments(slider.slider("option").max);
    }

    // $('#J-input-custom-bonus-value').change(function(event) {
    //     var value = $(this).val();
    //     sliderContainer.slider('value', value);
    // });
    // var slider = new bomao.SliderBar({
        // 'minDom'   :'#J-slider-minDom',
        // 'maxDom'   :'#J-slider-maxDom',
        // 'contDom'  :'#J-slider-cont',
        // 'handleDom':'#J-slider-handle',
        // 'innerDom' :'#J-slider-innerbg',
        // 'minNumDom':'#J-slider-num-min',
        // 'maxNumDom':'#J-slider-num-max',
        // 'isUpOnly' :true,
        // 'step'     : 1,
        // 'minBound' : sliderCfg['min'],
        // 'maxBound' : sliderCfg['max'],
        // 'value'    : +(defaultPrizeGroup['classic_prize'])
    // });
    // slider.addEvent('change', function(){
    //     var me = this,feedback,typeDom = $('#J-input-custom-type'),idDom = $('#J-input-custom-id'),feedback = [];
    //     $('#J-input-custom-bonus-value').val(me.getValue());
    //     cacheLotteryPrizeGroup();

    //     // feedback = getFeedback(sliderCfg['proxyBonus'], me.getValue(), sliderCfg['minMethodBonus'], sliderCfg['maxMethodBonus']);
    //     // $('#J-custom-feedback-value').text(feedback[0] + '% - ' + feedback[1] + '%');
    // });


    $('#J-input-custom-bonus-value').blur(function(){
        var v = $.trim(this.value),mul = 1,step = 1;
        v = v.replace(/[^\d]/g, '');
        v = Number(v);
        mul = Math.ceil(v/step);
        v = mul * step;
        this.value = v;
        sliderContainer.slider('value', v);
    }).keyup(function(){
        this.value = this.value.replace(/[^\d]/g, '');
    });
    $('#J-slider-num-min').click(function(event) {
        var step = +(sliderContainer.slider('option','step')),
            value = +(sliderContainer.slider('value'));
        sliderContainer.slider('option', 'value', value - step);
    });
    $('#J-slider-num-max').click(function(event) {
        var step = +(sliderContainer.slider('option', 'step')),
            value = +(sliderContainer.slider('value'));
        sliderContainer.slider('option', 'value', value + step);
    });

    $('#all_lotteries').click();

    //表单提交
    $('#J-button-submit').click(function(event) {
        event.preventDefault();
        var validDays = $.trim(selectDays.val()),
            // spreadChannel = $.trim(selectChannel.getValue()),
            // spreadChannelValue = $.trim($('#J-input-custom').val()),
            //套餐还是自定义
            index = $('#J-panel-cont').find('li.active').index() + 1;
        $('#J-input-group-type').val(index);
        var groupType = $.trim($('#J-input-group-type').val());
        var lotteriesJsonData = JSON.stringify(lotteryPrizeGroupCache),
            seriesJsonData    = JSON.stringify(seriesPrizeGroupCache);
            if (lotteriesJsonData != '{}') $('#J-input-lottery-json').val(lotteriesJsonData);
            if (seriesJsonData    != '{}') $('#J-input-series-json').val(seriesJsonData);
            // return false;
            // if(validDays == ''){
            //     alert('请选择链接有效期');
            //     return false;
            // }
            // if(spreadChannel == ''){
            //     alert('请选择推广渠道');
            //     return false;
            // }
            // if(spreadChannel == '0' && spreadChannelValue == ''){
            //     alert('自定义推广渠道，请填写渠道名称');
            //     return false;
            // }
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
            $('#J-form').submit();
            // return true;
    });
});