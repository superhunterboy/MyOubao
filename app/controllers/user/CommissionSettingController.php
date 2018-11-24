<?php

class CommissionSettingController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $customViewPath = 'fund.commissionSetting';
    protected $customViews = ['index', 'create', 'edit'];

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'CommissionSetting';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
    }

    /**
     * @return Response
     */
    public function index()
    {
        $oQuery = $this->indexQuery();
        $datas = $oQuery->paginate(static::$pagesize);
        $this->setVars(compact('datas'));
        return $this->render();
    }

}
