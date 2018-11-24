<?php

/**
 * 团队盈亏报表
 */
class UserTeamProfitController extends UserBaseController {

    protected $resourceView = 'centerUser.team_profit';
    protected $modelName = 'TeamProfit';
    public $resourceName = '';
    protected $customViews = [
        'index', 'self',
    ];

    public function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                $aUserTypes = User::$aUserTypes;
                $this->setVars(compact('aUserTypes'));
                $this->setVars('reportName', 'team-profit');
                break;
            case 'self':
                $this->setVars('reportName', 'self');
                break;
            case 'commission':
                $this->setVars('reportName', 'team-commission');
                break;
        }
    }

    /**
     * index 团队日结报表
     */
    public function index() {
        $sUsername = Session::get('username');
        if (!key_exists('date_from', $this->params) && !key_exists('date_to', $this->params)) {
            $this->params['date_from'] = date('Y-m-d', strtotime('-6 days'));
            $this->params['date_to'] = date('Y-m-d');
        }
        if (!array_get($this->params, 'date_from') && !array_get($this->params, 'date_to')) {
            $this->params['date_from'] = date('Y-m-d', strtotime('-6 days'));
            $this->params['date_to'] = date('Y-m-d');
        }
        if (array_get($this->params, 'date_from') && array_get($this->params, 'date_to') && array_get($this->params, 'date_from') > array_get($this->params, 'date_to')) {
            return $this->goBack('error', '开始日期不能大于结束日期');
        }
        $this->params['parent_user'] = key_exists('username', $this->params) && !empty($this->params['username']) ? $this->params['username'] : $sUsername;
        $this->params['username'] = key_exists('username', $this->params) && !empty($this->params['username']) ? $this->params['username'] : $sUsername;
        $this->setVars('search_params', $this->params);
        Request::merge(['group_by' => 'username']);
        $oUser = UserUser::getObjectByParams(['username' => $this->params['username']]);
        if (is_object($oUser) && $oUser->id != Session::get('user_id')) {
            if (!$oUser->forefather_ids) {
                return $this->goBack('error', __('_project.search-not-allowed'));
            }
            $aParent = explode(',', $oUser->forefather_ids);
            if (!in_array(Session::get('user_id'), $aParent)) {
                return $this->goBack('error', __('_project.search-not-allowed'));
            }
        }
        $oQuery = $this->model->getGroupProfit();
        $oQuery = $this->indexQuery($oQuery);
        
        $aUserProfit = UserProfit::getAgentSumInfo($this->params['date_from'], $this->params['date_to'], $oUser->id);
        $aUserProfit['username'] = $this->params['username'];
        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        $datas = $oQuery->paginate($iPageSize);
        $this->setVars(compact('datas','aUserProfit'));
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
     * 团队盈亏报表
     */
    public function groupIndex() {
        $sUsername = Session::get('username');
        if (!key_exists('date_from', $this->params) && !key_exists('date_to', $this->params)) {
            $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
        }
        if (!array_get($this->params, 'date_from') && !array_get($this->params, 'date_to')) {
            $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
        }
        if (array_get($this->params, 'date_from') && array_get($this->params, 'date_to') && array_get($this->params, 'date_from') > array_get($this->params, 'date_to')) {
            return $this->goBack('error', '开始日期不能大于结束日期');
        }

        $this->params['parent_user'] = key_exists('username', $this->params) && !empty($this->params['username']) ? $this->params['username'] : $sUsername;
        $this->params['username'] = key_exists('username', $this->params) && !empty($this->params['username']) ? $this->params['username'] : $sUsername;
        $this->setVars('search_params', $this->params);
        Request::merge(['group_by' => 'username']);
        $oUser = UserUser::getObjectByParams(['username' => $this->params['username']]);
        if (is_object($oUser) && $oUser->id != Session::get('user_id')) {
            if (!$oUser->forefather_ids) {
                return $this->goBack('error', __('_project.search-not-allowed'));
            }
            $aParent = explode(',', $oUser->forefather_ids);
            if (!in_array(Session::get('user_id'), $aParent)) {
                return $this->goBack('error', __('_project.search-not-allowed'));
            }
        }
        $oQuery = $this->model->getGroupProfit();
        $oQuery = $this->indexQuery($oQuery);
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

    public function commission() {
        return $this->index();
    }

    public function home() {
        $sUsername = Session::get('username');
        if (!key_exists('date_from', $this->params) && !key_exists('date_to', $this->params)) {
            $this->params['date_from'] = date('Y-m-d', strtotime("-6 days"));
            $this->params['date_to'] = date('Y-m-d');
        }
        if (!array_get($this->params, 'date_from') && !array_get($this->params, 'date_to')) {
            $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
        }
        if (array_get($this->params, 'date_from') && array_get($this->params, 'date_to') && array_get($this->params, 'date_from') > array_get($this->params, 'date_to')) {
            return $this->goBack('error', '开始日期不能大于结束日期');
        }

        $this->params['parent_user'] = key_exists('username', $this->params) && !empty($this->params['username']) ? $this->params['username'] : $sUsername;
        $this->params['username'] = key_exists('username', $this->params) && !empty($this->params['username']) ? $this->params['username'] : $sUsername;
        $this->setVars('search_params', $this->params);
        Request::merge(['group_by' => 'username']);
        $oUser = UserUser::getObjectByParams(['username' => $this->params['username']]);
        if (is_object($oUser) && $oUser->id != Session::get('user_id')) {
            if (!$oUser->forefather_ids) {
                return $this->goBack('error', __('_project.search-not-allowed'));
            }
            $aParent = explode(',', $oUser->forefather_ids);
            if (!in_array(Session::get('user_id'), $aParent)) {
                return $this->goBack('error', __('_project.search-not-allowed'));
            }
        }
        $oQuery = $this->model->getGroupProfit();
        $oQuery = $this->homeQuery($oQuery);
        $aTeamProjectData = UserProject::getTeamProjectInfo($oUser->id, $this->params['date_from'], $this->params['date_to'], TRUE);
        $aTeamAccountData = Account::getTeamAccountInfo($oUser->id, TRUE);
        $iSubUserCount = count(UserUser::getAllUsersBelongsToAgent($oUser->id));
        $iRegistUserCount = UserUser::getRegisterNums($oUser->id, $this->params['date_from'], $this->params['date_to']);
        $iTeamOnlineCount = UserOnline::getTeamAgentOnlineNum($oUser->id);
        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        $datas = $oQuery->paginate($iPageSize);
        $this->setVars(compact('datas', 'aTeamProjectData', 'aTeamAccountData', 'iSubUserCount', 'iTeamOnlineCount', 'iRegistUserCount'));
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

    public function indexQuery($oQuery = null) {
        $aConditions = & $this->makeSearchConditions();
        $oQuery = $aConditions || $oQuery ? $this->model->doWhere($aConditions, $oQuery) : $this->model;
        if (array_get($this->params, 'username') && array_get($this->params, 'parent_user')) {
            $oQuery = $oQuery->where(function($query) {
                $query->where('parent_user', '=', $this->params['parent_user']);
                ;
            });
        }
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

    public function homeQuery($oQuery = null) {
        $aConditions = & $this->makeSearchConditions();
        $oQuery = $aConditions || $oQuery ? $this->model->doWhere($aConditions, $oQuery) : $this->model;
        if (array_get($this->params, 'username') && array_get($this->params, 'parent_user')) {
            $oQuery = $oQuery->where(function($query) {
                $query->where('username', '=', $this->params['username']);
            });
        }
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

    public function self(){
    $iUserId = Session::get('user_id');
    $this->params['user_id'] = $iUserId;
    return parent::index();
}

}
