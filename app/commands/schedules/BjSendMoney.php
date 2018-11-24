<?php
/**
 * 计奖
 *
 * */
class BjSendMoney extends BaseTask {

    protected $Issue;
    protected $winningNumbers = [];
    protected $hasWatingTraceDetail;
    protected $betQueueIsEmpty;

    protected function doCommand() {
        extract($this->data, EXTR_PREFIX_ALL, 'cal');
        $parent_project_id = isset($this->data['parent_project_id'])?$this->data['parent_project_id']:NULL;
        $DB = DB::connection();
        $oManProject = BlackJackProject::where('id',$parent_project_id)->where('status',BlackJackProject::STATUS_NORMAL)->get();
        if(!$oManProject = $oManProject->first()){
            $this->log = ' ManProject Missing, Exiting';
            return self::TASK_SUCCESS;
        }
        $iSuccCount=0;
        $iFailCount=0;
        $projects = BlackJackProjectDetail::where('parent_project_id',$parent_project_id)
                                            ->where('status',BlackJackProjectDetail::STATUS_NORMAL)
                                            ->where('status_prize',BlackJackProjectDetail::PRIZE_STATUS_WAITING)
                                            ->get();
        if(count($projects)<1){
            $this->log = 'No Projects, Exiting ';
            return self::TASK_SUCCESS;
        }
        BlackJackProjectDetail::where('parent_project_id',$parent_project_id)
                                ->where('status',BlackJackProjectDetail::STATUS_NORMAL)
                                ->where('status_prize',BlackJackProjectDetail::PRIZE_STATUS_WAITING)
                                ->update(array('status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_SENDING));
        $iTotalCount = count($projects);
        $totalAmount = 0;
        $totalPrize = 0;
        $jacpot_id = NULL;
        foreach($projects as $pro){
            if (($iReturn = $this->sendPrize($pro,$DB,$oManProject)) > 0){
                $totalAmount += $pro->amount;
                $totalPrize += $pro->prize;
                $iSuccCount++;
            }
            else{
                $iFailCount++;
                $aErrnos[ $pro->id ] = $iReturn;
            }

            is_null($jacpot_id) && $jacpot_id = CasinoTable::find($pro->table_id)->jacpot_id;
        }

        $this->log = " $iSuccCount sent, $iFailCount Didn't Sent, ";
        if ($iTotalCount == $iSuccCount){
            $this->log .= 'Finished Exiting ';
            $notFinishedCount = BlackJackProjectDetail::checkeAllProjectFinished($oManProject->id);
            if($notFinishedCount == 0){
                $autoFinished = false;
                if(isset($this->data['autoCalculate']) && $this->data['autoCalculate']==1){
                    $autoFinished = true;
                }
                $iResult = $oManProject->setFinished(BlackJackProject::STATUS_FINISHE,$autoFinished);
                if($iResult){
                    $DB->beginTransaction();
                    $oLottery = CasinoLottery::find($oManProject->lottery_id);
                    $oTable = BlackJackTable::find($oManProject->table_id);
                    $oUser = UserUser::find($oManProject->user_id);
                    $oBlackStage = new BlackJackStage($oLottery,$oTable,$oUser);

                    $gameInfo = $oBlackStage->getStage();
                    Session::set('user_id',$oManProject->user_id);
                    $iResult = BlackJackJacpot::setJacpotWhenFinished($parent_project_id,$gameInfo['gameInfo']);
                    $oBlackStage->delStage();
                    if($iResult === false) {
                        $DB->rollback();
                        return self::TASK_KEEP;
                    }
                    else{
                        $DB->commit();
                        return self::TASK_SUCCESS;
                    }

                }else{
                    return self::TASK_KEEP;
                }
            }
            return self::TASK_SUCCESS;
        }
        else{

            $this->log .= $iTotalCount - $iSuccCount . ' Remaining, Exiting';
            if ($iFailCount){
                $errorFiles = ['system','schedule','fund','account','lottery','issue'];
                $oMessage   = new Message($errorFiles);
                $this->log .= " Failed Projects: ";
                $aErrorMsgs = [];
                foreach ($aErrnos as $iProjectId => $iErrno){
                    $sMsg         = $oMessage->getResponseMsg($iErrno);
                    $aErrorMsgs[] = "$iProjectId $sMsg";
                }
                $this->log .= implode("\n",$aErrorMsgs);
                $this->log .= ' Exiting';
                unset($aErrorMsgs);
            }
            return self::TASK_RESTORE;
        }


    }

    private function sendPrize(&$pro,$DB,$oManProject){

        $oCasinoWay = BlackJackWay::find($pro->way_id);
        if($pro->way_id == BlackJackWay::BLACKJACK_WAY_BET){
            $gameInfo = json_decode($oManProject->game_info,true);
            if($gameInfo['player'][$pro->stage_id]['split_table_id']!=0){
                $oCasinoWay = BlackJackWay::find(BlackJackWay::BLACKJACK_WAY_SPLIT);
            }
        }
        $prize = $oCasinoWay->{'prize'.$oCasinoWay->wn_function}($pro->player_number,$pro->amount,$oManProject->banker_number);

        $oAccount = Account::lock($pro->account_id, $iLocker);
        if (empty($oAccount)) {
            return Account::ERRNO_LOCK_FAILED;
        }
        $DB->beginTransaction();
        $iReturn = Transaction::ERRNO_CREATE_SUCCESSFUL;
        if($prize>0) {

            $oUser = User::find($pro->user_id);
            $pro->setUser($oUser);
            $pro->setAccount($oAccount);


            $aExtraData = $pro->getAttributes();
            $aExtraData['project_id'] = $pro->id;
            $aExtraData['project_no'] = $pro->serial_number;
            $aExtraData['coefficient'] = 1.00;
            $aExtraData['issue'] = 0;

            unset($aExtraData['id']);
            $iReturn = Transaction::addTransaction($pro->User, $pro->Account, TransactionType::TYPE_SEND_PRIZE, $prize, $aExtraData);
        }
        if($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL){
            if($prize != 0){
                $update = array(
                    'prize'=>$prize,
                    'status'=>BlackJackProjectDetail::STATUS_WON,
                    'status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_SENT,
                );
            }else{
                $update = array(
                    'prize'=>$prize,
                    'status'=>BlackJackProjectDetail::STATUS_LOST,
                    'status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_SENT,
                );
            }

            $iReturn = $pro->where('id',$pro->id)->where('status_prize',BlackJackProjectDetail::PRIZE_STATUS_SENDING)->where('status',BlackJackProjectDetail::STATUS_NORMAL)->update($update);

            if ($iReturn == true){
                $DB->commit();
            }
            else{
                $DB->rollback();
            }
        }
        Account::unLock($pro->account_id,$iLocker,false);
        return $iReturn;
    }

}
