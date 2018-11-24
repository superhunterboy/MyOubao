<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateProfitSnapshot extends BaseCommand {

	protected $sFileName = 'profitsnapshot';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:generate-profit-snapshot';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'generate profit snapshot';


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('table', InputArgument::REQUIRED, null),
            array('date', InputArgument::OPTIONAL, null),
        );
    }

    private $maxLimit = 10000;

    private $aTransType = [
            TransactionType::TYPE_DEPOSIT => 'deposit',
            TransactionType::TYPE_DEPOSIT_BY_ADMIN => 'deposit',
            TransactionType::TYPE_WITHDRAW => 'withdrawal',
            TransactionType::TYPE_WITHDRAW_BY_ADMIN => 'withdrawal',
            TransactionType::TYPE_BET => 'turnover',
            TransactionType::TYPE_DROP => 'turnover',
            TransactionType::TYPE_SEND_PRIZE => 'prize',
            TransactionType::TYPE_CANCEL_PRIZE => 'prize',
            TransactionType::TYPE_SEND_COMMISSION => 'commission',
            TransactionType::TYPE_PROMOTIANAL_BONUS => 'dividend',
            TransactionType::TYPE_DEPOSIT_COMMISSION => 'dividend',
            TransactionType::TYPE_TURNOVER_COMMISSION => 'dividend',
            TransactionType::TYPE_PROFIT_COMMISSION => 'dividend',
            TransactionType::TYPE_BET_COMMISSION => 'bet_commission',
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

		$this->writeLog('begin generate profit snapshot.'.date("H:i:s"));

        $date = $this->argument('date') ? $this->argument('date') : date('Ymd');
        $table = $this->argument('table');

        //00:00:00 到 00:10:00 之内，统计前一天数据，避免遗漏
        if((Carbon::now()->__get('timestamp') - Carbon::today()->__get('timestamp')) <= 600){
            $date = Carbon::yesterday();
        }

        $startTime = date('Y-m-d 00:00:00', strtotime($date));
        $endTime = date('Y-m-d 23:59:59', strtotime($date));

        $this->$table($startTime, $endTime);
        $this->writeLog('end generate profit snapshot.'.date("H:i:s"));
    }

    public function user_profits($startTime, $endTime){

        $oQuery = Transaction::where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime);
        $oTransUsers = $oQuery->selectRaw('distinct user_id, user_forefather_ids')->get();

        $aUpdateUserId = [];
        $aUserFatherId = [];
        foreach($oTransUsers as $oTransUser){
            $aUpdateUserId[] = $oTransUser->user_id;
            if($oTransUser->user_forefather_ids){
                $aUserForefatherId = explode(',', $oTransUser->user_forefather_ids);
                $aUpdateUserId = array_merge($aUpdateUserId, $aUserForefatherId);
                $aUserFatherId[$oTransUser->user_id] = $aUserForefatherId[count($aUserForefatherId)-1];
            }
        }
        $aUpdateUserId = array_unique($aUpdateUserId);

        $columns = ['id', 'type_id', 'user_id', 'amount'];

        foreach($aUpdateUserId as $iUserId){
            if($oProfitSnapshot = UserProfitSnapshot::getUserProfitObject($startTime = date('Y-m-d', strtotime($startTime)), $iUserId))
            {
                $startPos = $startPosTeam = 0;

                //修改自己
                do{
                    $oSelfTransactions = Transaction::where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)
                        ->where('user_id', '=', $iUserId)->whereIn('type_id',array_keys($this->aTransType))
                        ->skip($startPos)->take($this->maxLimit)->get($columns);

                    if ($oSelfTransactions->count()>0)
                    {
                        foreach($oSelfTransactions as $oSelfTransaction)
                        {
                            $amount = in_array($oSelfTransaction->type_id, $this->cancelTransType)? -$oSelfTransaction->amount: $oSelfTransaction->amount;
                            $oProfitSnapshot->updateSelfProfit($this->aTransType[$oSelfTransaction->type_id], $amount);
                        }
                    }
                    $startPos += $this->maxLimit;

                }while($oSelfTransactions->count() == $this->maxLimit);

                //修改团队
                do{
                    $oTransactions = Transaction::where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)
                        ->whereRaw(' find_in_set(?, user_forefather_ids)', [$iUserId])
                        ->whereIn('type_id',array_keys($this->aTransType))->skip($startPosTeam)->take($this->maxLimit)->get($columns);

                    if($oTransactions->count() > 0)
                    {
                        foreach($oTransactions as $oTransaction)
                        {
                            $bDirect = false;  //是否为直属代理
                            if(isset($aUserFatherId[$oTransaction->user_id]) && $iUserId == $aUserFatherId[$oTransaction->user_id]) $bDirect = true;

                            $amount = in_array($oTransaction->type_id, $this->cancelTransType)? -$oTransaction->amount: $oTransaction->amount;
                            $oProfitSnapshot->updateProfit($this->aTransType[$oTransaction->type_id], $bDirect, $amount);
                        }
                    }
                    $startPosTeam += $this->maxLimit;

                }while($oTransactions->count() == $this->maxLimit);

                $oProfitSnapshot->saveProfit();
            }
        }
    }

    public function profits($startTime, $endTime){

        $columns = ['id', 'type_id', 'user_id', 'amount', 'is_tester'];
        $oProfitSnapshot = ProfitSnapshot::getProfitObject($startTime = date('Y-m-d', strtotime($startTime)));

        $startPos = 0;
        do{
            $oTransactions = Transaction::where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime)
                ->whereIn('type_id',array_keys($this->aTransType))
                ->skip($startPos)->limit($this->maxLimit)->get($columns);

            if($oTransactions->count() > 0){
                foreach($oTransactions as $oTransaction)
                {
                    if($oTransaction->type_id == TransactionType::TYPE_BET_COMMISSION){
                        $sType = $this->aTransType[TransactionType::TYPE_SEND_COMMISSION];
                    }else{
                        $sType = $this->aTransType[$oTransaction->type_id];
                    }

                    $amount = in_array($oTransaction->type_id, $this->cancelTransType)? -$oTransaction->amount: $oTransaction->amount;
                    $oProfitSnapshot->updateProfitData($sType, $amount, $oTransaction->is_tester);
                }
            }
            $startPos += $this->maxLimit;

        }while($oTransactions->count() == $this->maxLimit);

        $oProfitSnapshot->save();
    }

}
