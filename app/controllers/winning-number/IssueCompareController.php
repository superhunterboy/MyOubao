<?php

/**
 * 与开奖中心通信
 */
class IssueCompareController extends WinningNumberBaseController {

    private $issueCompareFields = ['customer', 'lottery', 'startTime', 'endTime'];

    /**
     * 推送信息接收接口
     */
    public function getIssues() {
        //step1 : 日志功能设置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->action;
        $aRequestData = Input::all();
        //step2：推送数据验证
        $this->checkRequestData($aRequestData, $this->issueCompareFields);
        //step3 : 保存推送日志记录
        $this->savePushRecord();
        //step4：返回奖期信息
        $this->_getIssuesFromFireCat($aRequestData);
        exit;
    }
    
     /**
     * 验证请求数据是否符合要求
     * @param array $aData 请求数据数组
     * @param array $aNeed 验证字段数组
     */
    public function checkRequestData($aData, $aNeed) {
        $this->record['accept_time'] = microtime(TRUE);
        $aCheckInput = array_merge($aNeed, ['safestr']);
        //验证1：必须为POST请求提交
        if (Request::method() != 'POST') {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_NON_POST, false);
        }
        //验证2：判断请求数据是否为空
        if (empty($aData)) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_INVALID, false);
        }
        // 将请求数据记录文本日志
        $this->writeLog(var_export($aData, true));
        // 验证3：判断请求数据各项内容是否为空
        if (!$this->checkInput($aData, $aCheckInput)) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_INVALID, false);
        }
        $this->CodeCenter = CodeCenter::getCodeCenterByKey($aData['customer']);
        $this->checkRequestHookFirst($aData);
        // 验证4：判断加密字符串是否正确
        $sSafeStrFromRequest = $this->makeSafeStrFromRequest($aData, $aNeed);
        $this->writeLog($sSafeStrFromRequest);
        if ($sSafeStrFromRequest != strtolower($aData['safestr'])) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_INVALID);
        }
        // 验证5：验证商户信息是否正确
        // 验证5.1：验证商户是否存在
//        $this->CodeCenter = CodeCenter::getCodeCenterByKey($aData['customer']);
        if ($this->CodeCenter->customer_key != $aData['customer']) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_CODECENTER_ERROR);
        }
        $this->record['codecenter_id'] = $this->CodeCenter->id;
        // 验证5.2：验证商户ip地址是否正确
        $aCodecenterIPs = explode(',', $this->CodeCenter->ip);
        $this->writeLog($this->record['codecenter_ip']);
        if (!in_array($this->record['codecenter_ip'], $aCodecenterIPs)) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_CODECENTER_IP_ERROR);
        }
        // 验证5.3：验证商户url是否正确
        if (!$this->checkReferer($this->CodeCenter->domain . $this->CodeCenter->set_url)) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_HOST_ERROR);
        }
    }
    
        /**
     * 封装被动记录日志数据
     * @param array $aData 请求数据
     */
    public function checkRequestHookFirst($aData) {
        $this->record['request_data'] = var_export($aData, true);
        $this->record['request_lottery'] = $aData['lottery'];
        $this->record['customer_key'] = $aData['customer'];
        $this->record['safe_str'] = $aData['safestr'];
        $this->record['codecenter_ip'] = get_client_ip();
    }

    /**
     * 产生相应数据的json格式信息
     * @param string $sSucccess 请求是否成功，成功：'true'，失败：'false'
     * @param int $iErrCode  错误编码
     * @param array $aNumbers 开奖号码数组
     * @return type
     */
    private function _makeResponse($sSucccess, $sErrCode, $aIssues) {
        $this->writeLog(json_encode(array('isSuccess' => $sSucccess, 'message' => $sErrCode, 'numbers' => $aIssues)));
        echo json_encode(array('isSuccess' => $sSucccess, 'message' => $sErrCode, 'issues' => $aIssues));
    }

    /**
     * 停止进程运行
     * @param int $iErrCode  错误编码
     * @param bool $bSave  是否保存日志记录
     */
    public function exitPro($iErrCode, $bSave = true) {
        $this->writeLog($iErrCode, TRUE);
        $this->record['response'] = "N" . $iErrCode;
        $this->record['status'] = $iErrCode;
        isset($this->record['finish_time']) or $this->record['finish_time'] = microtime(TRUE);
        $this->record['spent_time'] = $this->record['finish_time'] - $this->record['accept_time'];
        // 生成响应信息
        $this->_makeResponse('false', $this->record['response'], array());
        // 保存日志记录到数据库
        if ($bSave) {
            $this->savePushRecord();
        }
        exit;
    }

    /**
     * 开奖处理
     * @param array $data    请求数据
     */
    private function _getIssuesFromFireCat($data) {
        $iLotteryId = Config::get('code-center.lotteries.' . $data['lottery']);
        $iStartTime = strtotime($data['startTime']);
        $iEndTime = strtotime($data['endTime']);
        $aIssues = ManIssue::getIssuesByLotteryIdAndTime($iLotteryId, $iStartTime, $iEndTime, $data['lottery']);
        $this->record['lottery_id'] = $iLotteryId;
        $this->_makeResponse('true', '', $aIssues);
    }

}
