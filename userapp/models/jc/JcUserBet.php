<?php

namespace JcModel;
/**
 * 注单模型
 */
class JcUserBet extends JcBet {
    
    /**
     * 取自购的投注记录
     * @param int $iUserId
     * @param int $iPageSize
     * @param array $aColumns
     * @return object
     */
    public static function getSelfListByLotteryIdAndUserId($iLotteryId = 0, $iUserId, $iPageSize = 15, $aColumns = ['*']){
        return self::where('lottery_id', $iLotteryId)->where('user_id', $iUserId)->where('group_id', 0)->orderby('id', 'desc')->paginate($iPageSize, $aColumns);
    }
    
    /**
     * 战绩页面方案列表
     * @param int $iUserId
     * @param int $iPageSize
     * @param array $aColumns
     * @return object
     */
    public static function getZjListByLotteryIdAndUserId($iLotteryId = 0, $iUserId, $aConditions = [], $iPageSize = 5, $aColumns = ['*']){
        return self::doWhere($aConditions)
                ->where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->where('return_percent', '>=', '101')
                ->with('userGroupBuy')
                ->orderby('type', 'desc')
                ->orderby('created_at', 'desc')
                ->paginate($iPageSize, $aColumns);
    }
    
    public function userGroupBuy(){
        return $this->belongsTo('\JcModel\JcUserGroupBuy', 'group_id');
    }
    
    public function checkBetData(){
        $iUserId = \Session::get('user_id');
        $this->user_id = $iUserId;
        $this->single_amount = 2 * $this->coefficient;
        $this->amount = 2 * $this->multiple * $this->total * $this->coefficient;
        
        $iMaxCountNum = \SysConfig::readValue('jc_bet_max_count');
        if ($this->total > $iMaxCountNum){
            return self::ERRNO_BET_COUNT_MAX;
        }
        $iMaxMultiple = \SysConfig::readValue('jc_bet_max_multiple');
        if ($this->multiple > $iMaxMultiple){
            return self::ERRNO_BET_CONTENT_MULTIPLE;
        }
        $fBetMaxAmount = \SysConfig::readValue('jc_bet_max_amount');
        if ($this->amount > $fBetMaxAmount){
            return self::ERRNO_BET_DAILY_AMOUNT_MAX;
        }
        if ($this->amount <= 0){
            return self::ERRNO_BET_ERROR_AMOUNT;
        }
        $aGameExtra = explode(',', $this->game_extra);
        $iMaxWayNum = \SysConfig::readValue('jc_bet_max_way_num');
        if (count($aGameExtra) > $iMaxWayNum){
            return self::ERRNO_BET_CONTENT_WAY;
        }
        $aMatchIds = explode(',', $this->match_ids);
        $iMaxMatchNum = \SysConfig::readValue('jc_bet_max_match_num');
        if (count($aMatchIds) > $iMaxMatchNum){
            return self::ERRNO_BET_CONTENT_MATCH_MAX;
        }
        return true;
    }

    public function addBet($aSingleBetData = []){
        try{
            if ($this->checkBetData() !== true){
                throw new \Exception("bet check failed");
            }
            $this->serial_number = self::makeSerialNumber($this->user_id);
            if (empty($this->save())){
                throw new \Exception("bet data save failed: " . var_export($this->errors(), 1));
            }
            $aJcBetData = $this->getAttributes();
            $iBetId = $aJcBetData['id'];
            $iLotteryId = $aJcBetData['lottery_id'];
            $iUserId = $aJcBetData['user_id'];
            $aAllMatchIds = [];
            $aSingleBetSumData = [];
            
            $aJcBetDetailInsertList = [];
            $oJcBetDetail = new \JcModel\JcBetsDetail($aJcBetData);
            $aJcBetDetailBasicData = $oJcBetDetail->getAttributes();
            foreach($aSingleBetData as $iWayId => $aSingleBet){
                foreach($aSingleBet as $aBetCodes){
                    $aJcDetailData = $aJcBetDetailBasicData;
                    $aJcDetailData['bet_id'] = $iBetId;
                    $aJcDetailData['way_id'] = $iWayId;
                    $aJcDetailData['total_matches'] = count($aBetCodes);
                    $aJcDetailData['amount'] = $aJcDetailData['single_amount'] * $aJcDetailData['multiple'];
                    
                    $aBetData = [];
                    foreach($aBetCodes as $oOdds){
                        $aBetMatch = $oOdds->getAttributes();
                        $aBetData[$aBetMatch['match_id']][$aBetMatch['code']] = $aBetMatch['odds'];
                        $aAllMatchIds[$aBetMatch['match_id']] = $aBetMatch['match_id'];
                        
                        //单关的统计投注金额
                        if ($aJcDetailData['total_matches'] == 1){
                            if (isset($aSingleBetSumData[$aBetMatch['match_id']])){
                                $aSingleBetSumData[$aBetMatch['match_id']] += $aJcDetailData['amount'];
                            }else{
                                $aSingleBetSumData[$aBetMatch['match_id']] = $aJcDetailData['amount'];
                            }
                        }
                    }
                    $aJcDetailData['bet_data'] = json_encode($aBetData);
                    $aJcBetDetailInsertList[] = $aJcDetailData;
                    $bSucc = \JcModel\JcBetsDetail::addInsertBetsDetail($aJcBetDetailInsertList);
                    if (!$bSucc){
                        throw new \Exception("bet detail save failed");
                    }
                }
            }
            if (count($aJcBetDetailInsertList) > 0){
                $bSucc = \JcModel\JcBetsDetail::batchInsertBetsDetail($aJcBetDetailInsertList);
                if (!$bSucc){
                    throw new \Exception("bet detail save failed");
                }
            }
            
            if ($bSucc){
                //初始化赛事关联数据，开奖使用
                foreach($aAllMatchIds as $iMatchId){
                    $aBetsMatchData = [
                        'lottery_id' => $iLotteryId,
                        'bet_id' => $iBetId,
                        'match_id' => $iMatchId,
                        'user_id' => $aJcBetData['user_id'],
                    ];
                    $oBetsMatch = new \JcModel\JcBetsMatch($aBetsMatchData);
                    $bSucc = $oBetsMatch->addBetsMatch();
                    if (!$bSucc){
                        throw new \Exception("bet match save failed. " . var_export($oBetsMatch->errors()->getMessages(), 1));
                    }
                }
            }
            
            $fSingleSumAmountBetLimit = \SysConfig::readValue('jc_single_bet_sum_amount_limit');
            foreach($aSingleBetSumData as $iMatchId => $fSumSingleAmount){
                $oBetSumData = JcBetSumData::getBetSumData($iLotteryId, $iUserId, $iMatchId);
                if ($oBetSumData->sum_single_amount + $fSumSingleAmount > $fSingleSumAmountBetLimit){
                    throw new \Exception("bet sum amount over limit", self::ERRNO_BET_DAILY_AMOUNT_MAX);
                }else{
                    $oBetSumData->sum_single_amount += $fSumSingleAmount;
                    $bSucc = $oBetSumData->saveBetSumData();
                    if (!$bSucc){
                        throw new \Exception("save bet sum data failed");
                    }
                }
            }
            
            $iReturn = self::ERRNO_BET_SUCCESSFUL;
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
//            var_dump($ex->getMessage(), $ex->getCode());die;
            
            $sCode = $ex->getCode();
            if ($sCode < 0){
                $iReturn = $sCode;
            }else{
                $iReturn = self::ERRNO_BET_ERROR_SAVE;
            }
        }
        
//        return $iReturn == self::ERRNO_BET_SLAVE_DATA_SAVED ? self::ERRNO_BET_SUCCESSFUL : $iReturn;
        return $iReturn;
    }
}
