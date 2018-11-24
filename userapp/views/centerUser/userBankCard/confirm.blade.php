@extends('l.home')

@section('title') 
    确认银行卡信息
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
        border-top: 0;
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
                @include('w.uc-menu-user')
            </div>
            


            <div class="page-content-inner page-content-inner-bg">


                <div class="step">
                    <table class="step-table">
                        <tbody>
                        @if (isset($bIsFirst) && $bIsFirst)
                            <tr>
                                <td class="clicked"><div class="con"><i>1</i>输入银行卡信息</div></td>
                                <td class="current"><div class="tri"><div class="con"><i>2</i>确认银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>3</i>绑定结果</div></div></td>
                            </tr>
                        @else
                            <tr>
                                <td class="clicked"><div class="con"><i>1</i>验证老银行卡</div></td>
                                <td class="clicked"><div class="tri"><div class="con"><i>2</i>输入银行卡信息</div></div></td>
                                <td class="current"><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

            <form action="{{ $iCardId ? route('bank-cards.modify-card', [3, $iCardId]) : route('bank-cards.bind-card', 3) }}" method="post" id="J-form" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                @if ($iCardId)
                <input type="hidden" name="method" value="PUT" />
                @endif
                <input type="hidden" name="bank_id" value="{{ isset($aFormData['bank_id']) ? $aFormData['bank_id'] : Input::get('bank_id') }}" />
                <input type="hidden" name="bank" value="{{ isset($aFormData['bank']) ? $aFormData['bank'] : Input::get('bank') }}" />
                <input type="hidden" name="province_id" value="{{ isset($aFormData['province_id']) ? $aFormData['province_id'] : Input::get('province_id') }}" />
                <input type="hidden" name="province" value="{{ isset($aFormData['province']) ? $aFormData['province'] : Input::get('province') }}" />
                <input type="hidden" name="city_id" value="{{ isset($aFormData['city_id']) ? $aFormData['city_id'] : Input::get('city_id') }}" />
                <input type="hidden" name="city" value="{{ isset($aFormData['city']) ? $aFormData['city'] : Input::get('city') }}" />
                <input type="hidden" name="town" value="{{ isset($aFormData['town']) ? $aFormData['town'] : Input::get('town') }}" />
                <input type="hidden" name="town_id" value="{{ isset($aFormData['town_id']) ? $aFormData['town_id'] : Input::get('town_id') }}" />
                <input type="hidden" name="branch" value="{{ isset($aFormData['branch']) ? $aFormData['branch'] : Input::get('branch') }}" />
                <input type="hidden" name="account_name" value="{{ isset($aFormData['account_name']) ? $aFormData['account_name'] : Input::get('account_name') }}" />
                <input type="hidden" name="account" value="{{ isset($aFormData['account']) ? $aFormData['account'] : Input::get('account') }}" />
                <input type="hidden" name="account_confirmation" value="{{ isset($aFormData['account_confirmation']) ? $aFormData['account_confirmation'] : Input::get('account_confirmation') }}" />
                <table width="100%" class="table-field">
                    <tr>
                        <td align="right">开户银行：</td>
                        <td>{{ isset($aFormData['bank']) ? $aFormData['bank'] : Input::get('bank') }}</td>
                    </tr>
                    <tr>
                        <td align="right">开户银行区域：</td>
                        <td>
                            {{ isset($aFormData['province']) ? $aFormData['province'] : Input::get('province') }}&nbsp;&nbsp;
                            {{ isset($aFormData['city']) ? $aFormData['city'] : Input::get('city') }}&nbsp;&nbsp;
                            {{ isset($aFormData['town']) ? $aFormData['town'] : Input::get('town') }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right">支行名称：</td>
                        <td>{{ isset($aFormData['branch']) ? $aFormData['branch'] : Input::get('branch') }}</td>
                    </tr>
                    <tr>
                        <td align="right">开户人姓名：</td>
                        <td>{{ isset($aFormData['account_name']) ? $aFormData['account_name'] : Input::get('account_name') }}</td>
                    </tr>
                    <tr>
                        <td align="right">银行账号：</td>
                        <td>{{ isset($aFormData['account']) ? $aFormData['account'] : Input::get('account') }}</td>
                    </tr>
                    <tr>
                        <td align="right"></td>
                        <td>
                            <input type="submit" value="确认提交" class="btn" id="J-submit">
                            <!-- <input type="button" value="返回上一步" class="btn btn-normal" id="J-button-back"> -->
                            <!-- <a class="btn" href="{{-- route('bank-cards.result') --}}">确认提交</a> -->
                            <a class="btn btn-normal" href="{{ $iCardId ? route('bank-cards.modify-card', [1, $iCardId]) : route('bank-cards.bind-card', 1) }}">返回上一步</a>
                        </td>
                    </tr>
                </table>
            </form>

            </div>
        </div>
    </div>

    @include('w.footer')
@stop

@section('end')
@parent
<script>
    (function($){

        $('#J-button-back').click(function(){
            history.back(-1);
        });

    })(jQuery);
</script>
@stop


