<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body onLoad="document.payForm.submit();">
        正在跳转 ...
        <form name="payForm" method="post" action="{{ $sUrl }}">
            @foreach($aInputData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            @endforeach
            <input type="hidden" name="___DepositUrl" value="{{ $___DepositUrl }}" />
        </form>
    </body>
</html>