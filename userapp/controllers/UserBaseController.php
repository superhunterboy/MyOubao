<?php

class UserBaseController extends BaseController {

    protected $messages;
    protected $errorFiles = array('system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway', 'suggestion');
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
    protected $viewVars = array();

    /**
     * pagesize
     * @var int
     */
    static protected $pagesize = 15;
    static protected $originalColumns = array();

    protected function checkLogin() {
        $user_id = Session::get('user_id');

        if (!$user_id) {
            return boolval($user_id);
        }

        return UserOnline::isOnline($user_id);
    }

    protected function doNotLogin() {
        Session::flush();

        if ($this->isAjax) {
            $this->halt(false, 'loginTimeout', Config::get('global_error.ERRNO_LOGIN_EXPIRED'));
        } else {
            $this->beforeFilter('user-auth');
            return Redirect::route('signin');
        }
    }

    protected function& getUserRights() {
        $roleIds = Session::get('CurUserRole');
        $aRights = &Role::getRightsOfRoles($roleIds);
        return $aRights;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('iDefaultPaymentPlatformId', PaymentPlatform::getDefaultPlatformId());
        $iUserId = Session::get('user_id');
        $current_uri = explode('/', Route::current()->getPath());
        $current_tab = end($current_uri);
        $fAvailable = Account::getAccountInfoByUserId($iUserId, array('available'))->available;
        $iOpen = (Session::get('is_tester') ? NULL : 1);
        $aLatestAnnouncements = CmsArticle::getLatestRecords(5);
        $aLotteries = &Lottery::getLotteryList();
        $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
        $this->setVars(compact('current_tab', 'unreadMessagesNum', 'aLotteries', 'fAvailable', 'aLatestAnnouncements'));
    }

    public function destroy($id) {
        if (!$this->filterRights($id)) {
            App::abort(404);
        }

        return parent::destroy($id);
    }

    public function edit($id) {
        if (!$this->filterRights($id)) {
            App::abort(404);
        }

        return parent::edit($id);
    }

    public function view($id) {
        if (!$this->filterRights($id)) {
            App::abort(404);
        }

        return parent::view($id);
    }

    private function filterRights($id) {
        $bSucc = true;

        if (in_array($this->action, array('view', 'edit', 'destroy'))) {
            $sModelName = $this->modelName;
            $sTable = $this->model->getTable();
            $originalColumns = Schema::getColumnListing($sTable);

            if (in_array('user_id', $originalColumns)) {
                $iUserId = Session::get('user_id');
                $rUserId = $sModelName::find($id)->user_id;
                $sForefatherIds = User::find($rUserId)->forefather_ids;
                $aForefatherIds = explode(',', $sForefatherIds);
                $bIsAgent = Session::get('is_agent');
                $bIsTopAgent = Session::get('is_top_agent');
                $bSucc = ($iUserId == $rUserId) || in_array($iUserId, $aForefatherIds);
            }
        }

        return $bSucc;
    }

    public function getSumData($aSumColumns, $bPerPage = false) {
        $aConditions = &$this->makeSearchConditions();
        $oQuery = $this->model->doWhere($aConditions);
        $aRawColumns = array();

        foreach ($aSumColumns as $key => $value) {
            $aRawColumns[] = DB::raw('SUM(' . $value . ') as ' . $value . '_sum');
        }

        $aSum = array();
        $aSum = $oQuery->get($aRawColumns)->toArray();

        if (count($aSum)) {
            $aSum = $aSum[0];
        }

        return $aSum;
    }

    protected function saveUrlToSession() {
        Session::forget('request_full_url');
        Session::push('request_full_url', Request::url());
    }

    protected function getUrlFromSession($sRoute = 'home.index') {
        if (Session::has('request_full_url')) {
            list($sUrl) = Session::get('request_full_url');
        } else {
            $sUrl = Route($sDefaultUrl);
        }

        return $sUrl;
    }

    protected function updateOnlineTime() {
        $updateTime = 1800;
        $sessionKey = 'PASS_LOGIN_TIMES';

        if (false == ($firstLoginTime = Session::get($sessionKey))) {
            Session::put($sessionKey, time());
        } else if ($updateTime <= time() - $firstLoginTime) {
            UserOnline::online(Session::get('user_id'));
            Session::put($sessionKey, time());
        }
    }

    public function setCache() {
        $UserCustomizedData = new UserCustomizedData();
        $aPrams = Input::except('page', 'sort_up', 'sort_down');

        if (!$UserCustomizedData->checkCache($aPrams, $aReturnMsg)) {
            return Response::json($aReturnMsg);
        }

        $UserCustomizedData->saveCache($aPrams, $aDatas);
        return Response::json($aDatas);
    }

    protected function getCache($key) {
        $UserCustomizedData = new UserCustomizedData();
        $rules = array('key' => 'required|regex:/^[a-zA-Z0-9.=&]+$/');
        $validator = Validator::make(array('key' => $key), $rules, $UserCustomizedData::$messages);
        $data = '';

        if ($validator->fails()) {
            $msg = $validator->messages()->first();
        } else {
            $sCacheKey = $UserCustomizedData->getCacheKey($key);

            if (!($data = Cache::get($sCacheKey))) {
                if ($data = $UserCustomizedData::where('m_key', $sCacheKey)->pluck('m_value')) {
                    Cache::forever($sCacheKey, $data);
                }
            }
        }

        $data = json_decode($data);
        return $data;
    }

    protected function render() {
        $this->beforeRender();

        if (!$this->view) {
            if (in_array($this->action, $this->customViews) && $this->customViewPath) {
                $this->resourceView = $this->customViewPath;
            }

            $this->view = $this->resourceView . '.' . $this->action;
        }
        $this->layout = View::make($this->view)->with($this->viewVars);
    }

}

?>