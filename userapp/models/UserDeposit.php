<?php

class UserDeposit extends Deposit {

    const ERRNO_DEPOSIT_ERROR_00 = -1800;
    const ERRNO_DEPOSIT_ERROR_01 = -1801;
    const ERRNO_DEPOSIT_ERROR_02 = -1802;
    const ERRNO_DEPOSIT_ERROR_03 = -1803;
    const ERRNO_DEPOSIT_ERROR_04 = -1804;
    const ERRNO_DEPOSIT_ERROR_05 = -1805;
    const ERRNO_DEPOSIT_ERROR_06 = -1806;
    const ERRNO_DEPOSIT_ERROR_07 = -1807;
    const ERRNO_DEPOSIT_ERROR_08 = -1808;
    const ERRNO_DEPOSIT_ERROR_09 = -1809;
    const ERRNO_DEPOSIT_UNKNOWN_ERROR = -1810;
    const ERRNO_DEPOSIT_AMOUNT_OUT_RANGE = -1811;

    public static $columnForList = [];

    protected function beforeValidate() {
        if (empty($this->order_no)) {
            $this->order_no = $this->compileOrderNo();
        }
        return parent::beforeValidate();
    }

    /**
     * 生成平台充值订单号
     * @return string
     */
    public static function compileOrderNo() {
        return uniqid(mt_rand());
    }

    public static function compileSignZf($aInputData, $sMerchantKey) {
        ksort($aInputData);
//            pr($aInputData);
        $aInputValues = [];
        $sQueryString = '';
        $sForSafe = '';
        foreach ($aInputData as $key => $value) {
            if (!empty($value)) {
                $aInputValues[] = "$key=$value";
            }
        }
        $sQueryString = implode('&', $aInputValues);
        return md5($sQueryString . '&key=' . $sMerchantKey);
    }

    private static function & compileData($iMode = self::DEPOSIT_MODE_THIRD_PART, $oUser, $oBank, $fAmount, $oPayment = null, $aBankcardInfo = null, $oPaymentAccount = null) {
        if ($oUser->parent_id) {
            $aForeFathers = explode(',', $oUser->forefathers);
            $aForeFatherIds = explode(',', $oUser->forefather_ids);
            $iTopAgentId = $aForeFatherIds[0];
            $sTopAgent = $aForeFathers[0];
        } else {
            $iTopAgentId = $oUser->id;
            $sTopAgent = $oUser->username;
        }
        $aDepositInfo = [
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'is_tester' => $oUser->is_tester,
            'is_agent' => $oUser->is_agent,
            'user_parent' => $oUser->parent,
            'user_forefather_ids' => $oUser->forefather_ids,
            'top_agent_id' => $iTopAgentId,
            'top_agent' => $sTopAgent,
            'bank_id' => is_object($oBank) ? $oBank->id : null,
            'amount' => $fAmount,
            'deposit_mode' => $iMode,
            'status' => Deposit::DEPOSIT_STATUS_RECEIVED,
            'ip' => get_client_ip(),
        ];
        $aDepositInfo['postscript'] = self::compilePostscript();
        if ($iMode == self::DEPOSIT_MODE_BANK_CARD) {
            $aDepositInfo['accept_card_num'] = $aBankcardInfo['account_no'];
            $aDepositInfo['accept_email'] = $aBankcardInfo['email'];
            $aDepositInfo['accept_acc_name'] = $aBankcardInfo['owner'];
        } else {
            $aDepositInfo['platform_id'] = $oPayment->id;
            $aDepositInfo['platform'] = $oPayment->name;
            $aDepositInfo['platform_identifier'] = $oPayment->identifier;
            $aDepositInfo['query_enabled'] = $oPayment->query_enabled;
            $aDepositInfo['account_no'] = $oPaymentAccount->serial_number;
            $aDepositInfo['merchant_key'] = $oPaymentAccount->safe_key;
            $aDepositInfo['merchant_code'] = $oPaymentAccount->account;
        }
        return $aDepositInfo;
    }

    public static function addDeposit($iMode = self::DEPOSIT_MODE_THIRD_PART, $oUser, $oBank = null, $fAmount = null, $oPayment = null, $aBankcardInfo = null, $oPaymentAccount = null) {
        $data = & self::compileData($iMode, $oUser, $oBank, $fAmount, $oPayment, $aBankcardInfo, $oPaymentAccount);
        $oDeposit = new UserDeposit($data);
        if ($bSucc = $oDeposit->save()) {
            $oDeposit->setNewFlag();
        }
        return $bSucc ? $oDeposit : false;
    }

    public static function compilePostscript() {
    	//将附言长度改为6位字母和数字的组合
    	$aSeed = array_merge(range('A', 'Z'),range(0, 9));
		$str = '';
        for ($i=0;$i<6;$i++)
		{
			shuffle($aSeed);
			$n = mt_rand(0, 35);
			$str .= $aSeed[$n];
		}
		return $str;
        /**
        $a = mt_rand(0, 25);
        $c = chr(0x41 + $a);
        $n = mt_rand(0, pow(10, 9) - 1);
        return $c . str_pad($n, 9, 0, STR_PAD_LEFT);*/
    }

    /**
     * 获取当天大于指定金额的充值记录
     * @param type $iUserId
     */
    public static function getFirstDepositByDate($iUserId, $fAmount = 0, $sBeginDate = null) {
        $oQuery = self::where('user_id', '=', $iUserId)->where('amount', '>=', $fAmount)->where('status', '=', UserDeposit::DEPOSIT_STATUS_SUCCESS);
        empty($sBeginDate) or $oQuery = $oQuery->where('created_at', '>=', $sBeginDate);
        $oQuery = $oQuery->orderBy('created_at', 'asc');
        return $oQuery->first();
    }

}
