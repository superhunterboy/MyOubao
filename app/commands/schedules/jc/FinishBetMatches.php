<?php
namespace JcCommand;

class FinishBetMatches extends \BaseTask {
    protected $pageSize = 100;
//    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];

    protected function doCommand() {        
        $iLotteryId = intval($this->data['lottery_id']);
        $iMatchId = intval($this->data['match_id']);
        
        $iDetailIdLimit = 5000;
        
        $oMatchInfo = \JcModel\JcMatchesInfo::getByMatchId($iMatchId);
        if (!$oMatchInfo->isFinished()){
            $this->logData[] = 'match is not finish: ' . $oMatchInfo->status;
            return self::TASK_RESTORE;
        }
        if ($oMatchInfo->prize_status == \JcModel\JcMatchesInfo::PRIZE_STATUS_NORMAL_CODE){
            $oMatchInfo->setDarwtingPrizeStatus();
        }
        if ($oMatchInfo->prize_status != \JcModel\JcMatchesInfo::PRIZE_STATUS_DRAWING_CODE){
            $this->logData[] = 'error prize_status: ' . $oMatchInfo->prize_status;
            return self::TASK_SUCCESS;
        }
        
//        $aMethodList = \JcModel\JcMethod::getAllByLotteryId($iLotteryId);
        
        $DB = \DB::connection();

        $oQueryRes = \JcModel\JcBetsMatch::getUnFinishedList($iMatchId, $this->pageSize);
        if (count($oQueryRes) <= 0){
            //加入计奖队列
            $aJobData = [
                'lottery_id' => $iLotteryId,
                'match_id' => $iMatchId,
            ];
            \BaseTask::addTask('\JcCommand\CalculatePrize', $aJobData, 'jc_calculate');

            if ($oMatchInfo->status == \JcModel\JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
                //若赛事取消则触发取消注单队列
                $aJobData = [
                    'lottery_id' => $iLotteryId,
                    'match_id' => $iMatchId,
                ];
                \BaseTask::addTask('\JcCommand\CancelBetsDetail', $aJobData, 'jc_calculate');
            }

            return self::TASK_SUCCESS;
        }
        
        $DB->beginTransaction();

        $bSucc = false;
        try{
            $aSelectDetailIds = [];
            foreach($oQueryRes as $oBetsMatch){
                if ($oBetsMatch->status == \JcModel\JcBetsMatch::STATUS_NORMAL){
                    $this->logData[] = 'status error: ' . $oBetsMatch->id;
                    $iBetId = $oBetsMatch->bet_id;
                    if (\JcModel\JcBetsMatch::addFillTask($iBetId)){
                        $this->logData[] = 'add fill task. bet_id: ' . $iBetId;
                    }
                    continue;
                }
                $aFinishedDetailIds = [];
                if ($oBetsMatch->finished_detail_ids){
                    $aTmpArr = explode(',', $oBetsMatch->finished_detail_ids);
                    foreach($aTmpArr as $iFinishedDetailId){
                        $aFinishedDetailIds[$iFinishedDetailId] = $iFinishedDetailId;
                    }
                }
                $aDetailIds = explode(',', $oBetsMatch->detail_ids);
                foreach($aDetailIds as $iDetailId){
                    if (!isset($aFinishedDetailIds[$iDetailId])){
                        $aFinishedDetailIds[$iDetailId] = $iDetailId;
                        $aSelectDetailIds[$iDetailId] = $iDetailId;
                    }
                    if (count($aSelectDetailIds) >= $iDetailIdLimit){
                        break;
                    }
                }
                if ($aFinishedDetailIds){
                    $oBetsMatch->finished_detail_ids = implode(',', $aFinishedDetailIds);
                    if ($oBetsMatch->finished_detail_ids === $oBetsMatch->detail_ids){
                        $oBetsMatch->status = \JcModel\JcBetsMatch::STATUS_FINISHED;
                    }
                    $bSucc = $oBetsMatch->save();
                    if ($bSucc){
                        $this->logData[] = 'set finished detail ids successed. id: ' . $oBetsMatch->id;
                    }else{
                        $this->logData[] = 'set finished detail ids failed. id: ' . $oBetsMatch->id;
                        break;
                    }
                }
                if (count($aSelectDetailIds) >= $this->pageSize){
                    break;
                }
            }

            if ($bSucc){
                if ($oMatchInfo->status == \JcModel\JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
                    $bSucc = \JcModel\JcBetsDetail::incrementCancelledMatches($aSelectDetailIds);
                    if ($bSucc){
                        $this->logData[] = 'set cancelled matches success';
                    }else{
                        $this->logData[] = 'set cancelled matches failed';
                    }
                }
                if ($bSucc){
                    $bSucc = \JcModel\JcBetsDetail::incrementFinishedMatches($aSelectDetailIds);
                    if ($bSucc){
                        $this->logData[] = 'set finished matches success';
                    }else{
                        $this->logData[] = 'set finished matches failed';
                    }
                }
            }
        }  catch (\Exception $ex){
            $bSucc = false;
            $this->logData[] = $ex->getMessage();
        }
        
        if ($bSucc){
            $this->logData[] = 'success';
            $DB->commit();
        }else{
            $this->logData[] = 'failed';
            $DB->rollback();
            return self::TASK_RESTORE;
        }
        
        return self::TASK_KEEP;
    }

    protected function checkData() {
        return isset($this->data['lottery_id']) && isset($this->data['match_id']);
    }

}
