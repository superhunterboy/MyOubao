<?php
# 管理员角色关联关系管理
class AdminUserRoleController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
    // protected $privateResourceView = 'admin.adminRole';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'AdminUserRole';
    protected $iRoleType = 0;


    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    public function destroy($id) {
        $model = $this->model->find($id);
        $role_id = $model->role_id;
        $user_id = $model->user_id;
        $sModelName = $this->modelName;
        $count = Role::find($role_id)->admin_users()->detach($user_id);
        $sMessage = $count > 0 ? $this->resourceName . ' was deleted.' : $this->resourceName . 'cannot be deleted.';
        return $this->goBackToIndex('success', $sMessage);
    }
}