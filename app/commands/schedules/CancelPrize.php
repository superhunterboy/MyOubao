<?php

/**
 * 撤销指定奖期内的所有派奖，删除原中奖详情，并将所有有效注单状态恢复为未计奖状态
 */
class CancelPrize extends MoneyBaseTask {

    protected $Issue;
    protected $pageSize = 100;
    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];

//    protected $prefix = 'can';

    protected function doCommand() {
        // TODO: 尚未完成
//        exit;
        $oLottery = Lottery::find($this->data['lottery_id']);
        if (!$oLottery->exists) {
            $this->log = 'Missing Lottery';
            return self::TASK_SUCCESS;
        }
        $oIssue = ManIssue::getIssue($this->data['lottery_id'], $this->data['issue']);
        if (!$oIssue->exists) {
            $this->log = 'Missing Issue';
            return self::TASK_SUCCESS;
        }

        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_CANCELED) {
            $this->log = 'Wrong WINNING NUMBER Status';
            return self::TASK_SUCCESS;
        }

        if ($oIssue->status_prize != ManIssue::PRIZE_FINISHED) {
            $this->log = 'Wrong PRIZE SENT Status';
            return self::TASK_RESTORE;
        }

        $i = 0;
        $aFailedProjects = [];
        $DB = DB::connection();
        $oMessage = new Message($this->errorFiles);

        do {
            $oTransactions = Transaction::getTransactions(TransactionType::TYPE_SEND_PRIZE, $this->data['lottery_id'], $this->data['issue'], null, $this->pageSize * $i++, $this->pageSize);
            foreach ($oTransactions as $oTransaction) {
//                if ($oProject->status != ManProject::STATUS_WON){
//                    continue;
//                }
                $oAccount = Account::lock($oTransaction->account_id, $iLocker);
                if (empty($oAccount)) {
                    $aFailedTransactions[$oTransaction->id] = $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED);
                    continue;
                }
//                $oProject->setAccount($oAccount);
                $DB->beginTransaction();
                if (($iReturn = $oTransaction->reverse($oAccount)) != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                    $DB->rollback();
                    $aFailedProjects[$oProject->id] = $oMessage->getResponseMsg($iReturn);
                } else {
                    if ($bSucc = PrjPrizeSet::cancelOfProject($oTransaction->project_id)) {
                        $oProject = ManProject::find($oTransaction->project_id);
                        if (!$bSucc = $oProject->reset()) {
                            $aFailedTransactions[$oTransaction->id] = 'Project Reset Failed';
                        }
                    } else {
                        $aFailedTransactions[$oTransaction->id] = 'Cancel Prize Details Failed';
                    }
                    if ($bSucc) {
                        $DB->commit();
                        $this->addProfitTask('prize', date('Y-m-d'), $oTransaction->user_id, -$oTransaction->amount, $oTransaction->lottery_id, $oTransaction->issue);
                    } else {
                        $DB->rollback();
                    }
                }
                Account::unLock($oTransaction->account_id, $iLocker, false);
            }
        } while ($oTransactions->count());
        $this->log = ($bSucc = empty($aFailedTransactions)) ? 'Successful' : 'Failed Transactions:' . var_export($aFailedTransactions, true);
        if ($bSucc) {
            $DB->beginTransaction();
            $bSucc = $oIssue->reset();
            $oCodeCenter = CodeCenter::getCodeCenterByKey($this->data['customer_key']);
            !$bSucc or $bSucc = $oIssue->setWinningNumber($this->data['new_code'], $oCodeCenter);
            if ($bSucc) {
                $DB->commit();
                $oIssue->setCalculateTask();
//                $oIssue->updateWnNumberCache();
            } else {
                $DB->rollback();
            }
        }
           if($this->data['lottery_id'] >= 44 ) $this->pushJob('Push2ClientService', ['iLotteryId'=>$this->data['lottery_id']], Config::get('schedule.push2clientserver'));
          
        return $bSucc ? self::TASK_SUCCESS : self::TASK_KEEP;
    }

    protected function checkData() {
        return $this->data['lottery_id'] > 0 && $this->data['issue'];
    }

}
