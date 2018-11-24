<?php

/**
 * 与开奖中心通信
 */
class ReviseWinningNumberController extends WinningNumberBaseController {

    private $reviseCodeFieldsV2 = ['customer', 'recordId', 'lottery', 'issue', 'time', 'number', 'correctTime'];
    private $reviseCodeCheckProV2 = ['customer', 'recordId', 'lottery', 'issue', 'time', 'logId', 'number', 'correctTime'];

    function reviseWinningNumber() {
        //step1 : 日志功能设置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->action;
        $aRequestData = Input::all();
        //step2：更正开奖号码数据验证
        $this->checkRequestData($aRequestData, $this->reviseCodeFieldsV2);
        //step3 : 保存推送日志记录
        $this->savePushRecord();
        //step4：推送进程校验
        $this->_checkRequestPro($aRequestData, $this->reviseCodeCheckProV2);
        //step5：更正开奖号码处理
        $this->_reviseWinningNumber($aRequestData);
        exit;
    }

    /**
     * 更正号码进程校验
     * @param array $aData        请求数据
     * @param array $aNeed       校验字段数组
     */
    function _checkRequestPro($aData, $aNeed) {
        $this->writeLog('Check get proc begin:');
        // 验证数据封装
        $aVerifyData = array(
            'customer' => $aData['customer'],
            'recordId' => $aData['recordId'],
            'lottery' => $aData['lottery'],
            'issue' => $aData['issue'],
            'time' => date('YmdHis'),
            'logId' => $this->record['id'],
            'number' => $aData['number'],
            'correctTime' => $aData['correctTime'],
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

        $oCurl = new MyCurl($this->CodeCenter->domain . $this->CodeCenter->revise_verify_url);
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
     * 更正开奖号码处理
     * @param array $data    请求数据
     */
    function _reviseWinningNumber($data) {
//        $iLotteryId = Config::get('code-center.lotteries.' . $data['lottery']);
        $oLottery = Lottery::getByIdentifier($data['lottery']);
        $oIssue = ManIssue::getIssue($oLottery->id, $data['issue']);
//        $oLottery = Lottery::find($iLotteryId);
        $this->record['lottery_id'] = $oLottery->id;
        $sCode = $oLottery->formatWinningNumber($data['number']);
        $this->writeLog('formated: ' . $sCode);
        $this->writeLog('exist wn_number: ' . $oIssue->wn_number);
        if ($oLottery->checkWinningNumber($sCode)) {
            //todo 更正开奖号码处理
            $this->record['correct_time'] = $data['correctTime'];
            $this->record['number'] = $sCode;
            $this->_saveAlarmRecord($data);
            unset($this->record['correct_time']);
            unset($this->record['number']);
            $iErrNo = CodeCenter::ERRNO_REQUEST_FINISHED;
            $this->record['code'] = $sCode;
            if (SysConfig::check('wn_number_auto_correct', true)) {
                $oIssue->setCancelPriceTask($data['customer'],$sCode);
                //todo 是否更新??
//                    $oIssue->updateWnNumberCache();
            }
        } else {
            $iErrNo = CodeCenter::ERRNO_REQUEST_INVALID;
        }
        $this->exitPro($iErrNo);
    }

    private function _saveAlarmRecord($aData) {
        $oAlarmRecord = new IssueWarning;
        foreach ($this->record as $key => $val) {
            if (in_array($key, $oAlarmRecord->getFillable())) {
                $oAlarmRecord->$key = $val;
            }
        }
        $oAlarmRecord->record_id = $this->record['codecenter_log_id'];
        $oAlarmRecord->correct_time = $this->record['correct_time'];
        $oAlarmRecord->warning_type = IssueWarning::ISSUE_REVISE_CODE;
        $oAlarmRecord->save();
    }

    /**
     * 核查请求原始URL是否正确
     *  
     * @param string $url   发送请求方URL
     * @return boolean  URL相同，返回true；URL不同，返回false
     */
    function checkReferer($url) {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $this->writeLog('Missing HTTP_REFERER');
            return FALSE;
        }
        $this->writeLog($_SERVER['HTTP_REFERER']);
        $this->writeLog($url);
        return $_SERVER['HTTP_REFERER'] == $url;
    }

}
