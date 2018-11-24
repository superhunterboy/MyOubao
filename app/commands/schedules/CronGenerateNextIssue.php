<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronGenerateNextIssue extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:cron-generate-next-issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cron generate next issue';

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

        if($oLotterys->count()>0)
        {
            $iStartDay = time();
            $oManIssue = new ManIssue;

            foreach($oLotterys as $oLottery)
            {
                $oLastIssue = Issue::where('lottery_id', '=', $oLottery->id)->orderBy('issue', 'desc')->first();

                if(!$oLastIssue || ($oLastIssue->end_time + 5*60) <= $iStartDay){
                    $oManIssue->generateNextIssue($oLottery->id);
                }
            }
        }

        $this->info('奖期生成完成！');
    }
}
