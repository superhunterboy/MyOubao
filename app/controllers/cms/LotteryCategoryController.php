<?php

class LotteryCategoryController extends AdminBaseController {

    protected $modelName = 'LotteryCategory';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aTemplates', Config::get('cms_template'));
        switch ($this->action) {
            case 'index':
            case 'view':
                $categoriesTree = & LotteryCategory::getTitleList();
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