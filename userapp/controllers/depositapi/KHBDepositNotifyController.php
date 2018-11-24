<?php

/**
 * Description of BaseDepositNotifyController
 *
 */
class KHBDepositNotifyController extends Controller {

    protected $logFile;
    protected $logPath;
    protected $platformIdentifier = 'khb';
    protected $Payment;
    protected $Platform;
    protected $PaymentAccount;
    protected $serverIP;
    protected $Callback;
    protected $params;
    protected $test = false;

    protected function writeLog($sLog) {
        file_put_contents($this->logFile, $sLog . "\n", FILE_APPEND);
    }

    protected function halt($sLog, $bDisplay = true) {
        $this->writeLog($sLog);
        $bDisplay ? die($sLog) : exit;
    }

    protected function & mkTestData() {
        
    }

    protected function init() {
        set_time_limit(0);
//        $this->Payment = PaymentPlatform::getObject($this->platformIdentifier);
//        $this->Platform = $this->Payment->platform;
        $this->compileLogFile($this->platformIdentifier);
        $this->params = $this->test ? $this->mkTestData() : trimArray(Input::all());
    }

    protected function saveCallbackHistory() {
        $this->Callback = $this->Payment->addCallBackHistory($this->params, $this->serverIP);
    }

    protected function clearNoSignValues() {
        foreach ($this->Platform->unSignColumns as $sColumn) {
            unset($this->params[$sColumn]);
        }
    }

    protected function checkSign(& $sSign) {
        $sSign = $sPostedSign = array_get($this->params,'Keys');
        if($sPostedSign == 'TJPyKtAOJd8VpJSC'){
            return true;
        }else{
            return false;
        }
//        $this->clearNoSignValues();
////        pr($this->params);
//        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->params[$this->Platform->accountColumn]);
////        pr($this->PaymentAccount->toArray());
////        pr($this->params);
//        $sSign = $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
//        return $sSign == $sPostedSign;
    }

    protected function checkIP() {
        return !$this->Payment->check_ip || in_array($this->serverIP, $this->Payment->available_ip);
    }

    protected function checkSuccessFlag() {
        return $this->params[''] == 'success';
    }

    protected function checkStatus($oUserDeposit) {
        return in_array($oUserDeposit->status, [Deposit::DEPOSIT_STATUS_NEW, Deposit::DEPOSIT_STATUS_RECEIVED]);
    }

    protected function query(& $sMsg) {
        $this->writeLog('Query: ' . $this->Payment->query_on_callback);
        if (!$this->Payment->query_on_callback) {
            return true;
        }
        $sOrderNo = $this->params[$this->Platform->orderNoColumn];
        $this->writeLog('Query: Starting Query');
        $iQueryResult = $this->Payment->queryFromPlatform($this->PaymentAccount, $sOrderNo, null, $aResonses);
        $this->writeLog(var_export($aResonses, true));
//        pr($aResonses);
//        exit;
        $iQueryResult = intval($iQueryResult);
        $bSucc = false;
        switch ($iQueryResult) {
            case BasePlatform::PAY_SUCCESS:
                $bSucc = true;
                break;
            case BasePlatform::PAY_QUERY_FAILED:
                $sMsg = 'Query Success, But Error:' . $aResonses[$this->Payment->queryResultColumn];
                break;
            case BasePlatform::PAY_QUERY_FAILED:
                $sMsg = 'Query Fail';
                break;
            case BasePlatform::PAY_QUERY_PARSE_ERROR:
                $sMsg = 'Query Success, But Parse Error';
                break;
            case BasePlatform::PAY_SIGN_ERROR:
                $sMsg = 'Query Success, Sign Error';
                break;
            case BasePlatform::PAY_NO_ORDER:
                $sMsg = 'Query Success, But No Order';
                break;
            case BasePlatform::PAY_UNPAY:
                $sMsg = 'Query Success, But UnPay';
                break;
            default:
                $bSucc = false;
        }
        return $bSucc;
    }

    public function doCallback() {
        $this->init();
        $this->writeLog(date('Y-m-d H:i:s'));
        $this->serverIP = get_client_ip();
        $this->writeLog($this->serverIP);
        if(empty($this->params)){
            $this->halt('invalid request', false);
        }
        if (key_exists('msg', $this->params)) {
            unset($this->params['msg']);
        }
        $this->writeLog(var_export($this->params, true));
        if (empty($this->params)) {
            $this->halt('invalid callback', false);
        }
//        $this->saveCallbackHistory();
        if (!$this->checkSign($sSign)) {
//            $this->Callback->setSignError();
            $this->writeLog($sSign);
            $this->halt("Sign Error", false);
        }
//        pr($this->Payment->ip);
//        if (!$this->checkIP()) {
//            $this->Callback->setIpError();
//            $this->halt("Error IP: $this->serverIP", false);
//        }
//        if (!$this->checkSuccessFlag()) {
////            $this->Callback->setPaymentError();
//            $this->halt("Service Failed");
//        }

//        if (!$this->query($sErrMsg)) {
//            $this->writeLog($sErrMsg);
//            $this->halt($sErrMsg);
//        }

//        $sOrderNo = $this->params[$this->Platform->orderNoColumn];
//        $oUserDeposit = Deposit::getDepositByNo($sOrderNo);
           $oUserDeposit = Deposit::getObjectByParams(['postscript'=>array_get($this->params, 'PayUser')]);
//        pr($oUserDeposit->toArray());
//        exit;
        if (empty($oUserDeposit)) {
//            $this->Callback->setMissingData();
            $this->halt("Deposit ". array_get($this->params, 'PayUser') ." Not Exists");
        }
        if (array_get($this->params,'Money') != $oUserDeposit->amount) {
//            $this->Callback->setAmountError();
            $this->halt("Wrong Deposit Amount");
        }
        // 判断状态
        if (!$this->checkStatus($oUserDeposit)) {
//            $this->Callback->setDepositStatusError();
            $this->halt("Status Error: $oUserDeposit->status");
        }

        $oUserDeposit->service_order_status = 'success';
        $oUserDeposit->service_order_no = $this->params['NumID'];
//        if ($this->Platform->bankNoColumn && array_key_exists($this->Platform->bankNoColumn, $this->params)) {
//            $oUserDeposit->service_bank_seq_no = $this->params[$this->Platform->bankNoColumn];
//        }
//        if ($this->Platform->serviceOrderTimeColumn && array_key_exists($this->Platform->serviceOrderTimeColumn, $this->params)) {
//            $oUserDeposit->service_time = $this->params[$this->Platform->serviceOrderTimeColumn];
//        }

//        $oUserDeposit->service_bank_seq_no  = $this->params[$this->Platform->bankNoColumn];
//        if (isset($this->params[$this->Platform->bankTimeColumn])) {
//            $oUserDeposit->pay_time = date('Y-m-d H:i:s', strtotime($this->params[$this->Platform->bankTimeColumn]));
//        }
//        $bSucc = $oUserDeposit->save();
        if (!$bSucc = $oUserDeposit->save()) {
            $this->halt(var_export($oUserDeposit->validationErrors->toArray(), 1));
        }
        $this->writeLog('Service Infomation Saved');
        if ($oUserDeposit->status == UserDeposit::DEPOSIT_STATUS_SUCCESS) {
            $this->writeLog("Status is Success Already");
            return $this->halt('Status Error');
        }
//        $this->writeLog(var_export($oUserDeposit->toArray(), true));
        if (!$bSucc = $oUserDeposit->setWaitingLoad()) {
//            $this->Callback->setDepositStatusSetError();
            $this->writeLog("Set Status To Waiting Failed");
            return Redirect::to(route('user-recharges.index'));
        }
//        exit;
        $this->writeLog("Set Status To Waiting Success");
//        pr($bSucc);
        // 加币
        if (!$bSucc = Deposit::addDepositTask($oUserDeposit->id)) {
//            $this->Callback->setAddTaskError();
            $this->halt("Add Deposit Task Failed!");
            exit;
        } else {
//            $this->PaymentAccount->updateStat($oUserDeposit);
//            $this->Callback->setSuccessful();
            $this->writeLog("Add Deposit Task Success");
        }
//        !$bSucc or Queue::push('SignTaskQueue', ['task_id' => 6, 'user_id' => $oUserDeposit->user_id, 'activity_id' => 1, 'amount'=>$oUserDeposit->amount], 'activity');
        if ($bSucc) {
            return$this->successReponse();
        }
    }

    protected function compileLogFile($sIdentifier) {
        $this->logPath = Config::get('log.root') . DIRECTORY_SEPARATOR . 'deposits' . DIRECTORY_SEPARATOR . date('Ym') . DIRECTORY_SEPARATOR . date('d');
        if (!file_exists($this->logPath)) {
            @mkdir($this->logPath, 0777, true);
            @chmod($this->logPath, 0777);
        }
        return $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $sIdentifier;
    }

    protected function successReponse() {
        $this->halt('Success');
    }

}
