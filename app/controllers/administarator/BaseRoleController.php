<?php
# 管理员, 用户, 和角色关联关系的管理基类
class BaseRoleController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $customViewPath = 'admin.role';
    protected $customViews    = [
        'bindUser',
        'showRights',
        'setRights',
    ];

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'Role';


    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    // protected $isAdmin = true; // 默认查询管理员和角色间的关系

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        // pr($this->action);exit;
        switch($this->action){
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
                $aPriority = $sModelName::$aPriority;
                $aRoleTypes = $sModelName::$aRoleTypes;
                $this->setVars(compact('aPriority', 'aRoleTypes'));
                break;
            case 'bindUser':
                $aRoles = Role::getAllRoleNameArray($this->roleType);
                $this->setVars(compact('aRoles'));
                break;
            case 'showRights':
            case 'setRights':
                break;
        }
        // pr($this->isAdmin);exit;
    }

    public function index ()
    {
        $this->params['role_type'] = $this->roleType;
        // pr($this->params);exit;
        return parent::index();
    }

    public function destroy($id) {
        $this->model = $this->model->find($id);
        $sModelName = $this->modelName;
        if ($sModelName::$treeable){
            if ($iSubCount = $this->model->where('parent_id', '=', $this->model->id)->count()) {
                return Redirect::back()->with('success', $this->resourceName . ' can not be deleted: it has some children ');
            }
        }
        if ((int)$this->model->is_system) return Redirect::back()->with('warning', 'System role can not be deleted.');
        $sMessage = $this->model->delete() ? $this->resourceName . ' was deleted.' : $this->resourceName . 'cannot be deleted.';
        return Redirect::back()->with('success', $sMessage);
    }
    /**
     * [generateSyncParams 生成角色用户关联表的其他字段内容]
     * @param  [String] $sModel    [用户/管理员模型名]
     * @param  [Array] $aUserIds   [用户/管理员id数组]
     * @param  [String] $sRoleName [绑定的角色名]
     * @return [Array]             [参数数组]
     */
    protected function generateSyncParams($sModel, $aUserIds, $sRoleName)
    {
        $aUsers = $sModel::getUsersByIds($aUserIds);
        $aParams = [];
        foreach ($aUsers as $oUser) {
            $aParams[$oUser->id] = ['username' => $oUser->username, 'role_name' => $sRoleName];
        }
        return $aParams;
    }
    /**
     * [bindUser 给角色绑定用户/管理员]
     * @param  [Int] $role_id [角色id]
     * @return [Response]          [description]
     */
    public function bindUser($role_id = null)
    {
        $sModel          = $this->sModel;
        $sPivotModelName = $this->sPivotModelName;
        $sChildrenName   = $this->sChildrenName;
        $role_id or $role_id = trim(Input::get('role_id'));
        if (Request::method() == 'POST' || Request::method() == 'PUT') {
            if (! $role_id) {
                $this->langVars['reason'] = __('_basic.no-role', $this->langVars);
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
            $oRole      = $this->model->find($role_id);
            $sRoleName  = $oRole->name;
            $aInputUserIds = Input::get('user_id');
            // pr($aInputUserIds);
            // pr($role_id);
            if (!$aInputUserIds) return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            else {
                if (!is_array($aInputUserIds)) $aInputUserIds = [$aInputUserIds];
                $aParams = $this->generateSyncParams($sModel, $aInputUserIds, $sRoleName);
            }
            // pr($aParams);exit;
            foreach ($aParams as $key => $value) {
                if (! $sPivotModelName::checkUserRoleRelation($role_id, $key)) {
                    $oRole->{$sChildrenName}()->attach($key, $value);
                }
            }

            return $this->goBack('success', __('_basic.update-success', $this->langVars));
        } else {
            $this->params = trimArray(Input::except('role_id', 'is_search_form'));
            $bIsSearchForm = trim(Input::get('is_search_form'));
            $aConditions = & $this->makeSearchConditions();
            if (isset($aConditions['username'])) {
                $aConditions['username'][0] = 'like';
                $aConditions['username'][1] = '%' . $aConditions['username'][1] . '%';
            }
            if ($bIsSearchForm) {
                $oModel  = new $sModel;
                $oQuery = $oModel->doWhere($aConditions);
                $datas  = $oQuery->paginate(static::$pagesize, ['id', 'username']);
                // $queries = DB::getQueryLog();
                // $last_query = end($queries);
                // pr($last_query);exit;
                $checked = [];
                if (isset($oRole) && $oRole) {
                    $aIds = $oRole->{$sChildrenName}()->paginate(static::$pagesize, [$sChildrenName . '.id', $sChildrenName . '.username']);

                    foreach ($aIds as $key => $value) {
                        $checked[] = $value->id;
                    }
                }
                $this->setVars(compact('datas', 'checked'));
            }
            // pr($datas->toArray());exit;
            if ($role_id)
                $this->setVars(compact('role_id'));
            // $this->action = 'bindUser';
            return $this->render();
        }
    }


    public function showRights ($role_id)
    {
        $sModel          = $this->sModel;
        $sPivotModelName = $this->sPivotModelName;
        $sChildrenName   = $this->sChildrenName;
        if (! $role_id) {
            $this->langVars['reason'] = __('_basic.no-role', $this->langVars);
            return $this->goBack('error', __('_basic.update-fail', $this->langVars));
        }
        $oRole      = $this->model->find($role_id);
        $sRoleName  = $oRole->name;
        // 获取排序条件
        $orderColumn = Input::get('sort_up', Input::get('sort_down', 'sequence'));
        $direction   = Input::get('sort_up') ? 'asc' : 'desc' ;
        // 构造查询语句
        if (!$oRole->right_settable) {
            return Redirect::back()
                ->with('error', '<strong>' . __("The Role don't allow setting rights") . '</strong>');
        }
        // $functionalities = $oRole->functionalities()->orderBy($orderColumn, $direction)->get();
        // pr($oRole->rights);exit;
        $checked = explode(',', $oRole->rights);
        Functionality::getTreeArray($datas, null, Functionality::REALM_USER);
        // $checked = [];
        // foreach ($functionalities as $functionality) {
        //     array_push($checked, $functionality->id);
        // }
        // pr($datas);exit;
        $readonly = true;
        $this->action = 'setRights';
        $this->setVars(compact('datas', 'checked', 'role_id', 'readonly', 'sRoleName'));
        return $this->render();
    }

    /**
     * [setRights 给角色绑定功能权限]
     * @param  [Int] $role_id [角色id]
     * @return [Response]          [description]
     */
    public function setRights ($role_id = null)
    {
        $sModel          = $this->sModel;
        $sPivotModelName = $this->sPivotModelName;
        $sChildrenName   = $this->sChildrenName;
        if (! $role_id) {
            $this->langVars['reason'] = __('_basic.no-role', $this->langVars);
            return $this->goBack('error', __('_basic.update-fail', $this->langVars));
        }
        $oRole      = $this->model->find($role_id);
        $sRoleName  = $oRole->name;

        if (Request::method() == 'POST' || Request::method() == 'PUT') {
            $backUrl = Session::get('curPage');
            // $aRights = $oRole->rights ? explode(',', $oRole->rights) : [];
            $aRights = Input::get('functionality_id', '');
            // pr($aRights);exit;
            // $aRights = array_merge($aRights, $aAddRights);
            if (is_array($aRights)){
                $aRights = array_unique($aRights);
                // pr($aRights);exit;
                $oRole->rights = implode(',', $aRights);
                // pr($oRole->validationErrors);exit;
            } else {
                $oRole->rights = $aRights;
            }
            $bSucc = $oRole->save();
            if ($bSucc) {
                return $this->goBackToIndex('success', '_role.right-updated');
            } else {
                return $this->goBack('error', __('_role.right-updated-fail', $this->langVars));
            }
        } else {
            // 获取排序条件
            $orderColumn = Input::get('sort_up', Input::get('sort_down', 'sequence'));
            $direction   = Input::get('sort_up') ? 'asc' : 'desc' ;
            // 构造查询语句
            if (!$oRole->right_settable) {
                return Redirect::back()
                    ->with('error', '<strong>' . __("The Role don't allow setting rights") . '</strong>');
            }
            Functionality::getTreeArray($datas, null, Functionality::REALM_USER);
            $checked = explode(',', $oRole->rights);

            // $checked = [];
            // $functionalities = $oRole->functionalities()->orderBy($orderColumn, $direction)->get();
            // foreach ($functionalities as $functionality) {
            //     array_push($checked, $functionality->id);
            // }
            $readonly = false;
            $this->setVars(compact('datas', 'role_id', 'checked', 'readonly', 'sRoleName'));
            // pr($datas);
            // exit;
            return $this->render();
        }
    }




}
