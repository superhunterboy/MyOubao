<?php

/**
 * 与开奖中心通信
 */
class DrawWinningNumberController extends WinningNumberBaseController {

    private $drawCodeCheckProV2 = ['customer', 'logId', 'lottery', 'issues', 'time', 'recordId'];
    private $oPassiveRecord;

    public function __construct() {
//        $this->SysConfig = new SysConfig;
        if ($this->writeTxtLog = SysConfig::check('service_write_log', true)) {
            $this->logPath = Config::get('code-center.logs.set-winning-number');
            if (!file_exists($this->logPath)) {
                @mkdir($this->logPath, 0777, true);
//                @chmod($this->logPath, 0777);
            }
        }new PassiveRecord;
        $this->processId = function_exists('posix_getpid') ? posix_getpid() : -1;
        list($this->controller, $this->action) = explode('@', Route::currentRouteAction());
    }

    function drawWinningNumberProCheck() {
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->action;
        $aRequestData = Input::all();
        $this->checkRequestData($aRequestData, $this->drawCodeCheckProV2);
        $this->exitPro(CodeCenter::ERRNO_SET_PROC_VALID);
    }

    /**
     * 验证请求数据是否符合要求
     * @param array $aData 请求数据数组
     * @param array $aNeed 验证字段数组
     */
    public function checkRequestData($aData, $aNeed) {
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
        if ($this->CodeCenter->customer_key != $aData['customer']) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_CODECENTER_ERROR);
        }
//        $this->oPassiveRecord->codecenter_id = $this->CodeCenter->id;
        // 验证5.2：验证商户ip地址是否正确
//        $aCodecenterIPs = explode(',', $this->CodeCenter->ip);
//        $this->writeLog($this->record['codecenter_ip']);
//        if (!in_array($this->record['codecenter_ip'], $aCodecenterIPs)) {
//            $this->exitPro(CodeCenter::ERRNO_REQUEST_CODECENTER_IP_ERROR);
//        }
        // 验证5.3：验证商户url是否正确
//        if (!$this->checkReferer($this->CodeCenter->set_url)) {
//            $this->exitPro(CodeCenter::ERRNO_REQUEST_HOST_ERROR);
//        }
        // 验证6：验证请求时间是否超时
//        $iRequstTime = strtotime($aData['time']);
//        $iOffset = time() - $iRequstTime;
//        if ($iOffset < CodeCenter::VALID_TIME_MIN_OFFSET || $iOffset > CodeCenter::VALID_TIME_MAX_OFFSET) {
//            $this->exitPro(CodeCenter::ERRNO_REQUEST_CODECENTER_EXPIRED);
//        }
    }

    /**
     * 停止进程运行
     * @param int $iErrCode  错误编码
     * @param bool $bSave  是否保存日志记录
     */
    public function exitPro($iErrCode, $bSave = true) {
        $this->writeLog($iErrCode, TRUE);
        $this->oPassiveRecord->response = "N" . $iErrCode;
        $this->oPassiveRecord->status = $iErrCode;
        // 生成响应信息
        echo "N" . $iErrCode;
        // 保存日志记录到数据库
        if ($bSave) {
            $this->savePassiveRecord();
        }
        exit;
    }

    /**
     * 保存服务记录
     */
    private function savePassiveRecord() {
//        $this->writeLog("records: " . var_export($this->record, true));
        $this->oPassiveRecord->save();
        $this->writeLog("push record id:" . $this->oPassiveRecord->id);
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
        return $_SERVER['HTTP_REFERER'] == $this->CodeCenter->domain . $this->CodeCenter->get_url;
    }

    /**
     * 钩子方法，供具体实现类调用
     * @param array $aData 
     */
    public function checkRequestHookFirst($aData) {
        $this->writelog('enter');
        $this->oPassiveRecord = PassiveRecord::find($aData['logId']);
        if (!$this->oPassiveRecord) {
            $this->exitPro(CodeCenter::ERRNO_SET_PROC_NOT_EXISTS);
        }
        $bValidOfReburnData = true;
//        pr($this->oPassiveRecord->getAttributes());
        $bValidOfReburnData &= $this->oPassiveRecord->customer_key == $aData['customer'];
        $bValidOfReburnData &= $this->oPassiveRecord->request_lottery == $aData['lottery'];
        $bValidOfReburnData or $this->exitPro(CodeCenter::ERRNO_REQUEST_LOTTERY_ERROR);
        $bValidOfReburnData &= $this->oPassiveRecord->issue == $aData['issues'];
        $bValidOfReburnData or $this->exitPro(CodeCenter::ERRNO_REQUEST_ISSUE_ERROR);
//        $bValidOfReburnData &= $this->oPassiveRecord->code == $aData['number'];
//        $bValidOfReburnData or $this->exitPro(CodeCenter::ERRNO_SET_PROC_CODE_ERROR);
        $this->oPassiveRecord->codecenter_log_id = $aData['recordId'];
        $this->oPassiveRecord->verify_data = var_export($aData, true);
        $this->oPassiveRecord->codecenter_ip = get_client_ip();
    }

}
