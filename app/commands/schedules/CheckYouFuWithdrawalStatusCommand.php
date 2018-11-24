<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 主动请求从开奖中心获取开奖号码
 */
class CheckYouFuWithdrawalStatusCommand extends BaseCommand {
    protected $sFileName = 'checkyoufuwithdrawalstatus';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'youfu:checkyoufuwithdrawalstatus';

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
        $url = SysConfig::readValue('YOUFU_REFERER') . '/xiafa_query.php';
        $aPostData = array();
//        $aPostData['input_charset'] = "UTF-8";
        $aPostData['version'] = Sysconfig::readValue('YOUFU_VERSION');
        $aPostData['mer_no'] = Sysconfig::readValue('YOUFU_MER_NO');
        $key1 = Sysconfig::readValue('YOUFU_MER_KEY');
        // 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;

        $aConditions = [
            'is_sdpay' => ['=', 3],
            'status' => ['=', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING]
        ];
        $aWithdrawals = Withdrawal::doWhere($aConditions)->get();
        foreach($aWithdrawals as $oWithdrawal) {
//            $oUserBankCard = UserBankCard::where('user_id', $oWithdrawal->user_id)->where('bank_id', $oWithdrawal->bank_id)->where('account', $oWithdrawal->account)->first();
            $aPostData['bank_account'] = $oWithdrawal->account_name;
            $oBank = Bank::where('id', $oWithdrawal->bank_id)->first();
            $aPostData['bank_name'] = $oBank->bank_code;
            $aPostData['bank_cardno'] = $oWithdrawal->account;
            $aPostData['order_no'] = $oWithdrawal->serial_number;
            if(!empty($oWithdrawal->mc_request_time)){
                $post_data['trade_date'] = date('Ymd', strtotime($oWithdrawal->mc_request_time));
            }
            $this->writeLog('post data : '.json_encode($aPostData)." Url : ".$url);
            $post_data = $aPostData;
            ksort($post_data);
            $url1 = '';
            foreach($post_data as $key => $val){
                    $url1 .= $key.'='.$val.'&';
            }
            $hmacstr = $url1 . 'KEY=' . $key1;
            $sign = md5($hmacstr);
            $post_data['sign'] = $sign;


            $sResponseXml = YoufuOrder::doPostRequest($url, $post_data, null);

            $aResponse = json_decode($sResponseXml, TRUE);
            
//            $aResponse = [
//                'status' => '10000',
//                'status_code' => '3'
//            ];

            $aOrderInfo = $aResponse;
            if($aOrderInfo['status'] == '10005'){
                $bWithdrawalUpdated = $oWithdrawal->where('id',$oWithdrawal->id)->where('status',Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->update(['status' => Withdrawal::WITHDRAWAL_STATUS_FAIL, 'mc_confirm_time' => date('Y-m-d H:i:s'), 'error_msg' => '订单不存在']);
                $bYoufuOrderUpdated = YoufuOrder::where('withdrawal_id', $oWithdrawal->id)->update(['status' => YoufuOrder::WITHDRAWAL_ORDER_STATUS_FAIL, 'response_time' => date('Y-m-d H:i:s')]);
                $this->writeLog('order is not exists. serial_number : '.$oWithdrawal->serial_number);continue;
            }

            $this->writeLog('response data : ' . json_encode($aOrderInfo));
            $o_current_yforder = YoufuOrder::where('company_order_num', $oWithdrawal->serial_number)->where('status', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->first();
            if (!$o_current_yforder) {
                $this->writeLog('YoufuOrder(' . $oWithdrawal->serial_number . ') it status != ' . Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING);
                exit;
            }

            $oWithdrawal = Withdrawal::where('id', $o_current_yforder->withdrawal_id)->where('status', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->first();
            if (!$oWithdrawal) {
                $this->writeLog('YoufuOrder(' . $oWithdrawal->serial_number . ') it status != ' . Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING);
                exit;
            }

            if ($aOrderInfo['status'] == '10000' && $aOrderInfo['status_code'] == 2) {//成功
                $o_account = Account::getAccountInfoByUserId($oWithdrawal->user_id);
                $account_id = $o_account->id;
                Account::lock($account_id, $iLocker);
                DB::connection()->beginTransaction();
                $b_deduct_result = $o_current_yforder->deductUserFund($oWithdrawal->user_id, $oWithdrawal->getAttribute("amount"), $oWithdrawal->getAttribute("amount"));
                if ($b_deduct_result) {
                    $return['status'] = 1;
                    $bWithdrawalUpdated = $oWithdrawal->where('id',$oWithdrawal->id)->where('status',Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->update(['status' => Withdrawal::WITHDRAWAL_STATUS_SUCCESS, 'mc_confirm_time' => date('Y-m-d H:i:s'), 'transaction_amount' => $oWithdrawal->getAttribute("amount"), 'mownecum_order_num' => '']);
                    $bYoufuOrderUpdated = $o_current_yforder->update(['status' => YoufuOrder::WITHDRAWAL_ORDER_STATUS_SUCCESS, 'response_time' => date('Y-m-d H:i:s')]);
                    Withdrawal::addProfitTask(date('Y-m-d'), $oWithdrawal->user_id, $oWithdrawal->getAttribute("amount"));

                    $this->writeLog('withdrawal success');
                } else {
                    $return['status'] = 0;
                    $bWithdrawalUpdated = $oWithdrawal->where('id',$oWithdrawal->id)->where('status',Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->update(['status' => Withdrawal::WITHDRAWAL_STATUS_FAIL, 'mc_confirm_time' => date('Y-m-d H:i:s')]);
                    $bYoufuOrderUpdated = $o_current_yforder->update(['status' => YoufuOrder::WITHDRAWAL_ORDER_STATUS_SUCCESS, 'response_time' => date('Y-m-d H:i:s')]);
                    $return['error_msg'] = "Deduct User Fund failed";
                    $this->writeLog($return['error_msg'] . ' bWithdrawalUpdated : ' . $bWithdrawalUpdated . ' bYoufuOrderUpdated : ' . $bYoufuOrderUpdated . ' b_deduct_result : ' . $b_deduct_result);
                }
                $b_deduct_result && $bWithdrawalUpdated && $bYoufuOrderUpdated ? DB::connection()->commit() : DB::connection()->rollback();
                Account::unLock($account_id, $iLocker, false);
            } elseif ($aOrderInfo['status'] == '10000' && $aOrderInfo['status_code'] == 3) {//失败
                $return['status'] = 0;
                $return['error_msg'] = "yf withdrawal failed";
                if ($bWithdrawalUpdated = $oWithdrawal->where('id',$oWithdrawal->id)->where('status',Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->update(['status' => Withdrawal::WITHDRAWAL_STATUS_FAIL, 'mc_confirm_time' => date('Y-m-d H:i:s')])) {
                    $yf_amount = 0;
                    $amount = $oWithdrawal->getAttribute("amount");
                    $user_id = $oWithdrawal->getAttribute("user_id");
                    $o_account = Account::getAccountInfoByUserId($user_id);
                    $account_id = $o_account->id;
                    Account::lock($account_id, $iLocker);
                    DB::connection()->beginTransaction();
                    $b_deduct_result = $o_current_yforder->deductUserFund($user_id, $yf_amount, $amount);
                    $bYoufuOrderUpdated = true;
                    if ($b_deduct_result) {
                        $bYoufuOrderUpdated = $o_current_yforder->update(['status' => YoufuOrder::WITHDRAWAL_ORDER_STATUS_FAIL, 'response_time' => date('Y-m-d H:i:s')]);
                        $return['error_msg'] = "yf withdrawal unfreeze success";
                        $this->writeLog($return['error_msg'] . ' bWithdrawalUpdated : ' . $bWithdrawalUpdated . ' bYoufuOrderUpdated : ' . $bYoufuOrderUpdated . ' b_deduct_result : ' . $b_deduct_result);
                    }
                    $b_deduct_result && $bYoufuOrderUpdated ? DB::connection()->commit() : DB::connection()->rollback();
                    Account::unLock($account_id, $iLocker, false);
                }
                $this->writeLog($return['error_msg']);
            }
        }
    }
}
