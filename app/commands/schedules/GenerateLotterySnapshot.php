<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateLotterySnapshot extends BaseCommand {

	protected $sFileName = 'lotterysnapshot';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:generate-lottery-snapshot';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'generate lottery snapshot';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('date', InputArgument::REQUIRED, null),
        );
    }

    private $aTransType = [
            TransactionType::TYPE_BET => 'turnover',
            TransactionType::TYPE_DROP => 'turnover',
            TransactionType::TYPE_SEND_PRIZE => 'prize',
            TransactionType::TYPE_CANCEL_PRIZE => 'prize',
            TransactionType::TYPE_SEND_COMMISSION => 'commission',
            TransactionType::TYPE_BET_COMMISSION => 'commission',
        ];

    private $cancelTransType = [TransactionType::TYPE_DROP, TransactionType::TYPE_CANCEL_PRIZE];

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// 设置日志文件保存位置
		!$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
 
		$this->writeLog('begin generate lottery snapshot.'.date("H:i:s"));

        $iLotteryId = $this->argument('lottery_id');

        $date = $this->argument('date');
        $startTime = date('Y-m-d 00:00:00', strtotime($date));
        $endTime = date('Y-m-d 23:59:59', strtotime($date));

        $this->lottery_profits($iLotteryId, $startTime, $endTime);
        $this->writeLog('end generate lottery snapshot.'.date("H:i:s"));
    }


    public function lottery_profits($iLotteryId, $startTime, $endTime){

        $columns = ['id', 'type_id', 'user_id', 'amount', 'is_tester'];

        if($oTransactions = Transaction::where('lottery_id', '=', $iLotteryId)->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)->whereIn('type_id',array_keys($this->aTransType))->get($columns))
        {
            if($oLotteryProfit = LotteryProfitSnapshot::getProfitObject($startTime = date('Y-m-d', strtotime($startTime)), $iLotteryId))
            {
                foreach($oTransactions as $oTransaction)
                {
                    $amount = in_array($oTransaction->type_id, $this->cancelTransType)? -$oTransaction->amount: $oTransaction->amount;
                    $oLotteryProfit->updateProfitData($this->aTransType[$oTransaction->type_id], $amount, $oTransaction->is_tester);
                }
                $oLotteryProfit->save();
            }
        }
    }

}
