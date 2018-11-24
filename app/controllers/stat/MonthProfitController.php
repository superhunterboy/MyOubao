<?php

/**
 * 代理销量报表
 *
 * @author snowan
 */
class MonthProfitController extends AdminBaseController {

    protected $errorFiles = [];
    protected $modelName = 'ManMonthProfit';

    protected function beforeRender() {
        parent::beforeRender();
        switch($this->action){
            case 'index':
                $sDataUpdatedTime = $this->viewVars['datas']->count() ? $this->viewVars['datas'][0]->updated_at : date('Y-m-d H:i:s');
                $this->setVars(compact('sDataUpdatedTime'));
        }
    }
    
    public function download() {
// 总代分布模块，根据奖金组查看总代销量
        if (isset($this->params['prize_group'])) {
// 将查询条件输出到视图，以便初始化默认查询条件
            $this->setVars('prize_group', $this->params['prize_group']);
        }
        $this->params['is_agent'] = 1; // 如果只要查看下级代理的数据，强制添加该查询条件
        $aConditions = & $this->makeSearchConditions();
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

        set_time_limit(0);
        $aTitles = [
            'date' => __('_userprofit.date'),
            'username' => __('_userprofit.username'),
            'is_agent' => __('_userprofit.user_type'),
            'parent_user' => __('_userprofit.parent_user'),
            'prize_group' => __('_userprofit.prize_group'),
            'team_deposit' => __('_userprofit.team_deposit'),
            'team_withdrawal' => __('_userprofit.team_withdrawal'),
            'team_turnover' => __('_userprofit.team_turnover'),
            'team_profit' => __('_userprofit.team_profit'),
            'direct_deposit' => __('_userprofit.direct_deposit'),
            'direct_withdrawal' => __('_userprofit.direct_withdrawal'),
            'direct_turnover' => __('_userprofit.direct_turnover'),
            'direct_commission' => __('_userprofit.direct_commission'),
        ];
        $aConvertFields = [
            'is_agent' => 'user_type_formatted',
        ];

        $aData = $oQuery->get(array_keys($aTitles));
        $aData = $this->_makeData($aData, array_keys($aTitles), $aConvertFields);
        $oDownExcel = new DownExcel;
        $oDownExcel->setTitle($aTitles);
        $oDownExcel->setData($aData);
        $sSheetTitle = $sFileName = 'User Profits Report';
        $oDownExcel->setActiveSheetIndex(0);
        $oDownExcel->setSheetTitle($sSheetTitle);
        $oDownExcel->setEncoding('gb2312');

        $oDownExcel->Download($sFileName);
        return $this->render();
    }

    /**
     * [makeSearchConditions 覆盖BaseController的同名方法, 适应根据parent_user_id判断是否总代/一代的搜索]
     * @return [Array] [搜索条件数组]
     */
//    protected function & makeSearchConditions() {
//// pr($this->params);
//        $aConditions = parent::makeSearchConditions();
//        $aOperators = ['=', '!='];
//        $sOperator = '';
//// pr((int)($this->params['parent_id'] === ''));exit;
//// parent_user_id 为-1或0时，才根据parent_user_id属性是否为null
//        if (isset($this->params['parent_user_id']) && $this->params['parent_user_id'] !== '' && $this->params['parent_user_id'] < 1) {
//            $sOperator = $aOperators[$this->params['parent_user_id'] + 1];
//            !$sOperator or $aConditions['parent_user_id'] = [$sOperator, null];
//// $aConditions['is_agent'] = ['=', 1]; // 如果要查看下级用户的数据，强制添加该查询条件
//        }
//// parent_user_id 为空时查询所有
//        if (isset($this->params['parent_user_id']) && $this->params['parent_user_id'] === '') {
//            array_forget($aConditions, 'parent_user_id');
//        }
//// pr($aConditions);exit;
//        return $aConditions;
//    }

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
