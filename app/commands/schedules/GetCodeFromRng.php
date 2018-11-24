<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GetCodeFromRng extends BaseCommand {
	protected $sFileName = 'rnggetcode';
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'rng:getcode';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get code from RNG';

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
		!$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName .'-'. $iLotteryId;
		$this->writeLog('begin getting the code from rng');
		$this->writeLog('startTime:'. date('Y-m-d H:i:s'));
		$oLottery = Lottery::find($iLotteryId);
		if (!$oLottery) {
			$this->writeLog("missing lottery, lottery_id=" . $iLotteryId);exit;
		}
		$lot_name = $oLottery->name;
		$project_time = date('Y-m-d H:i');
		$offical_time = strtotime($project_time);
		$aCondtions = [
			'lottery_id' => [ '=', $iLotteryId],
			'offical_time' => [ '=', $offical_time],
			'end_time' => [ '<', time()],
			'status' => ['in', [ManIssue::ISSUE_CODE_STATUS_WAIT_CODE, ManIssue::ISSUE_CODE_STATUS_WAIT_VERIFY]],
		];
		$oLastIssue = ManIssue::doWhere($aCondtions)->first();

		if (!$oLastIssue) {
			$this->writeLog("No issue, lottery_id=" . $iLotteryId);exit;
		}

		$rng_server_address1 = SysConfig::readValue('rng_server_address1');
		$rng_server_address2 = SysConfig::readValue('rng_server_address2');
		$rng_server_address3 = SysConfig::readValue('rng_server_address3');
		$server_list = array($rng_server_address1,$rng_server_address2,$rng_server_address3);

		$lot_name = $oLottery->identifier;

		$issue = $oLastIssue->issue;

		$obj_rng = new RandomNumber();

		$i = 0;
		do{
			$i++;
			$aRangNums = array_rand($server_list,2);
			$obj_rng->first_grab_server_path = $server_list[$aRangNums[0]];
			$obj_rng->second_grab_server_path = $server_list[$aRangNums[1]];

			$grab_result = $obj_rng->grabNumber($lot_name, $issue, $project_time);
			if(!$grab_result) sleep(1);
		}while(!$grab_result && $i < 10);
		$this->writeLog('getCodeTime:'.date('Y-m-d H:i:s'));
		$this->writeLog('rng_url1=' . $obj_rng->first_grab_server_path.' rng_url2=' . $obj_rng->second_grab_server_path);

		switch($oLottery->type){
			case 1 : $grab_result = str_replace(',','',$grab_result);break;
			case 2 : $grab_result = str_replace(',',' ',$grab_result);break;
		}
		$this->writeLog('grab_result:' . $grab_result.' lot_name:'.$lot_name.' issue:'.$issue.' project_time:'.$project_time.' current_time:'.date('Y-m-d H:i:s'));
		$oCenter = new CodeCenter;
		$oCenter->id = 1;
		$oCenter->name = "RNG";
		if ($oLottery->checkWinningNumber($grab_result)) {
			if ($oLastIssue->setWinningNumber($grab_result,$oCenter) === true) {
//				$this->oPassiveRecord->code = $grab_result;
				$oLastIssue->setCalculateTask();
				$this->writeLog('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $grab_result . ', saved success!');
			} else {
				$this->writeLog('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $grab_result . ', saved fail!');
			}
		} else {
			$this->writeLog('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $grab_result . ', format is not correct');
		}

		$this->writeLog('endTime:'.date('Y-m-d H:i:s'));
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

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}


}
