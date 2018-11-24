<?php
/**
 * 后台管理操作日志控制器
 */
class FunctionalityLogController extends AdminBaseController {

    protected $modelName = 'FunctionalityLog';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
//        $sModelName = $this->modelName;
//        $this->setVars('aValidRealms', $sModelName::$realms);
//        switch($this->action){
////            case 'index':
//            case 'view':
////                $functionalitiesTree = $this->model->getTitleList();
////                $this->setVars('functionalitiesTree', $functionalitiesTree);
//                break;
//            case 'edit':
//            case 'create':
//                $this->model->getTree($functionalitiesTree,null,null,['title' => 'asc']);
//                $this->setVars('functionalitiesTree', $functionalitiesTree);
//                break;
//        }
    }
    
}

