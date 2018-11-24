<?php

/**
 * 代理销量报表
 *
 * @author snowan
 */
class UserBonusAidCycleController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';

    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'userBonusAidCycles';

    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'index',
    ];
    protected $errorFiles = [];
    protected $modelName = 'UserBonusAidCycles';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
   
    }

    public function index($user_id=null) {
        $oUsers = User::find($user_id);
        $this->setVars(compact('oUsers'));
        $model = $this->model->find($user_id);

        if (Request::method() == 'PUT' ) {
                return parent::edit($user_id);
        }elseif(Request::method() == 'POST' ){
                return parent::create($user_id);
        }
        $data = !is_object($model) ? $this->model : $model;
        $isEdit = true;
        $this->setVars(compact('data', 'parent_id', 'isEdit', 'id'));
        return $this->render();
    }

}
