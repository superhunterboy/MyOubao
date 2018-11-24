<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RestartSendPrizeCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:restart-send-prize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'restart send prize';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('issue', InputArgument::REQUIRED, null),
            array('project_id', InputArgument::OPTIONAL, null),
        );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $issue = $this->argument('issue');
        $lottery_id = $this->argument('lottery_id');
        $project_id = $this->argument('project_id');
        if($project_id)
        {
            $project_id = explode(',',$project_id);
            $oProjects = ManProject::getUnSentPrizesProjects($project_id);
        }else{
            $oProjects = ManProject::getUnSentPrizeProjectLists($lottery_id, $issue);
        }

        if($oProjects->count() > 0) {
            $aProjectIds = array_column($oProjects->toArray(), 'id');
            $aJobData = [
                'type' => 'prize',
                'projects' => $aProjectIds,
            ];
            $iSucc = BaseTask::addTask('SendMoney',$aJobData,'send_money');
        }else{
            $iSucc = true;
        }

        if($iSucc) $this->info('派发奖金队列重启成功！');
        else $this->error('派发奖金队列重启失败！');
    }
}
