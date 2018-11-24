<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 主动请求从开奖中心获取开奖号码
 */
class GetCodeFromEC2 extends BaseCommand {

    const ERR_CODE_NO_NUMBER = '000111';

    protected $sFileName = 'getcode';
    public $record = [];
    public $oPassiveRecord;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ec2:getcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get code from ec2.0';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
        );
    }

    public function fire() {
// 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
        $this->writeLog('begin getting the code from ec');
        $iLotteryId = $this->argument('lottery_id');
        $oLottery = Lottery::find($iLotteryId);
        if (!$oLottery) {
            $this->exitPro("missing lottery, lottery_id=" . $iLotteryId, false);
        }
        $oLastIssue = ManIssue::getLatestIssueOfNoWnNumber($iLotteryId);
        if (!$oLastIssue) {
            $this->exitPro("No issue, lottery_id=" . $iLotteryId, false);
        }
        $this->writeLog('lottery_id = ' . $iLotteryId . ', issue=' . $oLastIssue->issue);
        $oCodeCenter = CodeCenter::getCenter(2);
        $this->writeLog('code center get_url=' . $oCodeCenter->domain . $oCodeCenter->get_url);
        $this->oPassiveRecord = new PassiveRecord;
        $this->oPassiveRecord->lottery_id = $iLotteryId;
        $this->oPassiveRecord->codecenter_id = $oCodeCenter->id;
        $this->oPassiveRecord->request_lottery = $oLottery->identifier;
        $this->oPassiveRecord->issue = $oLastIssue->issue;
        $this->oPassiveRecord->customer_key = $oCodeCenter->customer_key;
        $this->oPassiveRecord->request_time = microtime(true);
        $this->oPassiveRecord->save();
        $aRequestData = [
            'customer' => $oCodeCenter->customer_key,
            'logId' => $this->oPassiveRecord->id,
            'lottery' => $oLottery->identifier,
            'issues' => $oLastIssue->issue,
            'time' => date('YmdHis'),
        ];
        $sSafeStr = $this->makeSafeStrFromRequest($aRequestData, ['customer', 'logId', 'lottery', 'issues', 'time']);
        $aRequestData['safestr'] = $sSafeStr;
        $this->writeLog('request data=' . var_export($aRequestData, true));
        $this->oPassiveRecord->safe_str = $sSafeStr;
        $this->oPassiveRecord->request_data = var_export($aRequestData, true);

        $oCurl = new MyCurl($oCodeCenter->domain . $oCodeCenter->get_url);
        $oCurl->setTimeout(10);
        $oCurl->setPost($aRequestData);
        $oCurl->setReferer($this->_makeReferer());
        $oCurl->createCurl();
        $oCurl->execute();
        $sJsonResult = $oCurl->__tostring();
        $aResult = json_decode($sJsonResult, TRUE);
//        pr($aResult);
        if ($aResult['isSuccess'] != 'true') {
            $this->exitPro($aResult['message']);
        } else {
            foreach ($aResult['numbers'] as $aVal) {
                if ($aVal['errCode'] != "") {
                    $this->exitPro('lottery_id = ' . $iLotteryId . ', issue=' . $oLastIssue->issue . ', errcode = ' . $aVal['errCode'] . ', errmsg = ' . $aVal['errMessage'], true, true);
                    continue;
                } else {
                    if($iLotteryId == 58){
                        $aCode = explode(',', $aVal['number']);
                        if(count($aCode) != 20){
                            $this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $aVal['number'] . ', format is not correct');
                        }
                        sort($aCode);
                        $aVal['number'] = (array_sum(array_slice($aCode, 0, 6)) % 10).(array_sum(array_slice($aCode, 6, 6)) % 10).(array_sum(array_slice($aCode, 12, 6)) % 10);
                        $this->writeLog('number58 = ' . $aVal['number']);
                    }
                    if ($oLottery->checkWinningNumber($aVal['number'])) {
                        if ($oLastIssue->setWinningNumber($aVal['number'], $oCodeCenter) === true) {
                            $this->oPassiveRecord->code = $aVal['number'];
                            $oLastIssue->setCalculateTask();
                            if(in_array($iLotteryId,[1,3,6,7])) Issue::setKl28NumberTask($iLotteryId,$oLastIssue->issue,$aVal['number']);
                            $this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $aVal['number'] . ', saved success!');
                        } else {
                            $this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $aVal['number'] . ', saved fail!');
                        }
                    } else {
                        $this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $aVal['number'] . ', format is not correct');
                    }
                }
            }
        }
    }

    /**
     * 按照转换信息数组内容生成安全码数据
     * @param array $aData 请求数据数组
     * @param array $aNeed  转换信息数组
     */
    public function makeSafeStrFromRequest($aData, $aNeed) {
        $aString = [];
        foreach ($aNeed as $sKey) {
            if (!array_key_exists($sKey, $aData))
                continue;
            $aString[] = $sKey . '=' . $aData[$sKey];
        }
        return md5(implode('&', $aString));
    }

    /**
     * todo 方法封装
     * @return type
     */
    private function _makeReferer() {
        $oSysConfig = new SysConfig;
        $aRefererParts = array(
            'scheme' => 'http',
            'host' => 'ca.test.com',
            'port' => 80,
            'path' => '/service/getcode',
        );
        return Url::build_url($aRefererParts);
    }

    /**
     * 停止进程运行
     * @param int $iErrCode  错误编码
     * @param bool $bSave  是否保存日志记录
     */
    public function exitPro($sErrMessage, $bSave = true, $bExit = false) {
        $this->writeLog($sErrMessage, TRUE);
        if ($bSave) {
            $this->oPassiveRecord->finish_time = microtime(true);
            $this->oPassiveRecord->spent_time = $this->oPassiveRecord->finish_time - $this->oPassiveRecord->request_time;
//            pr($this->oPassiveRecord->getAttributes());
            // 保存日志记录到数据库
            $this->oPassiveRecord->save();
        }
        if (!$bExit) {
            exit;
        }
    }

}
