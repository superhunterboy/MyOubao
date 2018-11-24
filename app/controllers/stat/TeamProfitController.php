<?php

/**
 * 代理销量报表
 *
 */
class TeamProfitController extends ComplicatedSearchController {

    protected $errorFiles = [];
    protected $modelName = 'TeamProfit';
    protected $searchBlade = 'w.team_profit_search';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;

        switch ($this->action) {
            case 'index':
                $aUserTypes = $sModelName::$aUserTypes;
                $iTopAgentMaxPrizeGroup = SysConfig::readValue('top_agent_max_grize_group');
                $iAgentMinPrizeGroup = SysConfig::readValue('agent_min_grize_group');
                $aAgentPrizeGroups = [];
                foreach (range($iAgentMinPrizeGroup, $iTopAgentMaxPrizeGroup) as $key => $value) {
                    $aAgentPrizeGroups[$value] = $value;
                }
                $this->setVars(compact('aUserTypes', 'aAgentPrizeGroups'));
                $this->setVars('aTotalColumns', $sModelName::$totalColumns);
                break;
        }
    }

    public function indexQuery() {
        // 总代分布模块，根据奖金组查看总代销量
        if (isset($this->params['prize_group'])) {
            // 将查询条件输出到视图，以便初始化默认查询条件
            $this->setVars('prize_group', $this->params['prize_group']);
        }
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = & $this->makeSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
        $oQuery = $aConditions ? $this->model->doWhere($aConditions) : $this->model;
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

    public function download() {
        set_time_limit(0);

        $oQuery = $this->indexQuery();


        $aConvertFields = [
            'user_type' => 'user_type_formatted',
        ];

        $aData = $oQuery->get();
        $aData = $this->_makeData($aData, UserProfit::$columnForList, $aConvertFields);
        return $this->downloadExcel(UserProfit::$columnForList, $aData, 'User Profits Report');
    }

    /**
     * 账变搜索中附件的搜索条件
     */
    public function makePlusSearchConditions() {
        $aPlusConditions = [];
        // 包含下级
        if (isset($this->params['un_include_children']) && $this->params['un_include_children'] && !empty($this->params['username'])) {
            $aUserIds = User::getAllUsersBelongsToAgentByUsername($this->params['username'], isset($this->params['un_include_children']));
            if (count($aUserIds) > 0) {
                $aPlusConditions['user_id'] = ['in', $aUserIds];
                unset($this->params['username']);
            } else {
                $aPlusConditions['username'] = ['=', $this->params['username']];
            }
        } else if (!empty($this->params['username'])) {
            // 不包含下级
            $aPlusConditions['username'] = ['=', $this->params['username']];
        }
        //代理类型：总代，下级代理
        if (isset($this->params['user_type']) && $this->params['user_type']) {
            if ($this->params['user_type'] == 2) {
                $aPlusConditions['parent_user_id'] = ['=', null];
            }
        }
        return $aPlusConditions;
    }

    /**
     * [makeSearchConditions 覆盖BaseController的同名方法, 适应根据parent_user_id判断是否总代/一代的搜索]
     * @return [Array] [搜索条件数组]
     */
    protected function & makeSearchConditions() {
// pr($this->params);
        $aConditions = parent::makeSearchConditions();
        $aOperators = ['=', '!='];
        $sOperator = '';
// pr((int)($this->params['parent_id'] === ''));exit;
// parent_user_id 为-1或0时，才根据parent_user_id属性是否为null
        if (isset($this->params['parent_user_id']) && $this->params['parent_user_id'] !== '' && $this->params['parent_user_id'] < 1) {
            $sOperator = $aOperators[$this->params['parent_user_id'] + 1];
            !$sOperator or $aConditions['parent_user_id'] = [$sOperator, null];
// $aConditions['is_agent'] = ['=', 1]; // 如果要查看下级用户的数据，强制添加该查询条件
        }
// parent_user_id 为空时查询所有
        if (isset($this->params['parent_user_id']) && $this->params['parent_user_id'] === '') {
            array_forget($aConditions, 'parent_user_id');
        }
// pr($aConditions);exit;
        return $aConditions;
    }

    function _makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'user_type_formatted':
                            $a[] = $oDeposit->user_type_formatted;
                            break;
                    }
                } else {
                    $a[] = $oDeposit->$key;
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

    /**
     * 根据搜索配置生成搜索表单数据
     */
    function _setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('bNeedCalendar'));
//        !$bNeedCalendar or $this->setvars('aDateObjects',[]);
        $this->setVars('aSearchConfig', $this->searchConfig);
        // 从账户管理-->账变进入账变查询的情况
        if (isset($this->params['user_id'])) {
            $oUser = User::find($this->params['user_id']);
            if (is_object($oUser)) {
                $this->params['username'] = $oUser->username;
            }
        }
        $this->setVars('aSearchFields', $this->params);
        $this->addWidget($this->searchBlade);
    }

}
