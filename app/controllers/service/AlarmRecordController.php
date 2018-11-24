<?php

class AlarmRecordController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'lottery.alarm';
//    protected $customViewPath = 'series.create-lottery';

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'IssueWarning';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $aLotteries = Lottery::getTitleList();
        $this->setVars('aLotteries', $aLotteries);
        $this->setVars('aStatus', IssueWarning::$aStatus);
    }

    /**
     * 修改开奖号码后,触发取消派奖,重新计奖的流程
     */
    public function reviseCode($id) {
        $oIssueWarnning = IssueWarning::find($id);
        if (!is_object($oIssueWarnning)) {
            return $this->goBack('error', '_issuewarning.missing-data');
        }
        $oIssue = ManIssue::getIssueObject($oIssueWarnning->lottery_id, $oIssueWarnning->issue);
        if (!is_object($oIssue)) {
            return $this->goBack('error', '_issue.missing-data');
        }
        $oCodeCenter = CodeCenter::find($oIssueWarnning->codecenter_id);
        if (!is_object($oCodeCenter)) {
            return $this->goBack('error', '_codecenter.missing-data');
        }
        DB::connection()->beginTransaction();
        $bSucc = $oIssue->setCancelPriceTask($oCodeCenter->customer_key, $oIssueWarnning->number);
//        pr($bSucc?'true':'false');exit;
        !$bSucc or $bSucc = IssueWarning::setStatusToResolved($id);
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBackToIndex('success', '_issuewarning.revise-success');
        } else {
            DB::connection()->rollback();
            return $this->goBackToIndex('error', '_issuewarning.revise-fail');
        }
    }

    /**
     * 官方未开奖的处理流程
     */
    public function cancelCode($id) {
        $oIssueWarnning = IssueWarning::find($id);
        if (!is_object($oIssueWarnning)) {
            return $this->goBack('error', '_issuewarning.missing-data');
        }
        $oIssue = ManIssue::getIssueObject($oIssueWarnning->lottery_id, $oIssueWarnning->issue);
        if (!is_object($oIssue)) {
            return $this->goBack('error', '_issue.missing-data');
        }
        //修改奖期的开奖状态
        DB::connection()->beginTransaction();
        $bSucc = $oIssue->setCancelTask();
        !$bSucc or $bSucc = IssueWarning::setStatusToResolved($id);
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBackToIndex('success', '_issuewarning.cancel-success');
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', '_issuewarning.cancel-fail');
        }
    }

    /**
     * 提前开奖的处理流程
     */
    public function advanceCode($id) {
        $oIssueWarnning = IssueWarning::find($id);
        if (!is_object($oIssueWarnning)) {
            return $this->goBack('error', '_issuewarning.missing-data');
        }
        $oIssue = ManIssue::getIssueObject($oIssueWarnning->lottery_id, $oIssueWarnning->issue);
        if (!is_object($oIssue)) {
            return $this->goBack('error', '_issue.missing-data');
        }
        DB::connection()->beginTransaction();
        $bSucc = $oIssue->setCancelTask($oIssueWarnning->earliest_draw_time);
        !$bSucc or $bSucc = IssueWarning::setStatusToResolved($id);
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBackToIndex('success', '_issuewarning.advance-success');
        } else {
            DB::connection()->rollback();
            return $this->goBackToIndex('error', '_issuewarning.advance-fail');
        }
    }

}
