<?php
class GetTraceIssueCodeFromMmc extends BaseTask {


    protected function doCommand(){
        extract($this->data);
        $iIssue = $issue;
        $iLotteryId = $lottery_id;
        $sProjectTime = $project_time;

        $aConditions = [
            'lottery_id' => [ '=', $iLotteryId],
            'issue' => [ '=', $iIssue],
            'status' => ['in', [ManIssue::ISSUE_CODE_STATUS_WAIT_CODE, ManIssue::ISSUE_CODE_STATUS_WAIT_VERIFY]],
        ];
        $oIssue = ManIssue::doWhere($aConditions)->first();

        if(!$oIssue){
            $this->log .= ' Issue Missing, Exiting';
            return self::TASK_SUCCESS;
        }

        if($oIssue->end_time > time()) return self::TASK_RESTORE;

        //todo 启动抓号
        /*        $endSecond =date('s', $oIssue->end_time);

                $areaTime = intval($endSecond/10)*10 + $endSecond%10 > 5 ? 10 : 0;
                if($areaTime >= 60) $areaTime = 0;*/

//        $sCode = mt_rand(1,6).mt_rand(1,6).mt_rand(1,6);
        //---------------从RNG获取号码开始--------------
//        $rng_server_address1 = SysConfig::readValue('rng_server_address1');
//        $rng_server_address2 = SysConfig::readValue('rng_server_address2');
//        $rng_server_address3 = SysConfig::readValue('rng_server_address3');
//        $server_list = array($rng_server_address1,$rng_server_address2,$rng_server_address3);


        $oLottery = Lottery::find($iLotteryId);
        if (!$oLottery) {
            $this->log .= " missing lottery, lottery_id=" . $iLotteryId;
        }

        $identifier = $oLottery->identifier;
        $this->log .= ' identifier:'.$identifier;


        $oJackpot = Jackpots::getAvailableJackpotByLottery($iLotteryId);

        $draw_count = !$oJackpot ? 1 : $oJackpot->number_count;
        $lottery_type = $this->getLotteryType($iLotteryId);

        $this->log .= ' lottery_type:'.$lottery_type;
        $issue = $oIssue->issue;
        $obj_rng = new RandomNumberFromMmc();
        $this->log .= ' server_path:'.$obj_rng->server_path;
        $i = 0;
        do{
            //$project_time = $this->getProjectTime();
            $i++;
//            $aRangNums = array_rand($server_list,2);
//            $obj_rng->first_grab_server_path = $server_list[$aRangNums[0]];
//            $obj_rng->second_grab_server_path = $server_list[$aRangNums[1]];

            $grab_result = $obj_rng->grabNumber('BM', $identifier, $sProjectTime, $draw_count, $lottery_type);
            if(!$grab_result) sleep(1);
        }while(!$grab_result && $i < 10);

        $this->log .= ' MMC return : '.$grab_result;

        $grab_result = str_replace('result=', '', $grab_result);
        $grab_result = ($lottery_type == 'LHD' || $lottery_type == 'BJL') ? str_replace(',',' ',$grab_result) : str_replace(',', '', $grab_result);
        $aWinNumbers = explode('|',$grab_result);
        array_walk($aWinNumbers, array($oIssue, 'compileCode'));

        $oCountPrize = new CountPrize();
        $oCountPrize->lottery_id = $iLotteryId;
        $oCountPrize->issue = $issue;
        $sCode = $oCountPrize->getMinPrizeNumber($aWinNumbers, $oJackpot);
        //-----------------从Rng获取号码结束-----------
        $this->log .= ' WinNumber is : '.json_encode($sCode);

        if(($lottery_type == 'LHD' && strlen($sCode[0])!=5) || ($lottery_type == 'BJL' && strlen($sCode[0]) < 11) || ($lottery_type == 'K3' && strlen($sCode[0])!=3)){
            if((time() - strtotime($project_time)) > 10*60){
                $oIssue->code_center_return = $sCode[0] ? $sCode[0] : 'result=no number';
                $oIssue->save();
            }
            return self::TASK_KEEP;
        }

        $oCenter = new CodeCenter;
        $oCenter->id = 1;
        $oCenter->name = "MMC";

        $bSucc = $oIssue->setWinningNumber($sCode[0], $oCenter);

        if($bSucc > 0)
        {
            $aJobData = [
                'lottery_id' => $oIssue->lottery_id,
                'issue' => $oIssue->issue,
            ];
            BaseTask::addTask('CalculatePrizeForElectronicEntertainment', $aJobData, 'calculate_electronic_entertainment');
            BaseTask::addTask('ProjectIdQueue',$aJobData,'ProjectIdQueue');
            $oIssue->updateWnNumberCache();

            $this->log .= ' Issue WinCode is succ, WinCode:'.$sCode[0];

            return self::TASK_SUCCESS;
        }
        else{
            $this->log .= ' Issue WinCode is fail, WinCode:'.$sCode[0];
            return self::TASK_KEEP;
        }

    }

    private function getLotteryType($iLotteryId){
        $aLotteriesType = Config::get('lotteries_type');
        return $aLotteriesType[$iLotteryId];
    }
}
