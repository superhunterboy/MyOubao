<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-10-5
 * Time: 上午10:52
 * 1,通过lottery_id和issue在project表里获取project_id
 * 2,通过
 */
class ProjectIdQueue extends BaseTask {

    protected function doCommand(){
        extract($this->data);
        $oIssues = ManIssue::getIssue($lottery_id,$issue);
	    if(!$oIssues) return self::TASK_SUCCESS;
        if($oIssues->status == ManIssue::ISSUE_CODE_STATUS_CANCELED){
            $oIssues->setCancelTask();
            return self::TASK_SUCCESS;
        }

        $aProjects = Project::getProjectIdByLotteryIdAndIssue($lottery_id,$issue);

        if (!$aProjects->count()){

            $oIssues->status_commission = Issue::COMMISSION_FINISHED;
            if(!$oIssues->save()){
                $this->log = 'Issues set status_commission fail issue:'.$issue;
            }
            $this->log = ' Project Missing, Exiting';
            return self::TASK_SUCCESS;
        }

        $aCommissions = Commission::doWhere([
            'lottery_id' => $lottery_id,
            'issue' => $issue,
            'status' => Commission::STATUS_WAIT
        ])->get(['project_id']);
        /**
         * 1,判断该奖期Commission表是否有返点,如果没有就直接完成
         * 2,将该奖期返点的注单ID取出来$aCommission
         * 3,遍历该奖期所有的注单ID$aProjects,若注单$oProject->id有返点则派发返点$aCommission,否则就直接完成
         *
         */
        if(!$aCommissions->count()) {//如果该奖期不存在返点
            //更新注单的返点状态为已完成
            DB::table('projects')
                ->where('lottery_id',$lottery_id)
                ->where('issue',$issue)
                ->update(['status_commission'=>Project::COMMISSION_STATUS_SENT]);
            //更新奖期的返点状态为已完成
            DB::table('issues')
                ->where('lottery_id',$lottery_id)
                ->where('issue',$issue)
                ->update(['status_commission'=>Project::COMMISSION_STATUS_SENT]);
            return self::TASK_SUCCESS;
        }

        $aCommission = [];
        foreach ($aCommissions as $oCommission) {
            if(!in_array($oCommission->project_id,$aCommission)) $aCommission[] = $oCommission->project_id;
        }
        $this->log = 'Commission push for issue:'.$issue;
        foreach($aProjects as $oProject){
	        if(in_array($oProject->id,$aCommission)){//有返点发返点
                if(!$this->pushJob('SendCommission',array('project_id'=>$oProject->id),Config::get('schedule.send_commission'))) return self::TASK_RESTORE;
                $this->log = 'Commission push project_id :'.$oProject->id;
            }else{//该注单没有返点则直接将注单状态设置已完成
                DB::table('projects')
                    ->where('status_commission',Project::COMMISSION_STATUS_WAITING)
                    ->where('id',$oProject->id)
                    ->update(['status_commission'=>Project::COMMISSION_STATUS_SENT]);
            }
        }

        return self::TASK_SUCCESS;
    }
}
