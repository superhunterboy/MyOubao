<?php
namespace JcCommand;

class CancelBetsDetail extends \BaseTask {
    protected $pageSize = 100;
//    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];

    protected function doCommand() {
        $iLotteryId = $this->data['lottery_id'];
        
        $oBetsDetailList = \JcModel\JcBetsDetail::getUnCalculateCancelledList($iLotteryId, $this->pageSize);
        if (count($oBetsDetailList) <= 0){
            return self::TASK_SUCCESS;
        }
        
        $aBetIds = [];
        foreach($oBetsDetailList as $oBetsDetail){
            $aBetIds[$oBetsDetail->bet_id] = $oBetsDetail->bet_id;
        }
        $aBetList = \JcModel\JcBet::getByIds($aBetIds);
        $aProjects = \JcModel\JcProject::getByBetIds($aBetIds);
        $aProjectByBetId = [];
        foreach($aProjects as $oProject){
            if ($oProject->status != \JcModel\JcProject::STATUS_NORMAL || $oProject->group_id > 0){
                //合买注单和非待开奖状态的注单不进行退款
                continue;
            }
            $aProjectByBetId[$oProject->bet_id] = $oProject;
        }
        
        $DB = \DB::connection();
        
        $aBetDetailListByUid = [];
        foreach($oBetsDetailList as $oBetsDetail){
            $aBetDetailListByUid[$oBetsDetail->user_id][$oBetsDetail->id] = $oBetsDetail;
        }
        foreach($aBetDetailListByUid as $iUserId => $aBetsDetails){
            $oUser = \User::find($iUserId);
            $oAccount = \Account::lock($oUser->account_id, $iLocker);
            if (empty($oAccount)) {
                $this->logData[] = 'Account lock failed. account_id: ' . $oUser->account_id;
                continue;
            }
            
            $DB->beginTransaction();
            
            $bSucc = false;
            foreach($aBetsDetails as $oBetsDetail){
                $oBet = $aBetList[$oBetsDetail->bet_id];
                $bSucc = $oBetsDetail->setToCancelled();
                if ($bSucc){
                    $bSucc = $oBet->incrementCancelledAmount($oBetsDetail->amount);
                    if (!$bSucc){
                        $this->logData[] = "increment cancelled amount failed. bet_id: " . $oBet->id;
                    }
                }else{
                    $this->logData[] = "save bets detail status failed. acount_id: " . $oBetsDetail->id;
                }
                if ($bSucc && isset($aProjectByBetId[$oBetsDetail->bet_id])){
                    $oProject = $aProjectByBetId[$oBetsDetail->bet_id];
                    $aExtraData = $oProject->getTransactionData();
                    $iReturn = \Transaction::addTransaction($oUser, $oAccount, \TransactionType::TYPE_DROP, $oBetsDetail->amount, $aExtraData);

                    $bSucc = $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
                }

                if ($bSucc && $oBet->status == \JcModel\JcBet::STATUS_NORMAL){
    //                $this->logData[] = "count:" . \JcModel\JcBetsDetail::countUnCancelledByBetId($oBet->id);
                    if (\JcModel\JcBetsDetail::countUnCancelledByBetId($oBet->id) == 0){
                        $bSucc = $oBet->setToCancelled();
                        if ($bSucc){
                            if ($oBet->group_id > 0){
                                $oGroupBuy = \JcModel\JcGroupBuy::find($oBet->group_id);
                                if (in_array($oGroupBuy->status, [\JcModel\JcGroupBuy::STATUS_NORMAL, \JcModel\JcGroupBuy::STATUS_AVAILABLE])){
                                    $bSucc = $oGroupBuy->setToCancelled();
                                    if ($bSucc){
                                        $oGroupBuy->setDropTask();
                                    }else{
                                        $this->logData[] = 'set group buy status failed. group_id: ' . $oGroupBuy->id;
                                    }
                                }
                            }else{
                                $bSucc = $oProject->setToCancelled();
                            }
                        }else{
                            $this->logData[] = 'set bet status failed. bet_id: ' . $oBet->id;
                        }
                    }
                }else{
                    $this->logData[] = "Account return cancelled amount failed. acount_id: " . $oUser->account_id;
                }
                if (!$bSucc){
                    $this->logData[] = 'cancel bet detail failed. ' . $oBetsDetail->id;
                    break;
                }else{
                    $this->logData[] = 'cancel bet detail success. id: ' . $oBetsDetail->id;
                }
            }
            
            if ($bSucc){
                $this->logData[] = 'cancel success. user_id: ' . $iUserId;
                $DB->commit();
            }else{
                $this->logData[] = 'cancel failed. user_id: ' . $iUserId;
                $DB->rollback();
            }
            
            \Account::unlock($oUser->account_id, $iLocker);
        }
        
        return self::TASK_KEEP;
    }

    protected function checkData() {
        return isset($this->data['lottery_id']);
    }

}
