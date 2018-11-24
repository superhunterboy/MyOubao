<?php

//用于所有自动生成奖期的彩种,用于重启队列后再

use Illuminate\Console\Command;

class RestartGetTraceIssueCode extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:restart-trace-issue-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'restart trace issue code';

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
        $aLotterys = Lottery::where('is_trace_issue', '>', 0)->get(['id'])->toArray();

        foreach($aLotterys as $aLottery){
            $oIssue = ManIssue::where('lottery_id', '=', $aLottery['id'])
                ->where('status', '=', ManIssue::ISSUE_CODE_STATUS_WAIT_CODE)
                ->where('end_time', '<', time()-15)->orderBy('id','desc')->first();

            if($oIssue){
                $sProjectTime = $oIssue->getProjectTime($oIssue->end_time);
                $aJobData = ['lottery_id' => $oIssue->lottery_id, 'issue' => $oIssue->issue, 'project_time'=>date('Y-m-d H:i:s', $sProjectTime)];
                BaseTask::addTask('GetTraceIssueCodeFromMmc',$aJobData,'get_trace_issue_code');
            }
        }
        $this->info('抓号队列重启成功！');

/*        $issue = $this->argument('issue');
        $lottery_id = $this->argument('lottery_id');

        $oIssue = ManIssue::where('lottery_id', '=', $lottery_id)->where('issue','=', $issue)->first();*/


/*        if($oIssue)
        {
            $sProjectTime = $oIssue->getProjectTime($oIssue->end_time);
            $aJobData = ['lottery_id' => $lottery_id, 'issue' => $issue, 'project_time'=>date('Y-m-d H:i:s', $sProjectTime)];
            $iSucc = BaseTask::addTask('GetTraceIssueCode',$aJobData,'get_trace_issue_code');

            if($iSucc) $this->info('抓号队列重启成功！');
            else $this->error('抓号队列重启失败！');
        }else{
            $this->error('奖期丢失！');
        }*/
    }

}
