<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 天天返利
 */
class ActivityRebateEveryDay extends BaseCommand
{

    protected $sFileName = 'activityrebateeveryday';

    /**
     * The activity cash back command name.
     *
     * @var string
     */
    protected $name = 'firecat:activity-rebate-every-day';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'activity rebate every day';
    public $writeTxtLog = true;
    //默认手动充值状态
    const DEFAULT_MANUAL_DEPOSIT_STATUS=0;
    //系统用户id
    const ADMIN_USER_ID=1;
    public function fire(){
//日志目录
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
//前一天
        $today =date("Y-m-d");
        $yestoday=date('Y-m-d', strtotime($today . '-1 days'));
//        $yestoday='2014-12-06';
//系统用户
        if(!$admin_user=AdminUser::find(self::ADMIN_USER_ID)){
            $this->writeLog('Admin User is not exist, id='.self::ADMIN_USER_ID);
            exit;
        }
//活动类型
       if(!$transactionTypes=TransactionType::getTransactionTypeByTypeId(TransactionType::TYPE_PROMOTIANAL_BONUS)){
           $this->writeLog('Transaction Type is not exist, type_id='.TransactionType::TYPE_PROMOTIANAL_BONUS);
           exit;
       }
//获取有效投注额
        $userProfitColumns=['user_id','username','direct_turnover','is_tester','date'];
        $profits = UserProfit::where('date','=',$yestoday)->get($userProfitColumns);
        //pr($profits->toArray());exit;
        foreach($profits as $profit){
            $a=new ActivityRebateSetting();
            if($rebate =$a->getUserRebate($profit->direct_turnover)){
                //充值
                $ManualDeposit=new ManualDeposit();
                $ManualDeposit->user_id=$profit->user_id;
                $ManualDeposit->is_tester=$profit->is_tester;
                $ManualDeposit->username=$profit->username;
                $ManualDeposit->amount_add_coin=$rebate;
                $ManualDeposit->transaction_type_id=$transactionTypes->id;
                $ManualDeposit->note=$transactionTypes->cn_title;
                $ManualDeposit->transaction_description=$transactionTypes->cn_title;
                $ManualDeposit->administrator=$admin_user->username;
                $ManualDeposit->admin_user_id=$admin_user->id;
                $ManualDeposit->status=self::DEFAULT_MANUAL_DEPOSIT_STATUS;
                if(!$ManualDeposit->save()){
                    $this->writeLog('Manual Deposit save fail');
                    exit;
                }
                unset($ManualDeposit);
            }
        }
exit;
    }
}