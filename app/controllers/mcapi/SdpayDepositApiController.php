<?php

use SoapBox\Formatter\Formatter;

class SdpayDepositApiController extends Controller {

    /**
     * 充值响应验证规则
     * @var array
     */
    private $approveApiRules = [
        'money' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'order' => 'required|between:1,64',
        'username' => '',
        'unit' => 'required:in:1',
        'result' => 'required:in:0,1,2',
    ];

    /**
     * cmd
     */
//    const RESPOSNCMD = "60071";
//    const  CMD='6007';
    //merchantid
//    const MERCHANTID = "DY9793";
    //md5 key
//    const KEY3 = "fb2d4ac3ddd441229a13a122fdded4f4";
//    const KEY1='/bYmeXhZaOY=';
//    const KEY2='Api2Zp2TT5A=';

    public function index() {

// 接受请求
        $aRequest = trimArray(Input::all());
        //写日志
        $this->writeLog(['url' => __CLASS__, 'date' => date('Y-m-d H:i:s'), 'request' => trimArray(Input::all())]);

        if (empty($aRequest) || !isset($aRequest['pid']) || !isset($aRequest['res'])) {
            $aRequest['response_code'] = 102;
            return $this->makeResponse(null, $aRequest);
        }

        $aRequest = $this->getArrayFromDes($aRequest);
//        pr($aRequest);
        if (!$aRequest) {
            //解密失败103
            $aRequest['response_code'] = 103;
            return $this->makeResponse(null, $aRequest);
        }

        //商户号码
        if (empty($aRequest['merchantid']) || !isset($aRequest['merchantid']) || $aRequest['merchantid'] != Deposit::SDPAYMERCHANTID) {
            $aRequest['response_code'] = 101;
            return $this->makeResponse(null, $aRequest);
        }
        //cmd
        if (empty($aRequest['cmd']) || !isset($aRequest['cmd']) || $aRequest['cmd'] != Deposit::SDPAYCALLBACKCMD) {
            $aRequest['response_code'] = 104;
            return $this->makeResponse(null, $aRequest);
        }
        // 数据检查 102
        if (!$this->verifyApproveData($aRequest)) {
            $aRequest['response_code'] = 102;
            return $this->makeResponse(null, $aRequest);
        }
// 记录推送数据
        $aInitData = [
            'pay_time' => date('Y-m-d H:i:s'),
            'amount' => array_get($aRequest, 'money'),
            'company_order_num' => array_get($aRequest, 'order'),
            'cmd' => array_get($aRequest, 'cmd'),
            'username' => array_get($aRequest, 'username'),
            'unit' => array_get($aRequest, 'unit'),
            'result' => array_get($aRequest, 'result'),
            'merchantid' => array_get($aRequest, 'merchantid'),
            'status' => array_get($aRequest, 'result'),
        ];

// 事务前保存所有推送信息
        $oSdpayDepositCallback = SdpayDepositCallback::createCallback($aInitData);
        if (!$oSdpayDepositCallback) {
            $aRequest['response_code'] = 105;
            return $this->makeResponse(null, $aRequest);
        }

        $oDeposit = Deposit::findDepositByCompanyOrderNum($aInitData['company_order_num']);
// 订单是否存在 106
        if (!$oDeposit || $oDeposit->deposit_mode != Deposit::DEPOSIT_MODE_SDPAY || $oDeposit->username != $oSdpayDepositCallback->username) {
            $oSdpayDepositCallback->response_code = 106;
            $oSdpayDepositCallback->setResponseFailed('DEPOSIT NOT FOUND');
            return $this->makeResponse($oSdpayDepositCallback);
        }

// 订单是否在待处理状态 105
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_RECEIVED) {
            $oSdpayDepositCallback->response_code = 105;
            $oSdpayDepositCallback->setResponseFailed('REPEAT RECORD ERROR');
            return $this->makeResponse($oSdpayDepositCallback);
        }

//金额是否正常 105
        if ($oDeposit->amount != $oSdpayDepositCallback->amount) {
            $oSdpayDepositCallback->response_code = 105;
            $oSdpayDepositCallback->setResponseFailed('AMOUNT ERROR');
            return $this->makeResponse($oSdpayDepositCallback);
        }


//如果充值失败
        if (array_get($aRequest, 'result') == 0) {
            return $this->setSdpayDepositWaitting($oSdpayDepositCallback);
        } else if (array_get($aRequest, 'result') == 2) {
            return $this->setSdpayDepositFail($oSdpayDepositCallback, $oDeposit);
        }


//充值余额和帐变
        if ($this->addAccount($oDeposit, $oSdpayDepositCallback)) {
//        if(1){
            //手续费
            $resultAccountFee = $this->addAccountFee($oDeposit, $oSdpayDepositCallback);
            $oSdpayDepositCallback->response_code = 100;
            $oSdpayDepositCallback->setResponseSuccessful();
            return $this->makeResponse($oSdpayDepositCallback);
        } else {
            $oSdpayDepositCallback->response_code = 105;
            $oSdpayDepositCallback->setResponseFailed('UPDATE STATUS ERROR');
            return $this->makeResponse($oSdpayDepositCallback);
        }
    }

    /**
     * 充值
     */
    private function addAccount($oDeposit,  &$oSdpayDepositCallback) {

        $oUser = User::find($oDeposit->user_id);
        $oAccount = Account::getAccountInfoByUserId($oDeposit->user_id);
        Account::lock($oUser->account_id, $iLocker);
        DB::connection()->beginTransaction();
        try {
// 更新订单状态
            $bSuccessful = $oDeposit->setSuccess([
                'real_amount' => $oSdpayDepositCallback->amount,
//                'fee' => $fBankFee,
                'pay_time' => $oSdpayDepositCallback->pay_time,
            ]);

            if (!$bSuccessful) { // 是否更新状态成功
                throw new Exception('UPDATE STATUS ERROR');
            }
// 增加游戏币
//            $dDepositDate = date('Y-m-d');
            $aExtraData = [
                'note' =>  Deposit::DEPOSIT_MODE_SDPAY,
            ];
            $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_DEPOSIT, $oSdpayDepositCallback->amount, $aExtraData);
// 添加状态是否成功
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                throw new Exception('ADD COIN ERROR');
            }
        } catch (Exception $e) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $oDeposit->setAddFail();
            $oSdpayDepositCallback->setResponseFailed($e->getMessage());
            return false;
        }

        DB::connection()->commit();
        Account::unlock($oUser->account_id, $iLocker);
        return true;
    }

    /**
     * 充值手续费
     */
    private function addAccountFee($oDeposit, &$oSdpayDepositCallback) {
        $fBankFee = Bank::calculateSdpayBankFee($oSdpayDepositCallback->amount);
        if ($fBankFee <= 0) {
            //如果手续非为0  成功
            return true;
        }
        $oUser = User::find($oDeposit->user_id);
        $oAccount = Account::getAccountInfoByUserId($oDeposit->user_id);
        Account::lock($oUser->account_id, $iLocker);
        DB::connection()->beginTransaction();
        try {
// 更新订单手续费状态
            $bSuccessful = $oDeposit->setDeductFeeSuccess([
                'fee' => $fBankFee,
            ]);

            if (!$bSuccessful) { // 是否更新状态成功
                throw new Exception('UPDATE STATUS ERROR');
            }
// 扣除手续费
            $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_WITHDRAW_FEE, $fBankFee);
// 添加状态是否成功
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                throw new Exception('BANK CHARGE ERROR');
            }
        } catch (Exception $e) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $oDeposit->setDeductFeeFail();
            $oSdpayDepositCallback->setResponseFailed($e->getMessage());
            return ['status' => '0', 'msg' => $e->getMessage()];
        }
        DB::connection()->commit();
        Account::unlock($oUser->account_id, $iLocker);



        return ['status' => '1', 'msg' => 'success'];
    }

    /**
     * 充值失败
     * 订单装状态充值失败
     */
    private function setSdpayDepositFail(&$oSdpayDepositCallback, $oDeposit) {

        DB::connection()->beginTransaction();
        try {
// 更新订单手续费状态
            if (!$oDeposit->setDeductFeeFail()) {
                throw new Exception('SET DEDUCT FEE  ERROR');
            }
            if (!$oDeposit->setDepositFail()) {
                throw new Exception('SET SDPAY DEPOSIT CALLBACK  ERROR');
            }
        } catch (Exception $e) {

            $oSdpayDepositCallback->response_code = 105;
            DB::connection()->rollback();
            $oSdpayDepositCallback->setResponseFailed($e->getMessage());
            return $this->makeResponse($oSdpayDepositCallback);
        }
        $oSdpayDepositCallback->response_code = 100;
        DB::connection()->commit();
        $oSdpayDepositCallback->setResponseSuccessful();
        return $this->makeResponse($oSdpayDepositCallback);
    }

    /**
     * 充值等待
     * 直接返回接受成功
     */
    private function setSdpayDepositWaitting(&$oSdpayDepositCallback) {

        if ($oSdpayDepositCallback->setResponseSuccessful()) {
            $oSdpayDepositCallback->response_code = 100;
        } else {
            $oSdpayDepositCallback->response_code = 105;
        }
        return $this->makeResponse($oSdpayDepositCallback);
    }

    /*     * *
     * des解密数据
     * 
     */

    private function getArrayFromDes($aRequest) {
//    pr($aRequst);exit;
        $decryptStr = $this->decryptData($aRequest['res']);
        $xml = substr($decryptStr, 0, strlen($decryptStr) - 32);
        $sMd5 = substr($decryptStr, strlen($decryptStr) - 32, 32);
        $md5Str = md5($xml . Deposit::SDPAYKEY3);
//md5验证失败  102
        if ($sMd5 != $md5Str) {
            $aRequest['response_code'] = 102;
            return $this->makeResponse(null, $aRequest);
        }
        $axml = $this->compileReposeData($xml);
        return $axml;
    }

    private function compileReposeData($xml) {
        $formatter = Formatter::make($xml, Formatter::XML);
        return $formatter->toArray();
    }

    private function encryptData($xml) {
        return $xml;
        //todo
        $mencrypt = new SDPayEncrypt(Deposit::SDPAYKEY1,  Deposit::SDPAYKEY2);
        $md5Str = md5($xml . Deposit::SDPAYKEY3);
        $tempStr = $xml . $md5Str;
        return $mencrypt->encryptData($tempStr);
    }

    private function decryptData($resultStr) {
        //todo
        $decrypt = new SDPayEncrypt(Deposit::SDPAYKEY1,  Deposit::SDPAYKEY2);
        $sXML=$decrypt->decryptData($resultStr);
        $this->writeLog(['url' => __CLASS__, 'date' => date('Y-m-d H:i:s'), 'request_xml' =>$sXML]);
        return $sXML;
    }

    /**
     * 验证充值确认推送数据
     * @param array $aRequest 得到的推送信息
     * @return boolean
     */
    private function verifyApproveData(array $aRequest) {
        $validator = Validator::make($aRequest, $this->approveApiRules);
        if (!$validator->passes()) {
//            pr($validator->getMessageBag()->toArray());exit;
            return false;
        }
        return true;
    }

    /**
     * 响应请求
     * @param type $sMessage 指定要反馈的信息
     * @param Sdpay $oSdpayDepositCallback 提现推送对象
     * @return JsonResponse
     */
    private function makeResponse(SdpayDepositCallback $oSdpayDepositCallback = null, $aRequest = null) {
//    pr($oSdpayDepositCallback->toArray());exit;
        if ($oSdpayDepositCallback) {
            $aRequest = [ 'response_code' => $oSdpayDepositCallback->response_code,
                'order' => $oSdpayDepositCallback->company_order_num, 'username' => $oSdpayDepositCallback->username
            ];
        }
        $sResponse = $this->formatterData($aRequest);

        $sDes = $this->encryptData($sResponse);

        $this->writeLog(['url' => __CLASS__, 'date' => date('Y-m-d H:i:s'), 'request' => trimArray($aRequest), 'response' => $sDes]);

        echo $sDes;
        exit;
    }

    /*
     * 格式化返回至
     */

    private function formatterData($aRequest) {
        $aData=[ 'order'=>'',  'username'=>'','response_code'=>''];
        $aRequest=array_merge($aData,$aRequest);
        $responseStr = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>" .
                "<message>" .
                "<cmd>" . Deposit::SDPAYRESPOSNCMD. "</cmd>" .
                "<merchantid>" . Deposit::SDPAYMERCHANTID . "</merchantid>" .
                        "<order>" .  $aRequest['order'] . "</order>" .
                                "<username>" .$aRequest['username']. "</username>" .
                                        "<result>" . $aRequest['response_code'] . "</result>" .
                                        "</message>";
        return $responseStr;
    }

    /**
     * 写充值日志
     * @param string|array $msg
     */
    protected function writeLog($msg) {
        !is_array($msg) or $msg = var_export($msg, true);
        @file_put_contents('/tmp/bomao_sdpay_deposit_' . date('y-m-d'), $msg . "\n", FILE_APPEND);
    }

}
