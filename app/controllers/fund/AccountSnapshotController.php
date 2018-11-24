<?php
class AccountSnapshotController extends ActivityReportBaseController {

    protected $modelName = 'AccountSnapshot';


    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':

                // user search form
                $this->setVars('aSearchFields', $this->params);
                $this->setVars('aWidgets', ['w.accountSnapshot_search']);
                break;
            case 'view':
            case 'edit':
            case 'create':
            case 'resetPassword':
                break;
            case 'agentPrizeGroupList':
                $aUserTypes = [User::TYPE_AGENT => User::$aUserTypes[1], User::TYPE_TOP_AGENT => User::$aUserTypes[2]];
                $this->setVars(compact('aUserTypes'));
                break;
            // case 'agentDistributionList':
            //     $aButtonParamMap = User::$aButtonParamMap;
            //     $this->setVars(compact('aButtonParamMap'));
            //     break;
        }
    }


    public function indexQuery() {
        $aConditions = & $this->makeSearchConditions();
        if(isset($aConditions['date'])){
            $date= $aConditions['date'][1];
            unset($aConditions['date']);
            $aConditions['date']=['between',[$date.' 00:00:00',$date.' 23:59:59']];
        }
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
     * 用户搜索中附件的搜索条件
     */
    public function makePlusSearchConditions() {
        $aPlusConditions = [];
        if (isset($this->params['blocked']) && is_numeric($this->params['blocked']))
        {
            $this->params['blocked'] = intval($this->params['blocked']);

            if($this->params['blocked'] === AccountSnapshot::UNBLOCK)
            {
                $aPlusConditions['blocked'] = ['=', AccountSnapshot::UNBLOCK];
            }else{
                $aPlusConditions['blocked'] = ['!=', AccountSnapshot::UNBLOCK];
            }
        }

        return $aPlusConditions;
    }
}