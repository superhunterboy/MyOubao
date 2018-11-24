<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

//抓取所有自动生成下个奖期的彩种的开奖号
class CronGetGetTraceIssueCode extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:cron-get-trace-issue-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cron get trace issue code';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $oLotterys = Lottery::where('is_trace_issue', '=', 1)->get(['id']);

        $failLotteryId = [];

        if($oLotterys->count()>0)
        {
            foreach($oLotterys as $oLottery)
            {
                //最近一次的奖期
                if(!$oIssue = ManIssue::getLastestIssue($oLottery->id)){
                    continue;
                }

                $status = [Issue::ISSUE_CODE_STATUS_FINISHED, Issue::ISSUE_CODE_STATUS_CANCELED, Issue::ISSUE_CODE_STATUS_WAIT_VERIFY];
                if(in_array($oIssue->status, $status)){
                    continue;
                }

                $sProjectTime = $oIssue->getProjectTime($oIssue->end_time);
                $aJobData = ['lottery_id' => $oLottery->id, 'issue' => $oIssue->issue, 'project_time'=>date('Y-m-d H:i:s', $sProjectTime)];
                $iSucc = BaseTask::addTask('GetTraceIssueCodeFromMmc',$aJobData,'get_trace_issue_code');

                if(!$iSucc){
                    $failLotteryId[] = $oLottery->id;
                }
            }

            if($failLotteryId){
                $this->error('彩种：'.implode(',', $failLotteryId).'重启失败！');
            }else{
                $this->info('抓号队列重启成功！');
            }
        }else{
            $this->info('无需要抓取彩种！');
        }
    }

}
