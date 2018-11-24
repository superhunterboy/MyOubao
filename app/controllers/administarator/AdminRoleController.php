<?php

/**
 * 管理员角色管理
 *
 * @author frank
 */
class AdminRoleController extends AdminBaseController {
    protected $modelName = 'AdminRole';
    protected $customViewPath     = 'admin.role';
    protected $customViews    = [
        'setRights',
        'viewRights',
    ];

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
    }

    public function viewRights ($role_id){
//        $sModel          = $this->sModel;
//        $sPivotModelName = $this->sPivotModelName;
//        $sChildrenName   = $this->sChildrenName;
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
//        $datas = $this->_getFunctionalities(false, 2, 1);
        Functionality::getTreeArray($datas, null, Functionality::REALM_ADMIN);
//        Functionality::getTreeArray($datas, null, 1);
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
//        $sModel          = $this->model;
//        $sPivotModelName = $this->sPivotModelName;
//        $sChildrenName   = $this->sChildrenName;
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

//            $datas = $this->_getFunctionalities(false, 2, 1);
            Functionality::getTreeArray($datas, null, Functionality::REALM_ADMIN);
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
