<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateProfitsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'admin-tool:update-profits-from-other2';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update profits table from profits-slot and profits-sport';



	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$tableClass = array(
							'user_profits' 			=> 'UserProfit',
							'user_profits_slot'		=> 'UserProfitSlot',
							'user_profits_sport'	=> 'UserProfitSport',
							);
		$fields = array('deposit'		=> array(
											array(
												'table'	=>	'user_profits',
												'field'	=>	'Deposit',

												)
											),
						'net_deposit' 	=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Deposit',
												),
											),
						'withdrawal'	=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Withdrawal',
												),
											),
						'net_withdrawal' => array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Withdrawal',
												),
											),
						'turnover'		=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Turnover',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'turnover',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'turnover',
												),
											),
						'net_turnover'	=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Turnover',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'turnover',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'turnover',
												),
											),
						'prize'			=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Prize',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'prize',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'prize',
												),
											),
						'net_prize'		=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Prize',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'prize',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'prize',
												),
											),
						'commission'	=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Commission',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'commission',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'commission',
												),
											),
						'net_commission' => array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Commission',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'commission',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'commission',
												),
											),
						'profit'		=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Profit',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'profit',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'profit',
												),
											),
						'net_profit'	=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Profit',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'profit',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'profit',
												),
											),
						'dividend'		=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Dividend',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'dividend',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'dividend',
												),
											),
						'net_dividend'	=> array(
												array(
													'table'	=>	'user_profits',
													'field'	=>	'Dividend',
												),
												array(
													'table'	=>	'user_profits_slot',
													'field'	=>	'dividend',
												),
												array(
													'table'	=>	'user_profits_sport',
													'field'	=>	'dividend',
												),
											),
						);

		$y = date("Y");
		$m = date("m");
		$d = date("d");
		$todayTime= mktime(0,0,0,$m,$d,$y);
		$nowTime = time();
		$diffTime = 60*10;
		if(($nowTime-$todayTime) < $diffTime)
			$startDate = date('Y-m-d',$todayTime-$diffTime);
		else if(!is_null($this->argument('start_date')))
			$startDate = $this->argument('start_date');
		else {
			$startDate = date('Y-m-d');
		}
		$endDate = $startDate.' 23:59:59';
		$dateRange = array($startDate,$endDate);
		$count=0;
		$this->line($startDate);
		$profitCount = Profit::where('date', $startDate)->get()->count();
		if($profitCount == 0)
		{
			$insertResult = Profit::insert(array('date'=> $startDate));
			if($insertResult){
				$this->line('新建记录');
			}else{
				$this->line('新建记录失败');
				die();
			}

		}
		foreach($fields as $field => $tableData) {
			$fieldSum=0;
			foreach($tableData as $table){

				$fieldSum += $tableClass[$table['table']]::where('is_tester','0')->whereBetween('date',$dateRange)->sum($table['field']);
			}

			if($field == 'profit' || $field == 'net_profit') $fieldSum = 0-$fieldSum;
			$r = Profit::where('date', $startDate)->update(array("{$field}"=>$fieldSum));
				++$count;
				$this->line("$field 已经更新为$fieldSum");
		}

	}
	protected function getArguments() {
		return array(
			array('start_date', InputArgument::OPTIONAL,null),
		);
	}

}
