<?php
# 用户角色管理
class RoleController extends BaseRoleController
{
    protected $customViewPath     = 'admin.userRole';
    protected $roleType           = 1; // 角色类型
    protected $functionality_type = 2; // 功能权限的类型
    protected $sModel             = 'User';
    protected $sPivotModelName    = 'UserRole'; // 关联表模型
    protected $sChildrenName      = 'users'; // 获取某一角色的所有用户的关联函数

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('resourceName', __('_function.' . 'User Roles'));
    }

}
