<?php

/**
 * 代理销量报表
 *
 * @author snowan
 */
class StatUserProfitController extends AdminBaseController {

    protected $errorFiles = [];
    protected $modelName = 'UserProfit';
    protected $customViewPath = 'userProfit';
    protected $customViews = [
        'index',
    ];

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
                $this->setVars('aWidgets', ['w.search_download']);
                $this->setVars(compact('aUserTypes', 'aAgentPrizeGroups'));
                $this->setVars('aTotalColumns', $sModelName::$totalColumns);
                break;
        }
    }

    
    //统计每日之和
    
    
    
    public function indexQuery() {
        // 总代分布模块，根据奖金组查看总代销量
        if (isset($this->params['prize_group'])) {
            // 将查询条件输出到视图，以便初始化默认查询条件
            $this->setVars('prize_group', $this->params['prize_group']);
        }
        // 如果只要查看下级代理的数据，强制添加该查询条件
        $this->params['is_agent'] = 1;
        return parent::indexQuery();
    }

    public function download() {

        $oQuery = $this->indexQuery();

        set_time_limit(0);

        $aConvertFields = [
            'user_type' => 'user_type_formatted',
        ];

        $aData = $oQuery->get();
        $aData = $this->_makeData($aData, UserProfit::$columnForList, $aConvertFields);
        return $this->downloadExcel(UserProfit::$columnForList, $aData, 'User Profits Report');
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

}
