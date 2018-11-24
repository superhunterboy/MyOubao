<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-3-8
 * Time: 下午1:16
 */
class ProjectHistoryController extends ProjectController
{
    const MANUAL_INPUT = 1;
    const AGENT_LIST = 2;

    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];
    protected $searchBlade = 'w.project_history_search';
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'project';
    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'view',
    ];

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
            case 'index':
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
     * 查看投注号码
     * @param type $id
     * @return type
     */
    public function checkBetNum($id){
        $bet_num_pwd=Input::get('bet_num_pwd');
        $check_bet_num_keys=Config::get('bet.check_bet_num_keys');
        if(empty($bet_num_pwd)||empty($check_bet_num_keys)){
            $aDatas = [
                'isSuccess' => 0,
                'msg' =>'',
                'type' => 'error',
                'data' => ''
            ];
        }
        $isSucess=0;
        $display_bet_number='';
        if(in_array($bet_num_pwd,$check_bet_num_keys)){
            $isSucess=1;
            //查询数据
            $data=ManProject::find($id);
            $display_bet_number=$data->display_bet_number;
            //如果还未生成投注号码，进行格式化显示
            if (!isset($display_bet_number)){
                $display_bet_number=$data->getDisplayBetNumber();
            }

        }
        $aDatas = [
            'isSuccess' => $isSucess,
            'msg' =>'',
            'type' => $isSucess == 1 ? 'success' : 'error',
            'data' => $display_bet_number
        ];
        return Response::json($aDatas);
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        try{
            $oManProject = new ManProject();
            if(isset($this->params['year']) && isset($this->params['quarter']) && $this->params['year'] && $this->params['quarter']) {
                $oManProject->setTable('projects_'.$this->params['year'].$this->params['quarter']);
            }
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
            $oQuery = $this->doWhere($oManProject, $aConditions);
            // TODO 查询软删除的记录, 以后需要调整到Model层
            $bWithTrashed = trim(Input::get('_withTrashed', 0));
            // pr($bWithTrashed);exit;
            if ($bWithTrashed)
                $oQuery = $oQuery->withTrashed();
            if ($sGroupByColumn = Input::get('group_by')) {
                $oQuery = $oQuery->doGroupBy($oQuery, [$sGroupByColumn]);
            }
            // 获取排序条件
            $aOrderSet = [];
            if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
                $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
                $aOrderSet[$sOorderColumn] = $sDirection;
            }
            $oQuery = $oManProject->doOrderBy($oQuery, $aOrderSet);

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
        }catch (Exception $e){
            return $this->goBack('error', '该季度还未备份');
        }
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
        if(isset($this->params['bet_source']) && $this->params['bet_source'] != ''){
            $aPlusConditions['bet_source'] = ['=', $this->params['bet_source']];
        }
        return $aPlusConditions;
    }

    public function download() {
        try{
            $oManProject = new ManProject();
            if(isset($this->params['year']) && isset($this->params['quarter']) && $this->params['year'] && $this->params['quarter']) {
                $oManProject->setTable('projects_'.$this->params['year'].$this->params['quarter']);
            }
            $aConditions = & $this->makeSearchConditions();
            $aPlusConditions = $this->makePlusSearchConditions();
            $aConditions = array_merge($aConditions, $aPlusConditions);
            // pr(($aConditions));exit;
            $oQuery = $this->doWhere($oManProject, $aConditions);
            // TODO 查询软删除的记录, 以后需要调整到Model层
            $bWithTrashed = trim(Input::get('_withTrashed', 0));
            // pr($bWithTrashed);exit;
            if ($bWithTrashed)
                $oQuery = $oQuery->withTrashed();
            if ($sGroupByColumn = Input::get('group_by')) {
                $oQuery = $oManProject->doGroupBy($oQuery, [$sGroupByColumn]);
            }
            // 获取排序条件
            $aOrderSet = [];
            if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
                $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
                $aOrderSet[$sOorderColumn] = $sDirection;
            }
            $oQuery = $oManProject->doOrderBy($oQuery, $aOrderSet);
    //        $oQuery = $oQuery->limit(10);

            set_time_limit(0);

            //如果没有选择查看投注号码， 则不查投注号码
            $sModelName = $this->modelName;
            if (isset($this->params['is_bet_number']) && $this->params['is_bet_number'] == '1') {
                $sModelName::$columnForList[]='display_bet_number';
            }
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
        }catch (Exception $e){
            return $this->goBack('error', '该季度还未备份');
        }
    }

    protected function _makePrjData($aData, $aFields, $aConvertFields, $aRelations) {

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


    /**
     * 批量设置查询条件，返回Query实例
     *
     * @param array $aConditions
     * @return Query
     */
    public static function doWhere($oQuery, $aConditions = []) {
        is_array($aConditions) or $aConditions = [];
        foreach ($aConditions as $sColumn => $aCondition) {
            if (!is_array($aCondition)){
                $aCondition = ['=', $aCondition];
            }
            $sObject = isset($oQuery) ? '$oQuery->' : 'self::';
            $statement = '';
            switch ($aCondition[0]) {
                case '=':
                    if (is_null($aCondition[1])) {
                        $statement = '$oQuery = ' . $sObject . 'whereNull($sColumn);';
                    } else {
                        $statement = '$oQuery = ' . $sObject . 'where($sColumn , \'=\' , $aCondition[ 1 ]);';
                    }
                    break;
                case 'in':
                    $array = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    $statement = '$oQuery = ' . $sObject . 'whereIn($sColumn , $array);';
                    break;
                case '>=':
                case '<=':
                case '<':
                case '>':
                case 'like':
                    if (is_null($aCondition[1])) {
                        $statement = '$oQuery = ' . $sObject . 'whereNotNull($sColumn);';
                    } else {
                        $statement = '$oQuery = ' . $sObject . 'where($sColumn,$aCondition[ 0 ],$aCondition[ 1 ]);';
                    }
                    break;
                case '<>':
                case '!=':
                    if (is_null($aCondition[1])) {
                        $statement = '$oQuery = ' . $sObject . 'whereNotNull($sColumn);';
                    } else {
                        $statement = '$oQuery = ' . $sObject . 'where($sColumn,\'<>\',$aCondition[ 1 ]);';
                    }
//                    echo $statement,"\n";
                    break;
                case 'between':
                    $statement = '$oQuery = ' . $sObject . 'whereBetween($sColumn,$aCondition[ 1 ]);';
                    break;
            }
//            echo $statement,"\n";
            eval($statement);
        }
//        exit;
        if (!isset($oQuery)) {
            $oQuery = self::where('id', '>', '0');
        }
        return $oQuery;
    }

}