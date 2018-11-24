<?php
# 管理员对用户的操作日志管理
class UserManageLogController extends AdminBaseController
{
    protected $customViewPath     = 'admin.userManageLog';
    protected $customViews = ['index'];
    protected $modelName = 'UserManageLog';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        // pr($this->viewVars['aColumnForList']);exit;
        // $this->setVars('resourceName', __('_function.' . 'UserManageLog'));
    }

    public function updateComments()
    {
        // pr(Input::all());exit;
        $aParams = $this->params['comment'];
        $bSucc = UserManageLog::updateComments($aParams);
        if ($bSucc) return $this->goBack('success', __('_basic.updated', $this->langVars));
        else return $this->goBack('error', __('_basic.update-fail', $this->langVars));
    }

    // public function index()
    // {
    //     return $this->render();
    // }


}
