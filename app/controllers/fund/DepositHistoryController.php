<?php

class DepositHistoryController extends ComplicatedSearchController {

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'DepositHistory';
    protected $searchBlade = 'w.user_deposit_history_search';

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        $oQuery = $this->indexQuery();

        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        $datas = $oQuery->paginate($iPageSize);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        // pr(($datas->toArray()));exit;
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

    public function indexQuery() {
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
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
        return $oQuery;
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        // $sModelName = $this->modelName;
        // $aTotalColumns = [];
        // $aColumnsTotalData = [];
        // $aColumnsNeedTotal = $sModelName::$totalColumns;

        $this->setVars('validStatuses', Deposit::$validStatuses);
        $this->setVars('aDepositMode', Deposit::$aDepositMode);
        $this->setVars('aNoteMode', Deposit::$aNoteMode);
        $this->setVars('aDeductFee', Deposit::$aDeductFee);
//        pr(Deposit::$validStatuses);exit;
        $oBank = new Bank;
        $this->setVars('aBanks', $oBank->getTitleList());
        switch ($this->action) {
            case 'index':
                // foreach ($this->viewVars['datas']->toArray()['data'] as $key => $data) {
                //     foreach ($data as $column => $value) {
                //         if (! in_array($column, $aColumnsNeedTotal)) continue;
                //         if (! isset($aColumnsTotalData[$column])) {
                //             $aColumnsTotalData[$column] = 0;
                //         }
                //         $aColumnsTotalData[$column] += $value;
                //     }
                // }
                // foreach ($this->viewVars['aColumnForList'] as $sColumn) {
                //     if (in_array($sColumn, $aColumnsNeedTotal)) {
                //         $aTotalColumns[$sColumn] = $aColumnsTotalData[$sColumn];
                //     } else {
                //         $aTotalColumns[] = '';
                //     }
                // }
                // $this->setVars(compact('aTotalColumns'));
                $this->resourceView = 'fund.deposit';
                break;
        }
    }

    public function download() {

        $oQuery = $this->indexQuery();

        set_time_limit(0);

        $aConvertFields = [
            'status' => 'formatted_status',
            'bank_id' => 'bank',
            'deposit_mode' => 'deposit_mode',
            'created_at' => 'date',
            'updated_at' => 'deposit_add_game_money_time',
        ];

        $aBanks = Bank::getTitleList();
        $aData = $oQuery->get(Deposit::$columnForList);
        $aData = $this->_makeData($aData, Deposit::$columnForList, $aConvertFields, $aBanks);
        return $this->downloadExcel(Deposit::$columnForList, $aData, 'Deposit Report');
    }

    function makePlusSearchConditions() {
        $aConditions = [];
        if (isset($this->params['real_time'][0]) && !empty($this->params['real_time'][0]) || isset($this->params['real_time'][1]) && !empty($this->params['real_time'][1])) {
            $aConditions['status'] = ['=', Deposit::DEPOSIT_STATUS_SUCCESS];
        }
        return $aConditions;
    }

}
