<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateCommissionReport extends BaseCommand {

	protected $sFileName = 'CommissionReport';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:generate-commission';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'generate  commission';


        /**
         * Get the console command arguments.
         *
         * @return array
         */
        protected function getArguments() {
            return array(
                 array('type', InputArgument::OPTIONAL, null),
                array('date', InputArgument::OPTIONAL, null),
            );
        }

        private $sDate;
        private $sType;
        private $aCommissionSettings;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
         $aType=CommissionSettings::$Commission_type;

         $this->sType = $this->argument('type') ? $this->argument('type') : $aType['deposit'];
        $this->sDate = $this->argument('date') ? $this->argument('date') :  Carbon::yesterday();

         if(!in_array($this->sType, $aType)) return false;

		// 设置日志文件保存位置
		!$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName ."-". $this->sType;
		$this->writeLog('begin generate  '.$this->sType.' commission.'.date("H:i:s"));

        $aCommissionSettings = CommissionSettings::getAllSettingsByTypeId($this->sType);
        foreach($aCommissionSettings as $key=>$aCommissionSetting){
            $aCommissionSettings[$key]['multiple_amount'] = $aCommissionSetting['multiple'] * $aCommissionSetting['amount'];
        }
        $this->aCommissionSettings = array_reverse(array_sort($aCommissionSettings,  function($value) {
            return $value['amount'];
        }));
        $this->_delete_old_data();

        if($this->sType == $aType['deposit'])
            $this->_generate_deposit();
        else
            $this->generate();

        $this->writeLog('end generate  '.$this->sType.' commission.'.date("H:i:s"));
    }
        /**
         * 生成充值佣金
         * @param type $startTime
         * @param type $endTime
         */
        private function _generate_deposit(){
            return;
            $iMinAmount = min(array_column($this->aCommissionSettings, 'amount'));
            $iMinMultipleAmount = end($this->aCommissionSettings)['multiple_amount'];

/*            $oDeposits = Transaction::where('type_id','in',  [TransactionType::TYPE_DEPOSIT, TransactionType::TYPE_DEPOSIT_BY_ADMIN])
                ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($this->sDate)))
                ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($this->sDate)))
                ->groupBy('user_id')->get(['user_id', 'amount','user_forefather_ids']);*/

            $startTime = date('Y-m-d 00:00:00', strtotime($this->sDate));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->sDate));
//
            $sql = "select * from (
                      select  user_id, amount, user_forefather_ids from  transactions
                      where type_id in (1,18)  and created_at >='".$startTime."' and created_at <= '".$endTime."'
                      order by created_at,id asc
                    ) as tmp group by tmp.user_id";

            $oDeposits = DB::select($sql);

            foreach($oDeposits as $oDeposit){
                if($oDeposit->amount >= $iMinAmount && $oDeposit->user_forefather_ids)
                {
                    $oUserProfit = UserProfit::where('user_id', '=', $oDeposit->user_id)
                        ->where('turnover', '>=', $iMinMultipleAmount)
                        ->where('date', '=', $this->sDate)->first();

                    if(!is_object($oUserProfit)) continue;
                     foreach($this->aCommissionSettings as $key=>$aCommissionSetting){

                        if($oDeposit->amount >= $aCommissionSetting['amount'] && $oUserProfit->turnover >= $aCommissionSetting['multiple_amount']){
                            $this->_commissionToForefather($oDeposit->user_forefather_ids, $aCommissionSetting['amount']);
 
 
                            break;
                        }
                    }
                }
            }

//            $oDeposits = Transaction::where('type_id','in',  [TransactionType::TYPE_DEPOSIT, TransactionType::TYPE_DEPOSIT_BY_ADMIN])->where('amount','>=',end($this->aCommissionSettings)['amount'])->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)->orderBy('created_at','asc')->get(['user_id','amount']);
//
//            $aUserDeposits = [];
//            foreach($oDeposits as $deposit){
//                 if(isset($aUserDeposits[$deposit->user_id])) continue;
//                 $aUserDeposits[$deposit->user_id] = $deposit->amount;
//            }
//            foreach($aUserDeposits as $iUserId =>$iAmount){
//                $aPolicy=$this->_getAmountPolicy($iAmount);
//                if(!$aPolicy) continue;
//                $fBetPolicy = $aPolicy['multiple'] * $aPolicy['amount'];
//                $oUserProfit = UserProfitSnapshot::getUserProfitObject($this->sDate, $iUserId);
//                if(!$oUserProfit || !$oUserProfit->turnover < $fBetPolicy) continue;
//                $aCommissionUsers = [];
//                if(strpos(",", $parent_user_str)){
//                    $aParentUsers=  array_reverse(explode(",",$parent_user_str));
//                     $aCommissionUsers[1]=$aParentUsers[0];//0为上级
//                     if(count($aParentUsers)>1)
//                     $aCommissionUsers[2]=$aParentUsers[1];//0为上上级
//                     if(count($aParentUsers)>2)
//                     $aCommissionUsers[3]=$aParentUsers[2];//0为上上上级
//                }else {
//                    $aCommissionUsers[1] = $parent_user_str;
//                }
//                foreach($aCommissionUsers as $level=>$user_id){
//                     $return_money = $aPolicy['return_money_'.$level];
//                     $this->_saveUserCommission($sDate, $user_id, 1,$return_money);
//                }
//            }

        }
        /**
         * 获取投注额达成的指标
         * @param type $amount
         * @param type $iCommissionType
         * @return type
         */

//        private function _getAmountPolicy($amount=0){
//
//               $aCommissionSettings = CommissionSettings::getAllSettingsByTypeId(1);
//               foreach($aCommissionSettings as $aAmount){
//                   if($amount >= $aAmount['amount']){
//                       return $aAmount;
//                       break;
//                   }
//               }
//               return null;
//           }



        /**
         * 生成非充值佣金
         */
        private function generate(){

            $aCommissionType = [ 2 => 'turnover', 3 => 'profit'];
            $iField = $aCommissionType[$this->sType];

            if ($iMinMoney = end($this->aCommissionSettings)['multiple_amount'])
            {
                $oQuery = $this->sType == 3 ? UserProfit::where($iField, '<=', -$iMinMoney) : UserProfit::where($iField, '>=', $iMinMoney);
                $oUserProfits = $oQuery->where('date', '=', date('Y-m-d',strtotime($this->sDate)))->get();
                foreach($oUserProfits as $oUserProfit)
                {
                    if(! $oUserProfit->parent_user_id) continue;
                    if($oUser = User::find($oUserProfit->user_id)){
                        $this->_commissionToForefather($oUser->parent_str, abs($oUserProfit->$iField));
                    }
                }
            }
        }

        /**
         * 给上级返点
         * @param $sForefatherIds
         * @param $amount
         */
        private function _commissionToForefather($sForefatherIds, $amount){

            $aForefatherCommission = [];
            $MoneyKey = $this->sType == 1 ? 'amount' : 'multiple_amount';

            foreach ($this->aCommissionSettings as $aCommissionSetting)
            {
                if($amount >= $aCommissionSetting[$MoneyKey]){
                    $aForefatherCommission = $aCommissionSetting;
                    break;
                }
            }
            $aForefatherId = [];
            if(strpos($sForefatherIds,","))
                $aForefatherId = array_reverse(explode(",",$sForefatherIds));
            else
                $aForefatherId = [$sForefatherIds];
            foreach($aForefatherId as $key=>$iForefatherId){
                    $money_key = $key+1;
                    if(isset($aForefatherCommission['return_money_'.$money_key]) && $aForefatherCommission['return_money_'.$money_key] > 0){
                        $this->_saveUserCommission($iForefatherId, $aForefatherCommission['return_money_'.$money_key]);
                    }
                }
            
        }

        /**
         * 保存返点
         * @param $iUserId
         * @param $amount
         */
        private function _saveUserCommission($iUserId, $amount){
                $oUser = User::find($iUserId);
                if(!$oUser) return;
                if ($oUser->blocked || $oUser->is_tester){
                    //过滤非正常状态用户
                    return;
                }
                $oUserCommission =  UserCommission::where('date','=',$this->sDate)->where('user_id','=',$iUserId)->where('commission_type','=', $this->sType)->first();
                if(!$oUserCommission){
                    $oUserCommission = new UserCommission;
                    $oUserCommission->commission=0;
                }
                if($oUserCommission->status > 0) return;
                $oUserCommission->user_id=$iUserId;
                $oUserCommission->commission_type=$this->sType;
                $oUserCommission->commission += $amount;
                $oUserCommission->date = $this->sDate;
                $oUserCommission->username = $oUser->username;
                $oUserCommission->is_tester = $oUser->is_tester;
                $oUserCommission->status = 0;
                $oUserCommission->save();
        }
        
        private function _delete_old_data(){
            UserCommission::where('commission_type','=',$this->sType)->where('date','=',$this->sDate)->delete();
        }
}
