<?php
/**
 * 补录开奖号码
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateNextIssue extends BaseCommand {

    protected $sFileName = 'GenerateNextIssue';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rng:generate-next-issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate next issue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $iLotteryId = $this->argument('lottery_id');
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName . '-' .$iLotteryId;
        $this->writeLog('begin generate next issue');

        $oIssue = new ManIssue();

        if($oIssue->generateNextIssue($iLotteryId)){
            $this->writeLog('generate issue is suss!');
        }else{
            $this->writeLog('generate issue is fail!');
        }

        $this->writeLog('end generate next issue');
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
        );
    }
}
