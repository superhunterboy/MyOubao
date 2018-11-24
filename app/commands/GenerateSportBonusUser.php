<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateSportBonusUser extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'jc:generate-sport-bonus-user';
	protected $sFileName = 'generate-sport-bonus-user';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '过滤竞彩礼金用户';

	public function __construct()
	{
		parent::__construct();

		$this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
	}
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$aParentUsername = Voucher::VIRTUAL_SPORT_AGENT;
		$oParentUser = User::getUsersByUsernames([$aParentUsername])->first();
		$aParentUserId=$oParentUser->id;
		$aBonusMaxUserId = CashBonus::getLastRegisterUser();
		if(!$aBonusMaxUserId->maxuserid){
			$aBonusMaxUserId->maxuserid = -1;
		}

		$aUsers = User::where('parent_id', $aParentUserId)
				->where('id','>',$aBonusMaxUserId->maxuserid)
				->whereNotNull('phone')
				->whereNotNull('register_ip')
				->get(['id','username','phone','register_ip']);
		if(!$aUsers->count()){
			$this->writeLog('not have new register user');
			$this->line('not have new register user');
			return ;
		}
		foreach($aUsers as $user){
			$oBonus = new CashBonus();
			$oBonus->user_id = $user->id;
			$oBonus->username = $user->username;
			$oBonus->phone = $user->phone;
			$oBonus->register_ip=$user->register_ip;
			$oBonus->status_register = CashBonus::STATUS_WAITING_AUDIT;
			$oBonus->status_deposit = CashBonusDeposit::STATUS_DEPOSIT_WAITING_AUDIT;
			$oBonus->updated_at = date('Y-m-d H:i:s');
			$oBonus->created_at = date('Y-m-d H:i:s');
			if($oBonus->save()) {
				$this->line($user->username . ' success');
			}else{
				$this->writeLog('user insert error:'.$user->id.' '.$user->username);
				$this->line($user->username . ' error');
			}
		}

		$oBonus = new CashBonus();
		$condition = [];
		$condition['status_register']=CashBonus::STATUS_AUDIT_FINISH;
		$aBonusAll = $oBonus->getBonusByCondition($condition);
		foreach($aBonusAll as $aBonus){
			$log = '';
			$ids = $oBonus->getRepeatBonus($aBonus->user_name,$aBonus->phone,$aBonus->register_ip,$aBonus->id);
			if(!empty($ids)){
				$update = array('status_register'=>CashBonus::STATUS_SYS_DEL);
				if($oBonus->updateByIds($ids,$update)){
					$log = "delete $aBonus->user_name,$aBonus->phone,$aBonus->register_ip repeate";
				}else{
					$log = " can not delete $aBonus->user_name,$aBonus->phone,$aBonus->register_ip repeate";
				}
			}
			if(!empty($log)){
				$this->writeLog($log);
			}
		}
		$this->writeLog("--------------------------------------------------------------------------");
	}



}
