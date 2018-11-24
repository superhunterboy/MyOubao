<?php

class MethodController extends AdminBaseController {

    protected $modelName = 'Method';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
            case 'create':
                $aLotteries = & Lottery::getTitleList();
                $this->setVars(compact('aLotteries'));
                $aBasicMethod = & BasicMethod::getTitleList();
                $this->setVars(compact('aBasicMethod'));
                break;
            case 'view':
                $aLotteries = & Lottery::getTitleList();
                $oIssueRule = new IssueRule;
                $aIssueRules = $oIssueRule->getIssueRules();
                $this->setVars(compact('aIssueRules'));
                $this->setVars(compact('aLotteries'));
                $aIssueStatus = $this->model->bonusCodeStatus;
                $this->setVars(compact('aIssueStatus'));
                break;
//            case 'edit':
//            case 'create':
//                $this->model->getTree($functionalitiesTree);
//                $this->setVars('functionalitiesTree', $functionalitiesTree);
//                break;
        }
    }

}