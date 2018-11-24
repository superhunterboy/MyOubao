<?php

namespace JcCommand;

/**
 * 发奖金或佣金
 *
 **/
class SendGroupMoney extends \BaseTask {

    protected $pageSize = 100;

    protected function doCommand(){
        $iGroupId = $this->data['group_id'];

        $oGroupBuy = \JcModel\JcGroupBuy::find($iGroupId);
        if ($oGroupBuy->prize_status == \JcModel\JcGroupBuy::PRIZE_STATUS_CALCULATING_CODE){
            $bSucc = $oGroupBuy->setPrizeStatus(\JcModel\JcGroupBuy::PRIZE_STATUS_SENDING_CODE);
            if (!$bSucc){
                $this->logData[] = 'set prize status failed';
                return self::TASK_RESTORE;
            }
        }
        if ($oGroupBuy->prize_status != \JcModel\JcGroupBuy::PRIZE_STATUS_SENDING_CODE){
            $this->logData[] = 'error prize_status: ' . $oGroupBuy->prize_status;
            return self::TASK_RESTORE;
        }
        
        $oBet = \JcModel\JcBet::find($oGroupBuy->bet_id);
        $oQueryRes = \JcModel\ManJcProject::getUnPrizeListByGroupId($iGroupId, $this->pageSize);
        
        if (count($oQueryRes) <= 0){
            if ($oBet->status == \JcModel\JcBet::STATUS_WON){
                if ($oBet->setPrizeSent()){
                    $this->logData[] = 'set bet sent successed: ' . $oBet->id;
                }else{
                    $this->logData[] = 'set bet sent failed: ' . $oBet->id;
                    return self::TASK_RESTORE;
                }
            }
            if ($oGroupBuy->status == \JcModel\JcGroupBuy::STATUS_WON){
                if ($oGroupBuy->setPrizeSent()){
                    $this->logData[] = 'set group buy sent successed: ' . $oGroupBuy->id;
                }else{
                    $this->logData[] = 'set group buy sent failed: ' . $oGroupBuy->id;
                    return self::TASK_RESTORE;
                }
            }
            $aJobData = [
                'group_id' => $iGroupId,
            ];
            \BaseTask::addTask('\JcCommand\SendGroupFee', $aJobData, 'jc_send_money');//发放佣金
            return self::TASK_SUCCESS;
        }
        
        $DB = \DB::connection();
        
        foreach($oQueryRes as $oProject){
            $oUser = \User::find($oProject->user_id);
            if (empty($oUser)){
                $this->logData[] = 'user is empty. user_id: ' . $oProject->user_id;
                continue;
            }
            $oAccount = \Account::lock($oUser->account_id,$iLocker);
            if (empty($oAccount)){
                $this->logData[] = 'Account lock failed. account_id: ' . $oUser->account_id;
                continue;
            }
            $oProject->setUser($oUser);
            $oProject->setAccount($oAccount);
            
            $DB->beginTransaction();
            
            $bSucc = false;
            $aExtraData = [
              'amount' => $oProject->prize,
            ];
            if ($oProject->send($aExtraData)){
                $bSucc = $oProject->setPrizeSent();
                if($bSucc){
                    $this->logData[] = 'set project prize sent successed: ' . $oProject->id;
                }else{
                    $this->logData[] = 'set project prize sent failed: ' . $oProject->id;
                }
            }else{
                $this->logData[] = 'send project prize failed: ' . $oProject->id;
            }
            
            $bSucc ? $DB->commit() : $DB->rollback();
            
            \Account::unLock($oUser->account_id,$iLocker,false);
        }
        
        return self::TASK_KEEP;
    }

    protected function checkData() {
        return intval($this->data['group_id']) > 0;
    }
}
