@extends('l.home')

@section('title')
安全口令
@parent
@stop


@section ('styles')
@parent
{{ style('proxy-global') }}
{{ style('proxy') }}
<style type="text/css">
    .layout-row {float: left;}
</style>
<style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
    }
</style>
@stop



@section ('container')
@include('w.header')
<div class="banner">
    <img src="/assets/images/proxy/banner.jpg" width="100%" />
</div>
<div class="page-content page-content-password">
    <div class="g_main clearfix">
        @include('w.manage-menu')

        <div class="nav-inner clearfix">
            @include('w.uc-menu-user')

            @if(empty($oRes))
            <div class="safe-main">
                <form id='safe1' action="{{route('security-questions.checkrules')}}" method="post">
                    <ul class="safe-notice">
                        <li class="notice1">您还没有设置安全口令，为了您的资金安全，请您进行设置！安全口令是您进行资金操作时的双重保护！</li>
                        <li class="notice2">为了避免遗忘，请您填写真实信息，平台将在您执行重要资金操作时向您验证！</li>
                        <li class="notice3">安全口令问题设置后不可更改，请谨慎设置！</li>
                    </ul>

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <ul class="safe-block">
                        <li class="safe-list">
                            <span class="safe-title">问题一:</span>
                            <select id='J-select-question1' name="safe-question[]">
                                <option value="0" selected>请选择您的安全口令问题</option>
                                @foreach($aQuestions as $k=>$oData)
                                <option value ="{{$oData->id}}">{{$oData->content}}</option>
                                @endforeach
                            </select>
                        </li>
                        <li class="safe-list">
                            <span class="safe-title">答案:</span>
                            <input class="safe-answer" maxlength="10" type="text" name="safe-answer[]"/>
                        </li>

                        <li class="safe-list">
                            <span class="safe-title">问题二:</span>
                            <select id='J-select-question2' name="safe-question[]">
                                <option value="0" selected>请选择您的安全口令问题</option>
                                @foreach($aQuestions as $k=>$oData)
                                <option value ="{{$oData->id}}">{{$oData->content}}</option>
                                @endforeach
                            </select>
                        </li>
                        <li class="safe-list">
                            <span class="safe-title">答案:</span>
                            <input class="safe-answer" maxlength="10"  type="text" name="safe-answer[]"/>
                        </li>

                        <li class="safe-list">
                            <span class="safe-title">问题三:</span>
                            <select id='J-select-question3' name="safe-question[]">
                                <option value="0" selected>请选择您的安全口令问题</option>
                                @foreach($aQuestions as $k=>$oData)
                                <option value ="{{$oData->id}}">{{$oData->content}}</option>
                                @endforeach
                            </select>
                        </li>
                        <li class="safe-list">
                            <span class="safe-title">答案:</span>
                            <input class="safe-answer" maxlength="10"  type="text" name="safe-answer[]"/>
                        </li>
                    </ul>
                    <div class="error"></div>
                    <input class="btn next-step" type="submit" value="下一步">
                    <!--<input class="btn next-step" type="button" value="下一步">-->

                </form>
            </div>


            @else
            <div class="seted"><span>您已设置安全口令，安全口令不可修改！</span></div>
            @endif
        </div>

    </div>

</div>

@include('w.footer')
<script>
    window.onload=function () {
        new bomao.Select({realDom:'#J-select-question1',cls:'safe-question'});
        new bomao.Select({realDom:'#J-select-question2',cls:'safe-question'});
        new bomao.Select({realDom:'#J-select-question3',cls:'safe-question'});

        $('.choose-list-cont a').on('click',function (e) {
            var _c=$(this).attr('data-value'),
                    _d = $(this).parents('.safe-list').siblings('.safe-list').find('.safe-question .choose-list-cont a');

            //点击后其他选择框删除这个选项
            _d.each(function () {
                if($(this).attr('data-value')===_c){
                    $(this).hide();
                }
            });
        });

        $('.safe-question').on('click',function () {
            var _this = $(this);
            //点击标题后全部显示
            $('.safe-question .choose-list-cont a').removeAttr('style');

            //删除已经选择了的选项
            $('.choose-list-cont .choose-item-current').each(function () {
                var _aa =$(this).attr('data-value');
                _this.find('.choose-list-cont a').each(function () {
                    if($(this).attr('data-value')===_aa&&$(this).attr('data-value')!='0'){
                        $(this).hide();
                    }
                });
            })
        });

        //解决IE下，答案超过10个字符截断
        $('.safe-answer').blur(function () {
            var _a =$(this).val().length;
            if(_a>10){
                $(this).val($(this).val().slice(0,10));
            }
        });

    }

</script>
@stop



@section('end')

@parent
@stop

