<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RestartSendCommissionCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'admin-tool:restart-send-commission';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'restart send commission';

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
			array('issue', InputArgument::OPTIONAL, null),
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
		$iSucc = true;
		if(isset($issue) && isset($lottery_id)){
			$aJobData = [
				'lottery_id' => $lottery_id,
				'issue' => $issue,
			];

			$iSucc = BaseTask::addTask('ProjectIdQueue',$aJobData,'ProjectIdQueue');

		}elseif(isset($lottery_id) && !isset($issue)){
			$aCommissions = DB::table('commissions')->where('lottery_id',$lottery_id)->where('status',Commission::STATUS_WAIT)->take(1000)->get(['project_id']);
			foreach ($aCommissions as $oCommission) {
				$iSucc = BaseTask::addTask('SendCommission',$oCommission,Config::get('schedule.send_commission'));
			}
		}
		if($iSucc) $this->info('派发队列重启成功！');
		else $this->error('派发队列重启失败！');

	}


}
