<?php

/**
 * 移动端系统基础控制器
 *
 * @author frank
 */
class MobileBaseController extends BaseController {

    /**
     * 是否是移动端交互
     * @var bool
     */
    protected $isMobile = true;
    protected $messages;
    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway', 'mobile'];
    protected $resourceView = 'template.ucenter';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName;

    /**
     * 模型实例
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Resource , use for route
     */
    protected $resource;

    /**
     * 资源数据库表
     * @var string
     */
    protected $resourceTable = '';

    /**
     * 资源名称
     * @var string
     */
    protected $resourceName = '';

    /**
     * Controller
     */
    protected $controller;

    /**
     * Action
     */
    protected $action;

    /**
     * var for views
     */
    protected $viewVars = [];

    /**
     * pagesize
     * @var int
     */
    protected static $pagesize = 50;
    protected static $originalColumns = [];

    /**
     * 检查是否登录
     * @return bool
     */
    protected function checkLogin() {
        return boolval(Session::get('user_id'));
    }

    /**
     * 如果未登录时执行的动作
     * @return type
     */
    protected function doNotLogin() {
        $this->halt(false, 'loginTimeout', Config::get('global_error.ERRNO_LOGIN_EXPIRED'));
    }

    /**
     * 获取可访问的功能ID数组
     *
     * @return Array              根据$returnType得到的不同数组
     */
    protected function & getUserRights() {
        $roleIds = Session::get('CurUserRole');
        $aRights = & Role::getRightsOfRoles($roleIds);
        return $aRights;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $iUserId = Session::get('user_id');
        $oUser = User::find($iUserId);
        // 用户中心header所需的账户信息
        if (Session::get('is_agent')) {
            $sDate = Carbon::yesterday()->toDateString();
            // TODO UserProfit::getUserProfitObject 方法当没有在表中查询到数据时, 生成的UserProfit没有team_turnover属性
            $oUserProfit = UserProfit::getUserProfitObject($sDate, $iUserId);
            $fTeamTurnOver = formatNumber($oUserProfit->team_turnover + 0, 1);
            // $iTeamUserNum    = $oUser->getAgentDirectChildrenNum();
            $oChildUsers = $oUser->getUsersBelongsToAgent();
            $iTeamUserNumTotal = count($oChildUsers);
            // TODO 修改总代首页和一代一样，此处的分类可以先注释
            // if (Session::get('is_top_agent')) {
            //     $iTeamUserNumAgent  = 0;
            //     $iTeamUserNumPlayer = 0;
            //     foreach ($oChildUsers as $key => $value) {
            //         if ($value->is_agent) $iTeamUserNumAgent++;
            //         else $iTeamUserNumPlayer++;
            //     }
            //     $this->setVars(compact('iTeamUserNumAgent', 'iTeamUserNumPlayer'));
            // }
            $iTeamAccountCount = $oUser->getGroupAccountSum();
            $this->setVars(compact('fTeamTurnOver', 'iTeamUserNumTotal', 'iTeamAccountCount'));
        }
        $fAvailable = formatNumber(Account::getAvaliable($iUserId), 1);
        $iOpen = Session::get('is_tester') ? null : 1;
        $aSeriesLotteries = Series::getLotteriesGroupBySeries($iOpen);
        // App::setLocale('en');
        $aLotteries = & Lottery::getLotteryList();
        $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
//        $sNowHour = date('G');
//        $sWelcomeTime = $sNowHour < 12 ? '上午好,' : ( $sNowHour < 18 ? '下午好,' : '晚上好,');
        // pr($aSeriesLotteries);exit;
        $this->setVars(compact('unreadMessagesNum', 'fAvailable', 'aSeriesLotteries', 'aLotteries'));
        // pr($this->viewVars['resource']);
        // pr('--------');
        // pr($this->viewVars['resourceName']);
        // exit;
    }

    //    protected function render(){
    //        $this->beforeRender();
    //        // pr($this->viewVars);exit;
    //        return View::make($this->resourceView . '.' . $this->action)->with($this->viewVars);
    //    }
    // public function index()
    // {
    //     return $this->render();
    // }
    // public function create($id = null)
    // {
    //     return $this->render();
    // }
    public function destroy($id) {
        if (!$this->filterRights($id)) {
            App::abort(404);
        }
        $this->model = $this->model->find($id);
        $sModelName = $this->modelName;
        if ($sModelName::$treeable) {
            if ($iSubCount = $this->model->where('parent_id', '=', $this->model->id)->count()) {
                $reason = __('_basic.not-leaf', $this->langVars);
                $this->halt(false, 'error', null, $a, $a, $a, null, $reason);
            }
        }
        DB::connection()->beginTransaction();
        if ($bSucc = $this->model->delete()) {
            $bSucc = $this->afterDestroy();
        }

        $bSucc ? DB::connection()->commit() : DB::connection()->rollback();

        $sLangKey = '_basic.' . ($bSucc ? 'deleted' : 'delete-fail.');
        $this->halt($bSucc, 'success', null, $a, $a, $a, null, __($sLangKey, $this->langVars));
    }

    public function edit($id) {
        if (!$this->filterRights($id))
            App::abort(404);
        return parent::edit($id);
    }

    /**
     * [filterRights 过滤访问权限，只有属于该用户或总代的记录可以被访问]
     * @param  [Integer] $id [数据记录的id]
     * @return [Integer]     [是否有权限, 0:否, 1:是]
     */
    private function filterRights($id) {
        $bSucc = true;
        // 只需过滤view, edit, destroy三种视图
        if (in_array($this->action, ['view', 'edit', 'destroy'])) {
            $sModelName = $this->modelName;
            $sTable = $this->model->getTable();
            $oResult = $sModelName::find($id);
            if (!is_object($oResult)) {
                return false;
            }
            $originalColumns = Schema::getColumnListing($sTable);
            if (in_array('user_id', $originalColumns)) {
                $iUserId = Session::get('user_id');
                $rUserId = $sModelName::find($id)->user_id;
                $sForefatherIds = User::find($rUserId)->forefather_ids;
                $aForefatherIds = explode(',', $sForefatherIds);
                $bIsAgent = Session::get('is_agent');
                $bIsTopAgent = Session::get('is_top_agent');
                // pr($sModelName);
                // pr($bIsTopAgent);
                // pr($bIsAgent);
                // pr($aForefatherIds);
                // pr($rUserId);
                // pr($iUserId);
                // exit;
                // 只有view视图需要判断是否是代理的子用户的数据
                // $bSucc = ($bIsAgent && !$bIsTopAgent && $this->action == 'view') ? in_array($iUserId, $aForefatherIds) : ($iUserId == $rUserId);
                $bSucc = ($iUserId == $rUserId or in_array($iUserId, $aForefatherIds));
            }
        }
        // pr((int)$bSucc);exit;
        return $bSucc;
    }

    /**
     * [getSumData 获取统计值]
     * @param  [Array]  $aSumColumns [待统计的列]
     * @param  [boolean] $bPerPage   [是否按页统计，该功能采用视图中操作每页数据的方式实现，以前的逻辑暂时注释掉]
     * @return [Array]               [统计数据]
     */
    public function getSumData($aSumColumns, $bPerPage = false) {
        // TODO 和BaseController中的查询有所重复，后续改进
        $aConditions = & $this->makeSearchConditions();
        $oQuery = $this->model->doWhere($aConditions);
        // $iPage              = Input::get('page', 1);
        // pr($aConditions);
        // pr($iPage . ',' . static::$pagesize);
        // pr($this->params);exit;
        $aRawColumns = [];
        // $aParams     = array_values($this->params);
        foreach ($aSumColumns as $key => $value) {
            $aRawColumns[] = DB::raw('SUM(' . $value . ') as ' . $value . '_sum');
        }
        $aSum = [];
        // if ($bPerPage) {
        //     $oQuery = $oQuery->forPage($iPage, static::$pagesize);
        //     $oQuerySql = $oQuery->toSql();
        //     pr($oQuerySql);
        //     $sSql = $this->model->select($aRawColumns)->toSql();
        //     // pr($aParams);exit;
        //     $aSumObjects = DB::select('select ' . implode(',', $aRawColumns) . ' from (' . $oQuerySql . ') as temp', $aParams);
        //     foreach ($aSumObjects[0] as $key => $value) {
        //         $aSum[$key] = $value;
        //     }
        // } else {
        //     pr($oQuery->toSql());exit;
        $aSum = $oQuery->get($aRawColumns)->toArray();
        if (count($aSum))
            $aSum = $aSum[0];
        // }
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);
        // pr($aSum);exit;
        return $aSum;
    }

    /**
     * 将需要缓存的url信息保存到session中
     */
    protected function saveUrlToSession() {
        Session::forget('request_full_url');
        Session::push('request_full_url', Request::url());
    }

    /**
     * 返回先前请求的url信息
     * @param string $sRoute    路由信息
     * @return string
     */
    protected function getUrlFromSession($sRoute = 'home.index') {
        if (Session::has('request_full_url')) {
            $sUrl = Session::get('request_full_url')[0];
        } else {
            $sUrl = Route($sDefaultUrl);
        }
        return $sUrl;
    }

    /**
     * 检查当前用户是否有权限访问当前功能
     * @return boolean
     */
    protected function checkRight() {
        if ($this->functionality) {
            $this->paramSettings = FunctionalityParam::getParams($this->functionality->id);
            // pr($this->functionality->id);
            // $queries = DB::getQueryLog();
            // $last_query = end($queries);
            // pr($last_query);exit;
            // pr($this->paramSettings);
            // exit;
            if ($this->isMobile) {
                $this->params = getJsonData();
                if (is_array($this->params) && key_exists('jsessionid', $this->params)) {
                    unset($this->params['jsessionid']);
                }
            } else {
                $this->params = trimArray(Input::except('_token'));
            }
            if ($this->functionality->need_search) {
                $this->getSearchConfig();
                $this->_setSearchInfo();
            }
            $roleIds = Session::get('CurUserRole');
//                if ($this->admin){
//                    $adminRoleId = Role::ADMIN;
//                    $enabled = in_array($adminRoleId, $roleIds);
//                }
            if (!isset($enabled)) {
                $this->hasRights = & $this->getUserRights();
//                    pr($this->hasRights);
                // pr($this->functionality->id);
                // exit;
                $enabled = in_array($this->functionality->id, $this->hasRights);
//                    pr($enabled);
//                    exit;
            }
        } else {
            $enabled = false;
        }
        return $enabled;
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function mobileIndex($aColumns = ['*']) {
        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        !key_exists('page', $this->params) or Request::merge(['page' => array_get($this->params, 'page')]);
        $datas = $oQuery->paginate($iPageSize, $aColumns);
        if ($this->isMobile) {
            return $datas->toArray();
        }
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
     * view model
     * @param int $id
     * @return bool
     */
    public function view($id) {
        if (!$this->filterRights($id)) {
            if ($this->isMobile) {
                $iErrno = Config::get('global_error.ERRNO_MISSING_DATA');
                // 重载Message类，符合第三个参数传递字符串的情况
                $this->halt(false, 'error', $iErrno);
            } else {
                App::abort(404);
            }
        }
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $data = $this->model;
        if ($this->isMobile) {
            return $data->toArray();
        }
        $sModelName = $this->modelName;
        if ($sModelName::$treeable) {
            if ($this->model->parent_id) {
                if (!array_key_exists('parent', $this->model->getAttributes())) {
                    $sParentTitle = $sModelName::find($this->model->parent_id)->{$sModelName::$titleColumn};
                } else {
                    $sParentTitle = $this->model->parent;
                }
            } else {
                $sParentTitle = '(' . __('_basic.top_level', [], 3) . ')';
            }
        }
        $this->setVars(compact('data', 'sParentTitle'));
        return $this->render();
    }

    public function __destruct() {
        Session::save();
        $sJessionId = Session::getId();
        if (Cache::has($sJessionId)) {
            Cache::put($sJessionId, Cache::get($sJessionId), 10080);
        }
        parent::__destruct();
    }

}
