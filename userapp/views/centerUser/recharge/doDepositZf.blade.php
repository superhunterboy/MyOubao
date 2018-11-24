<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body onLoad="document.dinpayForm.submit();">
        正在跳转 ...
        <form name="dinpayForm" method="post" action="{{ $sUrl }}">
            @foreach($aInputData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            @endforeach
        </form>
    </body>
</html>