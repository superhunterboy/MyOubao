<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 主动请求从开奖中心获取开奖号码
 */
class CheckTongHuiKaWithdrawalStatusCommand extends BaseCommand {
    protected $sFileName = 'checktonghuikawithdrawalstatus';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tonghuika:checktonghuikawithdrawalstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
//            array('lottery_id', InputArgument::REQUIRED, null),
        );
    }

    public function fire()
    {
        header('content-type:text/html; charset=utf-8');
        $url = SysConfig::readValue('REMIT_URL') . '/query';
        $aPostData = array();
        $aPostData['input_charset'] = "UTF-8";
        $aPostData['merchant_code'] = Sysconfig::readValue('MER_NO');
        $key = Sysconfig::readValue('MER_KEY');
        // 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;

        $aConditions = [
            'is_sdpay' => ['=', 2],
            'status' => ['=', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING]
        ];
        $aWithdrawals = Withdrawal::doWhere($aConditions)->get();

        foreach($aWithdrawals as $oWithdrawal) {
            $aPostData['merchant_order'] = $oWithdrawal->serial_number;
            $this->writeLog('post data : '.json_encode($aPostData)." Url : ".$url);
            $o = "";
            $sign = "";
            $post_data = $aPostData;
            ksort($post_data);
            foreach ($post_data as $k => $v) {
                if (!empty($v)) {
                    $o .= "$k=" . $v . "&";
                }
            }

            $post_data = substr($o, 0, -1);
            $sign = md5($post_data . "&key=" . $key);
            $post_data = $post_data . "&sign=" . $sign;


            $sResponseXml = ThkOrder::doPostRequest($url, $post_data, null);

            $aResponse = json_decode(json_encode(simplexml_load_string($sResponseXml)), TRUE);

            $aOrderInfo = $aResponse['response'];
            if($aOrderInfo['is_success'] != 'TRUE'){
                $bWithdrawalUpdated = $oWithdrawal->update(['status' => Withdrawal::WITHDRAWAL_STATUS_FAIL, 'mc_confirm_time' => date('Y-m-d H:i:s'), 'error_msg' => '订单不存在']);
                $bThkOrderUpdated = ThkOrder::where('withdrawal_id', $oWithdrawal->id)->update(['status' => ThkOrder::WITHDRAWAL_ORDER_STATUS_FAIL, 'response_time' => date('Y-m-d H:i:s')]);
                $this->writeLog('order is not exists. serial_number : '.$oWithdrawal->serial_number);continue;
            }

            $this->writeLog('response data : ' . json_encode($aOrderInfo));

            $o_current_thkorder = ThkOrder::where('company_order_num', $oWithdrawal->serial_number)->where('status', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->first();
            if (!$o_current_thkorder) {
                $this->writeLog('ThkOrder(' . $oWithdrawal->serial_number . ') it status != ' . Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING);
                exit;
            }

            $oWithdrawal = Withdrawal::where('id', $o_current_thkorder->withdrawal_id)->where('status', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->first();
            if (!$oWithdrawal) {
                $this->writeLog('ThkOrder(' . $oWithdrawal->serial_number . ') it status != ' . Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING);
                exit;
            }

            if ($aOrderInfo['is_success'] == 'TRUE' && $aOrderInfo['remit_status'] == 3) {//成功
                $o_account = Account::getAccountInfoByUserId($oWithdrawal->user_id);
                $account_id = $o_account->id;
                Account::lock($account_id, $iLocker);
                DB::connection()->beginTransaction();
                $b_deduct_result = $o_current_thkorder->deductUserFund($oWithdrawal->user_id, $aOrderInfo['remit_amount'], $aOrderInfo['remit_amount']);
                if ($b_deduct_result) {
                    $return['status'] = 1;
                    $bWithdrawalUpdated = $oWithdrawal->update(['status' => Withdrawal::WITHDRAWAL_STATUS_SUCCESS, 'mc_confirm_time' => date('Y-m-d H:i:s'), 'transaction_amount' => $aOrderInfo['remit_amount'], 'mownecum_order_num' => $aOrderInfo['remit_order']]);
                    $bThkOrderUpdated = $o_current_thkorder->update(['status' => ThkOrder::WITHDRAWAL_ORDER_STATUS_SUCCESS, 'response_time' => date('Y-m-d H:i:s')]);
                    Withdrawal::addProfitTask(date('Y-m-d'), $oWithdrawal->user_id, $aOrderInfo['remit_amount']);

                    $this->writeLog('withdrawal success');
                } else {
                    $return['status'] = 0;
                    $bWithdrawalUpdated = $oWithdrawal->update(['status' => Withdrawal::WITHDRAWAL_STATUS_FAIL, 'mc_confirm_time' => date('Y-m-d H:i:s')]);
                    $bThkOrderUpdated = $o_current_thkorder->update(['status' => ThkOrder::WITHDRAWAL_ORDER_STATUS_SUCCESS, 'response_time' => date('Y-m-d H:i:s')]);
                    $return['error_msg'] = "Deduct User Fund failed";
                    $this->writeLog($return['error_msg'] . ' bWithdrawalUpdated : ' . $bWithdrawalUpdated . ' bThkOrderUpdated : ' . $bThkOrderUpdated . ' b_deduct_result : ' . $b_deduct_result);
                }
                $b_deduct_result && $bWithdrawalUpdated && $bThkOrderUpdated ? DB::connection()->commit() : DB::connection()->rollback();
                Account::unLock($account_id, $iLocker, false);
            } elseif ($aOrderInfo['is_success'] == 'TRUE' && $aOrderInfo['remit_status'] == 4) {//失败
                $return['status'] = 0;
                $return['error_msg'] = "thk withdrawal failed";
                if ($bWithdrawalUpdated = $oWithdrawal->update(['status' => Withdrawal::WITHDRAWAL_STATUS_FAIL, 'mc_confirm_time' => date('Y-m-d H:i:s')])) {
                    $thk_amount = 0;
                    $amount = $oWithdrawal->getAttribute("amount");
                    $user_id = $oWithdrawal->getAttribute("user_id");
                    $o_account = Account::getAccountInfoByUserId($user_id);
                    $account_id = $o_account->id;
                    Account::lock($account_id, $iLocker);
                    DB::connection()->beginTransaction();
                    $b_deduct_result = $o_current_thkorder->deductUserFund($user_id, $thk_amount, $amount);
                    $bThkOrderUpdated = true;
                    if ($b_deduct_result) {
                        $bThkOrderUpdated = $o_current_thkorder->update(['status' => ThkOrder::WITHDRAWAL_ORDER_STATUS_FAIL, 'response_time' => date('Y-m-d H:i:s')]);
                        $return['error_msg'] = "thk withdrawal unfreeze success";
                        $this->writeLog($return['error_msg'] . ' bWithdrawalUpdated : ' . $bWithdrawalUpdated . ' bThkOrderUpdated : ' . $bThkOrderUpdated . ' b_deduct_result : ' . $b_deduct_result);
                    }
                    $b_deduct_result && $bThkOrderUpdated ? DB::connection()->commit() : DB::connection()->rollback();
                    Account::unLock($account_id, $iLocker, false);
                }
                $this->writeLog($return['error_msg']);
            }
        }
    }
}
