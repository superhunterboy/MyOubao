<?php

/**
 * 投注
 */
class BetController extends UserBaseController {

    protected $errorFiles = [
        'system',
        'bet',
        'fund',
        'account',
        'lottery',
        'issue',
        'seriesway'
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
    public function bet($iLotteryId) {
        //后台是否禁止投注
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }

        if ($oUser->blocked == UserUser::BLOCK_BUY) {
            return $this->goBack('error', __('_user.bet-not-allowed'));
        }

        $oLottery = Lottery::find($iLotteryId);

        if (empty($oLottery)) {
            $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);
        }
     
          $bPost = Request::method() == 'POST';

        if ($oLottery->status == Lottery::STATUS_NOT_AVAILABLE) {
            return $this->goBack('error', __('_lottery.not-available', ['lottery' => $oLottery->friendly_name]));
        }
        $iNeedStatus = Session::get('is_tester') ? Lottery::STATUS_AVAILABLE_FOR_TESTER : Lottery::STATUS_AVAILABLE;
        if ($oLottery->status && !Session::get('is_tester') && $oLottery->status != $iNeedStatus) {
            return $this->goBack('error', __('_lottery.not-available', ['lottery' => $oLottery->friendly_name]));
        }

        if ($bPost) {
            $this->doBet($oLottery);
            exit;
        } else {

            $oSeries = Series::find($oLottery->series_id);

            if ($oSeries->is_muti_games) {
                $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);
            }

            return $this->betForm($oLottery);
        }
    }

    /**
     * 投注方法 [ Refactor ]
     * @param int $iLotteryId
     * @return mixed
     */
    public function bets($iSeriesId) {
        //后台是否禁止投注
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }

        if ($oUser->blocked == UserUser::BLOCK_BUY) {
            return $this->goBack('error', __('_user.bet-not-allowed'));
        }

//        $oSeries = Series::find($iSeriesId);
        $aConditions = [
            'id' => $iSeriesId,
            'is_muti_games' => 1
        ];
        $oSeries = Series::doWhere($aConditions)->first();
        if (empty($oSeries)) {
            $this->halt(false, 'series-missing', Series::ERRNO_SERIES_MISSING);
        }

        return $this->betsForm($oSeries);
    }

    /**
     * [getUserProjectsAndTraces 获取投注定时轮询最近的投注和追号数据 | Get betting timed bets and recent polling data recovery number]
     * @param  [Int] $iLotteryId [彩种id]
     * @return [Json]             [轮询数据]
     */
    public function getUserProjectsAndTraces($iLotteryId = null, $bResponse = true) {

        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        if (!is_object($oUser)) {
            $aReturnMsg = [
                'isSuccess' => 0,
                'type' => 'error',
                'data' => __('_user.missing-user')
            ];
            return Response::json($aReturnMsg);
        }

//        if (Session::get('is_agent')) {
//            $aReturnMsg = [
//                'isSuccess' => 0,
//                'type' => 'error',
//                'data' => __('_basic.no-rights')
//            ];
//
//            return Response::json($aReturnMsg);
//        }

        $sType = trim(Input::get('type'));
        $iNum = trim(Input::get('num', 10));

        $aResponse = [
            'isSuccess' => 1,
            'type' => 'success',
            'data' => []
        ];

        switch ($sType) {
            case 'bets':
                $aProjects = UserProject::getRecordsByParams($iLotteryId, $iNum);
                $aResponse['data'][] = ['type' => $sType, 'data' => $this->generateBetRecordArray($aProjects)];
                break;

            case 'traces':
                $aTraces = UserTrace::getRecordsByParams($iLotteryId, $iNum);
                $aResponse['data'][] = ['type' => $sType, 'data' => $this->generateTraceRecordArray($aTraces)];
                break;

            default:
                $aProjects = UserProject::getRecordsByParams($iLotteryId, $iNum);
                $aResponse['data'][] = ['type' => 'bets', 'data' => $this->generateBetRecordArray($aProjects)];
                $aTraces = UserTrace::getRecordsByParams($iLotteryId, $iNum);
                $aResponse['data'][] = ['type' => 'traces', 'data' => $this->generateTraceRecordArray($aTraces)];
                break;
        }

        if($bResponse)
            return Response::json($aResponse);
        else
        {
            return $aResponse['data'][0]['data'];
        }
    }


    public function getTrendGraph($iLotteryId){
        $oLottery = Lottery::find($iLotteryId);

        $datas = [];
        $oIssue = new Issue;
        $aIssues = array_reverse($oIssue->getIssueArrayForWinNum($oLottery->id, 120));

        $betCode = ['zhuangdui'=>'5','xiandui'=>'6'];
        $betCodeUnique = ['zhuangxianhe' => ['0','1','2']];
        $betCodeMerge = [];
        foreach($betCodeUnique as $name => $aCode) $betCodeMerge = array_merge($betCodeMerge, $aCode);

        foreach($aIssues as $aIssue)
        {
            $data = [];
            $aBetPrizes = $this->_getWinListForBet($oLottery, $aIssue['number'], $aIssue['code']);

            foreach($aBetPrizes as $seriesWayId => $aCodes)
            {
                foreach($aCodes as $sCode =>$aIsWin)
                {
                    if(in_array($sCode, $betCode)){
                        $name = array_keys($betCode, $sCode);
                        $data[ $name[0] ] = $aIsWin['is_win'];
                    }

                    elseif(in_array($sCode, $betCodeMerge))
                    {
                        foreach($betCodeUnique as $name => $aCode){
                            if($aIsWin['is_win']){
                                $data[$name] = $sCode;
                            }
                        }
                    }
                }
            }

            if($data) $datas[] = $data;
        }
        return Response::json($datas);
    }

    public function getIssueHistoryCount($iLotteryId, $iIssueCount = 60)
    {
        $aHistorys = [];

        //最近一次的奖期
        if(!$currIssue = Issue::getLastestIssue($iLotteryId)){
            return $aHistorys;
        }
        if($iIssueCount > 200) $iIssueCount = 200;

        $key = 'history_lottery_'.$iLotteryId. '_issue_'.$currIssue->id.'_count_'.$iIssueCount. '_maps';
        if ($history = Cache::get($key)){
            return $history;
        }

        $oLottery = Lottery::find($iLotteryId);

        $oIssue = new Issue;

        $aColdIssues = array_reverse($oIssue->getIssueArrayForWinNum($oLottery->id, 350));

        //计算冷号
        foreach($aColdIssues as $i => $aIssue)
        {
            $aWinList = $this->_getWinListForBet($oLottery, $aIssue['number'], $aIssue['code']);

            foreach($aWinList as $seriesIds=>$seriesInfo)
            {
                foreach ($seriesInfo as $betNumber => $betInfo)
                {
                    if($betInfo['is_win'])
                    {
                        if(($i + $iIssueCount) >= count($aColdIssues))
                        {
                            if(isset($aHistorys[$seriesIds][$betNumber]['win_count']))
                            {
                                $aHistorys[$seriesIds][$betNumber]['win_count']++;
                            }
                            else{
                                $aHistorys[$seriesIds][$betNumber]['win_count'] = 1;
                            }
                        }

                        $aHistorys[$seriesIds][$betNumber]['omission_count'] = 0;
                    }
                    else{
                        if(($i + $iIssueCount) >= count($aColdIssues))
                        {
                            if(! isset($aHistorys[$seriesIds][$betNumber]['win_count']))
                            {
                                $aHistorys[$seriesIds][$betNumber]['win_count'] = 0;
                            }
                        }

                        if(isset($aHistorys[$seriesIds][$betNumber]['omission_count']))
                        {
                            $aHistorys[$seriesIds][$betNumber]['omission_count']++;
                        }
                        else{
                            $aHistorys[$seriesIds][$betNumber]['omission_count'] = 1;
                        }

                    }
                }
            }
        }

        //热号公式：(出现次数/奖期次数)/中奖概率
        //冷号公式：(1-中奖概率）^ 遗漏次数
        //先计算冷，再计算热
        $aIssueHistoryCount = [];
        $aOseriesWays = [];
        foreach($aHistorys as $wayId =>$seriesInfo) {
            if(!isset($aOseriesWays[$wayId]))
                $aOseriesWays[$wayId] =  SeriesWay::find($wayId);
            if(!isset($aOseriesWays[$wayId]["oPrizeLevels"]))
                $aOseriesWays[$wayId]["oPrizeLevels"] = PrizeLevel::where('basic_method_id', $aOseriesWays[$wayId]->basic_methods)->orderBy('level','desc')->first();
            $fProbability = $aOseriesWays[$wayId]["oPrizeLevels"]->probability;

            foreach ($seriesInfo as $betNumber => $betInfo){
                if(!isset($betInfo['win_count'])){
                    $betInfo['win_count'] = 0;
                }

                $isHot = pow(1-$fProbability, $betInfo['omission_count']) <= 0.2 ? 0 : ($betInfo['win_count']/$iIssueCount/$fProbability >= 1.3 ? 2 : 1);
                $aIssueHistoryCount[$wayId][$betNumber]['is_hot'] = $isHot;
//                $aHistorys[$wayId][$betNumber]['probability'] = $fProbability;

            }

        }




        /*            $oSeriesWays = SeriesWay::where('series_id', $oLottery->series_id)->get();

                $aIssueHistoryCount = [];
                foreach($oSeriesWays as $oSeriesWay)
                {
                    $oBasicWay = BasicWay::find($oSeriesWay->basic_way_id);
                    $oBasicMethod = BasicMethod::find($oSeriesWay->basic_methods);
                    foreach($aIssues as $aIssue){
                        $sWnNumber = $aIssue['code'];
                        $aWinList = $this->_getWinListForBet($oBasicMethod, $oBasicWay, $oSeriesWay, $sWnNumber);
                        $aWinListKeys = array_keys($aWinList);
                        foreach($aWinListKeys as $iNum){
                            if (isset($aIssueHistoryCount[$oSeriesWay->id][$iNum])){
                                $aIssueHistoryCount[$oSeriesWay->id][$iNum]++;
                            }else{
                                $aIssueHistoryCount[$oSeriesWay->id][$iNum] = 1;
                            }
                        }
                    }
                }*/

        $response = Response::json($aIssueHistoryCount);
        Cache::put($key, $response, 20);
        return $response;
    }

    private function generateBetRecordArray($oRecords) {
        $aData = [];
        $aLotteries = Lottery::getLotteryList();

        foreach ($oRecords as $key => $oRecord) {
            $aRecord = [
                'id' => $oRecord->id,
                'prizeballs' => $oRecord->winning_number ? $oRecord->winning_number : '',
                'gamename' => $aLotteries[$oRecord->lottery_id],
                'method' => $oRecord->title,
                'number' => $oRecord->issue,
                'balls' => $oRecord->display_bet_number,
                'money' => sprintf("%.2f", $oRecord->amount),
                'prize' => sprintf("%.2f", $oRecord->prize),
                'commission' => $oRecord->commission_percents,
                'status' => $oRecord->formatted_status,
                'statuscode' => $oRecord->status,
                'bought_at' => $oRecord->bought_at,
                'is_overprize' => $oRecord->is_overprize,
            ];
            $aData[] = $aRecord;
        }
        return $aData;
    }

    /**
     * 生成赌桌版数据
     * @param $oLottery
     * @return mixed
     */
    private function _getGameWinNumberSettings($oLottery){

        $data = [
            'balance'         => Account::getAvaliable(Session::get('user_id')),
        ];

        //最近一次的奖期
        if(!$currIssue = Issue::getLastestIssue($oLottery->id)){
            return $data;
        }
        //最新的奖期
        $lastIssue = Issue::where('lottery_id', '=', $oLottery->id)->where('issue', '>', $currIssue->issue)->orderBy('id', 'asc')->first();
        $currIssue->issue == $lastIssue->issue or $data['nextNumber'] = $lastIssue->issue;

        $data['currNumber'] = $currIssue->issue;

        //1(可下注)、2(不可下注但是尚未完成计奖派奖)、3(已完成派奖处于等待前端完成动画)、4(撤奖)
        $curTime = time();

        if($currIssue->status == Issue::ISSUE_CODE_STATUS_CANCELED)
        {
            $aProjects = Project::getProjectIdByLotteryIdAndIssue($oLottery->id, $currIssue->issue);

            //如果还有注单没有给用户退款，则status为2，否则status为4
            if($aProjects->count() > 0 || $currIssue->issue == $lastIssue->issue){
                $data['status'] = 2;
                $data['leftTime'] = 0;
            }else{
                $leftTime = $lastIssue->begin_time - $curTime;
                $data['status'] = 4;
                $data['leftTime'] = $leftTime > 0 ?  $leftTime : 0 ;
            }
        }
        elseif($curTime >= $currIssue->begin_time && $curTime < $currIssue->end_time)
        {
            $data['status'] = 1;
            $data['leftTime'] = $currIssue->end_time - $curTime;
        }
        elseif($curTime >= $currIssue->end_time && $currIssue->status_count != Issue::CALCULATE_FINISHED)
        {
            $data['status'] = 2;
            $data['leftTime'] = 0;
        }
        else{
            $data['status'] = 3;
            $data['leftTime'] = $lastIssue->begin_time - $curTime; //todo
            if($data['leftTime']>12)
            {
            	$data['leftTime']=12;
            }
            $data['leftTime'] >= 0 or $data['leftTime'] = 0;
        }
        $data['status_prize'] = $currIssue->status_count;
        if($currIssue->status_count != Issue::CALCULATE_FINISHED){
            return $data;
        }

        $data['win_number'] = $currIssue->wn_number;

        $data['bet_prize'] = $this->_getWinListForBet($oLottery, $currIssue->issue, $currIssue->wn_number);
        if($oLottery->id >= 44){
            $data['currentTrend'] = $this->getTrendGraph4baijiale($oLottery,$currIssue);

        }
        return $data;
    }

    /**
     * 按开奖号码生成赌桌输赢数据
     * @param $oLottery
     * @param $sIssue
     * @param $sWnNumber
     * @return array
     */
    private function _getWinListForBet(& $oLottery, $sIssue, $sWnNumber){

        $key = 'win_number_lottery_'.$oLottery->id.'_issue_'.$sIssue. '_maps';
        if ($betPrize = Cache::get($key)){
            return $betPrize;
        }

        //选择的玩法 单挑一骰 大小单双
        $oSeriesWays = SeriesWay::where('series_id', $oLottery->series_id)->get();

        $aBetPrize = [];

        foreach($oSeriesWays as $oSeriesWay)
        {
            $oBasicWay = BasicWay::find($oSeriesWay->basic_way_id);
            $oBasicMethod = BasicMethod::find($oSeriesWay->basic_methods);

            if ($oBasicMethod->choose_count <= 0) {
                continue;
            }

            $aNumbers = $this->getNumberMaps($oBasicWay->function, $oBasicMethod);

            foreach($aNumbers as $sNumber){
                $aBetPrize[$oSeriesWay->id][$sNumber]['is_win'] = 0;
            }

            $sWningNumber = $oBasicMethod->getWinningNumber($sWnNumber);

            if($sWningNumber === false) continue;

            foreach($aNumbers as $sBetNumber)
            {
                if($oBasicMethod->countBetNumber($oBasicWay->function, $sBetNumber) && $oBasicMethod->checkPrize($oSeriesWay,$oBasicWay,$sWningNumber,$sBetNumber))
                {
                    $aBetPrize[$oSeriesWay->id][$sBetNumber]['is_win'] = 1;
                }
                else{
                    $aBetPrize[$oSeriesWay->id][$sBetNumber]['is_win'] = 0;
                }
            }

            Cache::put($key, $aBetPrize, 700);

            /*            $aCombinations = Math::getCombinationToString(str_split($sWnNumber), $oBasicMethod->choose_count);

                        foreach($aCombinations as $i => $sCombination){
                            $aCombinations[$i] = str_replace(',','',$sCombination);
                        }
                        $aCombinations = array_unique($aCombinations);

                        foreach($aCombinations as $sBetNumber)
                        {
                            $oBasicMethod->countBetNumber($oBasicWay->function, $sBetNumber);
                            echo $sBetNumber;exit;
                            if($oBasicMethod->countBetNumber($oBasicWay->function, $sBetNumber) && $oBasicMethod->checkPrize($oSeriesWay,$oBasicWay,$sWnNumber,$sBetNumber))
                            {
                                $aBetPrize[$oSeriesWay->id][$sBetNumber]['is_win'] = 1;
                            }
                            else{
                                $aBetPrize[$oSeriesWay->id][$sBetNumber]['is_win'] = 0;
                            }
                            echo $sBetNumber."<br>";
                        }*/


            //$aWinList = $this->_getWinListForBet($oBasicMethod, $oBasicWay, $oSeriesWay, $currIssue->wn_number);
            /*            if ($aWinList){
                            $aBetPrize[$oSeriesWay->id] = $aWinList;
                        }*/
        }
        return $aBetPrize;

    }

    private function getNumberMaps($function, $oBasicMethod){

        $aNumberList = [];
        $aAreaNum = explode('-', $oBasicMethod->valid_nums);

        if(count($aAreaNum) == 2)
        {
            for($i=$aAreaNum[0]; $i<=$aAreaNum[1]; $i++) $aNumberList[] = strval($i);
        }
        else{
            $aNumberList[] = $aAreaNum[0];
        }

        switch ($function){
            case 'Sum' /*和值 */:
            case 'BigSmallOddEven' /*大小单双*/:
            case 'SpecialConstituted' /*三星通选 */:
            case 'TwoStarSpecial' /*二星特殊 */:
            case 'BjlEnum' /*百家乐和值 */:
                break;

            default:
                //三同号 二同号
                if ($oBasicMethod->choose_count == $oBasicMethod->min_repeat_time && $oBasicMethod->min_repeat_time == $oBasicMethod->max_repeat_time){
                    foreach($aNumberList as $i => $iNumber){
                        $aNumberList[$i] = str_repeat( $iNumber, $oBasicMethod->choose_count);
                    }
                }else{
                    $aCombinations = Math::getCombinationToString($aNumberList, $oBasicMethod->choose_count);
                    $aNumberList = [];
                    foreach($aCombinations as $sCombination){
                        $aNumberList[] = str_replace(',','',$sCombination);
                    }
                }
                break;
        }

        return $aNumberList;
    }

    private function plusGameWinNumberSettings(& $oLottery, $sIssue, & $data){

        //投注信息
        $aProjects = UserProject::getRecordsByIssue($oLottery->id, $sIssue);

        $winAmount = 0;
        if($data['bet_count'] = $aProjects->count()){
            foreach($aProjects as $oProject){
                if($oProject->prize) $winAmount += $oProject->prize;

                $data['bet_prize'][$oProject->way_id][$oProject->bet_number]['bet_amount'] = $oProject->amount;
                $data['bet_prize'][$oProject->way_id][$oProject->bet_number]['win_amount'] = $oProject->prize;
            }
        }
        $data['win_amount'] = $winAmount;

        return $data;
    }


    private function generateTraceRecordArray($oRecords) {
        $aData = [];
        $aLotteries = Lottery::getLotteryList();
        foreach ($oRecords as $key => $oRecord) {
            $aRecord = [
                'id' => $oRecord->id,
                'gamename' => $aLotteries[$oRecord->lottery_id],
                'method' => $oRecord->title,
                'startnumber' => $oRecord->start_issue,
                'progress' => $oRecord->finished_issues . '/' . $oRecord->total_issues,
                'amount' => $oRecord->amount,
                'prize' => $oRecord->prize,
                'iswinstop' => $oRecord->formatted_stop_on_won,
                'status' => $oRecord->formatted_status,
                'statuscode' => $oRecord->status,
            ];
            $aData[] = $aRecord;
        }
        return $aData;
    }

    /**
     * 格式化投注数据 | Betting data format
     * @param Lottery $oLottery
     * @param array $aBetData
     * @param array $aBetNumbers &
     * @param array $aOrders &
     * @param array $aSeriesWays &
     */
    private function compileBetData($oLottery, $aBetData, & $aBetNumbers, & $aOrders, & $aSeriesWays) {
//        $aIssues = $this->getIssuesForBet($oLottery->id, 1);
        $aBetNumbers = $aOrders = [];
//        $sSplitChar = Config::get('bet.split_char') or $sSplitChar = '|';
        foreach ($aBetData['balls'] as $aBetNumber) {
            $oSeriesWay = isset($aSeriesWays[$aBetNumber['wayId']]) ? $aSeriesWays[$aBetNumber['wayId']] : ($aSeriesWays[$aBetNumber['wayId']] = SeriesWay::find($aBetNumber['wayId']));
            if (empty($oSeriesWay)){
                $this->writeLog('error: empty series way. bet number: ' . var_export($aBetNumber, 1));
                return false;
            }
            $sBetNumber = $oSeriesWay->compileBetNumber($aBetNumber['ball']);

            if (isset($sBetNumber) && $sBetNumber === '') {
                return false;
            }

            $data = [
                'way' => $aBetNumber['wayId'],
                'bet_number' => $sBetNumber,
                'coefficient' => formatNumber($aBetNumber['moneyunit'], 2),
                'multiple' => $aBetNumber['multiple'],
                'single_count' => $aBetNumber['num'],
                'amount' => $aBetNumber['num'] * $aBetNumber['multiple'] * 2,
                'bet_source' => $aBetData['bet_source'],
//                'amount' => $aBetNumber['amount'],
//                'price' => $aBetNumber['price'],
                'price' => 2,
                'prize_group' => $aBetNumber['prize_group'],

            ];

            if($oLottery->series_id == 20) $data['price'] = 1;

            if (isset($aBetData['is_encoded'])){
                $data['is_encoded'] = $aBetData['is_encoded'];
            }

            if(isset($aBetNumber['position']) && is_array($aBetNumber['position']) ){
                $aPosition = [];
                foreach($aBetNumber['position'] as $key=>$bol){
                    if($bol) array_push($aPosition,$key);
                }
                $data['position'] = implode("",$aPosition); // 123 012
            }else
                $data['position'] = "";

            $aBetNumbers[] = $data;
        };

        $aOrders = $aBetData['orders'];
        ksort($aOrders, SORT_REGULAR);
        return true;
    }
    private function getFinishedIssueBySeriesId($aFinishedIssuesArr,$series_id){

        if($series_id==19){
            $sFinishedIssues = implode('|', $aFinishedIssuesArr);
        }else{
            $sFinishedIssues = implode(',', $aFinishedIssuesArr);
        }
        return $sFinishedIssues;
    }
    /**
     * display bet form
     * @param Lottery $oLottery
     */
    private function betForm($oLottery) {
        if (!$aGameConfig = & $this->_getGameSettings($oLottery)) {
            //print_r($aGameConfig);
            //exit;
            $this->halt(false, 'prize-missing --- #1', UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
        }

        $sLotteryConfig = json_encode($aGameConfig);
        $iLotteryId = $oLottery->id;
        $sLotteryCode = ($oLottery->identifier);
        $sLotteryName = ($oLottery->friendly_name);


        $oIssue = new Issue;
        $aFinishedIssues = $oIssue->getIssueArrayForWinNum($iLotteryId, 10);
        $aFinishedIssuesArr = [];

        foreach ($aFinishedIssues as $key => $aIssue) {
            $aFinishedIssuesArr[] = $aIssue['number'] . '=' . $aIssue['code'];
        }

        $sFinishedIssues = $this->getFinishedIssueBySeriesId($aFinishedIssuesArr,$oLottery->series_id);
        // pr($aFinishedIssues);exit;


        $aLastBetHistory = $this->getLastBetHistory($iLotteryId);
        $sLastBetHistory = json_encode($aLastBetHistory);

        $WEB_SOCKET_SERVER = SysConfig::readValue('WEB_SOCKET_SERVER');
        $ways_note_url = Config::get('ways_note_url.'.$oLottery->series_id);
        $this->setVars(compact('sLotteryConfig', 'iLotteryId', 'sLotteryName', 'sLotteryCode', 'sLastBetHistory', 'sFinishedIssues'
            ,'WEB_SOCKET_SERVER','ways_note_url'));
        $this->setVars(compact('sLotteryConfig', 'iLotteryId', 'sLotteryName', 'sLotteryCode', 'sLastBetHistory', 'sFinishedIssues'
        ));

        $oSeries = Series::find($oLottery->series_id);
        $this->view = $this->customViewPath . '.' . strtolower($oSeries->identifier);



        //$this->view = 'centerGame.dice';
        //print_r($this->view);
        //exit;

        /* pr($aGameConfig);exit; */
        /*  die($this->view);  */
        return $this->render();
    }

    /**
     * display bet form
     * @param Lottery $oLottery
     */
    private function betsForm($oSeries) {
        $aGameConfig = [];
        $aLotteryCodes = [];
        $aLotteryNames = [];
        $aFinishedIssues = [];
        $aLastBetHistory = [];
        $aLastIssues = [];
        $aLotteryId = explode(',',$oSeries->lotteries);
        $aLotteries = Lottery::getLotteriesByLotteryIds($aLotteryId);
        $aGameConfig = & $this->_getGamesSettings($oSeries);
        foreach($aLotteries as $oLottery){

            $aLotteryCodes[$oLottery->id] = $oLottery->identifier;
            $aLotteryNames[$oLottery->id] = $oLottery->friendly_name;

            $oIssue = new Issue;
            $aFinishedIssues[$oLottery->id] = $oIssue->getIssueArrayForWinNum($oLottery->id, 10);

            $aFinishedIssuesArr = [];

            foreach ($aFinishedIssues[$oLottery->id] as $key => $aIssue) {
                $aFinishedIssuesArr[$oLottery->id][] = $aIssue['number'] . '=' . $aIssue['code'];
            }

            $aFinishedIssues[$oLottery->id] = isset($aFinishedIssuesArr[$oLottery->id]) ? implode(',', $aFinishedIssuesArr[$oLottery->id]) : '';

            $aLastBetHistory[$oLottery->id] = $this->getLastBetHistory($oLottery->id);

            $aLastIssues[$oLottery->id] = $oIssue->getLastIssues($oLottery->id,3);
        }

        $sLotteryConfig = json_encode($aGameConfig);
        $sLastBetHistory = json_encode($aLastBetHistory);
        $sLotteryName = __('_lotteries.kl28');
        $sLotteryCode = 'kl28';
        $sFinishedIssues = json_encode($aFinishedIssues);
        $sLastIssues = json_encode($aLastIssues);

        $oCmsArticle = CmsArticle::find(451);

        $sCmsArticle = json_encode($oCmsArticle->getAttributes());

        $WEB_SOCKET_SERVER = SysConfig::readValue('WEB_SOCKET_SERVER');
        $this->setVars(compact('sLotteryConfig', 'iLotteryId', 'sLotteryName', 'sLotteryCode', 'sLastBetHistory', 'sFinishedIssues'
            ,'WEB_SOCKET_SERVER','sLastIssues','aLotteryNames','aLotteryCodes','sCmsArticle'));

        $this->view = $this->customViewPath . '.' . strtolower($oSeries->identifier);


        return $this->render();
    }

    /**
     * 输出游戏设置
     * @param int $iLotteryId
     * @return string json
     */
    public function getGameSettingsForRefresh($iLotteryId) {
        $oLottery = Lottery::find($iLotteryId);

        if (empty($oLottery)) {
            echo '';
            return;
        }

        if(!$oLottery->is_trace_issue)
        {
            $aGameConfig = & $this->_getGameSettings($oLottery);
        }else{
            $aGameConfig = $this->_getGameWinNumberSettings($oLottery);
            if(isset($aGameConfig['currNumber'])) $this->plusGameWinNumberSettings($oLottery, $aGameConfig['currNumber'], $aGameConfig);
        }
        if(!$aGameConfig) $aGameConfig = '';


        $this->halt(true, 'info', null, $a, $a, $aGameConfig);
        exit;
    }

    /**
     * 获得游戏设置
     * @param Lottery $oLottery
     * @return array
     */
    private function & _getGameSettings($oLottery) {
        $iUserId = Session::get('user_id');
        $aWayGroups = & User::getWaySettings($iUserId, $oLottery, true);

        if (!$aWayGroups) {
            return $aWayGroups;
//            $this->halt(false,'no-right',UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
        }
//        pr($aWayGroups);
//        exit;
        $aGameInfo = [];
        $aIssues = & $this->getIssuesForBet($oLottery, $oLottery->trace_issue_count);
        if (empty($aIssues) && !$oLottery->is_trace_issue) {
            return $aGameInfo;
//            $this->halt(false,'issue-missing',Issue::ERRNO_ISSUE_MISSING);
        }
//        pr($aWayGroups);
        // todo
//        pr($gameNumbers);

        $user_prize_group = Session::get('user_prize_group');
        $bet_max_prize_group = Session::get('user_prize_group') >  SysConfig::readValue('bet_max_prize_group') ?  SysConfig::readValue('bet_max_prize_group')  : Session::get('user_prize_group');

        switch($oLottery->series_id){
            case 2:
                $subtract_prize_group = 20;
                break;
            case 3:
            case 13:
            case 19:
            default :
                $subtract_prize_group = 0;
                break;
        }
        $user_prize_group -= $subtract_prize_group;
        $bet_max_prize_group -= $subtract_prize_group;
        $oSeries = Series::find($oLottery->series_id);
        $oIssueRule = IssueRule::where('lottery_id', '=', $oLottery->id)->first(['cycle']);

        $oIssue = new Issue;
        $aGameInfo = [
            'subtract_prize_group' => $subtract_prize_group,
            'bet_min_prize_group'=>  SysConfig::readValue('bet_min_prize_group') - $subtract_prize_group,
            'user_prize_group' => $user_prize_group,
            'bet_max_prize_group'=> $bet_max_prize_group,
            'series_amount' =>$oSeries->classic_amount,
            'gameId' => $oLottery->id,
            'gameName_en' => $oLottery->identifier,
            'gameName_cn' => $oLottery->friendly_name,
            'defaultMethodId' => $oSeries->default_way_id,
            'gameMethods' => $aWayGroups,
            'uploadPath' => URL::route('bets.upload-bet-number'),
            'jsPath' => Config::get('var.js_config')[Config::get('var.environment')]['game_path'] . strtolower(trim($oSeries->identifier)) . '/',
            'jsSuffix' => Config::get('var.js_config')[Config::get('var.environment')]['suffix'],
            //游戏注单提交地址
            'submitUrl' => URL::route('bets.bet', ['lottery_id' => $oLottery->id]),
            'loaddataUrl' => URL::route('bets.load-data', ['lottery_id' => $oLottery->id]),
            'pollBetInfoUrl' => route('bets.bet-info'),
            'pollUserAccountUrl' => route('users.user-account-info'),
            'projectViewBaseUrl' => route('projects.view'),
            'traceViewBaseUrl' => route('traces.view'),
            'historyCountUrl' => route('bets.history-count'),   //冷热号
            'trendGraphUrl' => route('bets.trend-graph', $oLottery->id),
            'isAgent' => Session::get('is_agent'),
//            'historyNumbers' => $oIssue->getIssueArrayForWinNum($oLottery->id, $oLottery->series_id == 17 ? 200 : 60),
            'currentTime' => time(),
            '_token' => Session::get('_token'),
            'is_agent' => Session::get('is_agent'),
            'coefficient' => $this->getCache('gameid=' . $oLottery->id),
            'set_cache_url' => Route('home.set-cache', 'game_id=' . $oLottery->id),
            'max_prize' => intval($oLottery->max_prize/10000),
            'cycle' => $oIssueRule->cycle,
            //最大追号期数
        ];
        if($oLottery->series_id == 20){
            $aGameInfo['cycle'] = $aIssues[0]['cycle'];
            $aGameInfo['entertainedTime'] = $oLottery->entertained_time;
        }


        if($aIssues){
            $aGameInfo['currentNumber'] = $aIssues[0]['number'];
            $aGameInfo['gameNumbers'] = $aIssues;
            //最大追号期数
            $aGameInfo['traceMaxTimes'] = count($aIssues);
            $aGameInfo['currentNumberTime'] = strtotime($aIssues[0]['time']);
        }

        if ($aLatestWnNumber = Issue::getLatestWnNumber($oLottery->id)) {
            $aGameInfo['lastNumber'] = $aLatestWnNumber[0]['issue'];
            $aGameInfo['lotteryBalls'] = $aLatestWnNumber[0]['wn_number'];
        }

        //historyNumbers 走势图 仅骰宝和龙虎斗使用
        switch($oSeries->identifier){
            case 'DICE':
                $aGameInfo['historyNumbers'] = $oIssue->getIssueArrayForWinNum($oLottery->id, 60);
                break;
            case 'LHD':
                $aGameInfo['historyNumbers'] = $oIssue->getIssueArrayForWinNum($oLottery->id, 200);
                break;
//            case 'XY28':
//                $aGameInfo['historyNumbers'] = $oIssue->getIssueArrayForWinNum($oLottery->id, 200);
//                break;
            default :
                break;
        }

        return $aGameInfo;
    }

    /**
     * 获得游戏设置
     * @param Lottery $oLottery
     * @return array
     */
    private function & _getGamesSettings($oSeries) {
        $iUserId = Session::get('user_id');
        $aLotteryIds = explode(',', $oSeries->lotteries);
        $aLotteries = Lottery::getLotteriesByLotteryIds($aLotteryIds);
        $aGameInfo = [];
        $aWayGroups = [];
        $oIssue = new Issue;
        $user_prize_group = Session::get('user_prize_group');
        $bet_max_prize_group = Session::get('user_prize_group') >  1950 ?  1950 : Session::get('user_prize_group');

        switch($oSeries->id){
            case 2:
                $subtract_prize_group = 20;
                break;
            case 3:
            case 13:
                $subtract_prize_group = 30;
                break;
            default :
                $subtract_prize_group = 0;
                break;
        }
        $user_prize_group -= $subtract_prize_group;
        $bet_max_prize_group -= $subtract_prize_group;

        $aGameInfo = [
            'subtract_prize_group' => $subtract_prize_group,
            'bet_min_prize_group'=>  SysConfig::readValue('bet_min_prize_group') - $subtract_prize_group,
            'user_prize_group' => $user_prize_group,
            'bet_max_prize_group'=> $bet_max_prize_group,
            'series_amount' =>$oSeries->classic_amount,
            'defaultMethodId' => $oSeries->default_way_id,
            'uploadPath' => URL::route('bets.upload-bet-number'),
            'jsPath' => Config::get('var.js_config')[Config::get('var.environment')]['game_path'] . strtolower(trim($oSeries->identifier)) . '/',
            'jsSuffix' => Config::get('var.js_config')[Config::get('var.environment')]['suffix'],
            'pollBetInfoUrl' => route('bets.bet-info'),
            'pollUserAccountUrl' => route('users.user-account-info'),
            'projectViewBaseUrl' => route('projects.view'),
            'traceViewBaseUrl' => route('traces.view'),
            'historyCountUrl' => route('bets.history-count'),   //冷热号
            'isAgent' => Session::get('is_agent'),
            'currentTime' => time(),
            '_token' => Session::get('_token'),
            'is_agent' => Session::get('is_agent'),
            'bet_max_amount' => SysConfig::readValue('bet_amount_max_xy28')
        ];

        foreach ($aLotteries as $oLottery) {
            $aWayGroups = & User::getWaySettings($iUserId, $oLottery, true);
            $aIssues[$oLottery->id] = & $this->getIssuesForBet($oLottery, $oLottery->trace_issue_count);
            $aGameInfo['gameInfo'][$oLottery->id]['gameId'] = $oLottery->id;
            $aGameInfo['gameInfo'][$oLottery->id]['gameName_en'] = $oLottery->identifier;
            $aGameInfo['gameInfo'][$oLottery->id]['gameName_cn'] = $oLottery->friendly_name;
            $aGameInfo['gameInfo'][$oLottery->id]['submitUrl'] = URL::route('bets.bet', ['lottery_id' => $oLottery->id]);
            $aGameInfo['gameInfo'][$oLottery->id]['loaddataUrl'] = URL::route('bets.load-data', ['lottery_id' => $oLottery->id]);
            $aGameInfo['gameInfo'][$oLottery->id]['trendGraphUrl'] = route('bets.trend-graph', $oLottery->id);
            $aGameInfo['gameInfo'][$oLottery->id]['coefficient'] = $this->getCache('gameid=' . $oLottery->id);
            $aGameInfo['gameInfo'][$oLottery->id]['set_cache_url'] = Route('home.set-cache', 'game_id=' . $oLottery->id);
            $aGameInfo['gameInfo'][$oLottery->id]['max_prize'] = intval($oLottery->max_prize/10000);
//            $oIssueRule = IssueRule::where('lottery_id', '=', $oLottery->id)->first(['cycle']);
            if(isset($aIssues[$oLottery->id][0]['cycle'])) $aGameInfo['gameInfo'][$oLottery->id]['cycle'] = $aIssues[$oLottery->id][0]['cycle'];
            $aGameInfo['gameInfo'][$oLottery->id]['gameMethods'] = $aWayGroups;
            $aGameInfo['gameInfo'][$oLottery->id]['entertainedTime'] = $oLottery->entertained_time;
            $aGameInfo['gameInfo'][$oLottery->id]['orders'] = $this->getUserProjectsAndTraces($oLottery->id, false);
            $aGameInfo['gameInfo'][$oLottery->id]['winNumbers'] = json_decode($this->getWnNumbersHistory($oLottery->id));

            if($aIssues[$oLottery->id]){
                $aGameInfo['gameInfo'][$oLottery->id]['currentNumber'] = $aIssues[$oLottery->id][0]['number'];
                $aGameInfo['gameInfo'][$oLottery->id]['gameNumbers'] = $aIssues[$oLottery->id];
                //最大追号期数
                $aGameInfo['gameInfo'][$oLottery->id]['traceMaxTimes'] = count($aIssues[$oLottery->id]);
                $aGameInfo['gameInfo'][$oLottery->id]['currentNumberTime'] = strtotime($aIssues[$oLottery->id][0]['time']);
            }

            if ($aLatestWnNumber[$oLottery->id] = Issue::getLatestWnNumber($oLottery->id)) {
                $aGameInfo['gameInfo'][$oLottery->id]['lastNumber'] = $aLatestWnNumber[$oLottery->id][0]['issue'];
                $aGameInfo['gameInfo'][$oLottery->id]['lotteryBalls'] = $aLatestWnNumber[$oLottery->id][0]['wn_number'];
            }

        }

        return $aGameInfo;
    }

    /**
     * 返回用户的最后一次下注信息
     * @param int $iLotteryId
     * @return array
     */
    private function getLastBetHistory($iLotteryId){
        $iIssue = UserProject::getLastIssueByLotteryId($iLotteryId);
        $oProjects = UserProject::getRecordsByIssue($iLotteryId, $iIssue);
        $aLastBetHistory = [];
        foreach($oProjects as $oProject){
            $aTmp = [];
            $aTmp['wayId'] = $oProject->way_id;
            $aTmp['ball'] = $oProject->bet_number;
            $aTmp['multiple'] = $oProject->multiple;

            $aLastBetHistory['balls'][] = $aTmp;

            $aLastBetHistory['isFinish'] = $oProject->status != Project::STATUS_NORMAL;
            $aLastBetHistory['issue'] = $oProject->issue;
        }
        return $aLastBetHistory;
    }

    // todo: 获取最新开奖号码
    public function getLastWnNumber($iLotteryId) {
        //delayTime:30
        //
        ////请求上期开奖球的url地址
        //lastGameBallsUrl:'/xxx/xxx'
        ////请求上次开奖球的ajax返回格式
        //{
        //'isSuccess':1,
        //'type':'xxxx',
        //'data':{'lastBalls':[1,2,3,4,5]}
        //}
        if ($aLatestWnNumber = Issue::getLatestWnNumber($iLotteryId)) {
            $aGameInfo['lastNumber'] = $aLatestWnNumber[0]['issue'];
            $aGameInfo['lotteryBalls'] = $aLatestWnNumber[0]['wn_number'];
        }
    }

    /**
     * 取得奖期列表，供渲染投注页面使用
     * @param Lottery $oLottery
     * @param int $iCount
     * @return array &
     */
    protected function & getIssuesForBet($oLottery, $iCount = null) {
        $oIssue = new Issue;
        $gameNumbers = [];
        $iCount or $iCount = $oLottery->trace_issue_count;

//        $aIssues = $oIssue->getIssueArrayForWinNum($oLottery->id, $iCount);
        $aIssues = & $oIssue->getIssueArrayForBet($oLottery->id, $iCount, time());
//        $iCurrentTime = time();
//        foreach ($aIssues as $i => $aIssue){
//            pr($aIssue['cycle']);
//            if ($iCurrentTime > $aIssue['end_time'] - $aIssue['cycle']) {
//                break;
//            }
//        }
//        for($i++,$j = 0;$j < $iCount;$i++,$j++){
//            $gameNumbers[] = [
//                'number' => $aIssues[$i]['issue'] ,
//                'time'   => date('Y-m-d H:i:s' , $aIssues[$i]['end_time'])
//            ];
//        }
//        pr($gameNumbers);
//        exit;
        return $aIssues;
    }

    /**
     * 生成追号任务属性数组
     *
     * @param array $aTrace
     * @param SeriesWay $oSeriesWay
     * @param Lottery $oLottery
     * @param bool $bStopOnPrized
     * @return array &
     */
    private function & compileTraceData($aTrace, $oSeriesWay, $oLottery, $bStopOnPrized) {
        $fSingleAmount = $aTrace['bet']['single_count'] * $oSeriesWay->price * $aTrace['bet']['coefficient'];
        $aIssues = array_keys($aTrace['issues']);
        sort($aIssues);
        $data = [
            'user_id' => Session::get('user_id'),
            'username' => Session::get('username'),
            'account_id' => Session::get('account_id'),
            'user_forefather_ids' => Session::get('forefather_ids'),
            'total_issues' => count($aTrace['issues']),
            'title' => $oSeriesWay->compileDisplayName(),
            'bet_number' => $aTrace['bet']['bet_number'],
            'position'   => $aTrace['bet']['position'],
            'bet_source' => $aTrace['bet']['bet_source'],
            'display_bet_number' => isset($aTrace['bet']['display_bet_number']) ? $aTrace['bet']['display_bet_number'] : $aTrace['bet']['bet_number'],
            'note' => '',
            'lottery_id' => $oLottery->id,
            'start_issue' => array_shift($aIssues),
            'way_id' => $oSeriesWay->id,
            'coefficient' => $aTrace['bet']['coefficient'],
            'single_amount' => $fSingleAmount,
            'amount' => $fSingleAmount * array_sum($aTrace['issues']),
            'status' => Trace::STATUS_RUNNING,
            'stop_on_won' => (bool) $bStopOnPrized,
            'ip' => $this->clientIP,
            'proxy_ip' => $this->proxyIP,
            'bought_at' => Carbon::now()->toDateTimeString(),
            'prize_set' => $aTrace['prize_set'],
            'prize_group' => $aTrace['bet']['prize_group'],
            'commission' => $aTrace['commission'],
//            'series_number' => Project::makeSeriesNumber(Session::get('user_id'))
        ];
//        pr($data);
//        exit;
        return $data;
    }

    /**
     * 生成奖金设置数组，供投注功能使用
     *
     * @param int $iSeriesWayId
     * @param int $iPrizeGroupId
     * @param SeriesWay $oSeriesWay
     * @param array $aPrizeSettings &
     * @param array $aPrizeSettingOfWay &
     * @param array $aMaxPrize &
     */
    private function makePrizeSettingArray($iSeriesWayId, $iPrizeGroupId, $oSeriesWay, & $aPrizeSettings, & $aPrizeSettingOfWay, & $aMaxPrize, $sBetNumber) {

        if (isset($aPrizeSettings[$iSeriesWayId])) {
            $aPrizeSettingOfWay = $aPrizeSettings[$iSeriesWayId];
        } else {
//            pr($oSeriesWay->toArray());
            $sMethods = $oSeriesWay->basic_methods;
            $aMethodIds = explode(',', $oSeriesWay->basic_methods);
            $aPrizeSettingOfMethods = [];
            $fMaxPrize = 0;
            $aPrizeDetail = [];
            $iLevelCount = 1;

            foreach ($aMethodIds as $iMethodId) {
                $aPrizeSettingOfMethods[$iMethodId] = PrizeDetail::getPrizeSetting($iPrizeGroupId, $iMethodId);
                $fMaxPrize >= $aPrizeSettingOfMethods[$iMethodId][1] or $fMaxPrize = $aPrizeSettingOfMethods[$iMethodId][1];
                $aPrizeDetail = $aPrizeSettingOfMethods[$iMethodId];
                $iLevelCount = count($aPrizeDetail);
            }

            $aPrizeSettingOfWay = $aPrizeSettings[$iSeriesWayId] = $aPrizeSettingOfMethods;

            //对和值玩法注数限制做特殊处理
            if($oSeriesWay->is_enable_extra){
                $aBetNumber = explode('|', $sBetNumber);
                if($oSeriesWay->id == 267){
                    $aBetNumber = str_split($sBetNumber,1);
                }
                $iBasicMethods = explode(',',$oSeriesWay->basic_methods);
                $aPrizeLevels = PrizeLevel::whereIn('basic_method_id',$iBasicMethods)->get(['level','rule']);
                $aLevels = [];
                foreach ($aBetNumber as $sSingleBetNumber) {
                    foreach ($aPrizeLevels as $oPrizeLevel) {
                        $aLevelNumber = explode(',',$oPrizeLevel->rule);
                        if(in_array($sSingleBetNumber,$aLevelNumber)) {
                            $aLevels[] = $oPrizeLevel->level;
                        }
                    }
                }
                //todo 排查$aLevels为空的情况
                if (count($aLevels) > 0){
                    $fMaxPrize = $aPrizeDetail[min($aLevels)];
                }else{
                    $fMaxPrize = 0;
                    if($iLevelCount == 1){
                        $fMaxPrize = $aPrizeDetail[$iLevelCount];
                    }else{

                        $iMaxlevel = $iLevelCount;
                        foreach ($aBetNumber as $sSingleBetNumber) {
                            $ilevel = $sSingleBetNumber > $iLevelCount - 1 ? $ilevel = $iLevelCount * 2 - $sSingleBetNumber : $sSingleBetNumber + 1;
                            $iMaxlevel = $ilevel < $iMaxlevel ? $ilevel : $iMaxlevel;
                        }
                        $fMaxPrize = $aPrizeDetail[$iMaxlevel];
                    }
                }
            }
            $aMaxPrize[$iSeriesWayId] = $fMaxPrize;
        }

    }
    /**
     * 重新投注
     * @param type $project_id
     */
    public function redoBet($project_id){
        if(!$project_id)  $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);
        $oProject = Project::find($project_id);
        if(!$oProject) $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);
        if($oProject->user_id != Session::get('user_id')) $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);
        $latest_issue = Issue::getOnSaleIssue($oProject->lottery_id);
        if(!$latest_issue) $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);

        $latest_issue_no = $latest_issue->issue;
        $data=[
            "gameId"=>$oProject->lottery_id,
            "isTrace" =>0,
            "traceWinStop"=>1,
            "traceStopValue"=>1,
            "balls"=> [
                [
                    "prize_group"=>$oProject->prize_group,
                    "wayId"=>$oProject->way_id,
                    "ball"=>$oProject->bet_number,
                    "num"=>$oProject->amount/$oProject->multiple/$oProject->coefficient/2,
                    "onePrize"=>2 * $oProject->coefficient,
                    "moneyunit"=>$oProject->coefficient,
                    "multiple"=>$oProject->multiple,

                ]
            ],
            "orders"=>array($latest_issue_no=>1),
            "amount"=> $oProject->amount,
            "_token"=> Session::get('_token'),
            "bet_source"=>"web"
        ];
        $_POST['betdata'] = json_encode($data);
        $this->doBet(0);


    }

    /**
     * Bet
     * @param Lottery $oLottery
     */
    private function doBet($oLottery) {
//        if (!Session::get('is_player')) {
//            $this->halt(false, 'no-right', Project::ERRNO_BET_NO_RIGHT);
//        }

        $iUserId = Session::get('user_id');
        //$iGroupId = UserUserPrizeSet::getGroupId($iUserId, $oLottery->id, $sGroupName);

        $this->writeLog('start do bet');

        /*
          $this->writeLog(var_export($_SERVER,TRUE));
          $this->writeLog(var_export($_POST,TRUE));
          pr($this->params);
          pr($_POST);
          exit;
         */

        // 整理投注数据
        if ($this->isAjax) {
            $aBetData = Input::all();
            if (!empty($aBetData['is_encoded'])){
                $sDecodeContent = Encrypt::decode($aBetData['balls']);
                //print_r($sDecodeContent);exit;
                $aBetData['balls'] = json_decode($sDecodeContent, true);
            }
            $oCustomer = Customer::getCustomerByKey(SysConfig::readValue('bet_source_webpage'));
            !is_object($oCustomer) or $aBetData['bet_source'] = $oCustomer->name;
        } else {
            if ($this->isMobile) {
                $aBetData = getJsonData();
                $oCustomer = Customer::getCustomerByKey(Input::get('customer'));

                if (!is_object($oCustomer)) {
                    $this->halt(false, 'missing-data', Customer::ERRNO_MISSING_DATA);
                }

                $aBetData['bet_source'] = $oCustomer->name;
            } else {
                $sBetData = urldecode($_POST['betdata']);
                $aBetData = json_decode($sBetData);
                !is_object($aBetData) or $aBetData = objectToArray($aBetData);
            }
        }

        $this->writeLog(var_export($aBetData, TRUE));

        $iLotteryId = array_get($aBetData, 'gameId');

//            pr($iLotteryId);
        $oLottery = Lottery::find($iLotteryId);

        if (empty($oLottery)) {
            // todo : output json error msg and exit
            $this->halt(false, 'lottery-missing', Lottery::ERRNO_LOTTERY_MISSING);
        }

        $oOnSaleIssue = Issue::getOnSaleIssue($iLotteryId);

        if (empty($oOnSaleIssue)) {
            $this->halt(false, 'issue-missing', Issue::ERRNO_ISSUE_MISSING);
        }

        //过滤幸运28停盘的投注
        if($oLottery->series_id == 20 && ($oOnSaleIssue->end_time - time()) > $oOnSaleIssue->cycle){
            $this->halt(false, 'issue-error', Issue::ERRNO_ISSUE_MISSING);
        }

        //幸运28每期每个号码投注限额
    	if($oLottery->series_id == 20){
        	$fBetedAmount = UserProject::getBetedAmount($iUserId, $iLotteryId, $oOnSaleIssue->issue, $aBetData['balls'][0]['ball'], $aBetData['balls'][0]['wayId']);
        	if($fBetedAmount > SysConfig::readValue('bet_amount_max_xy28')){
        		$this->halt(false, 'issue-error', Issue::ERRNO_ISSUE_OVERBET);
        	}
        }

        //限制一期一单
        if (in_array($oLottery->name, Lottery::$validLotteriesForOnce)){
            $countProject = UserProject::countByLotteryIdAndIssue($iLotteryId, $oOnSaleIssue->issue);
            if ($countProject > 0){
                $this->halt(false, 'create-failed-issue-limit', Project::ERRNO_BET_ERROR_ISSUE_LIMIT);
            }
        }
        $oUser = User::find($iUserId);

//        if(isset($aBetData['prize_group'])){
//            $sGroupName = $aBetData['prize_group'];
//            $iGroupId = PrizeGroup::getPrizeGroupByClassicPrizeAndSeries($sGroupName, $oLottery->series_id)->id;
//        }else{
//            $iGroupId = UserUserPrizeSet::getGroupId($iUserId, $oLottery->id, $sGroupName);
//        }
//        if ((!$iGroupId) || $sGroupName > $oUser->prize_group) {
//            $this->halt(false, 'no-right', UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
//        }

        $aSeriesWays = [];
        if(! Series::find($oLottery->series_id)->bet_commission){
            foreach($aBetData['balls'] as $key=>$val){
                $aBetData['balls'][$key]['prize_group'] = $oUser->prize_group;
            }
        }else{
            if(!$this->checkBetPrizeGroup($oUser, $aBetData['balls'])){
                $this->halt(false, 'bet_failed', PrizeSysConfig::ERRNO_BET_PRIZE_GROUP_WRONG);
            }
        }

        if (!$this->compileBetData($oLottery, $aBetData, $aBetNumbers, $aOrders, $aSeriesWays)) {
            $this->halt(false, 'issue-error', SeriesWay::ERRNO_SERIES_BET_NUMBER_WRONG);
        }

        list($sIssue, $iTmp) = each($aOrders);

        /*
         * 提到前面处理，方便做彩种判断
        $oOnSaleIssue = Issue::getOnSaleIssue($iLotteryId);

        if (empty($oOnSaleIssue)) {
            $this->halt(false, 'issue-missing', Issue::ERRNO_ISSUE_MISSING);
        }
        */

        //增加对幸运28封盘时间做判断
        if($oLottery->series_id == 20 && $oOnSaleIssue->end_time - $oLottery->entertained_time < time() && $oOnSaleIssue->end_time > time() && $oOnSaleIssue->issue == $sIssue){
            $this->halt(false, 'issue-error', Issue::ERRNO_ISSUE_ENTERTAINED);
        }

        if ($oOnSaleIssue->issue != $sIssue) {
            $this->halt(false, 'issue-error', Issue::ERRNO_ISSUE_MISSING);
        }

        BetThread::addThread($iLotteryId, $sIssue, $this->dbThreadId);
//        pr(BetThread::isEmpty($iLotteryId,'140831068'));

        $fTotalAmount = formatNumber($aBetData['amount'], 2);

        if(!in_array($oLottery->name, Lottery::$noCheckBetMultiple)){
            if(! $this->_checkUserIssueMaxBet($iUserId,$oLottery->id,$sIssue,$aBetData['amount']/count($aOrders))){
                $this->halt (false, 'bet-failed', Issue::ERRNO_ISSUE_OVERBET);//类型换为投注失败的
            }
        }
        // 形成投注用数组

        $this->writeLog('compile-bet-data');
        $aProjects = $aTraces = $aMaxPrize = [];
        $aPrizeSettings = [];
        $iPrizeLimit = User::getPrizeLimit($iUserId, $iLotteryId);
        $oAccount = Account::lock($oUser->account_id, $this->accountLocker);

        if (empty($oAccount)) {
            $this->writeLog('lock-fail');
            $this->halt(false, 'netAbnormal', Account::ERRNO_LOCK_FAILED);
        }

        try{
            // if (!$bCheckedTotalPrice = $this->checkProjectsTotalPrice($aBetData, $aBetNumbers)) {
            //     $this->halt(false, 'errorTip', Project::ERRNO_COUNT_AMOUNT_ERROR);
            // }

            $this->compileTaskAndProjects($aTraces, $aProjects, $aBetData, $aBetNumbers, $aOrders, $oAccount, $oUser, $iPrizeLimit, $aMaxPrize, $aSeriesWays, $oLottery);

            // 投注
            $this->writeLog('crate-project');
            if ($bTrace = $aBetData['isTrace']) {
                $aBetResults = $this->createTraces($aTraces, $aSeriesWays, $oLottery, $oAccount, $oUser, $aBetData['traceWinStop']);
                $iBetCount = count($aTraces);
            } else {
                $aBetResults = $this->createProjects($aProjects, $aSeriesWays, $oLottery, $oAccount, $oUser);
                $iBetCount = count($aProjects);
            }

            $this->writeLog('result:');
            $this->writeLog(var_export($aBetResults, 1));
            if (count($aBetResults[1]) == $iBetCount) {
                $iErrno = Project::ERRNO_BET_ALL_CREATED;
                $bSuccess = true;
                $sType = 'success';
                $sLinkUrl = URL::route('projects.index');
                //跟新玩家最后投注时间
                $this->updateUserLastBet($oUser);


            } else {
                if (count($aBetResults[0]) != $iBetCount) {
                    $iErrno = Project::ERRNO_BET_PARTLY_CREATED;
                    $bSuccess = true;
                    $sType = 'bet_part';
                } else {
                    $iErrno = Project::ERRNO_BET_FAILED;
                    $bSuccess = false;
                    $sType = 'bet_failed';
                }
                $sLinkUrl = '';
            }
            $this->writeLog('response:');
            $aData = [];
            !$this->isMobile or $this->doMobileOperation($aData);
            if (is_array($aBetResults[1]) && count($aBetResults[1]) == 1){
                $aData['tplData']['detail_url'] = route('projects.view', $aBetResults[1][0]['id']);
            }
            $this->halt($bSuccess, $sType, $iErrno, $aBetResults[1], $aBetResults[0], $aData, $sLinkUrl);
        } catch (Exception $ex) {
            $this->writeLog('error:' . $ex->getMessage());
            $this->halt(false, 'bet_failed', Project::ERRNO_BET_FAILED);
        }
//        pr($oSeriesWay->toArray());
//        Account::unLock($oUser->account_id);
        exit;
    }

    /**
     * 追号任务入库
     *
     * @param array     $aTraces
     * @param array     $aSeriesWays &
     * @param Lottery   $oLottery
     * @param Account   $oAccount
     * @param User      $oUser
     * @param bool      $bStopOnPrized
     * @return array
     */
    private function createTraces($aTraces, & $aSeriesWays, $oLottery, $oAccount, $oUser, $bStopOnPrized) {

        $aBetResults = [[], []];

        if (!$aTraces) {
            return $aTraceCount;
        }

        foreach ($aTraces as $aTrace) {
            $aTraceAttributes = $this->compileTraceData($aTrace, $aSeriesWays[$aTrace['bet']['way']], $oLottery, $bStopOnPrized);

            $oTrace = new Trace($aTraceAttributes);
            $oTrace->setUser($oUser);
            $oTrace->setAccount($oAccount);

            DB::connection()->beginTransaction();

            $iReturn = $oTrace->addTrace($aTrace['issues'], $oFirstProject);

            if ($iReturn < 0) {
                DB::connection()->rollback();

                $aBetResults[0][] = [
                    'way' => $aTrace['bet']['way'],
                    'ball' => $aTrace['bet']['bet_number'],
                    'position'=>$aTrace['bet']['position'],
                    'reason' => $iReturn
                ];
                break;
            } else {
                DB::connection()->commit();
                $oFirstProject->setCommited();     // 建立销售量更新任务
                ActivityUserTaskFeedBack::updateEvent();
                $aBetResults[1][] = [
                    'id' => $oFirstProject->id,
                    'way' => $aTrace['bet']['way'],
                    'ball' => $aTrace['bet']['bet_number'],
                    'position'=>$aTrace['bet']['position']
                ];
            }
        }
        return $aBetResults;
    }

    /**
     * 注单入库
     *
     * @param array`    $aProjects
     * @param array     $aSeriesWays &
     * @param Lottery   $oLottery
     * @param Account   $oAccount
     * @param User      $oUser
     * @return array
     */
    private function createProjects($aProjects, & $aSeriesWays, $oLottery, $oAccount, $oUser) {
        $aBetResults = [[], []];

        if (!$aProjects) {
            return $aPrjCount;
        }

        $aExtraData = [
            'client_ip' => $this->clientIP,
            'proxy_ip' => $this->proxyIP,
            'is_tester' => $oUser->is_tester
        ];

        foreach ($aProjects as $aPrj) {
            $aProjectDetails = & Project::compileProjectData($aPrj, $aSeriesWays[$aPrj['way']], $oLottery, $aExtraData);

            $oProject = new Project($aProjectDetails);
            $oProject->setAccount($oAccount);
            $oProject->setUser($oUser);
            $oProject->setLottery($oLottery);

            DB::connection()->beginTransaction();

            $iReturn = $oProject->addProject();

            if ($iReturn != Project::ERRNO_BET_SUCCESSFUL) {
                DB::connection()->rollback();

                $this->writeLog($iReturn);
                $this->writeLog($oProject->toArray());
                $this->writeLog($oProject->validationErrors->toArray());

                $aBetResults[0][] = [
                    'way' => $aPrj['way'],
                    'ball' => $aPrj['bet_number'],
                    'position'=>$aPrj['position'],
                    'reason' => $iReturn
                ];

                break;
            } else {
                DB::connection()->commit();
                $oProject->setCommited();    // 建立销售量更新任务
                ActivityUserTaskFeedBack::updateEvent();
                $aBetResults[1][] = [
                    'id' => $oProject->id,
                    'way' => $aPrj['way'],
                    'ball' => $aPrj['bet_number'],
                    'position'=>$aPrj['position'],
                ];
            }
        }

        return $aBetResults;
    }

    private function updateUserLastBet($oUser) {
        $oUserLastBet = UserLastBet::where('user_id', '=', $oUser->id)->first();
        $data = [
            'user_id' => $oUser->id,
            'forefather_ids' => $oUser->forefather_ids,
            'last_bet_time' => time()
        ];
        if (!$oUserLastBet) {
            UserLastBet::create($data);
        } else {
            $oUserLastBet->update(['last_bet_time' => time()]);
        }
    }



    private function _checkUserIssueMaxBet($user_id = null,$lottery_id = null,$issue=null,$amount=0){
        $max_bet_amout = SysConfig::readValue('issue_max_bet');
        if(!$user_id || !$lottery_id || !$issue ||!$amount) return true;
        if($amount > $max_bet_amout) return false;
        $oUserIssueStatic = UserIssueStatic::where('user_id', '=', $user_id)->where('lottery_id','=',$lottery_id)->where('issue','=',$issue)->first();
        if(!$oUserIssueStatic) return true;
        if($oUserIssueStatic->bet_total + $amount > SysConfig::readValue('issue_max_bet')) return false;
        return true;
    }

    /**
     * 向追号任务数组中增加一个任务
     *
     * @param array     $aTraces &
     * @param array     $aBetNumber
     * @param array     $aOrders
     * @param SeriesWay $oSeriesWay
     * @param int       $iSingleCount
     */
    private function addTraceTaskQueue(& $aTraces, $aBetNumber, $aOrders, $oSeriesWay, $iSingleCount, $aPrizeSettingOfWay, $sGroupName) {
//        pr($aOrders);
        $aTraceIssues = [];
        $iOriginalMultiple = $aBetNumber['multiple'];
        $fSingleAmount = formatNumber($iSingleCount * $oSeriesWay->price * $aBetNumber['coefficient'], 2);    // get single amount
        $iTotalCount = $iSingleCount * $iOriginalMultiple;                 // get total amount
        $fValidBaseAmount = formatNumber($fSingleAmount * $iOriginalMultiple, 2);
        foreach ($aOrders as $sIssue => $iOrderMultiple) {
            $iMultiple = $iOrderMultiple * $iOriginalMultiple;
            $fValidAmount = formatNumber($fSingleAmount * $iMultiple, 2);   // get valid amount
            $aTraceIssues[$sIssue] = $iMultiple;
        }
        $fTraceMultiple = array_sum($aOrders);
        $fTraceAmount = $fTraceMultiple * $fValidBaseAmount;
        $aBetNumber['prize_group'] = $sGroupName;

        $aTraces[] = [
            'bet' => $aBetNumber,
            'issues' => $aTraceIssues,
            'prize_set' => json_encode($aPrizeSettingOfWay),
            'commission' => $this->getCommissionRate($aBetNumber['prize_group'], $aBetNumber['way']),
        ];
    }

    /**
     * 向注单数组中增加一个注单
     *
     * @param array     $aProjects &
     * @param array     $aBetNumber
     * @param array     $aOrders
     * @param float       $fSingleAmount
     */
    private function addSingleProject(& $aProjects, $aBetNumber, $aOrders, $fSingleAmount, $aPrizeSettingOfWay, $sGroupName) {
        $aIssue = each($aOrders);
        $sIssue = $aIssue[0];
        $aOrderInfo = [
            'issue' => $sIssue,
            'multiple' => array_sum($aOrders) * $aBetNumber['multiple'],
            'single_amount' => $fSingleAmount,
            'prize_set' => json_encode($aPrizeSettingOfWay),
            'prize_group' => $sGroupName,
            'bet_source' => $aBetNumber['bet_source'],
            'commission' => $this->getCommissionRate($sGroupName, $aBetNumber['way']),
        ];
        $aProjects[] = array_merge($aBetNumber, $aOrderInfo);
    }

    /**
     * [checkProjectsTotalPrice 检查注单实际总价和传入的参数的总价是否一致]
     * @param  [Array] $aBetData    [投注参数数组]
     * @param  [Array] $aBetNumbers [拆分后的注单数组]
     * @return [Boolean/Json]       [是否一致/中断时返回的json数据]
     */
    private function checkProjectsTotalPrice(& $aBetData, & $aBetNumbers) {
        $fTotalValidAmount = 0;
        $bTrace = $aBetData['isTrace'];
        $iCountTraceIssues = count($aBetData['orders']);
        foreach ($aBetNumbers as $k => $aBetNumber) {
            $iSeriesWayId = $aBetNumber['way'];
            $oSeriesWay = isset($aSeriesWays[$aBetNumber['way']]) ? $aSeriesWays[$aBetNumber['way']] : ($aSeriesWays[$aBetNumber['way']] = SeriesWay::find($aBetNumber['way']));
            if ($oSeriesWay->price != $aBetNumber['price']) {
                $this->halt('Price Error');
            }
            $iSingleCount = $oSeriesWay->count($aBetNumber);
            $iOriginalMultiple = $aBetNumber['multiple'];
            $fSingleAmount = formatNumber($iSingleCount * $oSeriesWay->price * $aBetNumber['coefficient'], 2);    // get single amount
            $iTotalCount = $iSingleCount * $iOriginalMultiple;                 // get total amount
            $fValidBaseAmount = formatNumber($fSingleAmount * $iOriginalMultiple, 2);
            if ($iSingleCount != $aBetNumber['single_count']) {
                $this->writeLog($oSeriesWay->toArray());
                $this->writeLog($aBetNumber);
                $this->writeLog($iSingleCount);
                $this->halt(false, 'errorTip', Project::ERRNO_COUNT_ERROR);
            }
            $fTotalValidAmount += $bTrace ? $fValidBaseAmount * $iCountTraceIssues : $fValidBaseAmount;
        }
        // pr($fTotalValidAmount);
        // pr(array_get($aBetData, 'amount'));
        // exit;
        return $fTotalValidAmount == formatNumber(array_get($aBetData, 'amount'), 2);
    }

    public function getLatestIssueWnNumbers($iLotteryId = 1, $iCount = 5) {
        $oIssue = new Issue;
        $aFinishedIssues = $oIssue->getIssueArrayForWinNum($iLotteryId, $iCount);
        return Response::json($aFinishedIssues);
    }

    /**
     * 生成追号任务数组及注单数组
     *
     * @param array     $aTraces        &
     * @param array     $aProjects      &
     * @param array     $aBetData       &
     * @param array     $aBetNumbers    &
     * @param array     $aOrders        &
     * @param Account   $oAccount
     * @param int       $oUser
     * @param int      $iPrizeLimit
     * @param array     $aMaxPrize      &
     * @param array     $aSeriesWays    &
     */
    function compileTaskAndProjects(& $aTraces, & $aProjects, & $aBetData, & $aBetNumbers, & $aOrders, $oAccount, & $oUser, $iPrizeLimit, & $aMaxPrize, & $aSeriesWays, & $oLottery) {
        $bTrace = $aBetData['isTrace'];
        $fTotalValidAmount = 0;

        foreach ($aBetNumbers as $k => $aBetNumber) {
//            !$bTrace or $aTraces[$k] = $aBetNumber;
//                $aBetNumber['amount'] = formatNumber($aBetNumber['amount'], 2);
//            pr($aBetNumber);
//            exit;
            // get way config
            $fTaskValidAmount = 0;
            $iSeriesWayId = $aBetNumber['way'];
            //$oSeriesWay = isset($aSeriesWays[$aBetNumber['way']]) ? $aSeriesWays[$aBetNumber['way']] : ($aSeriesWays[$aBetNumber['way']] = SeriesWay::find($aBetNumber['way']));
            $oSeriesWay = SeriesWay::find($aBetNumber['way']);
            // get prize config
            $sGroupName = $aBetNumber['prize_group'];
            $iGroupId = PrizeGroup::getPrizeGroupByClassicPrizeAndSeries($sGroupName, $aSeriesWays[$iSeriesWayId]->series_id)->id;
            if ((!$iGroupId) || $sGroupName > $oUser->prize_group) {
                $this->halt(false, 'no-right', UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
            }

            $this->makePrizeSettingArray($iSeriesWayId, $iGroupId, $oSeriesWay, $aPrizeSettings, $aPrizeSettingOfWay, $aMaxPrize, $aBetNumber['bet_number']);

            if (!isset($aMaxPrize[$iSeriesWayId]) || $aMaxPrize[$iSeriesWayId] <= 0){
                $this->writeLog('max prize error. series-way-id: ' . $iSeriesWayId . '. bet-number: ' . var_export($aBetNumber, 1));
                $this->halt(false, 'Max Prize Error', UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
            }
            $fMaxPrize = $aMaxPrize[$iSeriesWayId];
            // get max multiple
            $iMaxMultiple = intval($iPrizeLimit / ($fMaxPrize * $aBetNumber['coefficient']));
            // check price
            if ($oSeriesWay->price != $aBetNumber['price']) {
                $this->halt(false, 'Price Error', Project::ERRNO_BET_FAILED);
            }
            $iSingleCount = $oSeriesWay->count($aBetNumber);        // get single count
            $this->writeLog('bet-number; ' . var_export($aBetNumber, 1));
            // check count
            if ($iSingleCount != $aBetNumber['single_count']) {
                $this->writeLog($oSeriesWay->toArray());
                $this->writeLog($aBetNumber);
                $this->writeLog($iSingleCount);
                $this->halt(false, 'errorTip', Project::ERRNO_COUNT_ERROR);
            }

            // check mulitple
            if (! in_array($oLottery->name, Lottery::$noCheckBetMultiple)) {
                if ($aBetNumber['multiple'] > $iMaxMultiple || max($aOrders) > $iMaxMultiple) {
                    $this->halt(false, 'errorTip', Project::ERRNO_PRIZE_OVERFLOW);
                }
            }
//            exit;
            // get amount
            $iOriginalMultiple = $aBetNumber['multiple'];
            $fSingleAmount = formatNumber($iSingleCount * $oSeriesWay->price * $aBetNumber['coefficient'], 2);    // get single amount
            $iTotalCount = $iSingleCount * $iOriginalMultiple;                 // get total amount
            $fValidBaseAmount = formatNumber($fSingleAmount * $iOriginalMultiple, 2);
            if ($fValidBaseAmount > $oAccount->available) {
                $this->halt(false, 'low-balance', Project::ERRNO_BET_ERROR_LOW_BALANCE);
            }
            // pr($aBetData);
            // pr($fValidBaseAmount);

            if ($bTrace) {
                $this->addTraceTaskQueue($aTraces, $aBetNumber, $aOrders, $oSeriesWay, $iSingleCount, $aPrizeSettingOfWay, $sGroupName);
//                pr($aTraces);
//                exit;
            } else {
                $this->addSingleProject($aProjects, $aBetNumber, $aOrders, $fSingleAmount, $aPrizeSettingOfWay, $sGroupName);
                // if ($fValidBaseAmount != array_get($aBetData, 'amount')) {
                //     $this->halt(false, 'errorTip', Project::ERRNO_COUNT_ERROR);
                // }
//                pr($aProjects);
            }
            $iTotalOrderMultiple = array_sum($aOrders);
            $fTotalValidAmount += $fTaskValidAmount = $fValidBaseAmount * $iTotalOrderMultiple;
            // pr($fTotalValidAmount);
            // exit;
        }
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

    public function uploadBetNumber() {
        return $this->render();
    }

    /**
     * 移动端操作
     */
    protected function doMobileOperation(& $data) {
        $iUserId = Session::get('user_id');
        $fAvailable = formatNumber(Account::getAvaliable($iUserId), 1);
        $data['available'] = $fAvailable;
    }

    /**
     * 获取自己的返点奖金比例
     * @return float
     */
    public function getCommissionRate($prizeGroup, $wayId){
        $oUser = User::find(Session::get('user_id'));
        $oSeriesWay = SeriesWay::find($wayId);

        $commission = 0;

        if(SeriesSet::getTypeId($oSeriesWay->series_id) == SeriesSet::TYPE_LOTTERY)
        {
            if($prizeGroup < $oUser->prize_group)
            {
                $iClassicAmount = Series::find($oSeriesWay->series_id)->classic_amount;
                $commission = ($oUser->prize_group - $prizeGroup)/$iClassicAmount;
            }
        }else{
            $oUserCommission = UserCommissionSet::getUserSeriesCommissionSets(Session::get('user_id'), $oSeriesWay->series_id)->first();
            !$oUserCommission or $commission = $oUserCommission->commission_rate/100;
        }

        return number_format($commission,4);
    }

    /**
     * @param $oUser
     * @param $balls
     * @return bool
     */
    private function checkBetPrizeGroup($oUser, $balls){

        $minBetPrize = SysConfig::readValue('bet_min_prize_group');
        $maxBetPrize = SysConfig::readValue('bet_max_prize_group');

        if($oUser->prize_group <= $minBetPrize){
            return true;
        }
        foreach($balls as $ball)
        {
            if(!isset($ball['prize_group']) || $ball['prize_group'] > $oUser->prize_group){
                return false;
            }
            if($ball['prize_group'] < $minBetPrize || $ball['prize_group'] > $maxBetPrize){
                return false;
            }
        }

        return true;
    }

    private function getTrendGraph4baijiale($oLottery, $currIssue){


        $betCode = ['zhuangdui'=>'5','xiandui'=>'6'];
        $betCodeUnique = ['zhuangxianhe' => ['0','1','2']];
        $betCodeMerge = [];
        foreach($betCodeUnique as $name => $aCode) $betCodeMerge = array_merge($betCodeMerge, $aCode);

        $data = [];
        $aBetPrizes = $this->_getWinListForBet($oLottery, $currIssue->issue, $currIssue->wn_number);

        foreach($aBetPrizes as $seriesWayId => $aCodes)
        {
            foreach($aCodes as $sCode =>$aIsWin)
            {
                if(in_array($sCode, $betCode)){
                    $name = array_keys($betCode, $sCode);
                    $data[ $name[0] ] = $aIsWin['is_win'];
                }

                elseif(in_array($sCode, $betCodeMerge))
                {
                    foreach($betCodeUnique as $name => $aCode){
                        if($aIsWin['is_win']){
                            $data[$name] = $sCode;
                        }
                    }
                }
            }
        }

        return $data;
    }

    /*
        private function checkBetPrizeGroup($oUser, & $balls){

            $minBetPrize = SysConfig::readValue('bet_min_prize_group');
            $maxBetPrize = SysConfig::readValue('bet_max_prize_group');

            if($oUser->prize_group <= $minBetPrize){
                return true;
            }

            foreach($balls as $key => $ball)
            {
                //投注不存在
                if(!isset($ball['prize_group'])){
                    $ball['prize_group'] = $balls[$key]['prize_group'] = $oUser->prize_group;
                }
                //自己比最高大，投注自己
                if($oUser->prize_group > $maxBetPrize && $ball['prize_group'] > $maxBetPrize){
                    $balls[$key]['prize_group'] = $maxBetPrize;
                }
                //投注大于自己小于最高
                elseif($ball['prize_group'] > $oUser->prize_group && $ball['prize_group'] <= $maxBetPrize){
                    $balls[$key]['prize_group'] = $oUser->prize_group;
                }
                //投注大于自己
                elseif($ball['prize_group'] > $oUser->prize_group){
                    $balls[$key]['prize_group'] = $oUser->prize_group;
                }
                //投注大于最高
                elseif($ball['prize_group'] > $maxBetPrize){
                    $balls[$key]['prize_group'] = $maxBetPrize;
                }
                else{
                    $balls[$key]['prize_group'] = $oUser->prize_group;
                }
            }
            return true;
        }*/

    public function getWnNumbersHistory($iLotteryId, $bIsNewest = false, $iCount = 2, $sIssue = null){
        $aIssueHistories = [];
        $aConditions = [
            'lottery_id' => $iLotteryId,
            'end_time' => [ '<', time()],
        ];
        if(!$bIsNewest) unset($aConditions['end_time']);
        $aConditions['lottery_id'] = $iLotteryId;

        if($sIssue) {
            $aConditions['issue'] = $sIssue;
            $aIssues = Issue::doWhere($aConditions)->orderBy('issue', 'desc')->first();
        }else{
            if($bIsNewest) {
                $aIssues = Issue::doWhere($aConditions)->take($iCount)->orderBy('issue', 'desc')->get();
                if($aIssues->count() < 2){
                    $aYesterdayTwoIssues = Issue::where('lottery_id','=',$iLotteryId)
                        ->where('end_time','>',strtotime(date('Y-m-d 00:00:00',strtotime("-1 day"))))
                        ->where('end_time','<',strtotime(date('Y-m-d 23:59:59',strtotime("-1 day"))))
                        ->take(2-$aIssues->count())
                        ->orderBy('issue', 'desc')
                        ->get();
                    $aIssues = arrayToObject(array_merge($aIssues->toArray(),$aYesterdayTwoIssues->toArray()));
                }
            } else {
                $aIssues = Issue::where('lottery_id','=',$iLotteryId)
                    ->where('end_time','<',time())
                    ->where('end_time','>',strtotime(date('Y-m-d 00:00:00')))
                    ->orderBy('issue', 'desc')
                    ->get();
            }
        }
        $aIssueHistories = [];
        if($aIssues){
            foreach ($aIssues as $key => $oIssue) {
                $sWnNumber = $oIssue->status == Issue::ISSUE_CODE_STATUS_CANCELED ? '/' : $oIssue->wn_number;
                $aIssueHistories[$key]['number'] = $oIssue->issue;
                $aIssueHistories[$key]['code'] = $sWnNumber;
            }
        }
        return json_encode($aIssueHistories);
    }

    public function getProfit($iSeriesId = 20,$sStartTime = null,$sEndTime = null,$iCount = 15){
        $iUserId = Session::get('user_id');
        $aProfits = UserSeriesProfit::getProfitsBySeriesIdAndUserId($iSeriesId,$iUserId,$sStartTime,$sEndTime,$iCount);
        $aSeriesProfits = [];
        foreach ($aProfits as $key => $oProfit) {
            $aSeriesProfits[$key][] = $oProfit->date;
            $aSeriesProfits[$key][] = $oProfit->turnover;
            $aSeriesProfits[$key][] = $oProfit->prize;
            $aSeriesProfits[$key][] = $oProfit->profit;
        }
        return json_encode($aSeriesProfits);
    }

    public function getWnnumberResult(){
        $iLotteryId = Input::get('lottery_id', 54);
        $sDate = Input::get('date');
        $sIssue = Input::get('issue');
        $aIssues = Issue::getWinningResult($iLotteryId, $sDate, $sIssue);
        $this->setVars(compact('aIssues'));
        return $this->render();
    }

}
