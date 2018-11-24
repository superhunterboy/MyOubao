<?php

/**
 * 奖期规则控制器
 */
class IssueRuleController extends AdminBaseController {

    protected $modelName = 'IssueRule';

    protected function beforeRender() {
        parent::beforeRender();
        $aLotteries = & Lottery::getTitleList();
        $this->setVars(compact('aLotteries'));
    }

    public function getIssueRules() {
        $iLotteryId = Input::get('lottery_id');
        $aIssueRules = $this->model->findIssueRulesByLotteryId($iLotteryId);
        $oLottery = new Lottery;
        $aLotteryInfo = $oLottery->find($iLotteryId)->getAttributes();
        if (!$aLotteryInfo) {
//            $this->_setActionResult(0, __('Invalid Lottery ID'), 'index/' . $iLotteryId);
        }
        $oIssue     = new ManIssue;
        //获取最后一期已经存在的奖期
        if ($aLastIssue = $oIssue->getLastIssueInfo($iLotteryId)) {        // get last Draw Number
            $sLastIssue = $aLastIssue['issue'];
            //获取生成奖期的开始时间
            if (!empty($sBeginDate)) {
                $dBeginDate = $sBeginDate;
            } else {
                $dBeginDate = $oLottery->getNextDay($aLotteryInfo['issue_over_midnight'], $sLastIssue, $aLastIssue['end_time']);
            }
        } else {
            $sLastIssue = $dBeginDate = '';
        }
        $bAccumulating = $oLottery->isAccumulating($aLotteryInfo['issue_format']);   // $bAccumulating
        $oDate = new Date;
        // 获取一周开奖时间，即周一到周日哪几天开奖
        $aWeek = & $oDate->checkWeekDays($aLotteryInfo['days']);

        $this->setVars('weeks', $oDate->weeks);
        $this->setVars('aBonusDays', $aWeek);
        $this->setVars(compact('sLastIssue', 'bAccumulating', 'dBeginDate'));
        $this->setVars('sLotteryName', $aLotteryInfo['name']);
        $this->setVars('bNeedCalendar', true);
        $this->setVars('aIssueRules', $aIssueRules);
        echo json_encode($this->viewVars);
    }

}