<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-10-5
 * Time: 上午10:52
 * 1,通过lottery_id和issue在project表里获取project_id
 * 2,通过
 */
class CheckTongHuiKaWithdrawalStatus extends BaseTask {

    protected function doCommand(){

        extract($this->data);
        header('content-type:text/html; charset=utf-8');

        $url = SysConfig::readValue('REMIT_URL').'/query';

        $post_data = array() ;
        $post_data['input_charset'] = "UTF-8";
        $post_data['merchant_code'] = Sysconfig::readValue('MER_NO');
        $post_data['merchant_order'] = $merchant_order;
        $key = Sysconfig::readValue('MER_KEY');

        $o = "";
        $sign = "";
        ksort($post_data);
        foreach ($post_data as $k => $v) {
            if (!empty($v)) {
                $o.= "$k=".$v."&";
            }
        }

        $post_data = substr ($o , 0 ,-1);
        $sign = md5($post_data."&key=".$key);
        $post_data = $post_data."&sign=".$sign;


        $sResponseXml = ThkOrder::doPostRequest($url, $post_data, null);

        $aResponse = json_decode(json_encode(simplexml_load_string($sResponseXml)),TRUE);

        $aOrderInfo = $aResponse['response'];


        $this->log = 'response data : '.json_encode($aOrderInfo);

        $o_current_thkorder = ThkOrder::where('company_order_num', $merchant_order)->where('status', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->first();
        if(!$o_current_thkorder){
            $this->log = 'ThkOrder('.$merchant_order.') it status != '.Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING;
            return self::TASK_SUCCESS;
        }

        $oWithdrawal = Withdrawal::where('id', $o_current_thkorder->withdrawal_id)->where('status', Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING)->first();
        if(!$oWithdrawal){
            $this->log = 'oWithdrawal('.$merchant_order.')  it status != '.Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING;
            return self::TASK_SUCCESS;
        }

        if($aOrderInfo['is_success'] && $aOrderInfo['remit_status'] == 3){//成功
            $o_account    = Account::getAccountInfoByUserId($user_id);
            $account_id   = $o_account->id;
            Account::lock($account_id,$iLocker);
            DB::connection()->beginTransaction();
            $b_deduct_result    = $o_current_thkorder->deductUserFund($user_id,$aOrderInfo['remit_amount'],$aOrderInfo['remit_amount']);
            if ($b_deduct_result)
            {
                $return['status'] = 1;
                $bWithdrawalUpdated = $oWithdrawal->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_SUCCESS,'mc_confirm_time'=>date('Y-m-d H:i:s'),'transaction_amount'=>$aOrderInfo['remit_amount'],'mownecum_order_num'=>$aOrderInfo['remit_order']]);
                $bThkOrderUpdated = $o_current_thkorder->update(['status'=>ThkOrder::WITHDRAWAL_ORDER_STATUS_SUCCESS,'response_time'=>date('Y-m-d H:i:s')]);
                Withdrawal::addProfitTask(date('Y-m-d'), $user_id, $aOrderInfo['remit_amount']);

                $this->log = 'withdrawal success';
            }else {
                $return['status'] = 0;
                $bWithdrawalUpdated = $oWithdrawal->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_FAIL,'mc_confirm_time'=>date('Y-m-d H:i:s')]);
                $bThkOrderUpdated = $o_current_thkorder->update(['status'=>ThkOrder::WITHDRAWAL_ORDER_STATUS_SUCCESS,'response_time'=>date('Y-m-d H:i:s')]);
                $return['error_msg']    = "Deduct User Fund failed";
                $this->log = $return['error_msg'].' bWithdrawalUpdated : '.$bWithdrawalUpdated.' bThkOrderUpdated : '.$bThkOrderUpdated.' b_deduct_result : '.$b_deduct_result;
            }
            $b_deduct_result && $bWithdrawalUpdated && $bThkOrderUpdated ? DB::connection()->commit() : DB::connection()->rollback();
            Account::unLock($account_id,$iLocker,false);
        }elseif($aOrderInfo['is_success'] && $aOrderInfo['remit_status'] == 4){//失败
            $return['status'] = 0;
            $return['error_msg']    = "thk withdrawal failed";
            if ($bWithdrawalUpdated = $oWithdrawal->setToFailture()) {
                $thk_amount = 0;
                $amount = $oWithdrawal->getAttribute("amount");
                $user_id = $oWithdrawal->getAttribute("user_id");
                $o_account    = Account::getAccountInfoByUserId($user_id);
                $account_id   = $o_account->id;
                Account::lock($account_id,$iLocker);
                DB::connection()->beginTransaction();
                $b_deduct_result    = $o_current_thkorder->deductUserFund($user_id, $thk_amount, $amount);
                $bThkOrderUpdated = true;
                if ($b_deduct_result) {
                    $bThkOrderUpdated = $o_current_thkorder->update(['status'=>ThkOrder::WITHDRAWAL_ORDER_STATUS_FAIL,'response_time'=>date('Y-m-d H:i:s')]);
                    $return['error_msg']    = "thk withdrawal unfreeze success";
                    $this->log = $return['error_msg'].' bWithdrawalUpdated : '.$bWithdrawalUpdated.' bThkOrderUpdated : '.$bThkOrderUpdated.' b_deduct_result : '.$b_deduct_result;
                }
                $b_deduct_result && $bThkOrderUpdated ? DB::connection()->commit() : DB::connection()->rollback();
                Account::unLock($account_id,$iLocker,false);
            }
            $this->log = $return['error_msg'];

        }else{
            return self::TASK_RESTORE;
        }
        return self::TASK_SUCCESS;

    }

}
