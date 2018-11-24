<?php

namespace JcCommand;

/**
 * 发奖金或佣金
 *
 **/
class SendGroupFee extends \BaseTask {

    protected function doCommand(){
        $iGroupId = $this->data['group_id'];

        $oGroupBuy = \JcModel\JcGroupBuy::find($iGroupId);
        if ($oGroupBuy->prize_status == \JcModel\JcGroupBuy::PRIZE_STATUS_SENDING_CODE){
            if ($oGroupBuy->fee_amount > 0){
                $bSucc = $oGroupBuy->setPrizeStatus(\JcModel\JcGroupBuy::PRIZE_STATUS_SENDING_FEE_CODE);
            }else{
                //没有佣金则直接置为已完成
                $bSucc = $oGroupBuy->setPrizeStatus(\JcModel\JcGroupBuy::PRIZE_STATUS_DONE_CODE);
                if ($bSucc){
                    $this->logData[] = 'no prize';
                    return self::TASK_SUCCESS;
                }
            }
            if (!$bSucc){
                $this->logData[] = 'set prize status failed';
                return self::TASK_RESTORE;
            }
        }
        if ($oGroupBuy->prize_status != \JcModel\JcGroupBuy::PRIZE_STATUS_SENDING_FEE_CODE){
            $this->logData[] = 'error prize_status: ' . $oGroupBuy->prize_status;
            return self::TASK_RESTORE;
        }
        $DB = \DB::connection();

        $oUser = \User::find($oGroupBuy->user_id);
        $oAccount = \Account::lock($oUser->account_id,$iLocker);
        
        $DB->beginTransaction();
        if (empty($oAccount)){
            $this->logData[] = 'Account lock failed. account_id: ' . $oUser->account_id;
            return self::TASK_RESTORE;
        }
        $fFeeAmount = $oGroupBuy->fee_amount;

        $oProject = \JcModel\JcProject::find($oGroupBuy->project_id);
        $aExtraData = $oProject->getTransactionData();
        $iReturn = \Transaction::addTransaction($oUser, $oAccount, \TransactionType::TYPE_GROUP_BUY_BONUS, $fFeeAmount, $aExtraData);
        
        $bSucc = false;
        if ($iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL) {
            $bSucc = $oGroupBuy->setPrizeStatus(\JcModel\JcGroupBuy::PRIZE_STATUS_DONE_CODE);
            if (!$bSucc){
                $this->logData[] = 'set prize status failed';
            }
        }else{
            $this->logData[] = 'send prize failed. id: ' . $oGroupBuy->id . '. amount: ' . $oGroupBuy->fee_amout;
        }
        
        if ($bSucc){
            $this->logData[] = 'success. id: ' . $oGroupBuy->id;
            \DB::commit();
        }else{
            $this->logData[] = 'failed. ' . $oGroupBuy->id;
            \DB::rollback();
        }

        \Account::unlock($oUser->account_id,$iLocker);
        
        if ($bSucc){
            return self::TASK_SUCCESS;
        }else{
            return self::TASK_RESTORE;
        }

    }

    protected function checkData() {
        return intval($this->data['group_id']) > 0;
    }
}
