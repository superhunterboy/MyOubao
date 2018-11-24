<?php

class SearchItemController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
//    protected $resourceView = 'default';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'SearchItem';

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
                App::make('Functionality')->getTree($aFunctionalities,null,null,null,null);
                $aSearchForms = & SearchConfig::getTitleList();
                break;
            case 'edit':
            case 'create':
                App::make('Functionality')->getTree($aFunctionalities,null,null,['title' => 'asc']);
                $aSearchForms = & SearchConfig::getTitleList();
                break;
        }
//        pr($aSearchForms);
//        exit;
        $this->setVars(compact('aFunctionalities','aSearchForms'));
    }
}

