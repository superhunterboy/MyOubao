<?php

namespace JcCommand;

/**
 * 发奖金或佣金
 *
 **/
class SendMoney extends \BaseTask {

    protected $pageSize = 100;

    protected function doCommand(){
        $iLotteryId = intval($this->data['lottery_id']);
        $iMatchId = intval($this->data['match_id']);
        
        $oMatchInfo = \JcModel\JcMatchesInfo::getByMatchId($iMatchId);
        if (!$oMatchInfo->isFinished()){
            return self::TASK_RESTORE;
        }
        if ($oMatchInfo->prize_status == \JcModel\JcMatchesInfo::PRIZE_STATUS_CALCULATING_CODE){
            $oMatchInfo->setSendingPrizeStatus();
        }
        if ($oMatchInfo->prize_status != \JcModel\JcMatchesInfo::PRIZE_STATUS_SENDING_CODE){
            $this->logData[] = 'error prize_status: ' . $oMatchInfo->prize_status;
            return self::TASK_RESTORE;
        }

        $oQueryRes = \JcModel\JcBetsDetail::getUnPrizeList($iLotteryId, $this->pageSize);
        if (count($oQueryRes) <= 0 ){
            return self::TASK_SUCCESS;
        }

        $aBetIds = [];
        foreach($oQueryRes as $oBetsDetail){
            $aBetIds[$oBetsDetail->bet_id] = $oBetsDetail->bet_id;
        }
        $aProjects = \JcModel\JcProject::getByBetIds($aBetIds);
        $aProjectByBetId = [];
        $aAllowedStatus = [
            \JcModel\ManJcProject::STATUS_NORMAL,
            \JcModel\ManJcProject::STATUS_WON,
        ];
        foreach($aProjects as $oProject){
            if (!in_array($oProject->status, $aAllowedStatus)){
                continue;
            }
            if ($oProject->group_id > 0){
                continue;
            }
            $aProjectByBetId[$oProject->bet_id] = $oProject;
        }

        $aBetsDetailByUserId = [];
        foreach($oQueryRes as $oBetsDetail){
            $aBetsDetailByUserId[$oBetsDetail->user_id][$oBetsDetail->id] = $oBetsDetail;
        }
        foreach($aBetsDetailByUserId as $iUserId => $aBetsDetails){
            $oUser = \User::find($iUserId);
            $oAccount = \Account::lock($oUser->account_id,$iLocker);
            if (empty($oAccount)){
                $this->logData[] = 'Account lock failed. account_id: ' . $oUser->account_id;
                continue;
            }
            
            \DB::beginTransaction();
            
            foreach($aBetsDetails as $oBetsDetail){
                if (!isset($aProjectByBetId[$oBetsDetail->bet_id])){
                    //注单为合买或无法找到对应注单的直接计为派奖完成
                    $bSucc = $oBetsDetail->setPrizeSent();
                    if (!$bSucc){
                        $this->logData[] = 'set prize sent failed: ' . $oBetsDetail->id;
                    }
                    continue;
                }
                $oProject = $aProjectByBetId[$oBetsDetail->bet_id];
                $oProject->setUser($oUser);
                $oProject->setAccount($oAccount);
            
                $aExtraData = [
                  'amount' => $oBetsDetail->prize,
                ];
                $bSucc = $oProject->send($aExtraData);
                if ($bSucc){
                    $bSucc = $oBetsDetail->setPrizeSent();
                }
                if (!$bSucc){
                    $this->logData[] = 'send prize failed. id: ' . $oBetsDetail->id . '. prize: ' . $oBetsDetail->prize;
                    break;
                }
            }
            if ($bSucc){
                $this->logData[] = 'send prize success';
                \DB::commit();
            }else{
                \DB::rollback();
            }
            
            \Account::unLock($oUser->account_id,$iLocker,false);
        }
        
        $aBets = \JcModel\ManJcBet::getByIds($aBetIds);
        foreach($aBets as $oBet){
            $iBetId = $oBet->id;
            if (\JcModel\JcBetsDetail::countUnPrizeByBetId($iBetId) > 0){
                //跳过未全部派奖完成的方案
                continue;
            }
            $bSucc = $oBet->setPrizeSent();
            if ($bSucc){
                $this->logData[] = 'set bet sent successed. id: ' . $oBet->id;
            }else{
                $this->logData[] = 'set bet sent failed. id: ' . $oBet->id;
            }
            if ($bSucc && isset($aProjectByBetId[$iBetId])){
                $oProject = $aProjectByBetId[$iBetId];
                $bSucc = $oProject->setPrizeSent();
                if ($bSucc){
                    $this->logData[] = 'set project sent successed. id: ' . $oProject->id;
                }else{
                    $this->logData[] = 'set project sent failed. id: ' . $oProject->id;
                }
            }
        }
        
        return self::TASK_KEEP;
        
    }

//    /**
//     * 派发奖金
//     * @param Project $oProject
//     * @param DB $DB
//     * @return int
//     */
//    private function sendPrizes($oProject,$DB){
//        if ($oProject->status_prize == ManProject::PRIZE_STATUS_SENT){
//            return 1;
//        }
////        if (!$oProject->lock(TRUE)){
////            return ManProject::ERRNO_LOCK_FAILED;
////        }
//        $oAccount = Account::lock($oProject->account_id,$iLocker);
//        if (empty($oAccount)){
//            return Account::ERRNO_LOCK_FAILED;
//        }
//        $oUser = User::find($oProject->user_id);
//        $oProject->setUser($oUser);
//        $oProject->setAccount($oAccount);
//        $DB->beginTransaction();
//        if (($iReturn = $oProject->sendPrizes()) === true){
//            $iReturn = $oProject->setPrizeSentStatus();
//        }
////        $bSucc  = ($iCount = $oProject->sendPrizes()) && $oProject->setPrizeSentStatus();
////        file_put_contents('/tmp/sendprice',var_export($oProject->toArray(),true));
//        if ($iReturn === true){
//            $DB->commit();
//            $this->addProfitTask('prize',$oProject->prize_sent_at,$oProject->user_id,$oProject->prize,$oProject->lottery_id,$oProject->issue);
//        }
//        else{
//            $DB->rollback();
//        }
//        Account::unLock($oProject->account_id,$iLocker,false);
//        $iReturn === true or $oProject->unlock(true);
//        return $iReturn;
//    }

//    /**
//     * 派发佣金
//     * @param Project $oProject
//     * @param DB $DB
//     * @return int
//     */
//    private function sendCommissions($oProject,$DB){
//        if ($oProject->status_commission == ManProject::COMMISSION_STATUS_SENT){
//            return 1;
//        }
////        if (!$oProject->lock(FALSE)){
////            return ManProject::ERRNO_LOCK_FAILED;
////        }
//        $aCommissionUser = $oProject->user_forefather_ids ? $oProject->user_forefather_ids .',' . $oProject->user_id : $oProject->user_id;
//        $oAccounts = Account::lockManyOfUsers($aCommissionUser,$iLocker);
//        if (empty($iLocker)){
//            return Account::ERRNO_LOCK_FAILED;
//        }
//        $aAccounts = $aUsers    = [];
//        foreach ($oAccounts as $oAccount){
//            $aAccounts[ $oAccount->id ] = $oAccount;
//        }
//        unset($oAccounts);
//        $oUsers = User::getUsersByIds($aCommissionUser,['id','username','forefather_ids','is_tester']);
//        foreach ($oUsers as $oUser){
//            $aUsers[ $oUser->id ] = $oUser;
//        }
////        pr($aUsers);
////        exit;
//        unset($oUsers);
//        $DB->beginTransaction();
//        $aCommissions = [];
//
//        if (($iReturn = $oProject->sendCommissions($aUsers,$aAccounts,$aCommissions)) === true){
//            $iReturn = $oProject->setCommissionSentStatus();
//        }
////        file_put_contents('/tmp/sendcommission',var_export($oProject->toArray(),true));
//        if ($iReturn === true){
//            $DB->commit();
////            pr($aCommissions);
//            foreach($aCommissions as $iAgentId => $fAmount){
//                $this->addProfitTask('commission',$oProject->commission_sent_at,$iAgentId,$fAmount,$oProject->lottery_id,$oProject->issue);
//            }
//        }
//        else{
//            $DB->rollback();
//        }
//        Account::unlockManyOfUsers($aCommissionUser,$iLocker);
//        $iReturn === true or $oProject->unlock(false);
//
//        return $iReturn;
//    }
}
