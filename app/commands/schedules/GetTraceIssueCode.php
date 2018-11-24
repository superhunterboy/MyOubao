<?php
class GetTraceIssueCode extends BaseTask {


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
            $this->log = ' Issue Missing, Exiting';
            return self::TASK_SUCCESS;
        }

        if($oIssue->end_time > time()) return self::TASK_RESTORE;

        //todo 启动抓号
        /*        $endSecond =date('s', $oIssue->end_time);

                $areaTime = intval($endSecond/10)*10 + $endSecond%10 > 5 ? 10 : 0;
                if($areaTime >= 60) $areaTime = 0;*/

//        $sCode = mt_rand(1,6).mt_rand(1,6).mt_rand(1,6);
        //---------------从RNG获取号码开始--------------
        $rng_server_address1 = SysConfig::readValue('rng_server_address1');
        $rng_server_address2 = SysConfig::readValue('rng_server_address2');
        $rng_server_address3 = SysConfig::readValue('rng_server_address3');
        $server_list = array($rng_server_address1,$rng_server_address2,$rng_server_address3);


        $oLottery = Lottery::find($iLotteryId);
        if (!$oLottery) {
            $this->writeLog("missing lottery, lottery_id=" . $iLotteryId);exit;
        }

        $lot_name = $oLottery->identifier;
        $issue = $oIssue->issue;
        $obj_rng = new RandomNumber();

        $i = 0;
        do{
            //$project_time = $this->getProjectTime();
            $i++;
            $aRangNums = array_rand($server_list,2);
            $obj_rng->first_grab_server_path = $server_list[$aRangNums[0]];
            $obj_rng->second_grab_server_path = $server_list[$aRangNums[1]];

            $grab_result = $obj_rng->grabNumber($lot_name, $issue, $sProjectTime);
            if(!$grab_result) sleep(1);
        }while(!$grab_result && $i < 10);

        $sCode = str_replace(',','',$grab_result);
        //-----------------从Rng获取号码结束-----------
        $this->log = 'rng return : '.$sCode;

        if(!is_numeric($sCode) || strlen($sCode) != 3){
            if((time() - strtotime($project_time)) > 10*60){
                $oIssue->code_center_return = $grab_result ? $grab_result : 'result=no number';
                $oIssue->save();
            }
            return self::TASK_KEEP;
        }

        $oCenter = new CodeCenter;
        $oCenter->id = 1;
        $oCenter->name = "rng";

        $bSucc = $oIssue->setWinningNumber($sCode, $oCenter);

        if($bSucc > 0)
        {
            $aJobData = [
                'lottery_id' => $oIssue->lottery_id,
                'issue' => $oIssue->issue,
            ];
            BaseTask::addTask('CalculatePrize', $aJobData, 'calculate');
            BaseTask::addTask('ProjectIdQueue',$aJobData,'ProjectIdQueue');
            $oIssue->updateWnNumberCache();

            $this->log = ' Issue WinCode is succ, WinCode:'.$sCode;

            return self::TASK_SUCCESS;
        }
        else{
            $this->log = ' Issue WinCode is fail, WinCode:'.$sCode;
            return self::TASK_KEEP;
        }

    }
}