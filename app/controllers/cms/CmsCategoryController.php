<?php

class CmsCategoryController extends AdminBaseController {

    protected $modelName = 'CmsCategory';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aTemplates', Config::get('cms_template'));
        switch ($this->action) {
            case 'index':
            case 'view':
                $categoriesTree = & CmsCategory::getTitleList();
                $this->setVars('aCategoriesTree', $categoriesTree);
                break;
            case 'edit':
            case 'create':
                $this->model->getTree($categoriesTree, null, null, ['name' => 'asc']);
                $this->setVars('aCategoriesTree', $categoriesTree);
                break;
        }
    }

}