<?php

/**
 * 管理员角色关系管理
 *
 * @author frank
 */
class AdminRoleUserController extends AdminBaseController {
    protected $modelName = 'AdminRoleUser';
//    protected $customViewPath     = 'admin.role';
//    protected $customViews    = [
//        'setRights',
//        'viewRights',
//    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
         parent::beforeRender();
        $sModelName = $this->modelName;
        // pr($this->action);exit;
        switch($this->action){
//            case 'index':
//            case 'view':
            case 'create':
                $this->setVars('aRoles',AdminRole::getTitleList(true));
                $this->setVars('aAdministrators',AdminUser::getTitleList(true));
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

}
