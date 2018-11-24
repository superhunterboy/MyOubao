<!DOCTYPE html>
<html>
<head>
	<title>网关支付</title>
</head>
<body>
    @if($iDepositWay == 'tonghuika')
	<form action="{{SysConfig::readValue('REQ_REFERER')}}paySubmit.php" method="post">
		<input type="hidden" name="<?=AppConstants::$INPUT_CHARSET?>" value="{{$aResponse[AppConstants::$INPUT_CHARSET]}}"/>
		<input type="hidden" name="<?=AppConstants::$NOTIFY_URL?>" value="{{$aResponse[AppConstants::$NOTIFY_URL]}}"/>
		<input type="hidden" name="<?=AppConstants::$RETURN_URL?>" value="{{$aResponse[AppConstants::$RETURN_URL]}}"/>
		<input type="hidden" name="<?=AppConstants::$PAY_TYPE?>" value="{{$aResponse[AppConstants::$PAY_TYPE]}}"/>
		<input type="hidden" name="<?=AppConstants::$BANK_CODE?>" value="{{$aResponse[AppConstants::$BANK_CODE]}}"/>
		<input type="hidden" name="<?=AppConstants::$MERCHANT_CODE?>" value="{{$aResponse[AppConstants::$MERCHANT_CODE]}}"/>
		<input type="hidden" name="<?=AppConstants::$ORDER_NO?>" value="{{$aResponse[AppConstants::$ORDER_NO]}}"/>
		<input type="hidden" name="<?=AppConstants::$ORDER_AMOUNT?>" value="{{$aResponse[AppConstants::$ORDER_AMOUNT]}}"/>
		<input type="hidden" name="<?=AppConstants::$ORDER_TIME?>" value="{{$aResponse[AppConstants::$ORDER_TIME]}}"/>
		<input type="hidden" name="<?=AppConstants::$PRODUCT_NAME?>" value="{{$aResponse[AppConstants::$PRODUCT_NAME]}}"/>
		<input type="hidden" name="<?=AppConstants::$PRODUCT_NUM?>" value="{{$aResponse[AppConstants::$PRODUCT_NUM]}}"/>
		<input type="hidden" name="<?=AppConstants::$REQ_REFERER?>" value="{{$aResponse[AppConstants::$REQ_REFERER]}}"/>
		<input type="hidden" name="<?=AppConstants::$CUSTOMER_IP?>" value="{{$aResponse[AppConstants::$CUSTOMER_IP]}}"/>
		<input type="hidden" name="<?=AppConstants::$CUSTOMER_PHONE?>" value="{{$aResponse[AppConstants::$CUSTOMER_PHONE]}}"/>
		<input type="hidden" name="<?=AppConstants::$RECEIVE_ADDRESS?>" value="{{$aResponse[AppConstants::$RECEIVE_ADDRESS]}}"/>
		<input type="hidden" name="<?=AppConstants::$RETURN_PARAMS?>" value="{{$aResponse[AppConstants::$RETURN_PARAMS]}}"/>
		<input type="hidden" name="<?=AppConstants::$SIGN?>" value="{{$aResponse[AppConstants::$SIGN]}}"/>
	</form>
    @elseif($iDepositWay == 'youfu')
     <form action="{{SysConfig::readValue('YOUFU_REFERER')}}/paySubmit.php" method="post">
        <input type="hidden" name="VERSION" value="{{$aResponse['VERSION']}}"/>
        <input type="hidden" name="INPUT_CHARSET" value="{{$aResponse['INPUT_CHARSET']}}"/>
        <input type="hidden" name="RETURN_URL" value="{{$aResponse['RETURN_URL']}}"/>
        <input type="hidden" name="NOTIFY_URL" value="{{$aResponse['NOTIFY_URL']}}"/>
        <input type="hidden" name="BANK_CODE" value="{{$aResponse['BANK_CODE']}}"/>
        <input type="hidden" name="MER_NO" value="{{$aResponse['MER_NO']}}"/>
        <input type="hidden" name="ORDER_NO" value="{{$aResponse['ORDER_NO']}}"/>
        <input type="hidden" name="ORDER_AMOUNT" value="{{$aResponse['ORDER_AMOUNT']}}"/>
        <input type="hidden" name="PRODUCT_NAME" value="{{$aResponse['PRODUCT_NAME']}}"/>
        <input type="hidden" name="PRODUCT_NUM" value="{{$aResponse['PRODUCT_NUM']}}"/>
        <input type="hidden" name="REFERER" value="{{$aResponse['REFERER']}}"/>

        <input type="hidden" name="CUSTOMER_IP" value="{{$aResponse['CUSTOMER_IP']}}"/>
        <input type="hidden" name="CUSTOMER_PHONE" value="{{$aResponse['CUSTOMER_PHONE']}}"/>
        <input type="hidden" name="RECEIVE_ADDRESS" value="{{$aResponse['RECEIVE_ADDRESS']}}"/>
        <input type="hidden" name="RETURN_PARAMS" value="{{$aResponse['RETURN_PARAMS']}}"/>

        <input type="hidden" name="SIGN" value="{{$aResponse['SIGN']}}"/>
    </form>
    @else
    @endif
	<script type="text/javascript">
		document.forms[0].submit();
	</script>
</body>
</html>
