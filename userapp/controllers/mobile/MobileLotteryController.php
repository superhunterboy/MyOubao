<?php

class MobileLotteryController extends BetController {

    protected $isMobile = true;

    /**
     * 获取用户可用余额信息
     */
    public function getLotteryInfos() {
        $iOpen = Session::get('is_tester') ? null : 1;
        $aSeriesLotteries = Series::getLotteriesGroupBySeries($iOpen);
        $this->halt(true, 'info', null, $a, $a, $aSeriesLotteries);
    }

    /**
     * 获取指定彩种最近n期的奖期数据，$iLotteryId为null，则获取所有彩种最近n期奖期数据
     * @param int $iLotteryId 彩种id
     */
    public function getIssues($iLotteryId = null) {
        $iCount = Input::get('count');
        $iCount or $iCount = 20;
        $oIssue = new Issue;
        $aIssues = [];
        if ($iLotteryId == null) {
            $iOpen = Session::get('is_tester') ? null : 1;
            $aLotteries = Lottery::getAllLotteries($iOpen);
            foreach ($aLotteries as $aLottery) {
                $aIssues[$aLottery['id']] = $oIssue->getIssueArrayForWinNum($aLottery['id'], $iCount);
            }
        } else {
            $aIssues[$iLotteryId] = $oIssue->getIssueArrayForWinNum($iLotteryId, $iCount);
        }
        $this->halt(true, 'info', null, $a, $a, $aIssues);
    }

    /**
     * 输出游戏设置
     * @param int $iLotteryId
     * @return string json
     */
    public function loadData($iStep, $iLotteryId) {
        $oLottery = Lottery::find($iLotteryId);
        if (!is_object($oLottery)) {
            $this->halt(false, 'error', Lottery::ERRNO_LOTTERY_MISSING);
        }
        if (!$oLottery->open && !Session::get('is_tester')) {
            $this->halt(false, 'lottery-closed', Lottery::ERRNO_LOTTERY_CLOSED);
        }
        switch ($iStep) {
            case 1:
                $aGameConfig = $this->_getGameSettings($oLottery);
                break;
            case 2 :
                $aGameConfig = $this->_getGameWays($oLottery);
                break;
            case 3:
                $aGameConfig = $this->_getTraceIssues($oLottery);
                break;
        }
        $this->halt(true, 'info', null, $a, $a, $aGameConfig);
    }

    /**
     * 获得游戏设置
     * @param Lottery $oLottery
     * @return array
     */
    private function _getGameSettings($oLottery) {
        $aIssues = & $this->getIssuesForBet($oLottery);
        if (empty($aIssues)) {
            $this->halt(false, 'issue-missing', Issue::ERRNO_ISSUE_MISSING);
        }
        $oSeries = Series::find($oLottery->series_id);
        $aGameInfo = [
            'game_name_en' => $oLottery->identifier,
            'game_name_cn' => $oLottery->friendly_name,
            'default_method_id' => $oSeries->default_way_id,
            //最大追号期数
            'trace_max_times' => count($aIssues),
            'current_number' => $aIssues[0]['number'],
            'current_number_time' => $aIssues[0]['time'],
            'current_time' => date('Y-m-d H:i:s'),
        ];
        if ($aLatestWnNumber = Issue::getLatestWnNumber($oLottery->id)) {
            $aGameInfo['last_number'] = $aLatestWnNumber[0]['issue'];
            $aGameInfo['lottery_balls'] = $aLatestWnNumber[0]['wn_number'];
        }
        return $aGameInfo;
    }

    private function _getGameWays($oLottery) {
        $iUserId = Session::get('user_id');
        $aWayGroups = & User::getWaySettings($iUserId, $oLottery, true);
        if (!$aWayGroups) {
            $this->halt(false, 'no-right', UserPrizeSet::ERRNO_MISSING_PRIZE_SET);
        }
        $aGameInfo = [
            'game_ways' => $aWayGroups
        ];
        return $aGameInfo;
    }

    private function _getTraceIssues($oLottery) {
        $aIssues = & $this->getIssuesForBet($oLottery);
        if (empty($aIssues)) {
            $this->halt(false, 'issue-missing', Issue::ERRNO_ISSUE_MISSING);
        }
        $aGameInfo = [
            'trace_issues' => $aIssues
        ];
        return $aGameInfo;
    }

    public function getPrizeDetailsByLottery($iLotteryId) {
        $oLottery = Lottery::find($iLotteryId);
        if (!is_object($oLottery)) {
            $this->halt(false, 'error', Lottery::ERRNO_LOTTERY_MISSING);
        }
        $iUserId = Session::get('user_id');
        $aLotteriesPrizeSetsTable = User::getWaySettings($iUserId, $oLottery);
        $aResult = [];
        $aPrizeLevel = ['一等奖', '二等奖', '三等奖', '四等奖', '五等奖'];
        foreach ($aLotteriesPrizeSetsTable as $aWayGroup) {
            foreach ($aWayGroup['children'] as $aWay) {
                foreach ($aWay['children'] as $way) {
                    $aPrizes = explode(',', $way['prize']);
                    if (count($aPrizes) > 1) {
                        foreach ($aPrizes as $k => $v) {
                            $aResult[$aWayGroup['name_cn'] . $way['name_cn'] . $aPrizeLevel[$k]] = $v;
                        }
                    } else {
                        $aResult[$aWayGroup['name_cn'] . $way['name_cn']] = $way['prize'];
                    }
                }
            }
        }
        $this->halt(true, 'info', null, $a, $a, $aResult);
    }

}
