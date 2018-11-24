<?php

class GenerateAccountSnapshot extends BaseCommand {

	protected $sFileName = 'accountsnapshot';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:generate-account-snapshot';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'generate account snapshot';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// 设置日志文件保存位置
		!$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
		$this->writeLog('begin generate accounts snapshot.');
		$now = Carbon::now();
		$date = $now->toDateString();
		$nowTime = $now->toDateTimeString();

		//账户信息
		$aColumns = ['id', 'username', 'blocked', 'account_id', 'is_tester'];

		$aAgents = User::where('is_agent', '=', User::TYPE_AGENT)->whereNull('parent_id')->get($aColumns);

		$ssDatas = [];

		foreach ($aAgents as $sAgent) {

			$ssInfo = ['user_id' => $sAgent->id, 'date' => $nowTime, 'username'=>$sAgent->username, 'blocked' => $sAgent->blocked, 'is_tester' => $sAgent->is_tester ];

			//团队统计
			$frozen = $balance = $available = $withdrawable = $team_prize = $team_turnover = $team_commission = $team_deposit = $team_profit = $team_withdrawal = number_format(0, 6);

			$aUsers = $sAgent->getUsersBelongsToAgent()->toArray();

			$userIds = array_column($aUsers, 'id');

			//团队信息
			//  'team turnover' => '团队销量',
			//'team_prize' => '团队奖金',
			//'team_deposit' => '团队充值',
			//  'team_profit' => '团队盈利',
			//  'team_withdrawal' => '团队提现',
			//'team_commission' => '团队佣金', //返点

			if($userIds)
			{
				//总提现,团队提现
				$withdrawals = Withdrawal::doWhere(['status'=> ['=', Withdrawal::WITHDRAWAL_STATUS_SUCCESS ], 'transaction_amount' => ['>', 0], 'created_at'=>[ 'between', [$date, $nowTime] ] ])->whereIn('user_id', $userIds)->get(['transaction_amount'])->toArray();
				if(!empty($withdrawals)) $team_withdrawal = array_sum(array_column($withdrawals, 'transaction_amount'));

				//团队返点, 团队佣金
				$commissions = Commission::doWhere(['amount' => ['>', 0], 'created_at'=>[ 'between', [$date, $nowTime] ]])->whereIn('user_id', $userIds)->get(['amount'])->toArray();
				if(!empty($commissions)) $team_commission = array_sum(array_column($commissions, 'amount'));

				//团队奖金
				$prizes = Project::doWhere(['prize' => ['>', 0], 'created_at'=>[ 'between', [$date, $nowTime] ]])->whereIn('user_id', $userIds)->get(['prize'])->toArray();
				if(!empty($prizes)) $team_prize = array_sum(array_column($prizes, 'prize'));

				//团队充值
				$deposits = Deposit::doWhere(['real_amount' => ['>', 0], 'created_at'=>[ 'between', [$date, $nowTime] ]])->whereIn('user_id', $userIds)->get(['real_amount'])->toArray();
				if(!empty($deposits)) $team_deposit = array_sum(array_column($deposits, 'real_amount'));

				//团队销量
				$turnovers = UserTurnover::doWhere(['turnover' => ['>', 0], 'created_at'=>[ 'between', [$date, $nowTime] ]])->whereIn('user_id', $userIds)->get(['turnover'])->toArray();
				if(!empty($turnovers)) $team_turnover = array_sum(array_column($turnovers, 'turnover'));

				//团队盈利
				$team_profit = $team_commission + $team_prize - $team_turnover;
			}

			$ssInfo = array_merge($ssInfo, ['team_prize' => $team_prize, 'team_turnover' => $team_turnover, 'team_commission' => $team_commission, 'team_deposit' => $team_deposit, 'team_profit' => $team_profit, 'team_withdrawal' => $team_withdrawal ]);

			$datas = $sAgent->hasManyThrough(Account::$resourceName, User::$resourceName, 'parent_id', 'user_id')->get(['user_id', 'available', 'withdrawable', 'frozen', 'balance'])->toArray();

			if(!empty($datas))
			{
				$frozen = array_sum(array_column($datas, 'frozen'));
				$balance = array_sum(array_column($datas, 'balance')); //余额
				$available = array_sum(array_column($datas, 'available'));
				$withdrawable = array_sum(array_column($datas, 'withdrawable')); //可提现余额
			}

			$ssInfo = array_merge($ssInfo, ['frozen' => $frozen, 'balance' => $balance, 'available' => $available, 'withdrawable' => $withdrawable]);

			$ssDatas[] = $ssInfo;
		}

/*		$aAccounts =Account::all(['user_id', 'username', 'available'])->toArray();
		//$aAccounts =Account::query()->skip(5)->take(2)->get(['user_id', 'username', 'available'])->toArray();

		if(!$aAccounts)
			$this->writeLog('get accounts snapshot failed');
		$create_at = Carbon::now()->toDateTimeString();

		array_walk($aAccounts, function(&$value, $key, $return) {
			$value = array_merge($value, $return);
		}, ['created_at'=>$create_at]);*/

		if(! AccountSnapshot::insert($ssDatas))
			$this->writeLog('save accounts snapshot failed');

		$this->writeLog('save accounts snapshot end');

/*		foreach($aAccounts as $oAccounts){
			AccountSnapshot::create($oAccounts);
		}*/

	}


}
