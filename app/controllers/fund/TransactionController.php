<?php

class TransactionController extends ComplicatedSearchController {

    const MANUAL_INPUT = 1;
    const AGENT_LIST = 2;

    protected $searchBlade = 'w.transaction_search';
    /**
     * 资源视图目录
     * @var string
     */
//    protected $resourceView = 'default';

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'Transaction';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $aLotteries = & Lottery::getTitleList();
        $aCoefficients = Config::get('bet.coefficients');
        $this->setVars(compact('aCoefficients', 'aLotteries'));
        $this->setVars('aWays', SeriesWay::getTitleList());
        switch ($this->action) {
            case 'index':
                $aTransactionTypes = TransactionType::getFieldsOfAllTransactionTypesArray();
                $aAdminUsers = AdminUser::getTitleList();
                $aRootAgent = User::getAllUserNameArrayByUserType(User::TYPE_AGENT, 1);
//                $aLotteries = & Lottery::getTitleList();
                $this->setVars('aCoefficients', Config::get('bet.coefficients'));
                $this->setVars(compact('aTransactionTypes', 'aAdminUsers', 'aRootAgent', 'aLotteries'));
                break;
        }
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        $sModelName = $this->modelName;
//        if (isset($this->params['type_id']) && $this->params['type_id'] != ['']){
//            $sModelName::$totalColumnsAllPages[] ='amount';
//        }
        $oQuery = $this->indexQuery();
        
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

    public function indexQuery() {
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
        return $oQuery;
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
                    if (isset($this->params['ra_include_children'])) {
                        $aUserIds = User::getAllUsersBelongsToAgentByUsername($this->params['root_agent'], isset($this->params['ra_exclude_self']));
                        if (count($aUserIds) > 0) {
                            $aPlusConditions['user_id'] = ['in', $aUserIds];
                        }
                    } else if (!isset($this->params['ra_exclude_self'])) {
                        // 不包含下级
                        $aPlusConditions['username'] = ['=', $this->params['root_agent']];
                    } else {
                        $aPlusConditions['id'] = ['=', null];
                    }
                    break;
            }
        }
        return $aPlusConditions;
    }

    /**
     * 根据搜索配置生成搜索表单数据
     */
    function _setSearchInfo() {
        switch($this->action){
            case 'sumData':
                $this->searchBlade = 'w.transaction_sum_data_search';
                if (!isset($this->params['created_at'])){
                    $this->params['created_at'] = [
                      date('Y-m-d 00:00:00', strtotime('-1 Days')),
                      date('Y-m-d 00:00:00', strtotime('-1 Days')),
                    ];
                }
                break;
        }
        
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('bNeedCalendar'));
//        !$bNeedCalendar or $this->setvars('aDateObjects',[]);
        $this->setVars('aSearchConfig', $this->searchConfig);
        // 从账户管理-->帐变进入帐变查询的情况
        if (isset($this->params['user_id'])) {
            $oUser = User::find($this->params['user_id']);
            if (is_object($oUser)) {
                $this->params['username'] = $oUser->username;
            }
        }
        $this->setVars('aSearchFields', $this->params);
        $this->addWidget($this->searchBlade);
    }

    public function download() {
        $oQuery = $this->indexQuery();
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $aConvertFields = [
            'lottery_id' => 'lottery',
            'way_id' => 'way',
            'amount' => 'transaction_amount_formatted',
            'coefficient' => 'coefficient',
            'description' => 'friendly_description',
            'is_tester' => 'boolean',
            'user_forefather_ids' => 'friendly_agent_name',
        ];

        $aLotteries = Lottery::getTitleList();
        $aWays = SeriesWay::getTitleList();
        $aData = $oQuery->get(array_merge(['is_income'], Transaction::$columnForList))->toArray();
        $aData = $this->_makeData($aData, Transaction::$columnForList, $aConvertFields, $aWays, $aLotteries);
        return $this->downloadExcel(Transaction::$columnForList, $aData, 'transaction report');
    }
    
    public function sumData(){
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
//         pr(($aConditions));exit;
        $oQuery = $this->model->doWhere($aConditions);
        $oQuery = $this->model->doGroupBy($oQuery, ['type_id']);
        
        $aRawColumns = [
            'type_id',
            'description',
            'is_income',
        ];
        $aRawColumns[] = DB::raw('SUM(amount) as amount');
        $datas = $oQuery->paginate(100, $aRawColumns);
        
        $sModelName = $this->modelName;
        $sModelName::$columnForList = [
            'amount',
            'description',
//            'type_id',
        ];
        
        $this->action ='index';
    
        $this->setVars(compact('datas', 'aTransactionTypes'));
        $this->view = 'fund.transaction.sum_data';
        return $this->render();
    }

    function _makeData($aData, $aFields, $aConvertFields, $aWays = null, $aLotteries = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit[$key] === '') {
                    $a[] = $oDeposit[$key];
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'transaction_amount_formatted':
                            $a[] = ($oDeposit['is_income'] ? '+' : '-') . $oDeposit['amount'];
                            break;
                        case 'lottery':
                            if (array_key_exists($oDeposit[$key], $aLotteries)) {
                                $a[] = $aLotteries[$oDeposit[$key]];
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'boolean':
                            $a[] = $oDeposit[$key] ? __('Yes') : __('No');
                            break;
                        case 'way':
                            if (array_key_exists($oDeposit[$key], $aWays)) {
                                $a[] = $aWays[$oDeposit[$key]];
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'coefficient':
                            $aCoefficients = Config::get('bet.coefficients');
                            $a[] = $aCoefficients[$oDeposit[$key]];
                            break;
                        case 'friendly_description':
                            $a[] = __('_transactiontype.' . strtolower(Str::slug($oDeposit['description'])));
                            break;
                        case 'friendly_agent_name':
                            if ($aIds = array_get($oDeposit, 'user_forefather_ids')) {
                                $aIds = explode(',', $aIds);
                                $user = User::find($aIds[(count($aIds) - 1)]);
                                if (is_object($user)) {
                                    $a[] = $user->username;
                                } else {
                                    $a[] = '';
                                }
                            } else {
                                $a[] = '';
                            }
                            break;
                    }
                } else {
                    $a[] = $oDeposit[$key];
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

}
