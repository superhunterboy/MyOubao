<?php

# 用户角色关联关系管理

class RoleUserController extends AdminBaseController {

    protected $modelName = 'RoleUser';
    protected $roleType = 1; // 角色类型
    protected $functionality_type = 2; // 功能权限的类型
    protected $sModel = 'User';
    protected $sPivotModelName = 'RoleUser'; // 关联表模型
    protected $sChildrenName = 'users'; // 获取某一角色的所有用户的关联函数
    protected $customViewPath = 'admin.roleuser';
    protected $customViews = [
        'create'
    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aUserRoles = Role::getAllRoleNameArray(Role::USER_ROLE);
        $this->setVars(compact('aUserRoles'));
        switch ($this->action) {
            case 'index':
                break;
            case 'view':
                break;
            case 'create':
                break;
        }
    }

    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            $sStep = Input::get('step');
            $iRoleId = Input::get('role_id');
            if (!isset($iRoleId) || $iRoleId == '') {
                return $this->goBack('error', __('_roleuser.missing-role-id'));
            }
            if (isset($sStep) && $sStep == 'step1') {
                $sUsername = Input::get('username');
                $oUser = new User;
                if (isset($sUsername) && !empty($sUsername)) {
                    $aUsers = $oUser->getValueListArray('username', ['username' => ['like', '%' . $sUsername . '%']], [], true);
                } else {
                    return $this->goBack('error', __('_roleuser.missing-username'));
                }
                $aRoleUsers = RoleUser::getUserIdsFromRoleId($iRoleId);
                $oRole = Role::find($iRoleId);
                if (is_object($oRole)) {
                    $this->setVars('nonExpired', $oRole->non_expired);
                    $this->setVars('role_id', $oRole->id);
                }
                $this->setVars(compact('aUsers', 'aRoleUsers'));
                $this->view = 'admin.roleuser.bindUser';
                return $this->render();
            } else if (isset($sStep) && $sStep == 'step2') {
                DB::connection()->beginTransaction();
                $aUserIds = Input::get('user_id');
                if (!isset($aUserIds) || count($aUserIds) <= 0) {
                    return $this->goBack('error', __('_roleuser.save-fail'));
                }
                $sAddDate = Input::get('add_date');
                $sExpireDate = Input::get('expire_date');
                $bSucc = true;
                foreach ($aUserIds as $val) {
                    $oRoleUser = RoleUser::getUserRoleFromUserIdRoleId($iRoleId, $val);
                    if (!is_object($oRoleUser)) {
                        $oRoleUser = new RoleUser;
                    }
                    $oRoleUser->user_id = $val;
                    $oRoleUser->role_id = $iRoleId;
                    $oRole = Role::find($iRoleId);
                    $oUser = User::find($val);
                    $oRoleUser->role_name = $oRole->name;
                    $oRoleUser->username = $oUser->username;
                    $oRoleUser->expire_date = empty($sExpireDate) ? null : $sExpireDate;
                    $oRoleUser->add_date = empty($sAddDate) ? null : $sAddDate;
                    $bSucc = $oRoleUser->save();
                    if ($bSucc == false) {
                        break;
                    }
                }
                if ($bSucc == true) {
                    DB::connection()->commit();
                    return $this->goBack('success', __('_roleuser.save-success'));
                } else {
                    DB::connection()->rollback();
                    return $this->goBack('error', __('_roleuser.save-fail'));
                }
            }
        } else {
            $this->setVars('role_id', $id);
            return $this->render();
        }
    }

}
