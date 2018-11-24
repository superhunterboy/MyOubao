<?php

class FunctionalityRelationController extends AdminBaseController
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
    protected $modelName = 'FunctionalityRelation';

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
        switch($this->action){
            case 'index':
            case 'view':
//                $aMenuTree = & $this->model->getTitleList();
                $aFunctionalities = & Functionality::getTitleList();
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
                $oFunctionality = new Functionality;
                $oFunctionality->getTree($functionalitiesTree, null, null, ['title' => 'asc'], '--');
//                pr($functionalitiesTree);
//                exit;
                $this->setVars('aFunctionalities', $functionalitiesTree);
                break;
        }
    }
}
