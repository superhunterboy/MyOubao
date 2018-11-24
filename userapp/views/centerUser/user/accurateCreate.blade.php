@extends('l.home')

@section('title') 
    人工开户
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 0 0 10px 0;
        margin: 10px 0 0 0;
    }
    .page-content .row-nav {
        padding: 0 35px;
        margin-bottom: 10px;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
    }
    .page-content .row-nav ul{
        width: 176px;
        height: 38px;
        border-radius: 4px;
        background-color: #31CEAC;
        padding: 5px 10px;
        font-size: 13px;
    }
    </style>
@stop



@section ('container')
    
    @include('w.header')

    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>



    <div class="page-content">
        <div class="g_main clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                @include('w.uc-menu-proxy')
            </div>
            


            <div class="page-content-inner page-content-inner-bg">
                <form action="{{ route('users.accurate-create') }}" method="post" id="J-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="_random" value="{{ createRandomStr() }}" />
                    <input type="hidden" name="is_agent" id="J-input-userType" value="{{ Input::old('is_agent', 1) }}" />
                    <div class="row row-nav clearfix">
                        {{-- <label class="text-title">开户方式</label> --}}
                        <ul class="field field-type-switch">
                            <li class="current"><a href="{{ route('users.accurate-create') }}">人工开户</a></li>
                            <li><a href="{{ route('user-links.create') }}">链接开户</a></li>
                        </ul>
                    </div>

                    <div class="row clearfix" id="J-user-type-tab" style="display:none;">
                        <label class="text-title">选择账户类型</label>
                        <ul class="field field-type-switch field-user-type">
                            <li><a class="item-player" href="javascript:;">玩家账号</a></li>
                            <li class="current"><a class="item-agent" href="javascript:;">代理账号</a></li>
                        </ul>
                        <input class="user-type-cont" type="hidden" />
                        <input class="user-type-cont" type="hidden" />
                    </div>

                    <div class="row clearfix" style="padding-bottom:5px;">
                        <label class="text-title"></label>
                        <ul class="field field-user-input">
                            <li>
                                <span style="padding-right: 33px;">用户名</span>
                                <input name="username" id="J-input-userName" type="text" value="" class="input w-4" placeholder="6-16位字符，可使用字母或数字" />
                                <span class="tip text-danger"></span>
                                <span class="ico-right"></span>
                            </li>
                            <li>
                                <span style="padding-right:20px;">登录密码</span>
                                <input name="password" id="J-input-password" type="text" value="" class="input w-4" placeholder="6-16位字符，可使用字母或数字" />
                                <span class="tip text-danger"></span>
                                <span class="ico-right"></span>
                            </li>
                        </ul>
                    </div>



                <div class="row-mode mode-lottery">
                    <div class="row-mode-title">
                        彩票奖金组设置
                    </div>
                    <div class="row row-set-prize clearfix">

                        <input type="hidden" value="{{ $iAgentMinPrizeGroup }}" id="J-input-prize-min" />
                        <input type="hidden" value="{{ $iAgentMaxPrizeGroup }}" id="J-input-prize-max" />

                        <label class="text-title">设置奖金组</label>
                        <input type="text" class="input w-1" id="J-input-prize" name="prize_group" value="{{ $iAgentMaxPrizeGroup }}" style="text-align:center;" />
                        &nbsp;&nbsp;
                        <span class="tip-text">预计平均返点率</span>
                        <span class="percentage" id="J-text-percentage">0.00%</span>
                        <a id="J-ex-link" data-url="{{ route('user-user-prize-sets.prize-set-detail') }}" href="{{ route('user-user-prize-sets.prize-set-detail') }}/{{ $iAgentMaxPrizeGroup }}" class="ex_link" target="_blank">奖金详情</a>

                        <br />
                        <label class="text-title"></label>
                        {{-- <span id="J-angent-odd-tip" class="tip" style="font-size: 12px;padding-top:5px;color: #A3A3A3;">请输入数字</span> --}}

                        <div class="panel-progress">
                            <div class="bar" style="width:100%;" id="J-bar-middle">
                                <div class="bar-ce"></div>
                                <div class="sign-middle" id="J-prize-num-current">{{ $iAgentMaxPrizeGroup }}</div>
                            </div>

                            <div class="sign-start" id="J-prize-num-min">{{ $iAgentMinPrizeGroup }}</div>
                            <div class="sign-end" id="J-prize-num-max">{{ $iAgentMaxPrizeGroup }}</div>
                        </div>

                    </div>

                </div>



                    {{--===================JC==================--}}
                <div class="row-mode mode-sport" style="display: none;">
                    <div class="row-mode-title">
                        竞彩返点设置
                    </div>
                    <div class="row row-set-prize clearfix">

                        <input type="hidden" value="{{ isset($iPlayerMinJcSingleCommissionRate) ? $iPlayerMinJcSingleCommissionRate : 0 }}" id="J-input-single-commission-min" />
                        <input type="hidden" value="{{ isset($iPlayerMaxJcSingleCommissionRate) ? $iPlayerMaxJcSingleCommissionRate : 0}}" id="J-input-single-commission-max" />

                        <label class="text-title">单关返点</label>


                        @foreach($datas as $data)
                            @if($data->series_set_id == SeriesSet::ID_FOOTBALL_SINGLE)
                                <input type="text" class="input ct-input-point w-1" name="commission_rate_{{$data->series_set_id}}" value="{{ isset($iPlayerMinJcSingleCommissionRate) ? $iPlayerMinJcSingleCommissionRate : 0 }}" style="text-align:center;" />
                                %
                                &nbsp;
                                {{-- <span class="percentage" id="J-text-percentage-single">0.00%</span> --}}
                                <span class="tip">最高可分配 <span class="max-point">{{number_format($data->commission_rate, 2)}}</span> %</span>
                                <input type="hidden" value="0.5" class="setp" />
                            @endif
                        @endforeach


                        <br />
                        <label class="text-title"></label>
                        {{-- <span id="J-angent-odd-tip2" class="tip" style="font-size: 12px;padding-top:5px;color: #A3A3A3;">请输入数字</span> --}}

                        <div class="panel-progress">
                            <div class="bar" style="width:100%;" id="J-bar-middle-single-commission">
                                <div class="bar-ce"></div>
                                <div class="sign-middle" id="J-single-commission-num-current">{{ isset($iPlayerMaxJcSingleCommissionRate) ? $iPlayerMaxJcSingleCommissionRate : 0 }}</div>
                            </div>

                            <div class="sign-start" id="J-single-commission-num-min">{{ isset($iPlayerMinJcSingleCommissionRate) ? $iPlayerMinJcSingleCommissionRate : 0 }}</div>
                            <div class="sign-end" id="J-single-commission-num-max">{{ isset($iPlayerMaxJcSingleCommissionRate) ? $iPlayerMaxJcSingleCommissionRate : 0 }}</div>
                        </div>

                    </div>

                    <div class="row row-set-prize clearfix">

                        <input type="hidden" value="{{ isset($iPlayerMinJcMultipleCommissionRate) ? $iPlayerMinJcMultipleCommissionRate : 0 }}" id="J-input-multiple-commission-min" />
                        <input type="hidden" value="{{ isset($iPlayerMaxJcMultipleCommissionRate) ? $iPlayerMaxJcMultipleCommissionRate : 0 }}" id="J-input-multiple-commission-max" />

                        <label class="text-title">串关返点</label>


                        @foreach($datas as $data)
                            @if($data->series_set_id == SeriesSet::ID_FOOTBALL_MIX)
                                <input type="text" class="input ct-input-point w-1" name="commission_rate_{{$data->series_set_id}}" value="0" style="text-align:center;" />
                                %
                                &nbsp;
                                {{-- <span class="percentage" id="J-text-percentage-multiple">0.00%</span> --}}
                                <span class="tip">最高可分配 <span class="max-point">{{number_format($data->commission_rate, 2)}}</span> %</span>
                                <input type="hidden" value="0.5" class="setp" />
                            @endif
                        @endforeach



                        <br />
                        <label class="text-title"></label>
                        {{-- <span id="J-angent-odd-tip3" class="tip" style="font-size: 12px;padding-top:5px;color: #A3A3A3;">请输入数字</span> --}}

                        <div class="panel-progress">
                            <div class="bar" style="width:100%;" id="J-bar-middle-multiple-commission">
                                <div class="bar-ce"></div>
                                <div class="sign-middle" id="J-multiple-commission-num-current">{{ isset($iPlayerMaxJcMultipleCommissionRate) ? $iPlayerMaxJcMultipleCommissionRate : 100 }}</div>
                            </div>

                            <div class="sign-start" id="J-multiple-commission-num-min">{{ isset($iPlayerMinJcMultipleCommissionRate) ? $iPlayerMinJcMultipleCommissionRate : 0 }}</div>
                            <div class="sign-end" id="J-multiple-commission-num-max">{{ isset($iPlayerMaxJcMultipleCommissionRate) ? $iPlayerMaxJcMultipleCommissionRate : 100 }}</div>
                        </div>

                    </div>


                </div>
                    {{--===================JC==================--}}





                <div class="row-mode mode-sport">
                    <div class="row-mode-title">
                        电子娱乐返点设置
                    </div>

                    @foreach($datas as $data)
                        @if($data->type_id == SeriesSet::TYPE_ELECTRONIC)
                            <div class="row row-set-prize clearfix">
                                <label class="text-title">{{$data->name}}</label>
                                <input type="text" class="input ct-input-point w-1" value="0" name="commission_rate_{{$data->series_set_id}}" style="text-align:center;" />
                                %
                                &nbsp;
                                <span class="tip">最高可分配 <span class="max-point">{{number_format($data->commission_rate, 2)}}</span> %</span>
                                <input type="hidden" value="0.1" class="setp" />
                            </div>
                        @endif
                    @endforeach

                </div>












                    <div class="row-control">
                        <input id="J-button-submit" class="btn btn-sbumit" type="submit" value=" 立即开户 " />
                    </div>
                </form>
            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function(){

    //账户类型切换
    var typeTab = new bomao.Tab({par:'#J-user-type-tab', triggers:'.field-user-type li', panels:'.user-type-cont', eventType:'click'});
    var iCurrentPrizeGroup   = {{ $iCurrentUserPrizeGroup }};
        iPlayerMinPrizeGroup = {{ $iPlayerMinPrizeGroup }};
        iPlayerMaxPrizeGroup = {{ $iPlayerMaxPrizeGroup }};
        iAgentMinPrizeGroup  = {{ $iAgentMinPrizeGroup }};
        iAgentMaxPrizeGroup  = {{ $iAgentMaxPrizeGroup }};

    /*===========================JC===========================*/
    var iCurrentSingleJcCommission   = {{ isset($iCurrentUserSingleJcCommission) ? $iCurrentUserSingleJcCommission : 0 }};
        iPlayerMinSingleJcCommission = {{ isset($iPlayerMinSingleJcCommission) ? $iPlayerMinSingleJcCommission : 0 }};
        iPlayerMaxSingleJcCommission = {{ isset($iPlayerMaxSingleJcCommission) ? $iPlayerMaxSingleJcCommission : 0 }};
        iAgentMinSingleJcCommission  = {{ isset($iAgentMinSingleJcCommission) ?  $iAgentMinSingleJcCommission : 0}};
        iAgentMaxSingleJcCommission  = {{ isset($iAgentMaxSingleJcCommission) ? $iAgentMaxSingleJcCommission : 0 }};
    var iCurrentMultipleJcCommission   = {{ isset($iCurrentUserMultipleJcCommission) ?  $iCurrentUserMultipleJcCommission : 0}};
        iPlayerMinMultipleJcCommission = {{ isset($iPlayerMinMultipleJcCommission) ? $iPlayerMinMultipleJcCommission : 0}};
        iPlayerMaxMultipleJcCommission = {{ isset($iPlayerMaxMultipleJcCommission) ?  $iPlayerMaxMultipleJcCommission : 0}};
        iAgentMinMultipleJcCommission  = {{ isset($iAgentMinMultipleJcCommission) ?  $iPlayerMaxMultipleJcCommission : 0}};
        iAgentMaxMultipleJcCommission  = {{ isset($iAgentMaxMultipleJcCommission) ? $iAgentMaxMultipleJcCommission : 0 }};
    /*===========================JC===========================*/

    var isAddAgent = false;

    typeTab.addEvent('afterSwitch', function(e, index){
        $('#J-input-userType').val(index);
        isAddAgent = index == 0 ? false : true;
        switch(index)
        {
            case 0: //player
                $("#J-input-prize-min").val(iPlayerMinPrizeGroup);
                $("#J-input-prize-max").val(iPlayerMaxPrizeGroup);
                $("#J-prize-num-min").html(iPlayerMinPrizeGroup);
                $("#J-prize-num-max").html(iPlayerMaxPrizeGroup);
                updatePrize(iPlayerMaxPrizeGroup);
                $('#J-input-prize').val(iPlayerMaxPrizeGroup);

                $('#J-angent-odd-tip').text('请输入数字');

                /*===========================JC===========================*/
                $("#J-input-single-commission-min").val(iPlayerMinSingleJcCommission);
                $("#J-input-single-commission-max").val(iPlayerMaxSingleJcCommission);
                $("#J-single-commission-num-min").html(iPlayerMinSingleJcCommission);
                $("#J-single-commission-num-max").html(iPlayerMaxSingleJcCommission);

                $("#J-input-multiple-commission-min").val(iPlayerMinMultipleJcCommission);
                $("#J-input-multiple-commission-max").val(iPlayerMaxMultipleJcCommission);
                $("#J-multiple-commission-num-min").html(iPlayerMinMultipleJcCommission);
                $("#J-multiple-commission-num-max").html(iPlayerMaxMultipleJcCommission);
                updateSingleCommission(iPlayerMaxSingleJcCommission);
                updateMultipleCommission(iPlayerMaxMultipleJcCommission);

                $('#J-input-single-commission').val(iPlayerMaxSingleJcCommission);
                $('#J-input-multiple-commission').val(iPlayerMaxMultipleJcCommission);

                $('#J-angent-odd-tip2').text('请输入数字');
                $('#J-angent-odd-tip3').text('请输入数字');
                /*===========================JC===========================*/
            break;

            case 1: //agent
                $("#J-input-prize-min").val(iAgentMinPrizeGroup);
                $("#J-input-prize-max").val(iAgentMaxPrizeGroup);
                $("#J-prize-num-min").html(iAgentMinPrizeGroup);
                $("#J-prize-num-max").html(iAgentMaxPrizeGroup);
                updatePrize(iAgentMaxPrizeGroup);
                $('#J-input-prize').val(iAgentMaxPrizeGroup);

                $('#J-angent-odd-tip').text('请输入偶数');

                /*===========================JC===========================*/
                $("#J-input-single-commission-min").val(iPlayerMinSingleJcCommission);
                $("#J-input-single-commission-max").val(iPlayerMaxSingleJcCommission);
                $("#J-single-commission-num-min").html(iPlayerMinSingleJcCommission);
                $("#J-single-commission-num-max").html(iPlayerMaxSingleJcCommission);

                $("#J-input-multiple-commission-min").val(iPlayerMinMultipleJcCommission);
                $("#J-input-multiple-commission-max").val(iPlayerMaxMultipleJcCommission);
                $("#J-multiple-commission-num-min").html(iPlayerMinMultipleJcCommission);
                $("#J-multiple-commission-num-max").html(iPlayerMaxMultipleJcCommission);
                /*===========================JC===========================*/

                updateSingleCommission(iPlayerMaxSingleJcCommission);
                updateMultipleCommission(iPlayerMaxMultipleJcCommission);

                $('#J-input-single-commission').val(iPlayerMaxSingleJcCommission);
                $('#J-input-multiple-commission').val(iPlayerMaxMultipleJcCommission);

                $('#J-angent-odd-tip2').text('请输入数字');
                $('#J-angent-odd-tip3').text('请输入数字');
            break;
        }
    });

    //奖金组范围
    var prizeBound = {'min':Number($('#J-input-prize-min').val()), 'max':Number($('#J-input-prize-max').val())};

    //Preload data
    updatePrize(iAgentMaxPrizeGroup);

    /*===========================JC===========================*/
    var singleCommissionBound = {'min':Number($('#J-input-single-commission-min').val()), 'max':Number($('#J-input-single-commission-max').val())};
    var multipleCommissionBound = {'min':Number($('#J-input-multiple-commission-min').val()), 'max':Number($('#J-input-multiple-commission-max').val())};
    updateSingleCommission(iPlayerMaxSingleJcCommission);
    updateMultipleCommission(iPlayerMaxMultipleJcCommission);
    /*===========================JC===========================*/
    
    function updatePrize(num){
        prizeBound.min = Number($('#J-input-prize-min').val());
        prizeBound.max = Number($('#J-input-prize-max').val());
        var basePrize = 2000,
            rebate = (((iCurrentPrizeGroup - num) / basePrize) * 100).toFixed(2),
            bound = prizeBound['max'] - prizeBound['min'],
            bl = (100 - Number((num-prizeBound['min'])/bound*100)).toFixed(2);
        $('#J-bar-middle').css('width',  (100 - bl) + '%');
        if((100 - bl) <= 0){
            $('#J-prize-num-current').css('right', -30);
        }else{
            $('#J-prize-num-current').css('right', 0);
        }
        $('#J-prize-num-current').text(num);
        $('#J-text-percentage').text(rebate + '%');
        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);
    }

    /*===========================JC===========================*/
    function updateSingleCommission(num){
        singleCommissionBound.min = Number($('#J-input-single-commission-min').val());
        singleCommissionBound.max = Number($('#J-input-single-commission-max').val());
        var rebate = Number(num).toFixed(2),
            bound = singleCommissionBound['max'] - singleCommissionBound['min'],
            bl = (100 - Number(num/bound*100)).toFixed(2);

        $('#J-bar-middle-single-commission').css('width',  (100 - bl) + '%');
        if((100 - bl) <= 0){
            $('#J-single-commission-num-current').css('right', -30);
        }else{
            $('#J-single-commission-num-current').css('right', 0);
        }
        $('#J-single-commission-num-current').text(num);
        $('#J-text-percentage-single').text(rebate + '%');
//        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);
    }
    function updateMultipleCommission(num){
        multipleCommissionBound.min = Number($('#J-input-multiple-commission-min').val());
        multipleCommissionBound.max = Number($('#J-input-multiple-commission-max').val());
        var rebate = Number(num).toFixed(2),
            bound = multipleCommissionBound['max'] - multipleCommissionBound['min'],
            bl = (100 - Number(num/bound*100)).toFixed(2);
        $('#J-bar-middle-multiple-commission').css('width',  (100 - bl) + '%');
        if((100 - bl) <= 0){
            $('#J-multiple-commission-num-current').css('right', -30);
        }else{
            $('#J-multiple-commission-num-current').css('right', 0);
        }
        $('#J-multiple-commission-num-current').text(num);
        $('#J-text-percentage-multiple').text(rebate + '%');
//        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);
    }
    /*===========================JC===========================*/

    $('#J-input-prize').keyup(function(){
        var v = this.value.replace(/[^\d]/g, ''),
            bound = prizeBound['max'] - prizeBound['min'],
            bl = 0,
            num = Number(v);
        if(!num){
            num = prizeBound['max'];
        }
        if(isAddAgent && num > 1000){
            if(num%2 == 1){
                num = num - 1;
            }
            if(num < prizeBound['min']){
                num = prizeBound['min'];
            }
        }

        if(num > prizeBound['max']){
            num = prizeBound['max'];
        }
        this.value = num;
        updatePrize(num);

    }).blur(function(){
        var num = Number(this.value.replace(/[^\d]/g, '')),
            bound = prizeBound['max'] - prizeBound['min'];
        num = num > prizeBound['max'] ? prizeBound['max'] : num;
        num = num < prizeBound['min'] ? prizeBound['min'] : num;
        this.value = num;
        updatePrize(num);
    });
    /*===========================JC===========================*/
    $('#J-input-single-commission').keyup(function(){
        var v = this.value.replace(/[^\d\.]/g, ''),
            bound = singleCommissionBound['max'] - singleCommissionBound['min'],
            bl = 0,
            num = Number(v);
        if(!num){
            num = singleCommissionBound['min'];
        }
        if(num <= singleCommissionBound['min']){
            num = singleCommissionBound['min'];
        }
        if(num >= singleCommissionBound['max']){
            num = singleCommissionBound['max'];
        }
        if (v.length > 0 && v.indexOf('.') == v.length - 1){
            this.value = v;
        }else{
            num = Math.round(num*10)/10;
            this.value = num;
        }
        updateSingleCommission(num);
    }).blur(function(){
        var num = Number(this.value.replace(/[^\d\.]/g, '')),
            bound = singleCommissionBound['max'] - singleCommissionBound['min'];
        num = num > singleCommissionBound['max'] ? singleCommissionBound['max'] : num;
        num = num < singleCommissionBound['min'] ? singleCommissionBound['min'] : num;
        num = Math.round(num*10)/10;
        this.value = num;
        updateSingleCommission(num);
    });;
    $('#J-input-multiple-commission').keyup(function(){
        var v = this.value.replace(/[^\d\.]/g, ''),
            bound = multipleCommissionBound['max'] - multipleCommissionBound['min'],
            bl = 0,
            num = Number(v);
        if(!num){
            num = multipleCommissionBound['min'];
        }
        if(num <= multipleCommissionBound['min']){
            num = multipleCommissionBound['min'];
        }
        if(num >= multipleCommissionBound['max']){
            num = multipleCommissionBound['max'];
        }
        if (v.length > 0 && v.indexOf('.') == v.length - 1){
            this.value = v;
        }else{
            num = Math.round(num*10)/10;
            this.value = num;
        }
        updateMultipleCommission(Number(num));

    }).blur(function(){
        var num = Number(this.value.replace(/[^\d\.]/g, '')),
            bound = multipleCommissionBound['max'] - multipleCommissionBound['min'];
        num = num > multipleCommissionBound['max'] ? multipleCommissionBound['max'] : num;
        num = num < multipleCommissionBound['min'] ? multipleCommissionBound['min'] : num;
        num = Math.round(num*10)/10;
        updateMultipleCommission(num);
    });
    /*===========================JC===========================*/

    //表单检测
    var global_isLoading = false;
    var username = $('#J-input-userName'),
        password = $('#J-input-password');
    function check_username_service(v, callback){
        $.ajax({
            url:"/auth/check-username-is-exist?username=" + v,
            dataType:'json',
            beforeSend:function(){
                global_isLoading = true;
            },
            success:function(data){
                if(callback){
                    callback(data);
                }
            },
            error:function(){
                alert('网络请求失败，请刷新页面重试');
            },
            complete:function(){
                global_isLoading = false;
            }
        });
    }
    function check_username(){
        var el = username,v = $.trim(el.val()),par = el.parents('li'),CLS = 'has-error',CLSR = 'has-right';
        par.find('.text-danger').text('用户名格式不对，请重新输入');
        if(!(/[A-Za-z0-9]{6,16}/).test(v)){
            par.addClass(CLS).removeClass(CLSR);
        }else{
            check_username_service(v, function(data){
                if(Number(data['isSuccess']) == 1){
                    par.removeClass(CLS).addClass(CLSR);
                }else{
                    par.addClass(CLS).removeClass(CLSR);
                    par.find('.text-danger').text('该用户名已被注册，请重新输入');
                }
            });
            
        }
    }
    username.blur(check_username);

    function check_password(){
        var el = password,v = $.trim(el.val()),par = el.parents('li'),CLS = 'has-error',CLSR = 'has-right';
        if(!(/[A-Za-z0-9]{6,16}/).test(v)){
            par.find('.text-danger').text('登录密码格式不对，请重新输入');
            par.addClass(CLS).removeClass(CLSR);
        }else if($.trim(username.val()) == $.trim(password.val())){
            par.find('.text-danger').text('登录密码不能和用户名相同，请重新输入');
            par.addClass(CLS).removeClass(CLSR);
        }else{
            par.removeClass(CLS).addClass(CLSR);
        }
    }
    password.blur(check_password);



    //表单提交
    $('#J-button-submit').click(function(e){
        var userName = $.trim($('#J-input-userName').val()),
            passWord = $.trim($('#J-input-password').val());
        if(global_isLoading){
            return false;
        }
        if(userName == ''){
            alert('请输入登录帐号');
            $('#J-input-userName').focus();
            return false;
        }
        if(!(/^[a-zA-Z0-9]{6,16}$/).test(userName)){
            alert('登录帐号格式不正确');
            $('#J-input-userName').focus();
            return false;
        }
        if(username.parents('li').hasClass('has-error')){
            return false;
        }
        if(passWord == ''){
            alert('请输入登录密码');
            $('#J-input-password').focus();
            return false;
        } else if (!(/^[a-zA-Z0-9]{6,16}$/).test(passWord)) {
            alert('密码格式不正确，请重新输入');
            $('#J-input-password').focus();
            return false;
        }
        if(password.parents('li').hasClass('has-error')){
            return false;
        }
        if(userName == passWord){
            alert('账号和密码不能相同');
            $('#J-input-password').focus();
            return false;
        }
        $('#J-form').submit();
    });





    (function(){
        $('.ct-input-point').blur(function(){
            var el = $(this),
                min = 0,
                max = Number(el.parent().find('.max-point').text()),
                setp = Number(el.parent().find('.setp').val()),
                v = Number(this.value),
                varr,
                vpoint;
            v = isNaN(v) ? 0 : v;
            v = v.toFixed(6);
            vpoint = (v - Math.floor(v)).toFixed(2);
            //console.log(vpoint * 10 , setp * 10);
            if(vpoint != 0){
                if(vpoint * 100 < setp * 100){
                    v = Math.floor(v) + setp;
                }else if(vpoint * 100 > setp * 100){
                    if((vpoint * 100)%(setp * 100) != 0){
                        if(el.attr('name') == 'commission_rate_2' || el.attr('name') == 'commission_rate_3' || el.attr('name') == 'commission_rate_4'){
                            v = Math.floor(v) + Math.floor(vpoint * 10)/10;
                        }else{
                            v = Math.floor(v) + setp * 2;
                        }
                    }
                }
            }


            v = v < min ? min : v;
            v = v > max ? max : v;
            v = (Math.floor(Number(v)*10)/10).toFixed(1);
            this.value = v;
        });

    })();





})();
</script>
@stop

