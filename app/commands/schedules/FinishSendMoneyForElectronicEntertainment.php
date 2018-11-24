<?php

/**
 * 设置指定奖期的派奖和佣金派发状态为已完成
 *
 * @author frank
 */
class FinishSendMoneyForElectronicEntertainment extends BaseTask {

    protected function doCommand(){
        extract($this->data,EXTR_PREFIX_ALL,'cal');

        $oLottery = Lottery::find($cal_lottery_id);
        if (empty($oLottery)){
            $this->log = ' Lottery Missing, Exiting';
            return self::TASK_SUCCESS;
        }
//        $sLogMsg = preg_replace('/(Lottery:) [\d]+/','$1 ' . $oLottery->name,$sLogMsg);
        $oIssue = ManIssue::getIssueObject($cal_lottery_id,$cal_issue);
        if (!is_object($oIssue)){
            $this->log = ' Issue Missing, Exiting';
            return self::TASK_SUCCESS;
        }
        $this->logBase .= ' Expire Time: ' . date('H:i:s',$oIssue->end_time);
        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_FINISHED){
            $this->log = 'On sale, Exiting';
            return self::TASK_RESTORE;
        }
        if ($oIssue->status_count == ManIssue::CALCULATE_NONE){
            $this->log = 'Issue Status Error, Exiting';
            return self::TASK_RESTORE;
        }

        if($oIssue->status_prize == ManIssue::PRIZE_FINISHED) {
            $this->log = 'Issue Prize Status Finished, Exiting';
            return self::TASK_SUCCESS;
        }

        $bFinishedPrize      = !PrjPrizeSet::getCountOfIssue($this->data[ 'lottery_id' ],$this->data[ 'issue' ],PrjPrizeSet::STATUS_WAIT);
        $bSucc = $oIssue->setPrizeFinishStatus($bFinishedPrize);
        $this->log      = 'Prize ' . ($bFinishedPrize ? 'Finished' : 'Partial Finished') . ',';

        $bGenerateSucc = false;

        //派奖设置为已完成状态成功并且已经派完奖
        if($bSucc && $bFinishedPrize){
             //if($cal_lottery_id >=44) $this->pushJob('Push2ClientService', ['iLotteryId'=>$cal_lottery_id], Config::get('schedule.push2clientserver'));
            $bGenerateSucc = $oIssue->generateNextIssue($cal_lottery_id);   //生成下一个奖期
           
        }
        
        /*==============自动重新加入队列==============*/
        if(!$bFinishedPrize){  
            $oProjects = ManProject::where('lottery_id','=',$this->data['lottery_id'])
                    ->where('issue','=',$this->data['issue'])
                    ->where('status','=',  ManProject::STATUS_WON)
                    ->whereIn('status_prize',[ManProject::PRIZE_STATUS_WAITING, ManProject::PRIZE_STATUS_SENDING])
                    ->limit(100)
                    ->get();
            foreach($oProjects as $oProject){
                $oProject->setPrizeTask();
            }
        }
        /*==============自动重新加入队列==============*/

        //当已经派完奖且已经生成新奖期时退出队列,否则重新加入队列
        return $bFinishedPrize && $bGenerateSucc ? self::TASK_SUCCESS : self::TASK_RESTORE;


    }
}
