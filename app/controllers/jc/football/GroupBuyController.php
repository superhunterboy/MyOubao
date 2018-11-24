<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-11-26
 * Time: 上午11:36
 */

namespace JcController;
use AdminController;
use JcModel\ManJcBet;
use JcModel\ManJcGroupBuy;


class GroupBuyController extends \ComplicatedSearchController
{
    const MANUAL_INPUT = 1;
    const AGENT_LIST = 2;
    protected $searchBlade = 'w.jc_group_buy_search';
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\ManJcGroupBuy';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('validStatus', ManJcGroupBuy::$validStatus);
        $this->setVars('validPrizeStatus', ManJcGroupBuy::$validPrizeStatus);
        $this->setVars('aCommissionStatus', ManJcGroupBuy::$validCommissionStatus);
        $this->setVars('validTypes', ManJcGroupBuy::$validShowType);
        $this->setVars('aCoefficients', \Config::get('bet.coefficients'));
        switch ($this->action) {
            case 'index':
                $aRootAgent = \User::getAllUserNameArrayByUserType(\User::TYPE_AGENT, 1);
                $this->setVars(compact('aRootAgent'));
                break;
            case 'view':
            case 'edit':
                break;
            case 'create':
                break;
        }
    }
    
    public function viewBetDetail($id){
        $oGroupBuy = ManJcGroupBuy::find($id);
        $iBetId = $oGroupBuy->bet_id;
        $oBet = ManJcBet::getBet($iBetId);
        $this->setVars('aMatches',$oBet);
        $this->setVars('id',$iBetId);
        return $this->render();
    }
    
    public function drop($id){
        $oGroupBuy = ManJcGroupBuy::find($id);
        \DB::beginTransaction();
        $bSucc = $oGroupBuy->doDrop();
        $bSucc ? \DB::commit() : \DB::rollback();
        if ($bSucc) {
            return $this->goBack('success', '操作成功！');
        } else {
            return $this->goBack('error', '操作失败！');
        }
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
//         pr(($aConditions));
        $oQuery = $this->model->doWhere($aConditions);
        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(\Input::get('_withTrashed', 0));
        // pr($bWithTrashed);exit;
        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = \Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = \Input::get('sort_up', \Input::get('sort_down'))) {
            $sDirection = \Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);


        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        //如果没有选择查看投注号码， 则不查投注号码
        $realColumsForIndex=null;
        if (isset($this->params['is_bet_number']) && $this->params['is_bet_number'] == '1') {
            $realColumsForIndex=$sModelName::$realColumnsForIndex;
            $sModelName::$columnForList[]='display_bet_number_formal';
        }
        $datas = $oQuery->paginate($iPageSize,$realColumsForIndex);
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
                        $aUserIds = \User::getAllUsersBelongsToAgentByUsername($this->params['username'], isset($this->params['un_include_children']));
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
                        $aUserIds = \User::getAllUsersBelongsToAgentByUsername($this->params['root_agent']);
                        if (count($aUserIds) > 0) {
                            $aPlusConditions['user_id'] = ['in', $aUserIds];
                        } else {
                            $aPlusConditions['user_id'] = ['=', 0];
                        }
                    }
                    break;
            }
        }
        if(isset($this->params['bet_source']) && $this->params['bet_source'] != ''){
            $aPlusConditions['bet_source'] = ['=', $this->params['bet_source']];
        }
        return $aPlusConditions;
    }

    public function download() {
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);

        $oQuery = $this->model->doWhere($aConditions);
        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(\Input::get('_withTrashed', 0));

        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = \Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = \Input::get('sort_up', \Input::get('sort_down'))) {
            $sDirection = \Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);


        set_time_limit(0);

        $aData = $oQuery->get(ManJcProject::$columnForList);

        $listColumnMaps = [
            'status' => 'formatted_status',
            'status_prize' => 'status_prize_formatted',
            'status_commission' => 'status_commission_formatted',
            'is_tester' => 'formatted_is_tester',
        ];

        $aRelations = [];

        $aData = $this->_makePrjData($aData, ManJcProject::$columnForList, $listColumnMaps, $aRelations);
        return $this->downloadExcel(ManJcProject::$columnForList, $aData, 'Jc Project Report');
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
                    $a[] = $oData->{$aConvertFields[$key]};
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