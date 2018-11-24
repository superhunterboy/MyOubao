<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GetCodeFromMmc extends BaseCommand {
	protected $sFileName = 'mmcgetcode';
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mmc:getcode';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get code from MMC';

	public $oPassiveRecord;

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
		$iIssue = $this->argument('issue');
		$sNumbers = $this->argument('numbers');
		!$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName .'-'. $iLotteryId;
		$this->writeLog('begin getting the code from MMC');
		$this->writeLog('startTime:'. date('Y-m-d H:i:s'));
		$oLottery = Lottery::find($iLotteryId);
		if (!$oLottery) {
			$this->writeLog("missing lottery, lottery_id=" . $iLotteryId);exit;
		}
		$identifier = $oLottery->identifier;
		$project_time = date('Y-m-d H:i');
		$lottery_type = $this->getLotteryType($iLotteryId);
		$offical_time = strtotime($project_time);
		$aCondtions = [
			'lottery_id' => [ '=', $iLotteryId],
			'offical_time' => [ '=', $offical_time],
//			'end_time' => [ '<', time()],
//			'status' => ['in', [ManIssue::ISSUE_CODE_STATUS_WAIT_CODE, ManIssue::ISSUE_CODE_STATUS_WAIT_VERIFY]],
		];
		if($iIssue) {
			$aCondtions = [
				'lottery_id' => [ '=', $iLotteryId],
				'issue' => ['=',$iIssue]
			];
		}
		$this->writeLog('start get issue');
		$oLastIssue = ManIssue::doWhere($aCondtions)->first();
		$this->writeLog('end get issue');
		if (!$oLastIssue) {
			$this->writeLog("No issue, lottery_id=" . $iLotteryId);exit;
		}

//		$lot_name = $oLottery->identifier;

		$issue = $oLastIssue->issue;

		while(!BetThread::isEmpty($iLotteryId, $issue)) sleep(1);

		if (!Cache::has('jackpot_'.$iLotteryId)) {
			$oJackpot = Jackpots::getAvailableJackpotByLottery($iLotteryId);
			$draw_count = !$oJackpot ? 1 : $oJackpot->number_count;
		}else{
			$draw_count = !Cache::has('jackpot_number_count_'.Cache::get('jackpot_'.$iLotteryId)) ? 1 : Cache::get('jackpot_number_count_'.Cache::get('jackpot_'.$iLotteryId));
			$oJackpot = null;
		}

		if(!$sNumbers){
			$this->writeLog('start get code from MMC');
			$obj_rng = new RandomNumberFromMmc();

			$i = 0;
			do{
				$i++;
				$grab_result = $obj_rng->grabNumber('BM', $identifier, $project_time, $draw_count, $lottery_type);
				if(!$grab_result) sleep(1);
			}while(!$grab_result && $i < 10);
			$this->writeLog('end get code from MMC');
			$this->writeLog('getCodeTime:'.date('Y-m-d H:i:s'));
			$this->writeLog($grab_result);
			$grab_result = str_replace('result=', '', $grab_result);

			switch($oLottery->type){
				case 1 : $grab_result = str_replace(',','',$grab_result);break;
				case 2 : $grab_result = str_replace(',',' ',$grab_result);break;
			}
			$this->oPassiveRecord = new PassiveRecord;
			$this->oPassiveRecord->lottery_id = $iLotteryId;
			$this->oPassiveRecord->request_lottery = $oLottery->identifier;
			$this->oPassiveRecord->issue = $oLastIssue->issue;
			$this->oPassiveRecord->request_time = microtime(true);
			$this->oPassiveRecord->code = $grab_result;
			$this->oPassiveRecord->save();
			$aWinNumbers = explode('|',$grab_result);
		}else{
			$aWinNumbers = explode('-',$sNumbers);
		}



		$this->writeLog('start get prize');
		$oCountPrize = new CountPrize();
		$oCountPrize->lottery_id = $iLotteryId;
		$oCountPrize->issue = $issue;


		$result = $oCountPrize->getMinPrizeNumber($aWinNumbers, $oJackpot);
		$grab_result = $result[0];
		$this->writeLog('total bet amount:'.$result[2]);
		$this->writeLog('number:prize => '.$result[1]);
		$this->writeLog('end get prize');

//		$this->writeLog('grab_result:' . $grab_result.' lot_name:'.$lot_name.' issue:'.$issue.' project_time:'.$project_time.' current_time:'.date('Y-m-d H:i:s'));
		$oCenter = new CodeCenter;
		$oCenter->id = 1;
		$oCenter->name = "MMC";
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
			array('issue', InputArgument::OPTIONAL, null),
			array('numbers', InputArgument::OPTIONAL, null),
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

	private function getLotteryType($iLotteryId){
		$aLotteriesType = Config::get('lotteries_type');
		return $aLotteriesType[$iLotteryId];
	}

}
