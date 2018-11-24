<?php
            

class Push2ClientService extends BaseTask {
            

	public $oPassiveRecord;
        public $oPublic_data;

            
           /**
     * 生成赌桌版数据
     * @param $oLottery
     * @return mixed
     */
    private function _getGameWinNumberSettings($oLottery){

        $data = [
           
        ];
        //最新的奖期
        $lastIssue = Issue::where('lottery_id', '=', $oLottery->id)->orderBy('id', 'desc')->first();

        //最近一次的奖期
        if(!$currIssue = Issue::getLastestIssue($oLottery->id)){
            return $data;
        }

        $currIssue->issue == $lastIssue->issue or $data['nextNumber'] = $lastIssue->issue;

        $data['currNumber'] = $currIssue->issue;

        //1(可下注)、2(不可下注但是尚未完成计奖派奖)、3(已完成派奖处于等待前端完成动画)、4(撤奖)
        $curTime = time();

        if($currIssue->status == Issue::ISSUE_CODE_STATUS_CANCELED)
        {
            $aProjects = Project::getProjectIdByLotteryIdAndIssue($oLottery->id, $currIssue->issue);

            //如果还有注单没有给用户退款，则status为2，否则status为4
            if($aProjects->count() > 0 || $currIssue->issue == $lastIssue->issue){
                $data['status'] = 2;
                $data['leftTime'] = 0;
            }else{
                $leftTime = $lastIssue->begin_time - $curTime;
                $data['status'] = 4;
                $data['leftTime'] = $leftTime > 0 ?  $leftTime : 0 ;
            }
        }
        elseif($curTime >= $currIssue->start_time && $curTime < $currIssue->end_time)
        {
            $data['status'] = 1;
            $data['leftTime'] = $currIssue->end_time - $curTime;
        }
        elseif($curTime >= $currIssue->end_time && $currIssue->status_prize != Issue::PRIZE_FINISHED)
        {
            $data['status'] = 2;
            $data['leftTime'] = 0;
        }
        else{
            $data['status'] = 3;
            $data['leftTime'] = $lastIssue->begin_time - $curTime; //todo
            $data['leftTime'] >= 0 or $data['leftTime'] = 0;
        }
        $data['status_prize'] = $currIssue->status_prize;
        if($currIssue->status_prize != Issue::PRIZE_FINISHED){
            return $data;
        }

        $data['win_number'] = $currIssue->wn_number;

        $data['bet_prize'] = $this->_getWinListForBet($oLottery, $currIssue->issue, $currIssue->wn_number);

        return $data;
    }
    
    
      /**
     * 按开奖号码生成赌桌输赢数据
     * @param $oLottery
     * @param $sIssue
     * @param $sWnNumber
     * @return array
     */
    private function _getWinListForBet(& $oLottery, $sIssue, $sWnNumber){

        $key = 'win_number_lottery_'.$oLottery->id.'_issue_'.$sIssue. '_maps';
        if ($betPrize = Cache::get($key)){
            return $betPrize;
        }

        //选择的玩法 单挑一骰 大小单双
        $oSeriesWays = SeriesWay::where('series_id', $oLottery->series_id)->get();

        $aBetPrize = [];

        foreach($oSeriesWays as $oSeriesWay)
        {
            $oBasicWay = BasicWay::find($oSeriesWay->basic_way_id);
            $oBasicMethod = BasicMethod::find($oSeriesWay->basic_methods);

            if ($oBasicMethod->choose_count <= 0) {
                continue;
            }

            $aNumbers = $this->getNumberMaps($oBasicWay->function, $oBasicMethod);

            foreach($aNumbers as $sNumber){
                $aBetPrize[$oSeriesWay->id][$sNumber]['is_win'] = 0;
            }

            $sWningNumber = $oBasicMethod->getWinningNumber($sWnNumber);

            if($sWningNumber === false) continue;

            foreach($aNumbers as $sBetNumber)
            {
            
                if($oBasicMethod->countBetNumber($oBasicWay->function, $sBetNumber) && $oBasicMethod->checkPrize($oSeriesWay,$oBasicWay,$sWningNumber,$sBetNumber))
                {
                    $aBetPrize[$oSeriesWay->id][$sBetNumber]['is_win'] = 1;
                }
                else{
                    $aBetPrize[$oSeriesWay->id][$sBetNumber]['is_win'] = 0;
                }
            }

            Cache::put($key, $aBetPrize, 700);
 
        }
            
        return $aBetPrize;

    }
    
    
    private function getNumberMaps($function, $oBasicMethod){

        $aNumberList = [];
        $aAreaNum = explode('-', $oBasicMethod->valid_nums);

        if(count($aAreaNum) == 2)
        {
            for($i=$aAreaNum[0]; $i<=$aAreaNum[1]; $i++) $aNumberList[] = strval($i);
        }
        else{
            $aNumberList[] = $aAreaNum[0];
        }

        switch ($function){
            case 'Sum' /*和值 */:
            case 'BigSmallOddEven' /*大小单双*/:
            case 'SpecialConstituted' /*三星通选 */:
            case 'TwoStarSpecial' /*二星特殊 */:
            case 'BjlEnum' /*百家乐和值 */:
                break;

            default:
                //三同号 二同号
                if ($oBasicMethod->choose_count == $oBasicMethod->min_repeat_time && $oBasicMethod->min_repeat_time == $oBasicMethod->max_repeat_time){
                    foreach($aNumberList as $i => $iNumber){
                        $aNumberList[$i] = str_repeat( $iNumber, $oBasicMethod->choose_count);
                    }
                }else{
                    $aCombinations = Math::getCombinationToString($aNumberList, $oBasicMethod->choose_count);
                    $aNumberList = [];
                    foreach($aCombinations as $sCombination){
                        $aNumberList[] = str_replace(',','',$sCombination);
                    }
                }
                break;
        }

        return array_reverse($aNumberList);
    }

    private function plusGameWinNumberSettings(& $oLottery, $sIssue){

        $aUserData = [];

        //投注信息
        $oProjects = ManProject::getValidProjects($oLottery->id, $sIssue);

        if($oProjects->count() > 0)
        {
            //初始化
            $aUserId = array_unique(array_column($oProjects->toArray(), 'user_id'));
            foreach($aUserId as $iUserId){
                $aUserData[$iUserId]['bet_count'] = 0;
                $aUserData[$iUserId]['win_amount'] = 0;
                $aUserData[$iUserId]['balance'] = Account::getAvaliable($iUserId);
            }

            foreach($oProjects as $oProject){
                $oProject->bet_number = Encrypt::db_decode($oProject->bet_number);
                $aUserData[$oProject->user_id]['bet_count'] += 1;
                $aUserData[$oProject->user_id]['win_amount'] += $oProject->prize;

                $aUserData[$oProject->user_id]['bet_prize'][$oProject->way_id][$oProject->bet_number]['bet_amount'] = $oProject->amount;
                $aUserData[$oProject->user_id]['bet_prize'][$oProject->way_id][$oProject->bet_number]['win_amount'] = $oProject->prize ? $oProject->prize :0 ;;
            }
        }

        return $aUserData;

    }

            
	protected function doCommand()
	{
		  extract($this->data);
                  
		 $this->log = 'begin push the code to c++ server';
		$this->log = 'startTime:'. date('Y-m-d H:i:s');
                if (!$iLotteryId) {
			$this->log("missing lottery, lottery_id=" . $iLotteryId);exit;
		}
                if($iLotteryId<44) return self::TASK_SUCCESS;
             
                $oLottery = Lottery::find($iLotteryId);

                if (empty($oLottery)) {
                   $this->log = "no lottery found, lottery_id=" . $iLotteryId;
		
                  return self::TASK_SUCCESS;
                }
                
            
            
                        $aGameConfig = [];$aUserData = [];
            
                      $aGameConfig = $this->_getGameWinNumberSettings($oLottery);
                     
            
                  if(!$aGameConfig) $aGameConfig = [];
                  
                      if(isset($aGameConfig['currNumber'])){
                          $aUserData = $this->plusGameWinNumberSettings($oLottery, $aGameConfig['currNumber']);
                          empty($aGameConfig['win_number']) or $aGameConfig['currentTrend']=$this->getTrendGraph($oLottery->id, $aGameConfig['currNumber']);
                      }
                       $aGameConfig['lottery_id']=$iLotteryId;
                $push_data = ["count"=>1,"message"=>[["msg"=>$aGameConfig,"pid"=>"/bets/bet/".$iLotteryId]],"sid"=>52,"time"=>date("YmdHis"),"type"=>"broadcast"];
            
            
                $this->log = 'push public data: '.json_encode($push_data);
            
                // $oCurl = new MyCurl( "http://10.10.170.59:9009/msgapi" );
                $oCurl = new MyCurl( "http://10.10.4.64:9009/msgapi" );
                $oCurl->setTimeout(10);
                
                $oCurl->setPost(json_encode($push_data));
               // $oCurl->setReferer($this->_makeReferer());
                $oCurl->createCurl();
                $oCurl->execute();
                $sJsonResult = $oCurl->__tostring();
                $this->log = "return public data: ".$sJsonResult;
		$this->log = 'endTime:'.date('Y-m-d H:i:s');
                
                if(!empty($aUserData)){
            
                    $aPrivateData["count"] = count($aUserData);
                    $aPrivateData["sid"]=52;
                    $aPrivateData['time'] = date("YmdHis");
                    $aPrivateData['type'] = 'single';
                   
                    foreach($aUserData as $user_id =>$data){
                        $aGameConfig['bet_count']=$data['bet_count'];
                        $aGameConfig['balance']=$data['balance'];
            		if(!isset($aGameConfig['bet_prize'])) continue;
                        foreach($aGameConfig['bet_prize'] as $way_id=>$bet_prize_data){
                            if(isset($data['bet_prize'][$way_id])){
                               
                                foreach($bet_prize_data as $bet_number_key=>$bet_number_data){
                                   $aGameConfig['bet_prize'][$way_id][$bet_number_key]['win_amount']= ((isset($data['bet_prize'][$way_id][$bet_number_key]['win_amount']))  ? $data['bet_prize'][$way_id][$bet_number_key]['win_amount'] : 0);

                                   $aGameConfig['bet_prize'][$way_id][$bet_number_key]['bet_amount']= ((isset($data['bet_prize'][$way_id][$bet_number_key]['bet_amount']))  ? $data['bet_prize'][$way_id][$bet_number_key]['bet_amount'] : 0);
                                   
                                }
                            } 
                                  
                            else
                                foreach($bet_prize_data as $bet_number_key=>$bet_number_data){
                                    $aGameConfig['bet_prize'][$way_id][$bet_number_key]['win_amount']=0;
                                    $aGameConfig['bet_prize'][$way_id][$bet_number_key]['bet_amount'] = 0;
                                }
                        }

	
                   } 

                        $aPrivateData['message'][] = ['cid'=>$user_id, 'cook'=>'',
                            'msg'=> $aGameConfig,
                                   
                                    'pid'=>'/bets/bet/'.$iLotteryId,
                                    'sess'=> '',
                            
                            ];
                    
                     $this->log = 'push private data: '.json_encode($aPrivateData);
               //exit;
                     $oCurl->setPost(json_encode($aPrivateData));
               // $oCurl->setReferer($this->_makeReferer());
                $oCurl->createCurl();
                $oCurl->execute();
                $sJsonResult = $oCurl->__tostring();
                $this->log = "return private data: ".$sJsonResult;
		$this->log = 'endTime:'.date('Y-m-d H:i:s');
                return self::TASK_SUCCESS;
                }
	}


    public function getTrendGraph($iLotteryId, $sIssue){
        $oLottery = Lottery::find($iLotteryId);

        $currIssue = Issue::getIssue($iLotteryId, $sIssue);

        $betCode = ['zhuangdui'=>'5','xiandui'=>'6'];
        $betCodeUnique = ['zhuangxianhe' => ['0','1','2']];
        $betCodeMerge = [];
        foreach($betCodeUnique as $name => $aCode) $betCodeMerge = array_merge($betCodeMerge, $aCode);

        $data = [];
        $aBetPrizes = $this->_getWinListForBet($oLottery, $currIssue->issue, $currIssue->wn_number);

        foreach($aBetPrizes as $seriesWayId => $aCodes)
        {
            foreach($aCodes as $sCode =>$aIsWin)
            {
                if(in_array($sCode, $betCode)){
                    $name = array_keys($betCode, $sCode);
                    $data[ $name[0] ] = $aIsWin['is_win'];
                }

                elseif(in_array($sCode, $betCodeMerge))
                {
                    foreach($betCodeUnique as $name => $aCode){
                        if($aIsWin['is_win']){
                            $data[$name] = $sCode;
                        }
                    }
                }
            }
        }

        return $data;
    }
}
