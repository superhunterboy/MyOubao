<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

// 更新奖期数据,更新与奖期关联的销售额
class GenerateIssueSnapshot extends BaseCommand {

	protected $sFileName = 'issuesnapshot';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:generate-issue-snapshot';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'generate issue snapshot';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('issues', InputArgument::REQUIRED, null),
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
 
		$this->writeLog('begin generate issue snapshot.'.date("H:i:s"));

        $iLotteryId = $this->argument('lottery_id');
        $sIssue = $this->argument('issues');

        $columns = ['id', 'type_id', 'user_id', 'amount', 'is_tester'];

        $aIssueArea = explode('-', $sIssue);
        if(count($aIssueArea) == 2) $iIssues = range($aIssueArea[0], $aIssueArea[1]);

        foreach($iIssues as $iIssue)
        {
            if($oTransactions = Transaction::where('lottery_id', '=', $iLotteryId)->where('issue', '=', $iIssue)->whereIn('type_id',array_keys($this->aTransType))->get($columns))
            {
                //更新与奖期关联的销售额
                if($oIssueProfit = IssueProfitSnapshot::getProfitObject($iLotteryId, $iIssue))
                {
                    foreach($oTransactions as $oTransaction)
                    {
                        $amount = in_array($oTransaction->type_id, $this->cancelTransType)? -$oTransaction->amount: $oTransaction->amount;
                        $oIssueProfit->updateProfitData($this->aTransType[$oTransaction->type_id], $amount, $oTransaction->is_tester);
                    }
                    $oIssueProfit->save();
                }else{
                    echo 'no issue: '.$iIssue."\r\n";
                }

                //更新与奖期关联的销售额
                $aUserIds = array_unique(array_column($oTransactions->toArray(), 'user_id'));

                foreach($aUserIds as $iUserId)
                {
                    if($oTurnoverProfit = UserTurnoverSnapshot::getUserTurnverObject($iLotteryId, $iIssue, $oTransaction->user_id))
                    {
                        foreach($oTransactions as $oTransaction)
                        {
                            if($oTransaction->user_id != $iUserId || $this->aTransType[$oTransaction->type_id] != 'turnover') continue;

                            $amount = in_array($oTransaction->type_id, $this->cancelTransType)? -$oTransaction->amount: $oTransaction->amount;

                            $oTurnoverProfit->updateTurnoverData($amount);
                        }
                        $oTurnoverProfit->save();
                    }
                }
            }
        }

        $this->writeLog('end generate issue snapshot.'.date("H:i:s"));
    }

}
