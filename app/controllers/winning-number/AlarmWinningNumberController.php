<?php

/**
 * 与开奖中心通信
 */
class AlarmWinningNumberController extends WinningNumberBaseController {

    private $alarmCodeFieldsV2 = ['customer', 'recordId', 'lottery', 'issue', 'time', 'number', 'errCode', 'errMessage', 'earliestTime'];
    private $alarmCodeCheckProV2 = ['customer', 'recordId', 'lottery', 'issue', 'time', 'number', 'logId', 'errCode', 'errMessage', 'earliestTime'];

    /**
     * 告警信息接收接口
     */
    function alarmWinningNumber() {
        //step1 : 日志功能设置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->action;
        $aRequestData = Input::all();
        //step2：告警数据验证
        $this->checkRequestData($aRequestData, $this->alarmCodeFieldsV2);
        //step3 : 保存推送日志记录
        $this->savePushRecord();
        //step4：告警进程校验
        $this->_checkRequestPro($aRequestData, $this->alarmCodeCheckProV2);
        //step5：告警处理
        $this->_alarmWinningNumber($aRequestData);
        exit;
    }

    /**
     * 告警进程校验
     * @param array $aData        请求数据
     * @param array $aNeed       校验字段数组
     */
    function _checkRequestPro($aData, $aNeed) {
        $this->writeLog('Check alarm proc begin:');
        // 验证数据封装
        $aVerifyData = array(
            'customer' => $aData['customer'],
            'recordId' => $aData['recordId'],
            'lottery' => $aData['lottery'],
            'issue' => $aData['issue'],
            'time' => date('YmdHis'),
            'number' => $aData['number'],
            'logId' => $this->record['id'],
            'errCode' => $aData['errCode'],
            'errMessage' => $aData['errMessage'],
            'earliestTime' => $aData['earliestTime'],
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

        $this->writeLog('Url:' . $this->CodeCenter->domain . $this->CodeCenter->alarm_verify_url);
        $oCurl = new MyCurl($this->CodeCenter->domain . $this->CodeCenter->alarm_verify_url);
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
     * 检查数据是否包含指定的键值,覆盖基类中的该方法，取消对number字段的验证
     * @param array $aData  需要验证的请求数据
     * @param array $aNeed  验证内容
     * @return bool 如果验证内容的数据都不为空，返回true，否则返回false
     */
    public function checkInput($aData, $aNeed) {
        $aKeys = array_keys($aData);
        $aDiff = array_diff($aNeed, $aKeys);
        if ($bSucc = empty($aDiff)) {
            foreach ($aNeed as $sKey => $data) {
                if ($sKey == 'number')
                    continue;
                if (empty($data)) {
                    $bSucc = false;
                    break;
                }
            }
        }
        return $bSucc;
    }

    /**
     * 告警处理
     * @param array $data    请求数据
     */
    function _alarmWinningNumber($data) {
//        $iLotteryId = Config::get('code-center.lotteries.' . $data['lottery']);
        $oLottery = Lottery::getByIdentifier($data['lottery']);
        $oIssue = ManIssue::getIssue($oLottery->id, $data['issue']);
        $this->record['lottery_id'] = $oLottery->id;
        $sCode = $oLottery->formatWinningNumber($data['number']);
        $this->writeLog('encode status: ' . $oIssue->status);
        if ($oIssue->issue == $data['issue']) {
            //todo 告警处理
            $this->record['err_code'] = $data['errCode'];
            $this->record['err_msg'] = $data['errMessage'];
            $this->record['earliest_draw_time'] = $data['earliestTime'];
            $this->record['number'] = $data['number'];
            $this->_saveAlarmRecord();
            unset($this->record['err_code']);
            unset($this->record['err_msg']);
            unset($this->record['earliest_draw_time']);
            $iErrNo = CodeCenter::ERRNO_REQUEST_FINISHED;
            if ($iErrNo == CodeCenter::ERRNO_REQUEST_FINISHED) {
                $this->record['code'] = $sCode;
            }
            $this->exitPro($iErrNo);
        } else {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_FINISHED);
        }
    }

    /**
     * 临时保存告警记录
     */
    private function _saveAlarmRecord() {
        $oAlarmRecord = new IssueWarning;
        foreach ($this->record as $key => $val) {
            if (in_array($key, $oAlarmRecord->getFillable())) {
                $oAlarmRecord->$key = $val;
            }
        }
        $oAlarmRecord->warning_type = $this->record['err_code'];
        $oAlarmRecord->record_id = $this->record['codecenter_log_id'];
        if ($this->record['err_code'] == '105001') {
            $oAlarmRecord->earliest_draw_time = NULL;
        }
        $oAlarmRecord->save();
    }

}
