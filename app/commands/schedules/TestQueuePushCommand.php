<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 生成公共下拉框静态json数据或html文件，使用该命令时，需要切换paths.php中的路径定义为具体路径，不能用变量
 */
class TestQueuePushCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:push-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test push queue.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function fire()
    {
        $iLotteryId = $this->argument('lottery_id');
        $sIssue = $this->argument('sIssue');
        $iCount = ManIssue::where('lottery_id',$iLotteryId)
            ->where('status',ManIssue::ISSUE_CODE_STATUS_WAIT_CODE)
            ->where('wn_number','')
            ->where('end_time','<',time())
            ->count();
        $j = 0;
        do{
            $j++;
            $sNumber = '';
            for($i = 0; $i < 3; $i++){
                $sNumber .= rand(0,9);
            }
            $aJobData = array(
                'lottery_id'=> $iLotteryId,
                'sIssue'=> $sIssue,
                'sWnNumber'=> $sNumber,
            );

            $oLastIssue = ManIssue::getLatestIssueOfNoWnNumber($iLotteryId);
            if(!empty($oLastIssue)) BaseTask::addTask('SetWinningNumberTaskForKl28',$aJobData,'set_win_number_for_Kl28');
        }while($j < $iCount);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('sIssue', InputArgument::OPTIONAL, null),
        );
    }
}

