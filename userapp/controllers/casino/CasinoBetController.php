<?php

/**
 * 投注
 */
class CasinoBetController extends UserBaseController {

    protected $errorFiles = [
        'casino',
        'system',
        'bet',
        'fund',
        'account',
        'seriesway',

    ];
    protected $resourceView = 'centerUser.bet';
    protected $customViewPath = 'centerGame';
    protected $modelName = 'UserProject';
    protected $customViews = [
        'bet',
        'uploadBetNumber',
    ];
    protected $accountLocker = null;
    protected $dbThreadId = null;

    /**
     * 投注方法 [ Refactor ]
     * @param int $iLotteryId
     * @return mixed
     */
    public function bet($iLotteryId,$tableId) {

        //后台是否禁止投注
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }

        if ($oUser->blocked == UserUser::BLOCK_BUY) {
            return $this->goBack('error', __('_user.bet-not-allowed'));
        }

        $oLottery = CasinoLottery::find($iLotteryId);
        if (empty($oLottery)) {
            return $this->goBackToIndex('error', __('_casino.missing', $this->langVars));
        }
        $oTable = BlackJackTable::where('id',$tableId)->where('lottery_id',$iLotteryId)->get()->first();
        if (empty($oTable)) {
            return $this->goBackToIndex('error', __('_casino.untable', $this->langVars));
        }
        if(!$oTable->open){
            return $this->goBackToIndex('error', __('_casino.table_close', $this->langVars));

        }
        $bPost = Request::method() == 'POST';

        if (!$oLottery->open && !Session::get('is_tester')) {
            $this->halt(false, 'casino-closed', CasinoLottery::ERRNO_LOTTERY_CLOSED);
            return $this->goBackToIndex('error', __('_casino.close', $this->langVars));
        }

        if ($bPost) {
            $this->doBet($oLottery,$oTable);
            exit;
        } else {
            return $this->betForm($oLottery,$oTable);
        }
    }



    /**
     * display bet form
     * @param Lottery $oLottery
     */
    private function betForm($oLottery,$oTable) {



        $aGameConfig = & $this->_getGameSettings($oLottery,$oTable);


        $sLotteryConfig = json_encode($aGameConfig);
        $iLotteryId = $oLottery->id;
        $sLotteryCode = ($oLottery->identifier);
        $sLotteryName = ($oLottery->name);



        $WEB_SOCKET_SERVER = SysConfig::readValue('WEB_SOCKET_SERVER');
        $ways_note_url = Config::get('ways_note_url.'.$oLottery->series_id);
        $this->setVars(compact('sLotteryConfig', 'iLotteryId', 'sLotteryName', 'sLotteryCode','WEB_SOCKET_SERVER','ways_note_url'));
        $this->setVars(compact('sLotteryConfig', 'iLotteryId', 'sLotteryName', 'sLotteryCode'));
        $this->view = $this->customViewPath . '.' . strtolower($oLottery->identifier);

        return $this->render();
    }



    /**
     * 获得游戏设置
     * @param Lottery $oLottery
     * @return array
     */
    private function & _getGameSettings($oLottery,$oTable) {

        $iUserId = Session::get('user_id');
       // $aWayGroups = & User::getWaySettings($iUserId, $oLottery, true);

        $casinoWays = BlackJackWay::getWays($oLottery->id);
        if (!$casinoWays) {
            return $casinoWays;
//            $this->halt(false,'no-right',UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
        }
        $bjWays=array();
        foreach($casinoWays as $w)
        {
            $way['id'] = $w->id;
            $way['name'] = $w->name;
            $bjWays[] = $way;
        }



        $user_prize_group = Session::get('user_prize_group');
        $bet_max_prize_group = Session::get('user_prize_group') >  1950 ?  1950 : Session::get('user_prize_group');


        $aGameInfo = [
            'user_prize_group' => $user_prize_group,
            'bet_max_prize_group'=> $bet_max_prize_group,
            'gameId' => $oLottery->id,
            'gameName_en' => $oLottery->identifier,
            'gameName_cn' => $oLottery->name,

            'gameMethods' => $bjWays,
            //游戏注单提交地址
            'submitUrl' => URL::route('casino.bet', ['lottery_id' => $oLottery->id,'table_id'=>$oTable->id]),
            'loaddataUrl' => URL::route('bets.load-data', ['lottery_id' => $oLottery->id]),
            'rechargeUrl' => URL::route('user-recharges.netbank'),
            'pollUserAccountUrl' => route('users.user-account-info'),
            'currentTime' => time(),
            '_token' => Session::get('_token'),
            'is_agent' => Session::get('is_agent'),
            'is_encode'=>0,
            'env' => Config::get('var.environment'),
            'table' => $oTable->id,
            //最大追号期数
        ];

        return $aGameInfo;
    }



    /**
     * Bet
     * @param Lottery $oLottery
     */
    private function doBet($oLottery,$oTable) {


        $iUserId = Session::get('user_id');


        $this->writeLog('start do bet');


        $aBetData = Input::all();
        $env = Config::get('var.environment');
        if($env == 'develop'){
            if(isset($aBetData['is_encode']) && $aBetData['is_encode']==1){
                $aBetData['betdata'] = Encrypt::blackjack_decode($aBetData['betdata']);
            }
        }




        $aBetData = json_decode($aBetData['betdata'],true);
        $stageBetData = $aBetData['stage'];
        $aBetData=[
            'wayId'=>$aBetData['wayId'],
            'tableId'=>$oTable->id,
            'lotteryId'=>$oLottery->id,
            'stage' => $stageBetData,
        ];

        $this->writeLog(var_export($aBetData, TRUE));

        $oUser = User::find($iUserId);
        $blackjackWay = BlackJackWay::find($aBetData['wayId']);
        if (!$blackjackWay) {
            $this->output(false,CasinoLottery::ERRNO_LOTTERY_NOWAY);
            exit;
        }

        $oAccount = Account::lock($oUser->account_id, $this->accountLocker);


        if (empty($oAccount)) {
            $this->writeLog('lock-fail');
            $this->halt(false, 'netAbnormal', Account::ERRNO_LOCK_FAILED);
        }

        $oCasino = CasinoBasic::getInstance($oLottery,$oTable,$aBetData);
        DB::connection()->beginTransaction();
        try{
            $iReturn = CasinoBasic::doWays($oCasino,$blackjackWay);
        }catch(Exception $e){

//            pr($e->getMessage());

            $iReturn = BlackJack::BLACKJACK_SYS_CONDITION;
        }

        if($iReturn == BlackJack::BLACKJACK_BET_SUCCESS){
            DB::connection()->commit();
            if(isset($oCasino->gameInfo['gameInfo'])){
                unset($oCasino->gameInfo['gameInfo']);
            }
            $data = $oCasino->gameInfo;
            $data['account']=$iAvailable = Account::getAvaliable(Session::get('user_id'));
            $this->output(true,$iReturn,$data);
        }else{
            DB::connection()->rollback();
            $data=array();
            $data['account']=$iAvailable = Account::getAvaliable(Session::get('user_id'));
            $this->output(false,$iReturn);

        }


    }
    function output($iResult,$msgNo,$data=array()){
        foreach ($this->errorFiles as $sFile) {
            $sSet = 'errorcode/error-' . $sFile;
            $a = Config::get($sSet);
            if (empty($this->errors)) {
                $this->errors = $a;
            } else {
                foreach ($a as $iCode => $sKey) {
                    $this->errors[$iCode] = $sKey;
                }
//                $this->errors = array_merge($this->errors,$a);
            }
        }
        if($iResult)
            $return =array('iSuccess'=>1);
        else{
            $return =array('iSuccess'=>0);
            $return['errcode'] = $msgNo;
        }
        if(isset($this->errors[$msgNo])){
            $return['msg']=__($this->errors[$msgNo]);
        }

        $return['data']=$data;
        echo json_encode($return);
    }





    /**
     * 析构
     * 1 自动解锁
     * 2 自动删除交易线程
     */
    function __destruct() {
        empty($this->accountLocker) or Account::unLock(Session::get('account_id'), $this->accountLocker, false);
        empty($this->dbThreadId) or BetThread::deleteThread($this->dbThreadId);
        parent::__destruct();
    }


}
