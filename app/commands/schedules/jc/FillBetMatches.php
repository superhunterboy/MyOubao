<?php
namespace JcCommand;

class FillBetMatches extends \BaseTask {
    protected $pageSize = 10000;
//    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];

    protected function doCommand() {
        $iBetId = intval($this->data['bet_id']);
        $iStartId = isset($this->data['start_id']) ? $this->data['start_id'] : 0;
        
        $oBet = \JcModel\JcBet::find($iBetId);
        if (empty($oBet)){
            $this->logData[] = 'bet is empty: ' . $iBetId;
            return self::TASK_RESTORE;
        }
        $aBetsDetail = \JcModel\JcBetsDetail::getByBetIdAndStartId($iBetId, ['id', 'bet_data'], $iStartId, $this->pageSize);
        if (count($aBetsDetail) <= 0){
            $bSucc = \JcModel\JcBetsMatch::setToWaitingByBetId($iBetId);
            if ($bSucc){
                $this->logData[] = 'set to wating successed: ' . $iBetId;
            }else{
                $this->logData[] = 'set to wating failed: ' . $iBetId;
            }
            return self::TASK_SUCCESS;
        }
        
        $aDetailList = [];
        foreach($aBetsDetail as $oBetsDetail){
            $aBetData = json_decode($oBetsDetail->bet_data, true);
            $aMatchIds = array_keys($aBetData);
            foreach($aMatchIds as $iMatchId){
                $aDetailList[$iMatchId][$oBetsDetail->id] = $oBetsDetail->id;
            }
            $iStartId = $oBetsDetail->id;
        }
        
        $aRelatedMatchIds = array_keys($aDetailList);
        $aBetMatches = \JcModel\JcBetsMatch::getByBetId($iBetId, $aRelatedMatchIds);
        $aBetMatchList = [];
        foreach($aBetMatches as $oBetMatches){
            $aBetMatchList[$oBetMatches->match_id] = $oBetMatches;
        }
        
        foreach($aRelatedMatchIds as $iMatchId){
            if (isset($aBetMatchList[$iMatchId])){
                $oBetMatches = $aBetMatchList[$iMatchId];
            }else{
                $aBetMatchData = [
                    'bet_id' => $iBetId,
                    'match_id' => $iMatchId,
                ];
                $oBetMatches = new \JcModel\JcBetsMatch($aBetMatchData);
            }
            if ($oBetMatches->detail_ids){
                $aDetailIds = explode(',', $oBetMatches->detail_ids);
                foreach($aDetailIds as $iDetailId){
                    $aDetailList[$iMatchId][$iDetailId] = $iDetailId;
                }
            }
            sort($aDetailList[$iMatchId]);
            $iTotal = count($aDetailList[$iMatchId]);
            $oBetMatches->detail_ids = implode(',', $aDetailList[$iMatchId]);
            $bSucc = $oBetMatches->save();
            if ($bSucc){
                $this->logData[] = 'set detail ids successed: bet_id: ' . $iBetId . ' . match_id: ' . $iMatchId . '. count: ' . $iTotal;
            }else{
                $this->logData[] = var_export($oBetMatches->errors()->getMessages(), 1);
                $this->logData[] = 'set detail ids failed: bet_id: ' . $iBetId . ' . match_id: ' . $iMatchId;
            }
        }

        if ($bSucc){
            //若赛事取消则触发取消注单队列
            if (\JcModel\JcBetsMatch::addFillTask($iBetId, $iStartId)){
                return self::TASK_SUCCESS;
            }
        }
        
        return self::TASK_RESTORE;
    }

    protected function checkData() {
        return isset($this->data['bet_id']);
    }

}
