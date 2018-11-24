<?php
namespace JcCommand;

use JcModel\JcCommission;
use JcModel\ManJcCommission;

class SendCommission extends \BaseTask {
    protected function doCommand() {
        $aProjectIds = $this->data['project_ids'];
        
        $aCommission = ManJcCommission::getUnSendListByProjectIds($aProjectIds);

        if(count($aCommission) <=0) {
            $this->log = 'No waiting commission  project_ids:'.json_encode($aProjectIds);
            return self::TASK_SUCCESS;
        }

        $DB          = \DB::connection();
        foreach($aCommission as $oCommission){
            if($oCommission->amount <= 0){
                $oCommission->setToSent();
                continue;
            }

            $this->log = 'amount:'.$oCommission->amount;

            $oUser = \User::find($oCommission->user_id);
            $oAccount = \Account::lock($oUser->account_id,$iLocker);
            if(empty($oAccount))  {
                $this->log = 'Account lock fail account_id:'.$oCommission->account_id;
                return self::TASK_RESTORE;
            }

            $DB->beginTransaction();

//            $aAccountData[$oCommission->account_id] = $oAccount;
            $oProject = \JcModel\ManJcProject::find($oCommission->project_id);
            $bSucc = $oCommission->setToSent();
            if ($bSucc){
                $bSucc = $oCommission->send($oProject, $oUser, $oAccount);
                if($bSucc && !ManJcCommission::CountUnSentCommission($oCommission->project_id)) {
                    $bSucc = $oProject->setCommissionSent();
                }
            }
            if ($bSucc){
                $this->log = 'Commission success user_id :'.$oCommission->user_id.' account_id:'.$oCommission->account_id.' id:'.$oCommission->id;
                $DB->commit();
            }else{
                $this->log = 'Commission fail user_id :'.$oCommission->user_id.' account_id:'.$oCommission->account_id.' id:'.$oCommission->id;
                $DB->rollback();
            }
            \Account::unLock($oCommission->account_id , $iLocker ,false);
        }
        if ($bSucc) {
            return self::TASK_SUCCESS;
        } else {
            return self::TASK_RESTORE;
        }
    }
}
