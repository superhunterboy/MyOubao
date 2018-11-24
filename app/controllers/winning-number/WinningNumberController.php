<?php

/**
 * 与开奖中心通信
 */
class WinningNumberController extends WinningNumberBaseController {

    private $pushCodeFieldsV2 = ['customer', 'recordId', 'lottery', 'issue', 'time', 'number', 'verifiedTime', 'earliestTime', 'stopSaleTime', 'drawingTime'];
    private $setRequestFieldsV1 = ['customer', 'record_id', 'lottery', 'issue', 'time', 'code'];
    private $pushCodeCheckProV2 = ['customer', 'recordId', 'lottery', 'issue', 'time', 'logId', 'number', 'verifiedTime', 'earliestTime', 'stopSaleTime', 'drawingTime'];
    private $pushCodeFieldsV1 = ['customer', 'record_id', 'lottery', 'issue', 'time', 'code'];
    private $pushCodeCheckProV1 = ['customer', 'record_id', 'lottery', 'issue', 'time', 'code', 'log_id'];

    /**
     * 推送信息接收接口
     */
    public function setWinningNumber() {
        set_time_limit(60);
        $aRequestData = Input::all();
        $this->CodeCenter = CodeCenter::getCodeCenterByKey($aRequestData['customer']);
        if ($this->CodeCenter->version == 1) {
            $this->setWinningNumberV1();
        } else if ($this->CodeCenter->version == 2) {
            $this->setWinningNumberV2();
        }
    }

    /**
     * 推送信息接收接口,EC2.0
     */
    public function setWinningNumberV2() {
        //step1 : 日志功能设置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->action;
        $aRequestData = Input::all();
        //step2：推送数据验证
        $this->checkRequestData($aRequestData, $this->pushCodeFieldsV2);
        //step3 : 保存推送日志记录
        $this->savePushRecord();
        //step4：推送进程校验
        $this->_checkRequestProV2($aRequestData, $this->pushCodeCheckProV2);
        //step5：开奖
        $this->_setWinningNumber($aRequestData);
        exit;
    }

    /**
     * 推送信息接收接口 EC1.0
     */
    public function setWinningNumberV1() {
        //step1 : 日志功能设置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . date('Ymd');
        $aRequestData = Input::all();
        //step2：推送数据验证
        $this->checkRequestData($aRequestData, $this->pushCodeFieldsV1);
        //step3 : 保存推送日志记录
        $this->savePushRecord();
        //step4：推送进程校验
        $this->_checkRequestProV1($aRequestData, $this->pushCodeCheckProV1);
        //step5：开奖
        $this->_setWinningNumber($aRequestData);
        exit;
    }

    /**
     * 推送进程校验 EC2.0
     * @param array $aData        请求数据
     * @param array $aNeed       校验字段数组
     */
    function _checkRequestProV1($aData, $aNeed) {
        $this->writeLog('Check get proc begin:');
        $this->writeLog('Url:' . $this->CodeCenter->domain . $this->CodeCenter->set_verify_url);
        // 验证数据封装
        $aVerifyData = array(
            'customer' => $aData['customer'],
            'record_id' => $aData['record_id'],
            'lottery' => $aData['lottery'],
            'issue' => $aData['issue'],
            'time' => date('YmdHis'),
            'log_id' => $this->record['id'],
            'code' => $aData['code'],
        );
        $aVerifyData['safestr'] = $this->makeSafeStrFromRequest($aVerifyData, $aNeed);
        $this->record['verify_data'] = var_export($aVerifyData, TRUE);
        $this->writeLog(var_export($aVerifyData, true));
        $aRefererForVerifyUrlParts = array(
            'scheme' => 'http',
            'host' => $_SERVER['HTTP_HOST'],
//            'port' => $_SERVER['SERVER_PORT'],
            'path' => $_SERVER['REQUEST_URI'],
        );
        $sRefererForVerify = Url::build_url($aRefererForVerifyUrlParts);

        $oCurl = new MyCurl($this->CodeCenter->domain . $this->CodeCenter->set_verify_url);
        $oCurl->setTimeout(10);
        $oCurl->setPost($aVerifyData);
        $oCurl->setReferer($sRefererForVerify);
        $oCurl->createCurl();
        $oCurl->execute();
        $sVerifyResult = trim($oCurl->__tostring());

//        $this->writeLog($sVerifyResult);
        $this->record['verify_result'] = $this->getReturnCode($sVerifyResult);

        if ($this->record['verify_result'] != CodeCenter::ERRNO_SET_PROC_VALID) {
            $this->exitPro($this->record['verify_result']);
        }
    }

    /**
     * 推送进程校验 EC1.0
     * @param array $aData        请求数据
     * @param array $aNeed       校验字段数组
     */
    function _checkRequestProV2($aData, $aNeed) {
        $this->writeLog('Check get proc begin:');
        $this->writeLog('Url:' . $this->CodeCenter->domain . $this->CodeCenter->set_verify_url);
        // 验证数据封装
        $aVerifyData = array(
            'customer' => $aData['customer'],
            'recordId' => $aData['recordId'],
            'lottery' => $aData['lottery'],
            'issue' => $aData['issue'],
            'time' => date('YmdHis'),
            'logId' => $this->record['id'],
            'number' => $aData['number'],
            'verifiedTime' => $aData['verifiedTime'],
            'earliestTime' => $aData['earliestTime'],
            'stopSaleTime' => $aData['stopSaleTime'],
            'drawingTime' => $aData['drawingTime'],
        );
        $aVerifyData['safestr'] = $this->makeSafeStrFromRequest($aVerifyData, $aNeed);
        $this->record['verify_data'] = var_export($aVerifyData, TRUE);
        $this->writeLog(var_export($aVerifyData, true));
        $aRefererForVerifyUrlParts = array(
            'scheme' => 'http',
            'host' => $_SERVER['HTTP_HOST'],
//            'port' => $_SERVER['SERVER_PORT'],
            'path' => $_SERVER['REQUEST_URI'],
        );
        $sRefererForVerify = Url::build_url($aRefererForVerifyUrlParts);

        $oCurl = new MyCurl($this->CodeCenter->domain . $this->CodeCenter->set_verify_url);
        $oCurl->setTimeout(10);
        $oCurl->setPost($aVerifyData);
        $oCurl->setReferer($sRefererForVerify);
        $oCurl->createCurl();
        $oCurl->execute();
        $sVerifyResult = trim($oCurl->__tostring());

        $this->record['verify_result'] = $this->getReturnCode($sVerifyResult);

        if ($this->record['verify_result'] != CodeCenter::ERRNO_SET_PROC_VALID) {
            $this->exitPro($this->record['verify_result']);
        }
    }

    /**
     * 开奖处理
     * @param array $data    请求数据
     */
    function _setWinningNumber($data) {
//        $iLotteryId = Config::get('code-center.lotteries.' . $data['lottery']);
        $oLottery = Lottery::getByIdentifier($data['lottery']);
        if (empty($oLottery)) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_LOTTERY_ERROR);
        }
        $oIssue = ManIssue::getIssue($oLottery->id, $data['issue']);
        if (empty($oIssue)) {
            $this->writeLog('lottery_id=' . $oLottery->id . ', issue=' . $data['issue']);
            $this->exitPro(CodeCenter::ERRNO_REQUEST_ISSUE_ERROR);
        }
//        $oLottery = Lottery::find($iLotteryId);
        $this->record['lottery_id'] = $oLottery->id;
        if ($this->CodeCenter->version == 1) {
            $sCode = $oLottery->formatWinningNumber($data['code']);
        } else if ($this->CodeCenter->version == 2) {
            $sCode = $oLottery->formatWinningNumber($data['number']);
        }
        $this->writeLog('formated: ' . $sCode);
        $this->writeLog('encode status: ' . $oIssue->status);
        $this->writeLog('exist wn_number: ' . $oIssue->wn_number);
        if($oLottery->id == 58){
            $aCode = explode(',', $sCode);
            if(count($aCode) != 20){
                $this->exitPro('lottery_id = ' . $oLottery->id . ', issue = ' . $data['issue'] . ', code = ' . $sCode . ', format is not correct');
            }
            sort($aCode);
            $sCode = (array_sum(array_slice($aCode, 0, 6)) % 10).(array_sum(array_slice($aCode, 6, 6)) % 10).(array_sum(array_slice($aCode, 12, 6)) % 10);
        }
        if ($oIssue->status < ManIssue::ISSUE_CODE_STATUS_FINISHED) {
            if ($oLottery->checkWinningNumber($sCode)) {
                $iErrNo = ($oIssue->setWinningNumber($sCode, $this->CodeCenter) === true) ? CodeCenter::ERRNO_REQUEST_FINISHED : CodeCenter::ERRNO_REQUEST_NEED_PUSH_AGAIN;
                if ($iErrNo == CodeCenter::ERRNO_REQUEST_FINISHED) {
                    if(in_array($oLottery->id,[1,3,6,7])) Issue::setKl28NumberTask($oLottery->id,$data['issue'],$sCode);
                    $this->record['code'] = $sCode;
                    $oIssue->setCalculateTask();
                    $oIssue->updateWnNumberCache();
                }
            } else {
                $iErrNo = CodeCenter::ERRNO_REQUEST_INVALID;
            }
            $this->exitPro($iErrNo);
        } else {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_FINISHED);
        }
    }

}
