<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body onLoad="document.conformDepositForm.submit();">
        确认充值订单 ...
        <form action="{{ route('user-recharges.quick', $oPlatform->id) }}" method="post" id="J-form" name="conformDepositForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            @if ($oPlatform->need_bank)
            <input type="hidden" name="bank" value="{{ $oBank->id }}" />
            @endif
            <input type="hidden" name="amount" value="{{ $fAmount }}" />
            <input type="hidden" name="dodespoit" value="1" />
            <div class="content recharge-confirm">
                <table width="100%" class="table-field" id="J-table">
                    <tr>
                        <td align="right">充值渠道：</td>
                        <td align="left">{{ $oPlatform->display_name }}</td>
                    </tr>
                    @if ($oPlatform->need_bank)
                    <tr>
                        <td width="150" align="right" valign="top">充值银行：</td>
                        <td>
                            <label class="img-bank" for="J-bank-name-{{ $oBank->identifier }}" style="cursor:default;">
                                <input name="bank[]" id="J-bank-name-{{ $oBank->identifier }}" type="radio" style="visibility:hidden;" />
                                <span class="ico-bank {{ $oBank->identifier }}">{{$oBank->name}}</span>
                            </label>
                            <br />
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td align="right" valign="top">充值金额：</td>
                        <td>
                            {{ $sDisplayAmount }} 元
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </body>
</html>


