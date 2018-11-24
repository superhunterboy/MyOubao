<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateUserProfitsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:update-profits-from-user';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update userprofits table from user table';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		//$startDate = date('Y-m-d');
		 $startDate= '2016-01-31';
		$endDate = $startDate.' 23:59:59';
		//update user_profits table`s field : is_tester, is_agent , team_number
		//step 1 update is_tester,is_agent
		$profitsData = UserProfit::whereBetween('date',array($startDate,$endDate))->get(array('user_id'));
		if(isset($profitsData[0])){
			$userids = array();
			foreach($profitsData as $prod){
				$d = $prod->getAttributes();
				$userids[] = $d['user_id'];
			}
			$userField = array('is_tester','is_agent','id','parent_id','parent','username','prize_group');
			$userData = User::wherein('id',$userids)->get($userField);
			$updateCount=0;
			foreach($userData as $key=> $userd){
				//search team_numbers
				$teamNumber = User::whereRaw('find_in_set(?,`forefather_ids`)',array($userd['id']))->get()->count();
				$teamNumber && $teamNumber=0;
				$userUpdateArr = array(
												'is_tester' 	=> $userd['is_tester'],
												'is_agent'  	=> $userd['is_agent'],
												'team_numbers'	=> $teamNumber,
												'prize_group'	=> $userd['prize_group'],
												'username'		=> $userd['username'],
												'parent_user_id'=> $userd['parent_id'],
												'parent_user'	=> $userd['parent'],
												);

				$r = UserProfit::where('user_id', $userd['id'])->update($userUpdateArr);
				if($r)
					++$updateCount;
			}
			$this->line("$updateCount 条数据已经更新");
		}else{
			$this->line('profits have no data');
		}



	}



}
