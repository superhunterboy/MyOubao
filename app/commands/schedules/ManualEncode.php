<?php
/**
 * 补录开奖号码
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ManualEncode extends BaseCommand {
	public $oPassiveRecord;
	protected $sFileName = 'manualencode';
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'rng:manualencode';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'manual encode';

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
		$this->writeLog('begin getting the fixing code from rng');
		$issue = $this->argument('issue');
		$oLottery = Lottery::find($iLotteryId);
		if (!$oLottery) {
			$this->exitPro("missing lottery, lottery_id=" . $iLotteryId, false);
		}
		$lot_name = $oLottery->identifier;


		$aCondtions = [
			'lottery_id' => [ '=', $iLotteryId],
			'end_time' => [ '<', time()],
			'offical_time' => [ '<=', strtotime(date('Y-m-d H:i'))],
			'status' => ['in', [ManIssue::ISSUE_CODE_STATUS_WAIT_CODE, ManIssue::ISSUE_CODE_STATUS_WAIT_VERIFY]],
		];
//		$this->writeLog(json_encode($aCondtions));
		$oLastIssues = ManIssue::doWhere($aCondtions)->orderBy('id', 'asc')->limit(50)->get(['*']);
		if (!$oLastIssues) {
			$this->exitPro("No issue, lottery_id=" . $iLotteryId, false);
		}

		$oCenter = new CodeCenter;
		$oCenter->id = 1;
		$oCenter->name = "RNG";
		foreach($oLastIssues as $oLastIssue){

			$this->oPassiveRecord = new PassiveRecord;
			$this->oPassiveRecord->lottery_id = $iLotteryId;
			$this->oPassiveRecord->codecenter_id = $oCenter->id;
			$this->oPassiveRecord->request_lottery = $oLottery->identifier;
			$this->oPassiveRecord->issue = $oLastIssue->issue;
			$this->oPassiveRecord->customer_key = $oCenter->customer_key;
			$this->oPassiveRecord->request_time = microtime(true);
			$this->oPassiveRecord->save();
			$project_time = date('Y-m-d H:i',$oLastIssue->offical_time);
			if (!$oLastIssue) {
				$this->exitPro("No issue, lottery_id=" . $iLotteryId, false);
			}
			$issue = $oLastIssue->issue;
			$rng_server_address1 = SysConfig::readValue('rng_server_address1');
			$rng_server_address2 = SysConfig::readValue('rng_server_address2');
			$rng_server_address3 = SysConfig::readValue('rng_server_address3');
			$server_list = array($rng_server_address1,$rng_server_address2,$rng_server_address3);

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
			if(!$grab_result) {
				$this->writeLog("Didn't get number, push queue, lot_name=".$lot_name . " issue=" . $issue . " project_time=".$project_time);exit;
//				$aJobData = [
//					'iLotteryId' => $iLotteryId,
//					'lot_name' => $lot_name,
//					'issue' => $issue,
//					'project_time' => $project_time,
//				];
//				BaseTask::addTask('GetCodeFromRng',$aJobData,'get_code_from_rng'); exit;
			}
			$this->writeLog('rng_url1=' . $obj_rng->first_grab_server_path.' rng_url2=' . $obj_rng->second_grab_server_path);
			switch($oLottery->type){
				case 1 : $grab_result = str_replace(',','',$grab_result);break;
				case 2 : $grab_result = str_replace(',',' ',$grab_result);break;
			}
			$this->writeLog('grab_result:' . $grab_result.' lot_name:'.$lot_name.' issue:'.$issue.' project_time:'.$project_time.' current_time:'.date('Y-m-d H:i:s'));

			if ($oLottery->checkWinningNumber($grab_result)) {
				if ($oLastIssue->setWinningNumber($grab_result,$oCenter) === true) {
					$this->oPassiveRecord->code = $grab_result;
					$oLastIssue->setCalculateTask();
					$this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $grab_result . ', saved success!', true, true);
				} else {
					$this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $grab_result . ', saved fail!', true, true);
				}
			} else {
				$this->exitPro('lottery_id = ' . $iLotteryId . ', issue = ' . $oLastIssue->issue . ', code = ' . $grab_result . ', format is not correct', true, true);
			}
		}
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

	/**
	 * 停止进程运行
	 * @param int $iErrCode  错误编码
	 * @param bool $bSave  是否保存日志记录
	 */
	public function exitPro($sErrMessage, $bSave = true, $bExit = false) {
		$this->writeLog($sErrMessage, TRUE);
		if ($bSave) {
			$this->oPassiveRecord->finish_time = microtime(true);
			$this->oPassiveRecord->spent_time = $this->oPassiveRecord->finish_time - $this->oPassiveRecord->request_time;
//            pr($this->oPassiveRecord->getAttributes());
			// 保存日志记录到数据库
			$this->oPassiveRecord->save();
		}
		if (!$bExit) {
			exit;
		}
	}

}

