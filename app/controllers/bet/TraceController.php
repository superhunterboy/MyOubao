<?php

class TraceController extends ComplicatedSearchController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ManTrace';
    protected $searchBlade = 'w.trace_search';

    const MANUAL_INPUT = 1;
    const AGENT_LIST = 2;

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
//        $aUsers = User::getAllUserNameArrayByUserType(0);
        // $aBanks = Bank::getAllBankNameArray();
        // $aBankAccounts = UserBankCard::getUserAccounts($user_id);
        // $aAccounts = [];
        $this->setVars('aCoefficients', Config::get('bet.coefficients'));
        $aLotteries = & Lottery::getTitleList();
        $aStatuses = $sModelName::$validStatuses;
        $aHiddenColumns = $sModelName::$aHiddenColumns;
        $aReadonlyInputs = $sModelName::$aReadonlyInputs;
        $this->setVars(compact('aStatuses', 'aHiddenColumns', 'aReadonlyInputs', 'aLotteries'));
        switch ($this->action) {
            case 'index':
                $aTransactionTypes = TransactionType::getFieldsOfAllTransactionTypesArray();
                $aAdminUsers = AdminUser::getTitleList();
                $aRootAgent = User::getAllUserNameArrayByUserType(User::TYPE_AGENT, 1);
                if (isset($this->params['lottery_id']) && !empty($this->params['lottery_id'])) {
                    $aLotteryWays = LotteryWay::getLotteryWaysByLotteryId($this->params['lottery_id']);
                    $aIssues = Issue::getIssuesByLotteryId($this->params['lottery_id']);
                    $this->setVars(compact('aLotteryWays', 'aIssues'));
                }
                $this->setVars(compact('aTransactionTypes', 'aAdminUsers', 'aRootAgent', 'aLotteries'));
                break;
        }
    }

    /**
     * 终止追号任务
     * @param int $iTraceId
     * @return Redirect
     */
    public function stop($iTraceId) {
        $oTrace = Trace::find($iTraceId);
        if (empty($oTrace)) {
            $this->goBack('error', __('_basic.no-data'));
        }
        if ($oTrace->status != Trace::STATUS_RUNNING) {
            return Redirect::route('traces.view', $iTraceId)->with('error', __('_trace.stop-failed-status'));
        }
        $oAccount = Account::lock($oTrace->account_id, $iLocker);
        if (empty($oAccount)) {
            return Redirect::route('traces.view', $iTraceId)->with('error', __('_trace.stop-failed'));
        }
        $oUser = User::find($oTrace->user_id);
        $oTrace->setAccount($oAccount);
        $oTrace->setUser($oUser);
        $DB = DB::connection();
        $DB->beginTransaction();
        if (($iReturn = $oTrace->terminate(1)) === true) {
            $DB->commit();
            $sLangKey = '_trace.stoped';
            $sMsgType = 'success';
        } else {
            $DB->rollback();
            $sLangKey = '_trace.stop-failed';
            $sMsgType = 'error';
        }
        Account::unLock($oTrace->account_id, $iLocker, false);
        return Redirect::route('traces.view', $iTraceId)->with($sMsgType, __($sLangKey));
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        if (isset($this->params['action']) && $this->params['action'] == 'ajax') {
            $iLottery_id = $this->params['lottery_id'];
            $aLottery = Lottery::find($iLottery_id);
            if (!empty($aLottery)) {
                $aData = [];
                $aLotteryWays = LotteryWay::getLotteryWaysByLotteryId($iLottery_id);
                $aIssues = Issue::getIssuesByLotteryId($iLottery_id);
                $aData['lottery_ways'] = $aLotteryWays;
                $aData['issues'] = $aIssues;
                echo json_encode($aData);
            }
            exit;
        }
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
//         pr(($aConditions));
        $oQuery = $this->model->doWhere($aConditions);
        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(Input::get('_withTrashed', 0));
        // pr($bWithTrashed);exit;
        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);


        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        $datas = $oQuery->paginate($iPageSize);
        $this->setVars(compact('datas'));
        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1];
            } else {
                $$sMainParamName = null;
            }
            $this->setVars(compact($sMainParamName));
        }

        return $this->render();
    }

    /**
     * 账变搜索中附件的搜索条件
     */
    public function makePlusSearchConditions() {
        $aPlusConditions = [];
        if (isset($this->params['user_search_type'])) {
            // 判断用户搜索类型，手动搜索：1；总代列表：2
            switch ($this->params['user_search_type']) {
                case self::MANUAL_INPUT:
                    // 包含下级
                    if (isset($this->params['un_include_children']) && $this->params['un_include_children'] && !empty($this->params['username'])) {
                        $aUserIds = User::getAllUsersBelongsToAgentByUsername($this->params['username'], isset($this->params['un_include_children']));
                        if (count($aUserIds) > 0) {
                            $aPlusConditions['user_id'] = ['in', $aUserIds];
                        } else {
                            $aPlusConditions['username'] = ['=', $this->params['username']];
                        }
                    } else if (!empty($this->params['username'])) {
                        // 不包含下级
                        $aPlusConditions['username'] = ['=', $this->params['username']];
                    }
                    break;
                case self::AGENT_LIST:
                    // 包含下级
                    if (!empty($this->params['root_agent'])) {
                        $aUserIds = User::getAllUsersBelongsToAgentByUsername($this->params['root_agent']);
                        if (count($aUserIds) > 0) {
                            $aPlusConditions['user_id'] = ['in', $aUserIds];
                        } else {
                            $aPlusConditions['user_id'] = ['=', 0];
                        }
                    }
                    break;
            }
        }
        return $aPlusConditions;
    }

    public function setGenerateTask($id){
        $oTrace = ManTrace::find($id);
        if (empty($oTrace)){
            return $this->goBack('error',__('_basic.no-data'));
        }
        if ($oTrace->status != Trace::STATUS_RUNNING){
            return $this->goBack('error',__('_trace.wrong-status'));
        }
        if ($bSucc = $oTrace->setGenerateTask()){
            $sLangKey = '_trace.generate-task-seted';
            $sMsgType = 'success';
        }
        else{
            $sLangKey = '_trace.generate-task-set-failed';
            $sMsgType = 'error';
        }
        return $this->goBack($sMsgType,__($sLangKey));
    }
}
