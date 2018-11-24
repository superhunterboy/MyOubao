<?php
# Deprecated 管理员角色管理
class RoleAdminController extends BaseRoleController
{
    protected $customViewPath     = 'admin.role';
    protected $roleType           = 0; // 角色类型
    protected $functionality_type = 1; // 功能权限的类型
    protected $sModel             = 'AdminUser';
    protected $sPivotModelName    = 'AdminUserRole'; // 关联表模型
    protected $sChildrenName      = 'admin_users'; // 获取某一角色的所有用户的关联函数

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('resourceName', __('_function.' . 'Admin Roles'));
    }

}
