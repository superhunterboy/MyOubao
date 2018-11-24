<?php

/**
 * 与开奖中心通信
 */
class WinningNumberBaseController extends Controller {

    public $logPath;
    public $logFile;
    public $errNo = 0;
    public $record = [];
    public $saveRecord = true;
    public $writeTxtLog = false;
    public $processId;
    public $controller;
    public $action;
    public $CodeCenter;

    public function __construct() {
//        $this->SysConfig = new SysConfig;
        if ($this->writeTxtLog = SysConfig::check('service_write_log', true)) {
            $this->logPath = Config::get('code-center.logs.set-winning-number');
            if (!file_exists($this->logPath)) {
                @mkdir($this->logPath, 0777, true);
                @chmod($this->logPath, 0777);
//                @chmod($this->logPath, 0777);
            }
        }
        $this->processId = function_exists('posix_getpid') ? posix_getpid() : -1;
        list($this->controller, $this->action) = explode('@', Route::currentRouteAction());
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
        // 验证6：验证请求时间是否超时
        $iRequstTime = strtotime($aData['time']);
        $iOffset = time() - $iRequstTime;
        if ($iOffset < CodeCenter::VALID_TIME_MIN_OFFSET || $iOffset > CodeCenter::VALID_TIME_MAX_OFFSET) {
            $this->exitPro(CodeCenter::ERRNO_REQUEST_CODECENTER_EXPIRED);
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
        if ($this->CodeCenter->version == 1) {
            $this->record['codecenter_log_id'] = $aData['record_id'];
        } else if ($this->CodeCenter->version == 2) {
            $this->record['codecenter_log_id'] = $aData['recordId'];
        }
        $this->record['issue'] = $aData['issue'];
        $this->record['request_time'] = strtotime($aData['time']);
        $this->record['safe_str'] = $aData['safestr'];
        $this->record['codecenter_ip'] = get_client_ip();
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
        echo "N" . $iErrCode;
        // 保存日志记录到数据库
        if ($bSave) {
            $this->savePushRecord();
        }
        exit;
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

    /**
     * 保存推送日志记录
     */
    public function savePushRecord() {
        if (isset($this->record['id'])) {
            $oPushRecord = PushRecord::find($this->record['id']);
        } else {
            $oPushRecord = new PushRecord;
        }
        isset($this->record['status']) or $this->record['status'] = null;
        foreach ($this->record as $key => $val) {
            if (in_array($key, $oPushRecord->getFillable())) {
                $oPushRecord->$key = $val;
            }
        }
        $oPushRecord->save();
        $this->record['id'] = $oPushRecord->id;
        $this->writeLog("push record id:" . $oPushRecord->id);
    }

    /**
     * 写文件日志
     * @param string 日志内容
     * @param boolean 是否追加空行
     * @return boolean 写日志成功或者失败
     */
    public function writeLog($sString, $bAppendSpaceLine = false) {
        if (!$this->writeTxtLog)
            return true;
        $sString = date('Y-m-d H:i:s') . ': ' . $sString . "\n";
        !$bAppendSpaceLine or $sString .= "\n";
        return file_put_contents($this->logFile, $sString, FILE_APPEND);
    }

    /**
     * 按照转换信息数组内容生成安全码数据
     * @param array $aData 请求数据数组
     * @param array $aNeed  转换信息数组
     */
    public function makeSafeStrFromRequest($aData, $aNeed) {
//        $this->writeLog("safeStr: " . var_export(func_get_args(), true));
        $aString = [];
        foreach ($aNeed as $sKey) {
            if (!array_key_exists($sKey, $aData))
                continue;
            $aString[] = $sKey . '=' . $aData[$sKey];
        }
        return md5(implode('&', $aString));
    }

    /**
     * 检查数据是否包含指定的键值
     * @param array $aData  需要验证的请求数据
     * @param array $aNeed  验证内容
     * @return bool 如果验证内容的数据都不为空，返回true，否则返回false
     */
    public function checkInput($aData, $aNeed) {
        $aKeys = array_keys($aData);
        $aDiff = array_diff($aNeed, $aKeys);
        if ($bSucc = empty($aDiff)) {
            foreach ($aData as $sKey => $data) {
                if (empty($data)) {
                    $bSucc = false;
                    break;
                }
            }
        }
        return $bSucc;
    }

    /**
     * 取得返回值中的信息码
     *
     * @param string $sReturn
     * @return string
     */
    public function getReturnCode($sReturn) {
        if ($sReturn && $sReturn{0} == 'N') {
            return substr($sReturn, 1);
        }
        return $sReturn;
    }

}
