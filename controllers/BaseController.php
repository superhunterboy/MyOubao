<?php

class BaseController extends Controller {

    protected $Message;

    /**
     * 是否是ajax方式
     * @var bool
     */
    protected $isAjax = false;

    /**
     * 是否是移动端交互
     * @var bool
     */
    protected $isMobile = false;

    /**
     * 是否显示图形报表
     */
    protected $isShowGraph = false;

    /**
     * 需要加载的错误码定义文件
     * @var array
     */
    protected $errorFiles = array();

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';

    /**
     * self view path
     * @var string
     */
    protected $customViewPath = '';

    /**
     * view path
     * @var string
     */
    protected $view = '';

    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = array();

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName;

    /**
     * friendly model
     * @var string
     */
    protected $friendlyModelName;

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
     * pagesize
     * @var int
     */
    static protected $pagesize = 50;

    /**
     * 须自动准备数据的视图名称
     * @var array
     */
    protected $composerViews = array('view', 'index', 'create', 'edit');

    /**
     * Functionality Model
     * @var Functionality
     */
    protected $functionality;

    /**
     * 视图使用的样式名
     * @var array
     */
    public $viewClasses = array('div' => 'form-group', 'label' => 'col-sm-2 control-label', 'input_div' => 'col-sm-6', 'msg_div' => 'col-sm-4', 'msg_label' => 'text-danger control-label', 'radio_div' => 'switch ', 'select' => 'form-control', 'input' => 'form-control input-sm', 'radio' => NULL, 'date' => 'input-group date form_date');

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = array();

    /**
     * 消息对象
     * @var Illuminate\Support\MessageBag
     */
    protected $messages;

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
     * sysConfig model
     * @var sysConfig
     */
    protected $sysConfig;

    /**
     * search config
     * @var array
     */
    protected $searchConfig;

    /**
     * search fields
     * @var array
     */
    protected $searchItems = array();

    /**
     * param settings
     * @var array
     */
    protected $paramSettings;

    /**
     * use for redirect
     * @var string
     */
    protected $redictKey;

    /**
     * save the all input data: get,post
     * @var array
     */
    protected $params = array();

    /**
     * Widgets
     * @var array
     */
    protected $widgets = array();

    /**
     * Breadcrumb
     * @var array
     */
    protected $breadcrumbs = array();

    /**
     * for lang transfer
     * @var array
     */
    protected $langVars = array();

    /**
     * for lang transfer, short title
     * @var array
     */
    protected $langShortVars = array();

    /**
     * default lang file
     */
    protected $defaultLangPack;

    /**
     * if is admininistrator
     */
    protected $admin;

    /**
     * Client IP
     * @var string
     */
    protected $clientIP;

    /**
     * Proxy IP
     * @var string
     */
    protected $proxyIP;

    /**
     * Need Right Check
     * @var bool
     */
    protected $needRightCheck = true;

    /**
     * 当前用户可访问的功能ID列表
     * @var array
     */
    protected $hasRights;

    /**
     * 不进行权限检查的控制器列表
     * @var array
     */
    protected $openControllers = array('AdminController', 'HomeController', 'UserTrendController', 'BetController', 'MobileDownloadController');

    public function __construct() {
        $this->isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
        $this->checkLogin() || $this->doNotLogin();
        $this->updateOnlineTime();
        $this->admin = (bool) Session::get('admin_user_id');
        $this->initCA() || App::abort(404);
        $this->setFunctionality();

        if (!in_array($this->controller, $this->openControllers)) {
            $this->functionality || App::abort(404);
            $this->checkRight() || App::abort(403);
        }

        $this->messages = new \Illuminate\Support\MessageBag();
        $this->setReirectKey();
        if (Request::isMethod('GET') && in_array($this->action, array('index', 'unVefiriedRecords', 'settings', 'agentPrizeGroupList', 'agentDistributionList', 'listSearchConfig'))) {
            Session::put($this->redictKey, Request::fullUrl());
        }

        $this->initModel();
        $this->resource = $this->getResourceName();
        $this->sysConfig = new SysConfig();
        $sLanguage = ($this->admin ? Session::get('admin_language') : 'zh-CN');
        App::setLocale($sLanguage);
        $this->clientIP = get_client_ip();
        $this->proxyIP = get_proxy_ip();
    }

    protected function initModel() {
        if ($this->modelName) {
            $sModelName = $this->modelName;
            $this->resourceName = __('_model.' . $sModelName::$resourceName);
            $this->model = App::make($this->modelName);
            $this->resourceTable = $this->model->getTable();
            $this->friendlyModelName = Str::slug($this->modelName);
            $this->langVars = array('resource' => __('_model.' . Str::slug($sModelName::$resourceName)));
            $this->langShortVars = array('resource' => NULL);
            $this->defaultLangPack = $sModelName::comaileLangPack();
        }
    }

    protected function checkRight() {
        if ($this->functionality) {
            $this->paramSettings = FunctionalityParam::getParams($this->functionality->id);
            $this->params = trimArray(Input::except('_token'));

            if ($this->functionality->need_search) {
                $this->getSearchConfig();
                $this->_setSearchInfo();
            }

            $roleIds = Session::get('CurUserRole');

            if (!isset($enabled)) {
                $this->hasRights = &$this->getUserRights();
                $enabled = in_array($this->functionality->id, $this->hasRights);
            }
        } else {
            $enabled = false;
        }

        return $enabled;
    }

    protected function checkLogin() {
        return false;
    }

    protected function doNotLogin() {
        
    }

    protected function updateOnlineTime() {
        
    }

    protected function initCA() {
        if (!($ca = Route::currentRouteAction())) {
            return false;
        }

        list($this->controller, $this->action) = explode('@', $ca);
        return true;
    }

    protected function setReirectKey() {
        $this->redictKey = 'curPage-' . $this->modelName;
    }

    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function _generateTreeList($table, $parent_id) {
        $tree = array();

        foreach ($table as $row) {
            if ($row['parent_id'] == $parent_id) {
                $tmp = $this->_generateTreeList($table, $row['id']);

                if ($tmp) {
                    $row['children'] = $tmp;
                } else {
                    $row['leaf'] = true;
                }

                $tree[] = $row;
            }
        }

        return $tree;
    }

    protected function& _getButtons() {
        $pageButtons = array();
        $itemButtons = array();
        $pageBatchButtons = array();
        $data = array(
            'pageButtons' => array(),
            'itemButtons' => array(),
            'pageBatchButtons' => array()
        );
        if (!$this->admin || !$this->functionality) {
            return $data;
        }

        $aHadRights = $this->hasRights;
        $buttons = $this->functionality->getRelationFunctionalities($aHadRights, $aRelationIds);
        $functionalities = &Functionality::getActionArray($aRelationIds);

        foreach ($buttons as $key => $value) {
            if (!isset($functionalities[$value->r_functionality_id])) {
                continue;
            }

            $route_action = $functionalities[$value->r_functionality_id][1] . '@' . $functionalities[$value->r_functionality_id][2];

            switch ($functionalities[$value->r_functionality_id][2]) {
                case 'destroy':
                    $value->btn_type = 1;
                    $value->btn_action = 'modal';
                    break;

                case 'refuse':
                    $value->btn_type = 1;
                    $value->btn_action = 'refuseWithdrawal';
                    break;

                case 'waitingForConfirmation':
                    $value->btn_type = 1;
                    $value->btn_action = 'waitingForConfirmation';
                    break;

                case 'rejectBonus':
                    $value->btn_type = 1;
                    $value->btn_action = 'rejectBonus';
                    break;

                case 'auditBonus':
                    $value->btn_type = 1;
                    $value->btn_action = 'auditBonus';
                    break;

                case 'unblockUser':
                    $value->btn_type = 1;
                    $value->btn_action = 'setUnblockedStatus';
                    break;

                case 'blockUser':
                    $value->btn_type = 1;
                    $value->btn_action = 'setBlockedStatus';
                    break;

                case 'ignore':
                    $value->btn_type = 1;
                    $value->btn_action = 'ignore';
                    break;

                case 'addCoin':
                    $value->btn_type = 1;
                    $value->btn_action = 'addCoin';
                    break;

                case 'reviseCode':
                    $value->btn_type = 1;
                    $value->btn_action = 'reviseCode';
                    break;

                case 'advanceCode':
                    $value->btn_type = 1;
                    $value->btn_action = 'advanceCode';
                    break;

                case 'cancelCode':
                    $value->btn_type = 1;
                    $value->btn_action = 'cancelCode';
                    break;

                case 'addUserToWithdrawalWhiteList':
                    $value->btn_type = 1;
                    $value->btn_action = 'addUserToWithdrawalWhiteList';
                    break;

                case 'addUserToWithdrawalBlackList':
                    $value->btn_type = 1;
                    $value->btn_action = 'addUserToWithdrawalBlackList';
                    break;

                case 'addUserToICBCRechargeWhiteList':
                    $value->btn_type = 1;
                    $value->btn_action = 'addUserToICBCRechargeWhiteList';
                    break;

                case 'lockUserBankCards':
                    $value->btn_type = 1;
                    $value->btn_action = 'lockUserBankCards';
                    break;

                case 'lockUserBankCards':
                    $value->btn_type = 1;
                    $value->btn_action = 'lockUserBankCards';
                    break;

                case 'unlockUserBankCards':
                    $value->btn_type = 1;
                    $value->btn_action = 'unlockUserBankCards';
                    break;

                case 'manualSetToSuccess':
                    $value->btn_type = 1;
                    $value->btn_action = 'manualSetToSuccess';
                    break;

                case 'manualSetToFailure':
                    $value->btn_type = 1;
                    $value->btn_action = 'manualSetToFailure';
                    break;

                case 'edit':
                case 'show':
                    $value->btn_type = 2;
                    break;

                default:
                    $value->btn_type = 3;
                    break;
            }

            $route_name = $this->_getRouterName($route_action);
            $value->route_name = $route_name;
            $bShort = false;
            $aShortActions = array('view', 'edit', 'delete', 'create');

            if ($value->for_item) {
                $aWords = explode(' ', $value->label);
                $sFirst = $aWords[0];
                $bShort = in_array(strtolower($sFirst), $aShortActions);
            } else {
                $bShort = in_array(strtolower($value->label), $aShortActions);
                $sFirst = $value->label;
            }

            if ($bShort) {
                $sDictionary = '_basic';
                $sKeyword = strtolower($sFirst);
                $sReplaceVarName = 'langShortVars';
            } else {
                $sDictionary = '_function';
                $sKeyword = strtolower($value->label);
                $sReplaceVarName = 'langVars';
            }

            $value->label = __($sDictionary . '.' . $sKeyword, $this->$sReplaceVarName, 2);

            if (strcasecmp($value->label, $sKeyword) === 0) {
                $value->label = __($this->defaultLangPack . '.' . $sKeyword, $this->$sReplaceVarName, 2);
            }

            $iParamFunctionalityId = ($value->for_page ? $value->functionality_id : $value->r_functionality_id);

            if ($value->params) {
                $value->para_name = $value->params;
            } else if ($aParamConfig = FunctionalityParam::getParams($iParamFunctionalityId)) {
                foreach ($aParamConfig as $sParamName => $aRaramSetting) {
                    break;
                }

                $value->para_name = $sParamName;
            } else {
                $sModelName = &$this->modelName;

                switch ($functionalities[$value->r_functionality_id][2]) {
                    case 'index':
                    case 'list':
                    case 'create':
                        $value->para_name = $sModelName::$treeable ? 'parent_id' : NULL;
                        break;

                    case 'updateModels':
                    case 'generateAll':
                        $value->para_name = NULL;
                        break;

                    default:
                        $value->para_name = 'id';
                }
            }

            if ($value->use_redirector) {
                $value->url = Session::get($this->redictKey);
            }

            if ($value->for_page) {
                array_push($pageButtons, $value);
            } else if ($value->for_item) {
                array_push($itemButtons, $value);
            } else if ($value->for_page_batch) {
                array_push($pageBatchButtons, $value);
            }
        }

        $data = array('pageButtons' => $pageButtons, 'itemButtons' => $itemButtons, 'pageBatchButtons' => $pageBatchButtons);
        return $data;
    }

    protected function _getBreadcrumb() {
        return array();
    }

    protected function& getUserRights() {
        $a = array();
        return $a;
    }

    public function& getRights($aRoleIds = array()) {
        $aRoles = Role::whereIn('id', $aRoleIds)->get(array('id', 'rights'));
        $aRights = array();

        foreach ($aRoles as $oRole) {
            $aRights = array_merge($aRights, explode(',', $oRole->rights));
        }

        $aRights = array_unique($aRights);
        return $aRights;
    }

    protected function _getRouterName($route_action) {
        $router = Route::getRoutes()->getByAction($route_action);
        return $router ? $router->getName() : '';
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

    protected function beforeRender() {
        $this->setVars($this->params);
        $resource = $this->resource;
        $resourceName = $this->resourceName;
        $sModelName = &$this->modelName;
        $buttons = &$this->_getButtons();
        $breadcrumb = $this->_getBreadcrumb();
        $model = $this->model;
        $bTreeable = $sModelName::$treeable;
        $this->setVars(compact('resource', 'resourceName', 'buttons', 'breadcrumb', 'bTreeable', 'model'));
        $oFormHelper = new FormHelper();
        $bEdit = in_array($this->action, array('edit', 'create'));

        if ($this->model) {
            !empty($this->model->columnSettings) || $this->model->makeColumnConfigures($bEdit);
            $oFormHelper->setModel($this->model);
        }

        $this->setVars('aColumnSettings', $this->model->columnSettings);
        $sLangKey = '_basic.';

        switch ($this->action) {
            case 'index':
                $sLangKey .= 'management';
                $this->setVars('aColumnForList', $sModelName::$columnForList);
                $this->setVars('sModelSingle', __('_model.' . $this->friendlyModelName));
                $this->setVars('bSequencable', $sModelName::$sequencable);
                $this->setVars('bCheckboxenable', $sModelName::$checkboxenable);

                if ($sModelName::$sequencable) {
                    $sSetOrderRoute = $this->resource . '.set-order';
                    $this->setvars(compact('sSetOrderRoute'));
                }

                $this->setVars('aListColumnMaps', $sModelName::$listColumnMaps);
                $this->setVars('aNoOrderByColumns', $sModelName::$noOrderByColumns);

                if ($sModelName::$totalColumns) {
                    $this->setVars('aTotalColumns', $sModelName::$totalColumns);
                }

                break;

            case 'create':
                $sLangKey .= 'create';
                $this->setVars('aOriginalColumns', $sModelName::$originalColumns);
                $this->setVars(compact('oFormHelper'));
                break;

            case 'view':
                $this->setVars('aViewColumnMaps', $sModelName::$viewColumnMaps);
            default:
                $sLangKey = '_function.' . strtolower($this->functionality->title);
                break;
        }

        $sAction = Lang::get($sLangKey, $this->langVars);
        $oFormHelper->setClass($this->viewClasses);
        $oFormHelper->setLangPrev($this->defaultLangPack . '.');
        $this->setVars(compact('oFormHelper'));
        $this->setVars('aWidgets', $this->widgets);
        $this->setVars('sLangKey', $sLangKey);
        isset($this->viewVars['bNeedCalendar']) || $this->viewVars['bNeedCalendar'] = false;
        $sPageTitle = __('_basic.management', $this->langVars);

        if ($sAction != $sPageTitle) {
            $sPageTitle .= ' - ' . $sAction;
        }

        $this->setVars('isShowGraph', $this->isShowGraph);
        $this->setVars(compact('sPageTitle', 'sModelName'));
        $this->setvars('sLangPrev', $this->defaultLangPack . '.');
        $this->setvars('aLangVars', $this->langVars);
        $this->setVars('aNumberColumns', $sModelName::$htmlNumberColumns);
        $this->setVars('iDefaultAccuracy', $sModelName::$amountAccuracy);
        if ($this->functionality->refresh_cycle && (0 < $this->functionality->refresh_cycle)) {
            $this->setVars('iRefreshTime', $this->functionality->refresh_cycle);
        }
    }

    protected function& makeSearchConditions() {
        $aConditions = array();
        $iCount = count($this->params);

        foreach ($this->paramSettings as $sColumn => $aParam) {
            if (!isset($this->params[$sColumn])) {
                if ($aParam['limit_when_null'] && ($iCount <= 1)) {
                    $aFieldInfo[1] = NULL;
                } else {
                    continue;
                }
            }

            $mValue = (isset($this->params[$sColumn]) ? $this->params[$sColumn] : NULL);
            if (!mb_strlen($mValue) && !$aParam['limit_when_null']) {
                continue;
            }

            if (!isset($this->searchItems[$sColumn])) {
                $aConditions[$sColumn] = array('=', $mValue);
                continue;
            }

            $aPattSearch = array('!\\$model!', '!\\$\\$field!', '!\\$field!');
            $aItemConfig = &$this->searchItems[$sColumn];
            $aPattReplace = array($aItemConfig['model'], $mValue, $aItemConfig['field']);
            $sMatchRule = preg_replace($aPattSearch, $aPattReplace, $aItemConfig['match_rule']);
            $aMatchRule = explode("\n", $sMatchRule);

            if (1 < count($aMatchRule)) {
                
            } else {
                $aFieldInfo = array_map('trim', explode(' = ', $aMatchRule[0]));
                $aTmp = explode(' ', $aFieldInfo[0]);
                $iOperator = (1 < count($aTmp) ? $aTmp[1] : '=');
                if (!mb_strlen($mValue) && $aParam['limit_when_null']) {
                    $aFieldInfo[1] = NULL;
                }

                list($tmp, $sField) = explode('.', $aTmp[0]);
                ($sField[0] == '$') || ($sColumn = $sField);

                if (isset($aConditions[$sColumn])) {
                    $arr = array($aConditions[$sColumn][1], $aFieldInfo[1]);
                    sort($arr);
                    $aConditions[$sColumn] = array('between', $arr);
                } else {
                    $aConditions[$sColumn] = array($iOperator, $aFieldInfo[1]);
                }
            }
        }

        return $aConditions;
    }

    public function index() {
        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;
        $datas = $oQuery->paginate(static::$pagesize);

        if ($this->isShowGraph) {
            $aDataArray = $datas->toArray();
            $aDataArray = (empty($aDataArray) ? array() : array_get($aDataArray, 'data'));
            $newData = array(
                'x' => array_column($aDataArray, $sModelName::$columnForGraphX),
                'y' => array()
            );

            foreach ($sModelName::$columnForGraphList as $key => $val) {
                $newData['y'][$key]['name'] = __('_' . strtolower($sModelName) . '.' . $val);
                $newData['y'][$key]['data'] = array_column($aDataArray, $val);
            }

            $this->setVars('graphdatas', json_encode($newData));
        }

        $this->setVars(compact('datas'));

        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = (is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1]);
            } else {
                $$sMainParamName = NULL;
            }

            $this->setVars(compact($sMainParamName));
        }

        return $this->render();
    }

    public function indexQuery() {
        $aConditions = &$this->makeSearchConditions();
        $oQuery = $this->model->doWhere($aConditions);
        $bWithTrashed = trim(Input::get('_withTrashed', 0));

        if ($bWithTrashed) {
            $oQuery = $oQuery->withTrashed();
        }

        if ($sGroupByColumn = Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, array($sGroupByColumn));
        }

        $aOrderSet = array();

        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = (Input::get('sort_up') ? 'asc' : 'desc');
            $aOrderSet[$sOorderColumn] = $sDirection;
        }

        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    public function setVars($sKey, $mValue = NULL) {
        if (is_array($sKey)) {
            foreach ($sKey as $key => $value) {
                $this->setVars($key, $value);
            }
        } else {
            $this->viewVars[$sKey] = $mValue;
        }
    }

    public function create($id = NULL) {
        if (Request::method() == 'POST') {
            DB::connection()->beginTransaction();

            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = &$this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;
            list($sFirstParamName, $tmp) = each($this->paramSettings);
            !isset($sFirstParamName) || $this->setVars($sFirstParamName, $id);
            $aInitAttributes = (isset($sFirstParamName) ? array($sFirstParamName => $id) : array());
            $this->setVars(compact('aInitAttributes'));
            return $this->render();
        }
    }

    public function edit($id) {
        $this->model = $this->model->find($id);

        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }

        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();

            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = &$this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit', 'id'));
            return $this->render();
        }
    }

    public function view($id) {
        $this->model = $this->model->find($id);

        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }

        $data = $this->model;
        $sModelName = $this->modelName;

        if ($sModelName::$treeable) {
            if ($this->model->parent_id) {
                if (!array_key_exists('parent', $this->model->getAttributes())) {
                    $sParentTitle = $sModelName::find($this->model->parent_id)->{$sModelName::$titleColumn};
                } else {
                    $sParentTitle = $this->model->parent;
                }
            } else {
                $sParentTitle = '(' . __('_basic.top_level', array(), 3) . ')';
            }
        }

        $this->setVars(compact('data', 'sParentTitle'));
        return $this->render();
    }

    public function destroy($id) {
        $this->model = $this->model->find($id);
        $sModelName = $this->modelName;

        if ($sModelName::$treeable) {
            if ($iSubCount = $this->model->where('parent_id', '=', $this->model->id)->count()) {
                $this->langVars['reason'] = __('_basic.not-leaf', $this->langVars);
                return Redirect::back()->with('error', __('_basic.delete-fail', $this->langVars));
            }
        }

        DB::connection()->beginTransaction();

        if ($bSucc = $this->model->delete()) {
            $bSucc = $this->afterDestroy();
        }

        $bSucc ? DB::connection()->commit() : DB::connection()->rollback();
        $sLangKey = '_basic.' . ($bSucc ? 'deleted' : 'delete-fail.');
        return $this->goBackToIndex('success', __($sLangKey, $this->langVars));
    }

    protected function afterDestroy() {
        return true;
    }

    protected function saveData() {
        $this->_fillModelDataFromInput();
        $aRules = &$this->_makeVadilateRules($this->model);
        return $this->model->save($aRules);
    }

    protected function _fillModelDataFromInput() {
        $data = $this->params;
        $sModelName = $this->modelName;
        !empty($this->model->columnSettings) || $this->model->makeColumnConfigures();

        foreach ($this->model->columnSettings as $sColumn => $aSettings) {
            if ($sColumn == 'id') {
                continue;
            }

            if (!isset($aSettings['type'])) {
                continue;
            }

            switch ($aSettings['type']) {
                case 'bool':
                case 'numeric':
                case 'integer':
                    !empty($data[$sColumn]) || $data[$sColumn] = 0;
                    break;

                case 'select':
                    if (isset($data[$sColumn]) && is_array($data[$sColumn])) {
                        sort($data[$sColumn]);
                        $data[$sColumn] = implode(',', $data[$sColumn]);
                    }
            }
        }

        $this->model = $this->model->fill($data);

        if ($sModelName::$treeable) {
            $this->model->parent_id || $this->model->parent_id = NULL;

            if ($sModelName::$foreFatherColumn) {
                $this->model->{$sModelName::$foreFatherColumn} = $this->model->setForeFather();
            }
        }
    }

    protected function& _makeVadilateRules($oModel) {
        $sClassName = get_class($oModel);
        return $sClassName::$rules;
    }

    protected function unique($column = NULL, $id = NULL, $extraParam = NULL) {
        $rule = 'unique:' . $this->resourceTable;

        if (!is_null($column)) {
            $rule .= ',' . $column;
        }

        if (!is_null($id)) {
            $rule .= ',' . $id . ',id';
        } else {
            $rule .= ',NULL,id';
        }

        if (!is_null($extraParam) && is_array($extraParam)) {
            foreach ($extraParam as $key => $value) {
                $rule .= ',' . $key . ',' . $value;
            }
        }

        return $rule;
    }

    protected function getResourceName() {
        $sControllerName = str_replace('Controller', '', $this->controller);
        $aParts = explode('_', $sControllerName);
        $sName = $aParts[count($aParts) - 1];
        $sName = String::snake($sName);
        return String::plural(String::slug($sName, '-'));
    }

    public function _setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('aSearchFields', 'bNeedCalendar'));
        $this->setVars('aSearchConfig', $this->searchConfig);
        $this->addWidget('w.search');
    }

    protected function getSearchConfig() {
        $iFunctionalityId = $this->functionality->id;

        if ($this->searchConfig = SearchConfig::getForm($iFunctionalityId, $this->admin)) {
            $this->searchItems = &$this->searchConfig->getItems();
        }
    }

    protected function addWidget($sWidget) {
        $this->widgets[] = $sWidget;
    }

    protected function goBackToIndex($sMsgType, $sMessage) {
        return Redirect::to(Session::get($this->redictKey))->with($sMsgType, $sMessage);
    }

	protected function goBack($sMsgType, $sMessage, $bWithModelErrors = false) {
        $oRedirectResponse = Redirect::back()->withInput()->with($sMsgType, $sMessage);
        !$bWithModelErrors || ($oRedirectResponse = $oRedirectResponse->withErrors($this->model->validationErrors));
        return $oRedirectResponse;
    }

    protected function setFunctionality() {
        $this->functionality = Functionality::getByCA($this->controller, $this->action, $this->admin);
    }

    public function setOrder() {
        if (Request::method() != 'POST') {
            return $this->goBack('error', __('_basic.method-error'));
        }

        if (!isset($this->params['sequence']) || !is_array($this->params['sequence'])) {
            return $this->goBack('error', __('_basic.data-error'));
        }

        $sModelName = $this->modelName;
        DB::connection()->beginTransaction();
        $bSucc = true;

        foreach ($this->params['sequence'] as $id => $sequence) {
            $oModel = $sModelName::find($id);

            if ($oModel->sequence == $sequence) {
                continue;
            }

            $oModel->sequence = $sequence;

            if (!($bSucc = $oModel->save(array('sequence' => 'numeric')))) {
                break;
            }
        }

        if ($bSucc) {
            DB::connection()->commit();
            $sInfoType = 'success';
            $sLangKey = '_basic.ordered';
        } else {
            DB::connection()->rollback();
            $sInfoType = 'error';
            $sLangKey = '_basic.order-fail';
        }

        return $this->goBack($sInfoType, __($sLangKey));
    }

    protected function halt($bSuccess, $sType, $iErrno, &$aSuccessedBets = NULL, &$aFailedBets = NULL, &$aData = NULL, $sLinkUrl = NULL, $sErrMsg = NULL) {
        is_object($this->Message) || $this->Message = new Message($this->errorFiles, $this->isMobile);
        $this->Message->output($bSuccess, $sType, $iErrno, $aData, $aSuccessedBets, $aFailedBets, $sLinkUrl, $sErrMsg);
        exit();
    }

    protected function jsonEcho($msg) {
        header('Content-Type: application/json');
        echo json_encode($msg);
        exit();
    }

    protected function writeLog($msg) {
        !is_array($msg) || ($msg = var_export($msg, true));
        $sFile = implode(DIRECTORY_SEPARATOR, array('/tmp', date('Ym'), date('d'), date('H')));

        if (!file_exists($sFile)) {
            @mkdir($sFile, 511, true);
        }

        file_put_contents($sFile . '/bet', $msg . "\n", FILE_APPEND);
    }

    protected function createUserManageLog($iUserId, $sComment = NULL) {
        $iFunctionalityId = $this->functionality->id;
        $sFunctionality = $this->functionality->title;
        return UserManageLog::createLog($iUserId, $iFunctionalityId, $sFunctionality, $sComment);
    }

    public function downloadExcel($aTitles, $aData, $sFileName, $sModelName = NULL) {
        if (0 < count($aData)) {
            PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized);
            $oDownExcel = new DownExcel();
            $sModelName = ($sModelName ? $sModelName : (starts_with($this->modelName, 'Man') ? substr($this->modelName, 3) : $this->modelName));
            $oDownExcel->setTitle(strtolower($sModelName), $aTitles);
            $oDownExcel->setData($aData);
            $oDownExcel->setActiveSheetIndex(0);
            $oDownExcel->setSheetTitle($sFileName);
            $oDownExcel->setEncoding('gb2312');
            $oDownExcel->Download($sFileName);
        }

        return Redirect::route(str_replace('.download', '.index', Route::currentRouteName()));
    }

    public function __destruct() {
        if (SysConfig::check('sys_use_sql_log', true)) {
            $sLogPath = Config::get('log.root') . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . date('Ymd');

            if (!file_exists($sLogPath)) {
                @mkdir($sLogPath, 511, true);
                @chmod($sLogPath, 511);
            }

            $sLogFile = $sLogPath . DIRECTORY_SEPARATOR . date('H') . '.sql';
            $queries = DB::getQueryLog();

            foreach ($queries as $aQueryInfo) {
                $sql = '';
                $aSqlParts = explode('?', $aQueryInfo['query']);

                foreach ($aSqlParts as $i => $sPart) {
                    $sql .= $aSqlParts[$i];

                    if (isset($aQueryInfo['bindings'][$i])) {
                        $bindings = $aQueryInfo['bindings'][$i];
                        !(is_string($bindings) && (0 < strlen($bindings)) && ($bindings[0] != '\'')) || ($bindings = '\'' . $bindings . '\'');
                        $sql .= $bindings;
                    }
                }

                $aLogs[] = $sql;
                $aLogs[] = number_format($aQueryInfo['time'], 3) . 'ms';
            }

            @file_put_contents($sLogFile, date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            @file_put_contents($sLogFile, var_export($queries, true) . "\n\n", FILE_APPEND);
            @file_put_contents($sLogFile, implode("\n", $aLogs) . "\n\n", FILE_APPEND);
        }
    }

}

?>