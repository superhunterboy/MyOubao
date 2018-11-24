<?php

class LongHuProjectController extends ComplicatedSearchController {

    const MANUAL_INPUT = 1;
    const AGENT_LIST = 2;

    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];
    protected $searchBlade = 'w.project_longhehu_search';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ManProject';

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
        $aStatusDesc = $sModelName::$validStatuses;
        $aHiddenColumns = $sModelName::$aHiddenColumns;
        $aReadonlyInputs = $sModelName::$aReadonlyInputs;
        $aBetSources = Customer::getCustomerAll();
        $this->setVars(compact('aStatusDesc', 'aHiddenColumns', 'aReadonlyInputs', 'aLotteries', 'aBetSources'));
        switch ($this->action) {
            case 'longHeHuIndex':
                $this->view='default.index';
            case 'index':

//                        $this->searchBlade = 'w.project_longhehu_search';
//                $aTransactionTypes = TransactionType::getFieldsOfAllTransactionTypesArray();
//                $aAdminUsers = AdminUser::getTitleList();
                $aRootAgent = User::getAllUserNameArrayByUserType(User::TYPE_AGENT, 1);
                if (isset($this->params['lottery_id']) && !empty($this->params['lottery_id'])) {
                    $aLotteryWays = LotteryWay::getLotteryWaysByLotteryId($this->params['lottery_id']);
                    $aIssues = Issue::getIssuesByLotteryId($this->params['lottery_id']);
                    $this->setVars(compact('aLotteryWays', 'aIssues'));
                }
                $this->setVars(compact('aRootAgent', 'aLotteries'));
                break;
        }
    }

    /**
     * 撤单
     * @param int $id
     * @return Redirect
     */
    function drop($id) {
        $oMessage = new Message($this->errorFiles);
        $oProject = UserProject::find($id);
        if (empty($oProject)) {
            return $this->goBack('error', $oMessage->getResponseMsg(Project::ERRNO_PROJECT_MISSING));
        }
        $oAccount = Account::lock($oProject->account_id, $iLocker);
        if (empty($oAccount)) {
            return $Redirect->with('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }
        try {
            $oProject->setAccount($oAccount);
            DB::connection()->beginTransaction();
            $this->writeLog('begin DB Transaction');
            if (($iReturn = $oProject->drop(Project::DROP_BY_ADMIN)) != Project::ERRNO_DROP_SUCCESS) {
                DB::connection()->rollback();
                $this->writeLog('Rollback');
                Account::unLock($oAccount->id, $iLocker, false);
                return $this->goBack('error', $oMessage->getResponseMsg($iReturn));
                exit;
            }
            DB::connection()->commit();
            $this->writeLog('Commit');
            $oProject->setCommited();    // 建立销售量更新任务
            Account::unLock($oAccount->id, $iLocker, false);
        } catch (Exception $e) {
            Account::unLock($oAccount->id, $iLocker, false);
        }
        return $this->goBack('success', __('_project.droped'));
    }

    /**
     * 重新派发奖金
     * @param int $id
     * @return Redirect
     */
    public function setPrizeTask($id) {
        $oProject = ManProject::find($id);
        if (empty($oProject)) {
            return $this->goBack('error', __('_basic.no-data'));
        }
        if ($oProject->status != ManProject::STATUS_WON) {
            return $this->goBack('error', __('_project.did-not-win'));
        }
        if ($bSucc = $oProject->setPrizeTask(True)) {
            $sLangKey = '_project.prize-task-seted';
            $sMsgType = 'success';
        } else {
            $sLangKey = '_project.prize-task-set-failed';
            $sMsgType = 'error';
        }
        return $this->goBack($sMsgType, __($sLangKey));
    }

    /**
     * 重新派发佣金
     * @param int $id
     * @return Redirect
     */
    public function setCommissionTask($id) {
        $oProject = ManProject::find($id);
        if (empty($oProject)) {
            return $this->goBack('error', __('_basic.no-data'));
        }
        if (!in_array($oProject->status, [ ManProject::STATUS_WON, ManProject::STATUS_LOST])) {
            return $this->goBack('error', __('_project.status-error'));
        }
        if ($bSucc = $oProject->setCommissionTask()) {
            $sLangKey = '_project.prize-task-seted';
            $sMsgType = 'success';
        } else {
            $sLangKey = '_project.prize-task-set-failed';
            $sMsgType = 'error';
        }
        return $this->goBack($sMsgType, __($sLangKey));
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index($type = NULL) {
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
        
        
            $aseries_way_id = SeriesWay::where('series_methods', '=', '179,180')->get(['id', 'name'])->toArray();
            $series_way_ids = array_column($aseries_way_id, 'id');
            $aConditions['way_id'] = ['in', $series_way_ids];
            
            
        
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
        //如果没有选择查看投注号码， 则不查投注号码
        $realColumsForIndex = null;
        if (isset($this->params['is_bet_number']) && $this->params['is_bet_number'] == '1') {
            $realColumsForIndex = $sModelName::$realColumnsForIndex;
            $sModelName::$columnForList[] = 'display_bet_number';
        }
        $datas = $oQuery->paginate($iPageSize, $realColumsForIndex);
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
        if (isset($this->params['bet_source']) && $this->params['bet_source'] != '') {
            $aPlusConditions['bet_source'] = ['=', $this->params['bet_source']];
        }
        return $aPlusConditions;
    }

    public function download() {
// 总代分布模块，根据奖金组查看总代销量
//        if (isset($this->params['prize_group'])) {
//// 将查询条件输出到视图，以便初始化默认查询条件
//            $this->setVars('prize_group', $this->params['prize_group']);
//        }
//        $this->params['is_agent'] = 1; // 如果只要查看下级代理的数据，强制添加该查询条件
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
        // pr(($aConditions));exit;
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
//        $oQuery = $oQuery->limit(10);

        set_time_limit(0);

        $aData = $oQuery->get(ManProject::$columnForList);
        $aLotteries = & Lottery::getTitleList();
//        $aStatusDesc = $sModelName::$validStatuses;
        $aCoefficients = Config::get('bet.coefficients');
        $aRelations = [
            'lottery_id' => $aLotteries,
            'coefficients' => $aCoefficients
        ];

        $listColumnMaps = [
            'status' => 'formatted_status',
//            'prize' => 'prize_formatted',
            'status_prize' => 'status_prize_formatted',
            'status_commission' => 'status_commission_formatted',
            'is_tester' => 'formatted_is_tester',
        ];
        $aData = $this->_makePrjData($aData, ManProject::$columnForList, $listColumnMaps, $aRelations);
        return $this->downloadExcel(ManProject::$columnForList, $aData, 'Project Report');
    }

    private function _makePrjData($aData, $aFields, $aConvertFields, $aRelations) {

        $aResult = array();
        foreach ($aData as $oData) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oData->$key === '') {
                    $a[] = $oData->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
//                    die($aConvertFields[$key]);
//                    switch ($aConvertFields[$key]) {
//                        case 'user_type_formatted':
                    $a[] = $oData->{$aConvertFields[$key]};
//                            break;
//                    }
                } else {
                    if (array_key_exists($key, $aRelations)) {
                        $a[] = $aRelations[$key][$oData->$key];
                    } else {
                        $a[] = $oData->$key;
                    }
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

}
