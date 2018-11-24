<?php

class FunctionalityParamController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
//    protected $resourceView = 'default';

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'FunctionalityParam';

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $oFunctionality = new Functionality;
        $this->setVars('aValidTypes', ['int' => 'int','string' => 'string']);
        switch($this->action){
            case 'index':
            case 'view':
//                $aMenuTree = & $this->model->getTitleList();
                $aFunctionalities = & $oFunctionality->getTitleList();
//                pr($aFunctionalities);
//                exit;
//                $aObjFunctionality = Functionality::all(['id','title']);
//                $aFunctionalities = [];
//                foreach($aObjFunctionality as $oFunctionality){
//                    $aFunctionalities[$oFunctionality->id] = $oFunctionality->title;
//                }
//                unset($aObjFunctionality);
                $this->setVars(compact('aFunctionalities'));
                break;
            case 'edit':
            case 'create':
                App::make('Functionality')->getTree($functionalitiesTree,null,null,['title' => 'asc']);
                $this->setVars('aFunctionalities', $functionalitiesTree);
                break;
        }
    }

}
