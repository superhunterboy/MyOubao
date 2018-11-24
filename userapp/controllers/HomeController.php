<?php

# 首页

class HomeController extends UserBaseController {

    protected $modelName = 'UserMessage';

    public function beforeRender() {
        parent::beforeRender();
    }

    protected function doNotLogin() {
        $sRouteAction = Route::currentRouteAction();
        $aRouteAction = explode('@', $sRouteAction);
        if (Session::get('is_client') && $aRouteAction && $aRouteAction[1] == 'getIndex') {
            return;
        } else {
            return parent::doNotLogin();
        }
    }

    /**
     * 更新在线时间
     */
    protected function updateOnlineTime() {
        if ($this->checkLogin()) {
            parent::updateOnlineTime();
        }
    }

    /**
     * [getIndex 首页，输出首页需要渲染的参数]
     * @return [type] [description]
     */
    public function getIndex() {
        if (Session::get('is_client')) {
            return $this->getClientIndex();
        }
        if(isset($_GET['flag']) && $_GET['flag']=='availableBalance')
        {
        	$fAvailable = Account::getAccountInfoByUserId(Session::get('user_id'), ['available'])->available;
        	return Response::json(array('isSuccess'=>TRUE,'data'=>number_format($fAvailable, 2)));
        }
//         $session = new Session(UserOnline::getSessionIdByUserId($iUserId));
//                   var_dump($session);exit;
        $aLatestProjects = null;
        $aLatestTraces = null;
        $aTransactionTypes = null;
        $aLatestTransactions = null;
        if (Session::get('is_agent'))
            $aLatestAnnouncements = CmsArticle::getLatestRecords();
        else
            $aLatestAnnouncements = CmsArticle::getLatestRecords(7);
        // pr(($aLatestProjects->toArray()));exit;
        // 中奖信息，25条
        $aPrizePrj = PrjPrizeSet::getCurrentWonProjects(10);
        $aADInfo = AdInfo::getLatestRecords();
        $this->beforeRender();
        $fAvailable = Account::getAccountInfoByUserId(Session::get('user_id'), ['available'])->available;
        $this->setVars('fAvailable', $fAvailable);
        $oUser = User::find(Session::get('user_id'));
        $bFirstLogin = Session::get('first_login');
        Session::forget('first_login');
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
//        pr($aMsgTypes);exit;
        $aResults = UserMessage::getUserLatest10UnreadMessages($oUser->id);
        $aStationLetters = [];
        foreach ($aResults as $oData) {

            $aStationLetters[] = [
                'msg_title' => $oData->msg_title,
                'id' => $oData->id,
                'msg_type' => $aMsgTypes[$oData->type_id],
                'updated_at' => date('m-d', strtotime($oData->updated_at))
            ];
        }

        /* ==================竞彩相关数据=================== */
        $oRecommendMatches = \JcModel\JcUserMatchesInfo::getSellingMatch(['match_id']);
        if (count($oRecommendMatches) < 2) {
            //在售赛事数量不足时，取最新赛事
            $oRecommendMatches = \JcModel\JcUserMatchesInfo::getLastMatches(['match_id']);
        }
        $aRecommendMatchIds = [];
        foreach ($oRecommendMatches as $oMatch) {
            $aRecommendMatchIds[$oMatch->match_id] = [$oMatch->match_id];
        }
        $aMatchIds = array_rand($aRecommendMatchIds, 2);
        $aRecommendMatches = \JcModel\JcUserMatchesInfo::getByMatchIdsWithLeagueAndTeam($aMatchIds);
        $this->viewVars['aRecommendMatches'] = $aRecommendMatches;

        $oRecommendLottery = \JcModel\JcLotteries::getByLotteryKey('football');
        $iRecommendLotteryId = $oRecommendLottery->id;
        $aRecommendGroupBuys = \JcModel\JcUserGroupBuy::getForIndexByLotteryId($iRecommendLotteryId);
        foreach ($aRecommendGroupBuys as $oGroupBuy) {
            $aExtraData = [
                'lottery_id' => $iRecommendLotteryId,
                'user_id' => $oGroupBuy->user_id,
            ];
            $oGroupBuy->user_extra = new \JcModel\JcUserExtra($aExtraData);
            $oGroupBuy->project_count = \JcModel\JcUserProject::getCountByGroupId($oGroupBuy->id);
        }
        $this->viewVars['aRecommendGroupBuys'] = $aRecommendGroupBuys;
        /* ==================竞彩相关数据=================== */

        $sViewFileName = 'index';


        return View::make($sViewFileName)->with($this->viewVars + compact('bFirstLogin', 'aLatestAnnouncements', 'aADInfo', 'aStationLetters', 'aPrizePrj'));
    }

    /**
     * [getIndex 首页，输出首页需要渲染的参数]
     * @return [type] [description]
     */
    public function getAgentCenter() {

//         $session = new Session(UserOnline::getSessionIdByUserId($iUserId));
//                   var_dump($session);exit;
        $aLatestProjects = null;
        $aLatestTraces = null;
        $aTransactionTypes = null;
        $aLatestTransactions = null;



        // pr(($aLatestProjects->toArray()));exit;
        $aADInfo = AdInfo::getLatestRecords();
        $this->beforeRender();
        $fAvailable = Account::getAccountInfoByUserId(Session::get('user_id'), ['available'])->available;
        $this->setVars('fAvailable', $fAvailable);




        $oUser = User::find(Session::get('user_id'));
        $bFirstLogin = Session::get('first_login');
        Session::forget('first_login');

        $this->getAgentCenterData($oUser);

//        $users=User::getAllUsersBelongsToAgent($oUser->id);
//        $iUserChildrenNum =count(User::getAllUsersBelongsToAgent($oUser->id));

        $sViewFileName = 'agentCenter';
//        if (!Session::get('is_player')) {
//            $sViewFileName = 'agentCenter';
//        }else{
//            $sViewFileName = 'index-v3';
//        }
//        if (Session::get('is_player')) {
//        $aLatestProjects     = UserProject::getLatestRecords(4);
//        $aLatestTraces       = UserTrace::getLatestRecords(4);
//        $aTransactionTypes   = TransactionType::getAllTransactionTypesArray();
//        $aLatestTransactions = UserTransaction::getLatestRecords(4);
        //七日累计中奖
//        $weekStart = date('Y-m-d', strtotime( date("Y-m-d").' -6 days'));
//        $iThisWeekPrize = UserProfit::getUserTotalPrize(Session::get('user_id'), $weekStart, date("Y-m-d"));
        //获取圆角分
        //todo
        //route('home.set-cache')
        //$this->getCache(Config::get('var.js_cache_key')['game_id']);
//        }

        return View::make($sViewFileName)->with(
                        $this->viewVars +
                        compact('bFirstLogin', 'aADInfo')
        );
    }

    //默认代理中心要加载 的数据, todo
    private function getAgentCenterData(User $oUser) {
        $iAgentCount = UserOnline::getTeamAgentOnlineNum($oUser->id);
        $iPlayerCount = UserOnline::getTeamPlayerOnlineNum($oUser->id);
        $this->setVars('iAgentCount', $iAgentCount);
        $this->setVars('iPlayerCount', $iPlayerCount);
        $this->setVars('iUserOnline', $iAgentCount + $iPlayerCount);
        //echo '代理'.$iAgentCount;
        // echo '---玩家'.$iPlayerCount;

        $aOverLimits = OverlimitPrizeGroup::getPrizeGroupByTopAgentId($oUser->id);
        $this->setVars(compact("aOverLimits"));
        $cache_key = "aCommissionAndProfit_" . $oUser->id . '_tmp';
//	Cache::forget($cache_key);
        $aCommissionAndProfit = Cache::get($cache_key);
        if (!$aCommissionAndProfit) {
            $aCommissionAndProfit = UserProfit::getCurrentMonthData($oUser->id);
            $aCommissionAndProfit['cached_time'] = time();
            Cache::put($cache_key, $aCommissionAndProfit, Carbon::now()->addMinutes(60));
        }
        $aCommissionAndProfit['cached_before_minutes'] = floor((time() - $aCommissionAndProfit['cached_time'] ) / 60) + 1;
        $this->setVars('aCommissionAndProfit', $aCommissionAndProfit);


        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
//        pr($aMsgTypes);exit;
        $aResults = UserMessage::getUserLatest10UnreadMessages($oUser->id);
        $aStationLetters = [];
        foreach ($aResults as $oData) {

            $aStationLetters[] = [
                'msg_title' => $oData->msg_title,
                'id' => $oData->id,
                'msg_type' => $aMsgTypes[$oData->type_id],
                'updated_at' => date('m-d', strtotime($oData->updated_at))
            ];
        }
        $this->setVars('aStationLetters', $aStationLetters);
        //只有总代显示分红信息
//            $pointDay  = date('Y-m-05 12:00:00',time());
//            $this->setVars('bonus_day_diff',  getDiffByTwoDate(date('Y-m-d H:i:s'), $pointDay));
//            
        $this->setVars('agent_type', $oUser->getUserType());
    }

    //获取我的团队数据 Ajax
    public function getMyTeamData($period = 'today') {

        //period参数标识今日，本月或本周
        // $period = Input::get('period')  or $period = 'today';
        $iFromDate = 0;
        $iEndDate = time();

        switch ($period) {
            case 'today':
            default:
                $iFromDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                break;
            case 'week':

                $iFromDate = mktime(0, 0, 0, date("m"), date("w", 0), date("Y"));

                // $iFromDate = mktime(0,0,0,$date->format('m'),1,$date->format('Y'));
                break;
            case 'month':
                $iFromDate = mktime(0, 0, 0, date('m'), 1, date('Y'));
                break;
        }
        $iUserId = Session::get('user_id');
//        $cache_key = $iUserId ."_" .$period."_".$iFromDate."_".$iEndDate;
//        $aData = Cache::get($cache_key);
//        if(!$aData){
        $aData = AgentCreateStat::getMyTeamData($iUserId, $iFromDate, $iEndDate);
//        }
        return Response::json($aData);
    }

    /**
     * 获取团队排名
     * @return type
     */
    public function getAgentMonthRank($rankby = 'sale') {
        //rankby字段标识以什么为排序
        // $rankBy = strtolower(Input::get('rankby')) or $rankBy = 'sale';
//        echo $rankby;exit;
        $iCurrentUserId = Session::get('user_id');
        //  $cache_key = "agent_month_rank_".$iCurrentUserId."_".$rankby;
        //  $aData = Cache::get($cache_key);
        //   if(!$aData){
//            echo 'new';
        $aData = AgentCreateStat::getMyAgentRank($rankby, $iCurrentUserId);
        //       $expiresAt = Carbon::now()->addMinutes(60);
        //       Cache::put($cache_key,$aData,$expiresAt);
        //   }

        foreach ($aData as $key => $oA) {
            $oUser = User::find($oA['user_id']);
            $aData[$key]['group_balance_sum'] = number_format($oUser->getGroupAccountSum(), 2);
            $aData[$key]['direct_child_num'] = $oUser->getUserCountsBelongsToAgentId();
            $aData[$key]['user_level_txt'] = ($oUser->is_agent) ? "代理" : "玩家";
            if ($rankby != 'newaccount')
                $aData[$key]['data'] = number_format($oA['data'], 2);
            else
                $aData[$key]['data'] = $oA['data'];
        }

        return Response::json($aData);
    }

    /**
     * 获取30天内的团队销量
     * @param int $iCount
     * @return mixed
     */
    public function getUserMonthTeamTurnover($iCount = 30, $sDay = 10) {
        $oCarbonNow = Carbon::now();
//        //测试数据
//        $oCarbonNow = Carbon::createFromDate(2015, 07, 01);
        //分红截止日期
        if ($oCarbonNow->day > $sDay) {
            $iCount = $oCarbonNow->day > $iCount ? $oCarbonNow->daysInMonth : $iCount;
            $oCarbonNow = Carbon::createFromDate($oCarbonNow->year, $oCarbonNow->month, $oCarbonNow->daysInMonth);
        }
        $sEndDate = $oCarbonNow->toDateString();
        //
        $aDatas[$oCarbonNow->toDateString()] = [$oCarbonNow->toDateString(), 0];
        for ($i = 1; $i < $iCount; $i++) {
            $oCarbonNow->subDays(1);
            $aDatas[$oCarbonNow->toDateString()] = [$oCarbonNow->toDateString(), 0];
        }
//        var_dump($aDatas);exit;
        $sBeginDate = $oCarbonNow->toDateString();
        //获取团队月销量
        $oMonthTurnovers = UserProfit::getUserMonthTeamTurnover($sBeginDate, $sEndDate, Session::get('user_id'));
        foreach ($oMonthTurnovers as $data) {
            //$aDatas[$data->date][1] = $data->team_turnover;
            $aDatas[$data->date][1] = $data->team_turnover + $data->turnover;
        }
        $aDatas = array_reverse(array_values($aDatas));
//        $aDatas = array_reverse($aDatas);
        return Response::json($aDatas);
    }

    public function getClientIndex() {
        $oNumberTopLottery = LotteryArticle::getNewsNumberTopArticle();     //获取最近的6条记录
        $oJcTopLottery = LotteryArticle::getNewsJcTopArticle();     //获取最近的6条记录
        $oNumberLottery = LotteryArticle::getNewNumberArticle();
        $oJcLottery = LotteryArticle::getNewJcArticle();
        $oCmsArticle = CmsArticle::getNewArticle();
//        $oLotteryInfoCate = LotteryCategory::getCategoryPid();
//        pr($oLotteryInfoCate);exit();
//        $aNumberLottery = $this->sortLotteryNews($oNumberTopLottery, $oNumberLottery);
//        $aJcData =  $this->sortLotteryNews($oJcTopLottery, $oJcLottery);
        //获取昨日竞彩赛事
        $date = date('Y-m-d', strtotime('-1day'));
        $startDate = $date . " 00:00:00";
        $endDate = Date('Y-m-d H:i:s', time());
        $oLastMatchInfo = JcModel\JcMatchesInfo::getLastMatchInfo($startDate, $endDate);
        $aTeamIds = [];
        foreach ($oLastMatchInfo as $k => $v) {
            $aTeamIds[$v->home_id] = $v->home_id;
            $aTeamIds[$v->away_id] = $v->away_id;
        }
        $oTeams = \JcModel\JcTeam::getByIds($aTeamIds);
        $aTeamList = [];
        foreach ($oTeams as $oTeam) {
            $aTeamList[$oTeam->id] = $oTeam;
        }

//        pr($aTeamList);
//        $aAllTeams = JcModel\JcTeam::getAllTeams();
        //合买
        $oRecommendLottery = \JcModel\JcLotteries::getByLotteryKey('football');
        $iRecommendLotteryId = $oRecommendLottery->id;
        $aRecommendGroupBuys = \JcModel\JcUserGroupBuy::getForIndexByLotteryId($iRecommendLotteryId, '', 5);
        foreach ($aRecommendGroupBuys as $oGroupBuy) {
            $aExtraData = [
                'lottery_id' => $iRecommendLotteryId,
                'user_id' => $oGroupBuy->user_id,
            ];
            $oGroupBuy->user_extra = new \JcModel\JcUserExtra($aExtraData);
            $oGroupBuy->project_count = \JcModel\JcUserProject::getCountByGroupId($oGroupBuy->id);
        }
        //推荐赛事
        $oRecommendMatches = \JcModel\JcUserMatchesInfo::getSellingMatch(['match_id']);
        if (count($oRecommendMatches) < 1) {
            //在售赛事数量不足时，取最新赛事
            $oRecommendMatches = \JcModel\JcUserMatchesInfo::getLastMatches(['match_id']);
        }
        $aRecommendMatchIds = [];
        foreach ($oRecommendMatches as $oMatch) {
            $aRecommendMatchIds[$oMatch->match_id] = [$oMatch->match_id];
        }
        $aMatchIds = array_rand($aRecommendMatchIds, 1);
        $aMatchIds = [$aMatchIds];
        $aRecommendMatches = \JcModel\JcUserMatchesInfo::getByMatchIdsWithLeagueAndTeam($aMatchIds);
        $oMethod = \JcModel\JcMethod::getMethodByIdentifier($oRecommendLottery->id, \JcModel\JcMethod::STRING_IDENTIFIER_WIN);
        $aOdds = JcModel\JcOdds::getOddsByMatchIdsAndMeothid($aMatchIds, $oMethod->id);
        foreach ($aOdds as $k => $v) {
            $aOddAndStatus[$k]['status'] = JcModel\JcMethod::getNameWin($v->code);
            $aOddAndStatus[$k]['odd'] = $v->odds;
        }
        $aRecommendMatches[0]['odds'] = $aOddAndStatus;
        $totalMatch = \JcModel\JcUserMatchesInfo::countSellingMatch();
        //热销彩种
        $oCqssc = Issue::getLastestIssue(1);        //重庆时时彩
        $oFd = Issue::getLastestIssue(13);          //重庆时时彩
//        pr($aRecommendMatches);exit();
        //开奖公告
        $aIssue = [];
        $aLotteryId = [1, 9, 13];
        $oIssue = new Issue;
        $oLottery = Lottery::getLotteriesByLotteryIds($aLotteryId);
        foreach ($oLottery as $k => $v) {
            $aGetIssue = $oIssue->getIssueArrayForWinNum($v->id, 1);
            if (!count($aGetIssue)) {
                continue;
            }
            $aGetIssue[0]['id'] = $v->id;
            $aGetIssue[0]['name'] = $v->friendly_name;
            $aWinNumber = explode(" ", $aGetIssue[0]['code']);      //因为广东十一选五的开奖号码规则不同所以加这个区分
            if (count($aWinNumber) > 1) {
                $aGetIssue[0]['code'] = $aWinNumber;
            } else {
                $aWinNumberLen = strlen($aGetIssue[0]['code']);
                $aCode = [];
                for ($i = 0; $i < $aWinNumberLen; $i++) {
                    $aCode[$i] = $aGetIssue[0]['code']{$i};        //开奖号码
                }
                $aGetIssue[0]['code'] = $aCode;
            }
            $aIssue[] = $aGetIssue[0];
        }

        $iHistoryPrize = self::getHistroyPrize();

        $sViewFileName = 'indexClient';
        $this->setVars(compact('oCmsArticle', 'aLatestAnnouncements', 'oNumberTopLottery', 'oJcTopLottery', 'oNumberLottery', 'oJcLottery', 'oLastMatchInfo', 'aTeamList', 'aRecommendGroupBuys', 'aRecommendMatches', 'totalMatch', 'aIssue', 'oCqssc', 'oFd', 'oLotteryInfoCate', 'iHistoryPrize'));
        if (Session::get('user_id')) {
            $this->beforeRender();
        }
        return View::make($sViewFileName)->with($this->viewVars);
    }

    protected static function getHistroyPrize() {
        $iHistoryPrize = SysConfig::readValue('history_prize');
        $dHistoryPrizeStartTime = SysConfig::readValue('history_prize_start_time');
        $iUnixTimeToday = strtotime(date('Y-m-d'));
        $iUnixTimePrizeStartTime = strtotime($dHistoryPrizeStartTime);
        if (!$iHistoryPrize || !$iUnixTimePrizeStartTime) {
            return 0;
        }
        if ($iUnixTimeToday > $iUnixTimePrizeStartTime) {
            $iDiffDays = ceil(($iUnixTimeToday - $iUnixTimePrizeStartTime) / 3600 / 24);
            for ($i = 0; $i < $iDiffDays; $i++) {
                $iHistoryPrize += mt_rand(20, 50);
            }
            SysConfig::setValue('history_prize', $iHistoryPrize);
            SysConfig::setValue('history_prize_start_time', date('Y-m-d'));
            $iHistoryPrize = min($iHistoryPrize, 999999);
        }
        return $iHistoryPrize;
    }

//    private function sortLotteryNews($topArticle, $newArticle){
//          $i = 0;
//          $aData = [];
//          foreach ($topArticle as $k=>$v) {
//            $aData[$i]['id'] = $v->id;  
//            $aData[$i]['title'] = $v->title;  
//            $i++;
//        }
//        foreach ($newArticle as $k=>$v) {
//            $aData[$i]['id'] = $v->id;  
//            $aData[$i]['title'] = $v->title;  
//            $i++;
//        }
//        return $aData;
//    }
}
