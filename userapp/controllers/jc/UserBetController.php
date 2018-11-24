<?php

namespace JcController;

use JcModel\JcProject;
use JcModel\JcUserMatchesInfo;

class UserBetController extends \UserBaseController {
    
    protected $errorFiles = [
        'jc',
        'system',
        'fund',
        'account',
    ];
//    
//    protected $resourceView = 'jc';
    protected $customViewPath = 'jc';
    protected $modelName = '\JcModel\JcUserMatchesInfo';
//    protected $customViews = [
//        'football',
//        'bet',
//    ];
    protected $accountLocker = null;
    protected $dbThreadId = null;
    
    protected $JcLottery = null;
    
    protected function beforeRender() {
        parent::beforeRender();
        
        if (isset($this->viewVars['oJcLottery'])){
            $oJcLottery = $this->viewVars['oJcLottery'];
            $this->viewVars['aMenuList'] = \JcModel\JcMethodGroup::getMenuByLotteryId($oJcLottery->id);
        }
        if (!isset($this->viewVars['sTabKey'])){
            $this->viewVars['sTabKey'] = 'hunhe';
        }
        
        if (empty($this->view)){
            $this->view = $this->customViewPath . '.' . $this->action;
        }
    }

    public function index(){
        $this->match_list();
    }
    
    public function match_list($sLotteryKey, $sTabKey = null){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $iLotteryId = $oJcLottery->id;
        $oFilterMethodGroupKey = 'hunhe';
        if (isset($sTabKey) && $sTabKey != 'single'){
            //单关特别处理
            $oFilterMethodGroup = \JcModel\JcMethodGroup::getMenuByIdentifier($iLotteryId, $sTabKey);
            if (empty($oFilterMethodGroup)){
                \App::abort(404);
            }
            $oFilterMethodGroupKey = $oFilterMethodGroup->identifier;
        }
        $iGroupId = \Input::get('bind_group_id');
        if (isset($iGroupId) && $iGroupId !== ''){
            $oGroupBuy = \JcModel\JcGroupBuy::find($iGroupId);
            if (empty($oGroupBuy)){
                \App::abort(404);
            }
        }
        
//        if (\Input::get('test')){
//            return $this->football_test();
//        }
        $dBetDate = strtotime(\Input::get('betDate')) ? \Input::get('betDate') : null;
        $aMatchList = \JcModel\JcUserMatchesInfo::getMatchByDate($dBetDate);
        
        $iCountEndMatch = 0;
        
        $aLeagueList = [];

        $datas = [];
        $aAllMatches = [];
        $aWayList = \JcModel\JcWay::getWayByLotteryId($iLotteryId);
        
        if (count($aMatchList) > 0){
//            $this->halt(false, 'jc-error-selling-match-is-empty', \JcModel\JcUserMatchesInfo::ERRNO_SELLING_MATCH_IS_EMPTY);
            $aLeagueIds = [];
            $aTeamIds = [];

            foreach($aMatchList as $oMatch){
                $aMatchData = $oMatch->getAttributes();
                $aLeagueIds[$aMatchData['league_id']] = $aMatchData['league_id'];
                $aTeamIds[$aMatchData['home_id']] = $aMatchData['home_id'];
                $aTeamIds[$aMatchData['away_id']] = $aMatchData['away_id'];
                $aMatchIds[$aMatchData['match_id']] = $aMatchData['match_id'];
            }

            $aMatchMethods = [];
            $oMatchMethods = \JcModel\JcMatchMethod::getByMatchIds($aMatchIds);
            foreach($oMatchMethods as $oMatchMethod){
                $aMatchMethods[$oMatchMethod->match_id][$oMatchMethod->method_id] = $oMatchMethod;
            }

            $oLeagues = \JcModel\JcLeague::getByIds($aLeagueIds);
            foreach($oLeagues as $oLeague){
                $aLeagueList[$oLeague->id] = $oLeague;
            }
            $oTeams = \JcModel\JcTeam::getByIds($aTeamIds);
            $aTeamList = [];
            foreach($oTeams as $oTeam){
                $aTeamList[$oTeam->id] = $oTeam;
            }
            $aMethodList = \JcModel\JcMethod::getAllByLotteryId($iLotteryId);
            $aOddsList = \JcModel\JcOdds::getOddsDataByMatchIds($iLotteryId, $aMatchIds);
            foreach($aMethodList as $key => $oMethod){
                $aMethodList[$key] = $oMethod;
            }
            $aJsonField = [
                'lottery_id',
                'match_id',
                'bet_date',
                'match_time',
                'status',
                'league_id',
                'league_name',
                'home_id',
                'away_id',
                'home_team',
                'away_team',
                'handicap',
                'weather',
                'temperature',
                'weather_pic',
                'score',
                'half_score',
            ];
            foreach($aMatchList as $oMatch){
                $iMatchId = $oMatch->match_id;
                $aMethod = [];
                foreach($aMethodList as $oMethod){
                    $aMethodData = $oMethod->getAttributes();
                    $iMehtodId = $aMethodData['id'];
                    $oMethod = new \JcModel\JcMethod($aMethodData);
                    $bIsSingle = false;
                    $bIsEnable = false;
                    if (isset($aMatchMethods[$iMatchId][$iMehtodId])){
                        $oMatchMethod = $aMatchMethods[$iMatchId][$iMehtodId];
                        $bIsSingle = $oMatchMethod->is_single;
                        $bIsEnable = $oMatchMethod->is_enable;
                    }
                    $aCodeList = [];
                    if ($sTabKey == 'single' && !$bIsSingle){
                        //单关页面屏蔽非单关玩法
                        $bIsEnable = false;
                    }
                    $oMethod->is_single = $bIsSingle;
                    $oMethod->is_enable = $bIsEnable;
                    if ($bIsEnable){
                        $aCodes = explode(',', $oMethod->valid_nums);
                        foreach($aCodes as $sCode){
                            if (!isset($aOddsList[$iMatchId][$iMehtodId][$sCode])){
                                continue;
                            }
                            $aCodeList[$sCode] = (object)$aOddsList[$iMatchId][$iMehtodId][$sCode];
                        }
                    }
                    $oMethod->codeList = $aCodeList;
                    $aMethod[$iMehtodId] = $oMethod;
//                     $a = \JcModel\JcMatchOriginal::find($oMatch->original_id);
//                     $oMatch->weather = $a->weather_pic;
                    if (in_array($oMethod->identifier, [\JcModel\JcMethod::STRING_IDENTIFIER_WIN, \JcModel\JcMethod::STRING_IDENTIFIER_HANDICAP_WIN])){
                        if ($oMethod->is_single){
                            $oMatch->is_single = true;
                        }
                    }
                }
                $oMatch->method = $aMethod;
                $oMatch->home_team = $aTeamList[$oMatch->home_id]->short_name;
                $oMatch->away_team = $aTeamList[$oMatch->away_id]->short_name;
                $oMatch->league_name = $aLeagueList[$oMatch->league_id]->short_name;
                $datas[$oMatch->bet_date][$iMatchId] = $oMatch;
                if (!$oMatch->is_selling){
                    $iCountEndMatch++;
                }
                $aMatchData = $oMatch->getAttributes();
                $aNewMatchData = [];
                foreach($aJsonField as $sKey){
                    $aNewMatchData[$sKey] = $aMatchData[$sKey];
                }
                $aAllMatches[$oMatch->match_id] = $aNewMatchData;
            }
        }
        //单关赛事重新排序
        if ($sTabKey == 'single'){
            $aSortMatchesForSingle = [];
            foreach($datas as $dDate => $aMatches){
                $aSingleMatches = [];
                $aNormalMatches = [];
                foreach($aMatches as $sMatchId => $oMatch){
                    if ($oMatch->is_single){
                        $aSingleMatches[$sMatchId] = $oMatch;
                    }else{
                        $aNormalMatches[$sMatchId] = $oMatch;
                    }
                }
                $aSortMatchesForSingle[$dDate] = $aSingleMatches + $aNormalMatches;
            }
            $datas = $aSortMatchesForSingle;
        }
        $sJsonAllMatches = json_encode($aAllMatches);
        $iUserId = \Session::get('user_id');
        $oUser = \User::find($iUserId);
        $oAccount = \Account::find($oUser->account_id);
        
        $aGameConfigs = [
            'MaxMatchNum' => \SysConfig::readValue('jc_bet_max_match_num'),
            'MaxMultiple' => \SysConfig::readValue('jc_bet_max_multiple'),
            'MaxWayNum' => \SysConfig::readValue('jc_bet_max_way_num'),
            'MaxCount' => \SysConfig::readValue('jc_bet_max_count'),
            'MaxAmount' => \SysConfig::readValue('jc_bet_max_amount'),
        ];
        
        $this->setVars(
            compact(
                'aGameConfigs',
                'datas',
                'oJcLottery',
                'oAccount',
                'aMethodList',
                'aLeagueList',
                'aTeamList',
                'aWayList',
                'sLotteryKey',
                'sTabKey',
                'oFilterMethodGroupKey',
                'dBetDate',
                'iCountEndMatch',
                'sJsonAllMatches',
                'oGroupBuy'
            )
        );
        $this->view = 'jc.football';
        $this->render();
    }
    
    public function football_test(){
        $oLottery = \JcModel\JcLotteries::getByLotteryKey('football');
        $iLotteryId = $oLottery->id;
        
        $oSellinMatches = \JcModel\JcUserMatchesInfo::getSellingMatch();
        if (empty($oSellinMatches) || count($oSellinMatches) <= 0){
            $this->halt(false, 'jc-error-selling-match-is-empty', \JcModel\JcUserMatchesInfo::ERRNO_SELLING_MATCH_IS_EMPTY);
        }
        
        $aLeagueIds = [];
        $aTeamIds = [];
        foreach($oSellinMatches as $aMatch){
            $aLeagueIds[$aMatch->league_id] = $aMatch->league_id;
            $aTeamIds[$aMatch->home_id] = $aMatch->home_id;
            $aTeamIds[$aMatch->away_id] = $aMatch->away_id;
            $aMatchIds[$aMatch->match_id] = $aMatch->match_id;
        }
        
        $aMatchMethods = [];
        $oMatchMethods = \JcModel\JcMatchMethod::getByMatchIds($aMatchIds);
        foreach($oMatchMethods as $oMatchMethod){
            $aMatchMethods[$oMatchMethod->match_id][$oMatchMethod->method_id] = $oMatchMethod;
        }
        
        $oLeagues = \JcModel\JcLeague::getByIds($aLeagueIds);
        $aLeagueList = [];
        foreach($oLeagues as $oLeague){
            $aLeagueList[$oLeague->id] = $oLeague->short_name;
        }
        $oTeams = \JcModel\JcTeam::getByIds($aTeamIds);
        $aTeamList = [];
        foreach($oTeams as $oTeam){
            $aTeamList[$oTeam->id] = $oTeam->short_name;
        }
        
        $aWayList = \JcModel\JcWay::getWayByLotteryId($iLotteryId);
        $aMethodList = \JcModel\JcMethod::getAllByLotteryId($iLotteryId);

        $datas = [];
        foreach($oSellinMatches as $aMatch){
            $aMethod = [];
            foreach($aMethodList as $oMethod){
                $bIsSingle = 0;
                $bIsEnable = 0;
                if (isset($aMatchMethods[$aMatch->match_id][$oMethod->id])){
                    $oMatchMethod = $aMatchMethods[$aMatch->match_id][$oMethod->id];
                    $bIsSingle = $oMatchMethod->is_single;
                    $bIsEnable = $oMatchMethod->is_enable;
                }
                $aMethod[$oMethod->id] = (object)[
                    'is_single' => $bIsSingle,
                    'is_enable' => $bIsEnable,
                ];
            }
            
            $aMatch->method = $aMethod;
            $aMatch->match_no = substr($aMatch->match_id, 9);
            
            $datas[$aMatch->match_id] = $aMatch;
        }
        
        $this->setVars(
            compact(
                'datas','aLeagueList','aTeamList','aWayList','aMethodList'
            )
        );
        $this->view = 'jc.football_test';
        $this->render();
    }
    
    public function confirm(){
        if (\Request::method() == 'POST') {
            $aInputData = \Input::only('gameId', 'gameData', 'gameExtra', 'betTimes', 'coefficent');
            $iLotteryId = $aInputData['gameId'];
            $sBetData = $aInputData['gameData'];
            $sGameExtra = $aInputData['gameExtra'];
            $iBetTimes = $aInputData['betTimes'];
//            $sCoefficent = $aInputData['coefficent'];
            $sCoefficent = 1.00;//暂不使用圆角分模式，强制为元
        }else{
            return $this->goBackToIndex('error', __('_jc.error-bet-content'));
        }
        $oJcLottery = \JcModel\JcLotteries::find($iLotteryId);
        if (empty($oJcLottery)){
            return $this->goBack('error', __('_jc.error-bet-content') . ':' . \JcModel\JcLotteries::ERRNO_LOTTERY_IS_NOT_EXISTS);
        }
        $iGroupId = \Input::get('bind_group_id');
        if (isset($iGroupId) && $iGroupId !== ''){
            $oGroupBuy = \JcModel\JcGroupBuy::find($iGroupId);
            if (empty($oGroupBuy)){
                \App::abort(404);
            }
        }

        if (!isset($iLotteryId) || empty($sBetData) || empty($sGameExtra) || empty($iBetTimes) || empty($sCoefficent)){
            return $this->goBack('error', __('_jc.error-bet-content'));
        }
        
        $aData = $aInputData;
        $aData['formatGameData'] = $this->_formatBetContent($sBetData);
        $iErrno = $this->_getFormatData($aData);
        if (!is_array($iErrno)){
            $oMessage = new \Message($this->errorFiles, $this->isMobile);
            $sMsg = $oMessage->getResponseMsg($iErrno);
            return $this->goBack('error', $sMsg . ':' . $iErrno);
        }
        $aMatchList = $datas = $iErrno;
        $aWays = \JcModel\JcWay::getWayByLotteryIdAndIdentifiers($iLotteryId, explode(',', $sGameExtra));
        $aWayList = $this->_formatWayData($aWays);//格式化过关方式，取子集
        
        $iUserId = \Session::get('user_id');
        $oUser = \User::find($iUserId);
        $oAccount = \Account::find($oUser->account_id);
        
        if ($oUser->isEnableVoucher()){
            $bAllowVoucher = true;
            foreach($aMatchList as $oMatch){
                foreach($oMatch->method as $oMethod){
                    foreach($oMethod->codeList as $oOdds){
                        $fMinOdds = isset($fMinOdds) ? min($fMinOdds, $oOdds->odds) : $oOdds->odds;
                    }
                }
            }
            if (!isset($fMinOdds) || $fMinOdds < \Voucher::MIN_SPORT_ODDS){
                $bAllowVoucher = false;
            }
        }
        try{
            $aSingleBetData = $this->_getMatchCombinationList($aMatchList, $aWayList);
        }  catch (\Exception $e){
            return $this->goBack('error', $e->getMessage() . ' : ' . $e->getCode());
        }
        
        $aTeamIds = $aLeagueIds = [];
        foreach($datas as $data){
            $aTeamIds[$data['home_id']] = $data['home_id'];
            $aTeamIds[$data['away_id']] = $data['away_id'];
            $aLeagueIds[$data['league_id']] = $data['league_id'];
        }
        $aTeamList = \JcModel\JcTeam::getByIds($aTeamIds);
        $aLeagueList = \JcModel\JcLeague::getByIds($aLeagueIds);
        foreach($datas as $key => $data){
            $datas[$key]['home_team'] = $aTeamList[$data->home_id];
            $datas[$key]['away_team'] = $aTeamList[$data->away_id];
            $datas[$key]['league'] = $aLeagueList[$data->league_id];
        }
        $is_group_buy = \Input::get('is_group_buy');
        
        $iTotal = 0;
        foreach($aSingleBetData as $aSingleBet){
            $iTotal += count($aSingleBet);
        }
        
        $aJcBetData = [
            'game_extra' => $sGameExtra,
            'multiple' => $iBetTimes,
            'total' => $iTotal,
            'coefficient' => formatNumber($sCoefficent, 2),
            'match_ids' => implode(',', array_keys($aMatchList)),
        ];
        $oBet = new \JcModel\JcUserBet($aJcBetData);
        $iErrno = $oBet->checkBetData();
        if ($iErrno !== true){
            return $this->goBack('error', __('_jc.error-bet-content') . ':' . $iErrno);
        }
        
        $iAmount = $oBet->amount;
        
        $aShowType = \JcModel\JcUserGroupBuy::$validShowType;
        \JcModel\JcUserGroupBuy::translateArray($aShowType);
        
        $iUserId = \Session::get('user_id');
        $oUser = \User::find($iUserId);
        
        $this->setVars(
            compact(
                'datas', 'oJcLottery','oBet', 'is_group_buy', 'aInputData', 'iTotal', 'iAmount', 'iBetTimes', 'aWays','oGroupBuy', 'oAccount', 'aShowType', 'bAllowVoucher'
            )
         );
        
        $this->view = 'jc.bet_confirm';
        $this->render();
    }
    
    public function bet(){
        //$content = '201511242006:31.104:0+201511242007:33.1011:0+201511242008:13.1031.52:0';
                
        $iUserId = \Session::get('user_id');
        $oUser = \User::find($iUserId);
        
        if (\Request::method() == 'POST') {
            $aInputData = \Input::only('gameId', 'gameData', 'gameExtra', 'betTimes', 'coefficent');
            $iLotteryId = $aInputData['gameId'];
            $sBetData = $aInputData['gameData'];
            $sGameExtra = $aInputData['gameExtra'];
            $sBetTimes = $aInputData['betTimes'];
//            $sCoefficent = $aInputData['coefficent'];
            $sCoefficent = 1.00;//暂不使用圆角分模式，强制为元
            
            $iGroupId = \Input::get('group_id');
        }
        
        if (!isset($iLotteryId) || empty($sBetData) || empty($sGameExtra) || empty($sBetTimes) || empty($sCoefficent)){
            $this->halt(false, 'jc-error-bet-content', \JcModel\JcUserBet::ERRNO_BET_PARAM_IS_EMPTY);
        }
        
        $oJcLottery = \JcModel\JcLotteries::find($iLotteryId);
        if (empty($oJcLottery)){
            $this->halt(false, 'jc-error-bet-content', \JcModel\JcLotteries::ERRNO_LOTTERY_IS_NOT_EXISTS);
        }

        if(!$this->_isAvailableBetMatche($sBetData)){
            $this->halt(false, 'jc-error-bet-content', \JcModel\JcUserBet::ERRNO_BET_MATCHE_NOT_AVAILABLE);
        }
        
        $aData = $aInputData;
        $aData['formatGameData'] = $this->_formatBetContent($sBetData);
        $iErrno = $this->_getFormatData($aData);
        if (!is_array($iErrno)){
            $this->halt(false, 'jc-error-bet-content', $iErrno);
        }
        
        $aMatchList = $iErrno;
        
        $aBetData = $aMethodIdentifiers = [];
        foreach($aMatchList as $oMatch){
            foreach($oMatch->method as $oMethod){
                $aMethodIdentifiers[$oMethod->identifier] = $oMethod->identifier;
                foreach($oMethod->codeList as $oOdds){
                    $aBetData[$oMatch->match_id][$oOdds->code] = $oOdds->odds;
                    $fMinOdds = isset($fMinOdds) ? min($fMinOdds, $oOdds->odds) : $oOdds->odds;
                }
            }
        }
        
        $aWays = \JcModel\JcWay::getWayByLotteryIdAndIdentifiers($iLotteryId, explode(',', $sGameExtra));
        $aWayList = $this->_formatWayData($aWays);//格式化过关方式，取子集
        
        try{
            $aSingleBetData = $this->_getMatchCombinationList($aMatchList, $aWayList);
        }  catch (\Exception $e){
            $this->halt(false, 'jc-error-bet-content', $e->getCode());
        }
        
        $iTotal = 0;
        foreach($aSingleBetData as $aSingleBet){
            $iTotal += count($aSingleBet);
        }
        $iMethodGroupId = 0;
        foreach($aMethodIdentifiers as $sMethodIdentifier){
            if (count($aMethodIdentifiers) > 1){
                $sMethodIdentifier = 'hunhe';
            }
            $oMethodGroup = \JcModel\JcMethodGroup::getBasicByIdentifier($oJcLottery->id, $sMethodIdentifier);
            $iMethodGroupId = $oMethodGroup->id;
            break;
        }
        
        $aDanmaMatches = [];
        foreach($aData['formatGameData'] as $aMatch){
            if ($aMatch['is_danma']){
                $aDanmaMatches[] = $aMatch['match_id'];
            }
        }
        
        $aJcBetData = [
            'lottery_id' => $iLotteryId,
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'account_id' => $oUser->account_id,
            'game_extra' => $sGameExtra,
            'bet_content' => $sBetData,
            'bet_data' => json_encode($aBetData),
            'multiple' => $sBetTimes,
            'total' => $iTotal,
            'coefficient' => formatNumber($sCoefficent, 2),
            'method_group_id' => $iMethodGroupId,
            'danma' => implode(',', $aDanmaMatches),
            'match_ids' => implode(',', array_keys($aMatchList)),
            'type' => isset($iGroupId) ? \JcModel\JcBet::TYPE_GROUP_BUY : \JcModel\JcBet::TYPE_SELF_BUY,
        ];
        
        $oAccount = \Account::lock($oUser->account_id, $this->accountLocker);
        if (empty($oAccount)) {
            $this->halt(false, 'jc-error-bet-failed', \Account::ERRNO_LOCK_FAILED);
        }
        
        \DB::beginTransaction();
        try{
            $oJcBet = new \JcModel\JcUserBet($aJcBetData);
            $iReturn = $oJcBet->addBet($aSingleBetData);
            $iBetId = $oJcBet->id;
            if ($iReturn != \JcModel\JcUserBet::ERRNO_BET_SUCCESSFUL){
                throw new \Exception('add bet faild');
            }
            if (isset($iGroupId)){
                $iReturn = $this->_doGroupBuy($oJcBet, $oUser, $oAccount, $oGroupBuy);
                if ($iReturn != \JcModel\JcUserBet::ERRNO_BET_SUCCESSFUL){
                    throw new \Exception('add group buy faild');
                }
            }else{
                //自购
                $fAmount = $oJcBet->amount;

                /*=================支持礼金支付===================*/
                $bUseVoucher = intval(\Input::get('use_voucher'));
                if ($bUseVoucher){
                    if ($oUser->isEnableVoucher()){
                        if (isset($fMinOdds) && $fMinOdds >= \Voucher::MIN_SPORT_ODDS){
                            $fVoucherAmount = \UserVoucher::getVoucherAmount($iUserId);
                            if ($fVoucherAmount > 0){
                                if ($fVoucherAmount >= $fAmount){
                                    $fCostVoucherAmount = $fAmount;
                                }else{
                                    $fCostVoucherAmount = $fVoucherAmount;
                                }
                                if (!\UserVoucher::decrementAmount($iUserId, $iLotteryId, $fCostVoucherAmount)){
                                    $iReturn = \JcModel\JcUserBet::ERRNO_BET_FAILED;
                                    throw new \Exception('decement voucher amount faild, amount: ' . $fCostVoucherAmount);
                                }
                                $iReturn = \Transaction::addTransaction($oUser, $oAccount,\TransactionType::TYPE_VOUCHER_DEPOSIT, $fCostVoucherAmount);
                                if ($iReturn != \Transaction::ERRNO_CREATE_SUCCESSFUL){
                                    throw new \Exception('add voucher transaction faild');
                                }
                            }
                        }else{
                            $iReturn = \JcModel\JcUserBet::ERRNO_BET_FAILED;
                            throw new \Exception("odds too less: {$fMinOdds}");
                        }
                    }else{
                        $iReturn = \JcModel\JcUserBet::ERRNO_BET_FAILED;
                        throw new \Exception('voucher is not enable');
                    }
                }
                /*=================支持礼金支付===================*/
                
                if ($oAccount->available < $fAmount){
                    $iReturn = \JcModel\JcUserBet::ERRNO_BET_ERROR_LOW_BALANCE;
                }else{
                    $oProject = new \JcModel\JcProject($oJcBet->getAttributes());
                    $oProject->setUser($oUser);
                    $oProject->setAccount($oAccount);
                    $oProject->bet_id = $iBetId;
                    $oProject->type = \JcModel\JcUserProject::TYPE_SELF_BUY;
                    $iReturn = $oProject->addProject();
                    if ($iReturn != \JcModel\JcProject::ERRNO_SUCCESSFUL){
                        throw new \Exception('add project faild');
                    }else{
                        $iReturn = \JcModel\JcUserBet::ERRNO_BET_SUCCESSFUL;
                    }
                }
            }
        }  catch (\Exception $e){
            \DB::rollback();
            $iReturn = \JcModel\JcUserBet::ERRNO_BET_FAILED;
//            var_dump($e->getMessage());
//            die;
        }
        $bSucc = $iReturn == \JcModel\JcUserBet::ERRNO_BET_SUCCESSFUL;
        if ($bSucc){
            //异步写入赛事注单关联表，开奖使用
            $bSucc = \JcModel\JcBetsMatch::addFillTask($iBetId);
        }
        if ($bSucc){
            \DB::commit();
        }else{
            \DB::rollback();
            $this->halt(false, 'jc-error-bet-failed', $iReturn);
        }
        
        $aResponseData = [];
        if (isset($oGroupBuy)){
            $aResponseData['RedirectUrl'] = route('jc.follow', $oGroupBuy->id);
        }else{
            $aResponseData['RedirectUrl'] = route('jc.bet_view', $iBetId);
        }
        $this->halt(true, 'info',  null, $aSuccessedBets, $aFailedBets, $aResponseData);
    }
    
    private function _getMatchCombination($aMatchList, $iCount){
        if (count($aMatchList) < $iCount || $iCount < 1){
            $this->halt(false, 'jc-error-match-num', \JcModel\JcUserBet::ERRNO_BET_CONTENT_WAY);
        }
        if (count($aMatchList) == $iCount){
            return [ $aMatchList ];
        }

        //先取出赛事组合
        $aCombinations = \Math::getCombinationToString($aMatchList, $iCount);

        $aResult = [];
        foreach($aCombinations as $sCombination){
            $aResult[] = explode(',', $sCombination);
        }

        return $aResult;
    }
    
    /**
     * 格式化投注数组
     * @param string $sBetContent
     * @return array
     */
    private function _formatBetContent($sBetContent = ''){
        return \JcModel\JcUserBet::getMatchData($sBetContent);
    }

    private function _getFormatData($aData = []){
        $iLotteryId = $aData['gameId'];
        $aFormatGameData = $aData['formatGameData'];
        $sBetWayIdentifiers = $aData['gameExtra'];
        $iMultiple = $aData['betTimes'];
        
        $aAllMatchIds = $aDanmaMatchIds = [];
        foreach($aFormatGameData as $aBet){
            $aAllMatchIds[] = $aBet['match_id'];
            if ($aBet['is_danma']){
                $aDanmaMatchIds[] = $aBet['match_id'];
            }
        }

        if (count($aAllMatchIds) < 1){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_MATCHID;
        }
        //判断投注赛事数量
        $iMaxMatchCount = \SysConfig::readValue('jc_bet_max_match_num');
        if (count($aAllMatchIds) > $iMaxMatchCount){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_MATCH_MAX;
        }
        //倍数限制
        $iBetMaxMultiple = \SysConfig::readValue('jc_bet_max_multiple');
        if ($iMultiple < 0){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_MULTIPLE;
        }
        if ($iMultiple > $iBetMaxMultiple){
//            error-bet-multiple-max
            return \JcModel\JcUserBet::ERRNO_BET_MULTIPLE_MAX;
        }
        //过关方式限制
        $iBetMaxWayNum = \SysConfig::readValue('jc_bet_max_way_num');
        $aIdentifiers = explode(',', $sBetWayIdentifiers);
        if (count($aIdentifiers) > $iBetMaxWayNum){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_WAY;
        }
        
        //胆码赛事数量不能大于N-1
        if ($aDanmaMatchIds && count($aDanmaMatchIds) >= count($aAllMatchIds)){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_DANMA;
        }
        
        //校验赛事是否合法
        $aMatches = \JcModel\JcUserMatchesInfo::getByMatchIds($aAllMatchIds);
        if (count($aMatches) != count($aAllMatchIds)){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_MATCHID;
        }
        $aMatchesList = [];
        foreach($aMatches as $oMatch){
            $aMatchesList[$oMatch->match_id] = $oMatch;
        }
        
        //校验过关方式
        $aWayList = \JcModel\JcWay::getWayByLotteryIdAndIdentifiers($iLotteryId, $aIdentifiers);
        if (count($aIdentifiers) != count($aWayList)){
            return \JcModel\JcUserBet::ERRNO_BET_CONTENT_WAY;
        }
        $bIsSingleWay = false;
        foreach($aWayList as $oWay){
            if (\JcModel\JcWay::checkIsSingleWay($oWay)){
                $bIsSingleWay = true;
                break;
            }
        }
        
        //取赛事玩法关联
        $aMatchMethods = \JcModel\JcMatchMethod::getByMatchIds($aAllMatchIds);
        $aMatchMethodList = [];
        foreach($aMatchMethods as $oMatchMethod){
            $aMatchMethodList[$oMatchMethod->match_id][$oMatchMethod->method_id] = $oMatchMethod;
        }
        
        //校验玩法格式 已下注的记录是否作验证？
        $aMethods = [];
        $iMaxChooseCount = $iMaxMatchCount;//根据玩法不同 可选的最大串作限制
        foreach($aFormatGameData as $aBet){
            $sMatchId = $aBet['match_id'];
            foreach ($aBet['bet_data'] as $sCode){
                $oMethod = \JcModel\JcMethod::getMethodByCode($iLotteryId, $sCode);
                $aMethods[$sMatchId][$oMethod->id] = $oMethod;
                $iMaxChooseCount = min($iMaxChooseCount, $oMethod->max_count);
                if (
                    !$oMethod
                    || !isset($aMatchMethodList[$sMatchId][$oMethod->id])
                    || !$aMatchMethodList[$sMatchId][$oMethod->id]->is_enable
                ){
                    return \JcModel\JcUserBet::ERRNO_BET_CONTENT_METHOD;
                }
                if ($bIsSingleWay && !$aMatchMethodList[$sMatchId][$oMethod->id]->is_single){
                    return \JcModel\JcUserBet::ERRNO_BET_CONTENT_METHOD;
                }
            }
        }
        //校验过关方式最大值
        foreach ($aWayList as $oWay){
            if ($oWay->choose_count > $iMaxChooseCount || $oWay->choose_count > count($aAllMatchIds)){
                return  \JcModel\JcUserBet::ERRNO_BET_CONTENT_WAY;
            }
        }
        
        //追加赔率数据
        $aMatchOdds =  \JcModel\JcOdds::getOddsByMatchIds($iLotteryId, $aAllMatchIds);
        $aOddsList = [];
        foreach($aMatchOdds as $oOdds){
            $aOddsList[$oOdds->match_id][$oOdds->code] = $oOdds;
        }
        
        $aBetList = [];
        foreach($aFormatGameData as $aBet){
            $sMatchId = $aBet['match_id'];
            $oMatch = $aMatchesList[$sMatchId];
            $aCodeList = [];
            foreach($aBet['bet_data'] as $sCode){
                if (!isset($aOddsList[$sMatchId][$sCode])){
                    return \JcModel\JcUserBet::ERRNO_BET_CONTENT_ODDS;
                }
                $oOdds = $aOddsList[$sMatchId][$sCode];
                
                $aCodeList[$oOdds->match_id][$oOdds->method_id][$oOdds->code] = $oOdds;
                $oMethod = $aMethods[$oOdds->match_id][$oOdds->method_id];
                $oMethod->codeList = $aCodeList[$oOdds->match_id][$oOdds->method_id];
            }
            if (in_array($sMatchId, $aDanmaMatchIds)){
                $oMatch->is_danma = 1;
            }
            $oMatch->method = $aMethods[$sMatchId];
            $aBetList[$sMatchId] = $oMatch;
        }
        return $aBetList;
    }
    
    private function _formatWayData($aWayList){
        $aWayById = $aChildWayIds = [];
        
        foreach ($aWayList as $oWay){
            if (!isset($oWay->child_ways)){
                $oWay->rule = json_encode([$oWay->identifier => $oWay->identifier]);
                $oWay->child_ways = $oWay->id;
            }
            $aWayById[$oWay->id] = $oWay;
        }
        foreach ($aWayList as $oWay){
            $aChildWays = explode(',',$oWay->child_ways);
            foreach($aChildWays as $iChildWayId){
                if (!isset($aWayById[$iChildWayId])){
                    $aChildWayIds[$iChildWayId] = $iChildWayId;
                }
            }
        }
        if (count($aChildWayIds) > 0){
            $aChildWay = \JcModel\JcWay::getWayByWayIds($aChildWayIds);
            foreach($aChildWay as $oWay){
                $aWayById[$oWay->id] = $oWay;
            }
        }
        foreach($aWayList as $oWay){
            $aChild = [];
            $aChildWayIds = $oWay->child_ways ? explode(',',$oWay->child_ways) : [$oWay->id];
            foreach($aChildWayIds as $iChildWayId){
                $aChild[$iChildWayId] = $aWayById[$iChildWayId];
            }
            $oWay->child =$aChild;
        }
        return $aWayList;
    }
    
    private function _getMatchCombinationList($aMatchList, $aWayList){
        //胆码赛事
        $aDanmaMatchIds = [];
        foreach($aMatchList as $oMatch){
            if ($oMatch->is_danma){
                $aDanmaMatchIds[] = $oMatch->match_id;
            }
            $aAllMatchIds[] = $oMatch->match_id;
        }
        $aMatchCombinationList = [];
        $iCountNum = 0;
        $iMaxCountNum = \SysConfig::readValue('jc_bet_max_count');
        
        foreach($aWayList as $oParentWay){
            //先从总赛事中取M场赛事
//            $oParentWay = $aWayListByWayId[$iParentWayId];
            
            //有设胆的情况
            if (count($aDanmaMatchIds) > 0){
                $aSourceMatchIds = array_values(array_diff($aAllMatchIds, $aDanmaMatchIds));
                if (!$aSourceMatchIds){
                    throw new \Exception(__('_jc.error-bet-content'), \JcModel\JcUserBet::ERRNO_BET_CONTENT_DANMA);
                }
                $iParentChooseCount = $oParentWay->choose_count - count($aDanmaMatchIds);
                $aMatchGroupList = $this->_getMatchCombination($aSourceMatchIds, $iParentChooseCount);
                foreach($aMatchGroupList as $sKey => $aMatches){
                    $aMatchGroupList[$sKey] = array_merge($aMatchGroupList[$sKey], $aDanmaMatchIds);
                }
            }else{
                $aMatchGroupList = $this->_getMatchCombination($aAllMatchIds, $oParentWay->choose_count);
            }

            foreach($aMatchGroupList as $aMatches){
                //根据赛事的玩法类型生成组合
                $aMatchGroup = $aMatchMethods = [];
                foreach($aMatches as $sMatchId){
                    $aTmpMethod = $aMatchList[$sMatchId]->method;
                    $aMatchGroup[$sMatchId] = array_keys($aTmpMethod);
                    foreach($aTmpMethod as $k => $v){
                        $aMatchMethods[$sMatchId][$k] = $v->codeList;
                    }
                }
                $aMethodCartesianProduct = \Math::getCartesianProductWithKey($aMatchGroup);
                foreach($aMethodCartesianProduct as $aMethods){
                    //根据过关方式来生成赛事组合
                    foreach($oParentWay->child as $oWay){
                        $iWayId = $oWay->id;
                        if ($oWay->all_count != 1){
                            //仅允许M串1
                            throw new \Exception(__('_jc.error-bet-way') , \JcModel\JcUserBet::ERRNO_BET_CONTENT_WAY);
                        }
                        $iChooseCount = $oWay->choose_count;
                        //生成M串N
                        $aMatchCombinations = $this->_getMatchCombination($aMatches, $iChooseCount);
                        foreach($aMatchCombinations as $aMatchCombination){
                            $aTmpList = [];
                            foreach($aMatchCombination as $iMatchId){
                                $iMethodId = $aMethods[$iMatchId];
                                //取赛事投注赔率数据
                                $aTmpList[] = $aMatchMethods[$iMatchId][$iMethodId];
                            }
                            //取同类玩法所有组合
                            $aMatchCartesianProduct = \Math::getCartesianProductWithKey($aTmpList);
                            if (isset($aMatchCombinationList[$iWayId])){
                                foreach($aMatchCartesianProduct as $v){
                                    $aMatchCombinationList[$iWayId][] = $v;
                                }
                            }else{
                                $aMatchCombinationList[$iWayId] = $aMatchCartesianProduct;
                            }
                            $iCountNum += count($aMatchCartesianProduct);
                            if ($iCountNum > $iMaxCountNum){
                                throw new \Exception(__('_jc.error-bet-count-max',['num' => $iMaxCountNum]), \JcModel\JcUserBet::ERRNO_BET_COUNT_MAX);
                            }
                        }
                    }
                }
            }
        }
        return $aMatchCombinationList;
    }


    public function bet_list($sLotteryKey = null){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $aMethodGroup = \JcModel\JcMethodGroup::getAllByLotteryId($oJcLottery->id);
        $iLotteryId = $oJcLottery->id;
        $iUserId = \Session::get('user_id');

        $aInputData = \Input::only('status', 'searchDate', 'type');
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'user_id' => ['=', $iUserId],
        ];
        foreach($aInputData as $sKey => $sVal){
            if (isset($sVal) && $sVal !== ''){
                if ($sKey == 'searchDate'){
                    $sStartDate = date('Y-m-d 00:00:00', strtotime($sVal));
                    $sEndDate = date('Y-m-d 23:59:59', strtotime($sVal));
                    $aConditions['created_at'] = ['between', [$sStartDate, $sEndDate]];
                }elseif ($sKey == 'type' && $sVal == \JcModel\JcProject::TYPE_GROUP_BUY){
                    $aConditions['type'] = ['in', [\JcModel\JcProject::TYPE_GROUP_BUY, \JcModel\JcProject::TYPE_GROUP_BUY_FOLLOW]];
                }else{
                    $aConditions[$sKey] = $sVal;
                }
            }
        }
        $datas = \JcModel\JcUserProject::getList($aConditions);
        
        $aStatus = [
            \JcModel\JcProject::STATUS_NORMAL => 'Normal',
            \JcModel\JcProject::STATUS_DROPED => 'Droped',
            \JcModel\JcProject::STATUS_LOST => 'Lost',
            \JcModel\JcProject::STATUS_WON => 'Won',
            \JcModel\JcProject::STATUS_PRIZE_SENT => 'Prize Sent',
            \JcModel\JcProject::STATUS_DROPED_BY_SYSTEM => 'Droped By System',
        ];
        \JcModel\JcUserProject::translateArray($aStatus);
        
        $this->setVars(
            compact(
                'datas','oJcLottery','oAccount','sLotteryKey','aMethodGroup','aStatus'
            )
         );
        
        $this->viewVars['sTabKey'] = 'bet_list';
        
        $this->view = 'jc.bet_list';
        $this->render();
    }
    
    public function bet_view($iBetId){
        $oBet = \JcModel\JcUserBet::find($iBetId);
        if (empty($oBet)){
            //仅允许自购方案展示
            \App::abort(404);
        }
        if ($oBet->group_id > 0){
            return \Redirect::route('jc.follow', $oBet->group_id);
        }
        $oJcLottery = \JcModel\JcLotteries::find($oBet->lottery_id);
        $iLotteryId = $oJcLottery->id;
        
        $oAccount = \Account::find(\Session::get('account_id'));
        
        $datas = $oBet->getBetMatchData();
        $aIdentifiers = explode(',', $oBet->game_extra);
        $aWayList = \JcModel\JcWay::getWayByLotteryIdAndIdentifiers($iLotteryId, $aIdentifiers);
        
        $fSumCommission = \JcModel\JcCommission::getSumCommissionByBetId($iBetId);
        
        $oMethodGroup = \JcModel\JcMethodGroup::find($oBet->method_group_id);
        
        $this->setVars(
            compact(
                'datas', 'oJcLottery','oAccount','oBet','aBetDetailList','aWayList','oMethodGroup','fSumCommission'
            )
         );
        
        $this->view = $this->customViewPath . '.' . $this->action;
        $this->render();
    }
    
    public function bet_detail($iBetId){
        $oBet = \JcModel\JcUserBet::find($iBetId);
        if (empty($oBet)){
            \App::abort(404);
        }
        if ($oBet->group_id > 0){
            $oGroupBuy = \JcModel\JcUserGroupBuy::find($oBet->group_id);
            if (empty($oGroupBuy) || !$oGroupBuy->checkDisplayBet()){
                \App::abort(404);
            }
        }
        $oJcLottery = \JcModel\JcLotteries::find($oBet->lottery_id);
        $iLotteryId = $oJcLottery->id;
        
        $oAccount = \Account::find(\Session::get('account_id'));
        
        $datas = $oBet->getBetMatchData();
        
        $aIdentifiers = explode(',', $oBet->game_extra);
        $aWayList = \JcModel\JcWay::getWayByLotteryIdAndIdentifiers($iLotteryId, $aIdentifiers);
        
        $aDetailWayIds = [];
        $aBetDetailList = \JcModel\JcUserBetsDetail::getListByBetId($oBet->id);
        $oBet->formatBetDetailData($aBetDetailList);
        foreach($aBetDetailList as $oBetDetail){
            $aDetailWayIds[$oBetDetail->way_id] = $oBetDetail->way_id;
        }
        
        $aDetailWayList = [];
        if ($aDetailWayIds){
            $aDetailWays = \JcModel\JcWay::getWayByWayIds($aDetailWayIds);
            foreach($aDetailWays as $oWay){
                $aDetailWayList[$oWay->id] = $oWay;
            }
        }
        
        $this->setVars(
            compact(
                'datas', 'oJcLottery','oAccount','oBet','aBetDetailList','aWayList','aDetailWayList'
            )
         );
        
        $this->view = 'jc.bet_detail';
        $this->render();
    }
    
    public function result($sLotteryKey, $sMethodKey = null){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $iLotteryId = $oJcLottery->id;
        if (isset($sMethodKey)){
            $oFilterMethod = \JcModel\JcMethod::getMethodByIdentifier($iLotteryId, $sMethodKey);
            if (empty($oFilterMethod)){
                \App::abort(404);
            }
        }
        $dBetDate = \Input::get('bet_date');
        if (!isset($dBetDate)){
            $dBetDate  = date('Y-m-d', time() - 86400);
        }
        if (strtotime($dBetDate) === false){
            $dBetDate = null;
        }
        $aMethodList = \JcModel\JcMethod::getAllByLotteryId($iLotteryId);
        $datas = \JcModel\JcUserMatchesInfo::getResultListByBetDate($dBetDate);
        if (count($datas) > 0){
            $aMatchIds = [];
            foreach($datas as $key => $data){
                $aMatchIds[$data->match_id] = $data->match_id;
                $aLeagueIds[$data->league_id] = $data->league_id;
                $aTeamIds[$data->home_id] = $data->home_id;
                $aTeamIds[$data->away_id] = $data->away_id;
            }
            $aLeagues = \JcModel\JcLeague::getByIds($aLeagueIds);
            $aTeams = \JcModel\JcTeam::getByIds($aTeamIds);
            $aOdds = \JcModel\JcOdds::getOddsByMatchIds($iLotteryId, $aMatchIds);
            $aOddsByMatchId = [];
            foreach($aOdds as $oOdds){
                $aOddsByMatchId[$oOdds->match_id][$oOdds->code] = $oOdds;
            }
            foreach($datas as $key => $oMatch){
                $aResult = [];
                foreach($aMethodList as $oMethod){
                    if (isset($sMethodKey) && $oMethod->identifier != $sMethodKey){
                        continue;
                    }
                    $sCode = $oMethod->getResult($oMatch);
                    if (isset($aOddsByMatchId[$oMatch->match_id][$sCode])){
                        $aResult[$oMethod->identifier] = $aOddsByMatchId[$oMatch->match_id][$sCode];
                    }else{
                        $aResult[$oMethod->identifier] = (object)[
                            'name' => \JcModel\JcMethod::getCodeName($iLotteryId, $sCode),
                            'odds' => '-'
                        ];
                    }
                }
                $datas[$key]->home_name = $aTeams[$oMatch->home_id]->name;
                $datas[$key]->away_id = $aTeams[$oMatch->away_id]->name;
                $datas[$key]->league_name = $aLeagues[$oMatch->league_id]->name;
                $datas[$key]->result = $aResult;
    //            var_dump($aCode);die;
    //            $data[$key]->odds = $aOddsByMatchId[$data->match_id][];
            }
        }
        
        $this->setVars(
            compact(
                'datas', 'oJcLottery', 'aMethodList', 'oFilterMethod', 'dBetDate','sMethodKey'
            )
         );
        
        $this->viewVars['sTabKey'] = 'result';
        
        if (isset($oFilterMethod)){
            $this->setVars( compact('oFilterMethod') );
        }
        $this->view = 'jc.result';
        $this->render();
    }
    
    private function _doGroupBuy($oBet, $oUser, $oAccount, &$oGroupBuy){
        $iGroupId = \Input::get('group_id');
        //合买
        if ($iGroupId == -1){
            //新发布合买
            $iGuarantee = \Input::get('guarantee');
            $fGuaranteeAmount = 0;
            $fBuyAmount = \Input::get('buy_amount');
            if ($iGuarantee == 2){
                $fGuaranteeAmount = \Input::get('guarantee_amount');
                $fMinGuaranteeRate = \SysConfig::readValue('jc_group_buy_min_guarantee_rate');
                if ($fGuaranteeAmount / $oBet->amount < $fMinGuaranteeRate){
                    return \JcModel\JcUserGroupBuy::ERRNO_GUARANTEE_AMOUNT;
                }
            }else if ($iGuarantee == 3){
                $fGuaranteeAmount = $oBet->amount - $fBuyAmount;
            }
            $aData = array_merge(
                $oBet->getAttributes(), 
                [
                    'bet_id' => $oBet->id,
                    'fee_rate' => \Input::get('fee_rate'),
                    'buy_amount' => $fBuyAmount,
                    'guarantee_amount' => $fGuaranteeAmount,
                    'show_type' => \Input::get('show_type'),
                    'end_time' => $oBet->getFirstMatchTime(),
                    'allow_type' => \Input::get('buy_user_type'),
                    'buy_type' => JcProject::BUY_TYPE_FIRST,
                    'is_tester' => $oUser->is_tester
                ]
            );
            $oGroupBuy = new \JcModel\JcUserGroupBuy($aData);
            $oGroupBuy->setUser($oUser);
            $oGroupBuy->setAccount($oAccount);
            $iRes = $oGroupBuy->addGroupBuy();
            if ($iRes != \JcModel\JcUserGroupBuy::ERRNO_SUCCESSED){
                return $iRes;
            }
            $iGroupId = $oGroupBuy->id;
        }else{
            //预投直接绑定方案
            $oGroupBuy = \JcModel\JcUserGroupBuy::find($iGroupId);
            if (empty($oGroupBuy)){
                return \JcModel\JcUserGroupBuy::ERRNO_GROUP_BUY_IS_NOT_EXISTS;
            }else{
                if (!$oGroupBuy->bindBet($oBet)){
                    return \JcModel\JcUserGroupBuy::ERRNO_BIND_BET_FAILED;
                }
            }
        }
        if ($oGroupBuy){
            //方案表设置合买ID
            if ($oBet->bindGroupBuy($oGroupBuy)){
                return \JcModel\JcUserBet::ERRNO_BET_SUCCESSFUL;
            }
        }
        return \JcModel\JcUserBet::ERRNO_BET_ERROR_SAVE;
    }
    
    public function help($sPath){
        if (!preg_match('/^[\w\-\_]+$/isU', $sPath)){
            //防止路径注入
            \App::abort(404);
        }
        $this->view = $this->customViewPath . '.' . $this->action . '.' . $sPath;
        $this->render();
    }
    
    public function prize_detail($sLotteryKey = null){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $this->setVars(
            compact(
                'oJcLottery'
            )
        );
        $this->render();
    }


    public function __destruct() {
        if ($this->accountLocker){
            \Account::unlock(\Session::get('account_id'), $this->accountLocker);
        }
        
        parent::__destruct();
    }

    private function _isAvailableBetMatche($aData){
        $aGameData = explode('+',$aData);
        $bIsAvailable = true;
        foreach($aGameData as $sSingleMatcheData){
            $aSingleMatcheData = explode(':',$sSingleMatcheData);
            $oMatch = JcUserMatchesInfo::getByMatchId($aSingleMatcheData[0]);
            if(!$oMatch){
                $bIsAvailable = false;
            }
            $sMatcheTime = $oMatch->match_time;
            if(strtotime($sMatcheTime." -".\sysConfig::readValue('jc_bet_stop_time')." minute") <= time()){
                $bIsAvailable = false;
            }
        }
        return $bIsAvailable;
    }
}
