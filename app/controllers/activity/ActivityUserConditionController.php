<?php

class ActivityUserConditionController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityUserCondition';
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
                $this->setVars('aTasks', ActivityTask::getTitleList());
                $this->setVars('aUsers', User::getTitleList());
                $this->setVars('aActivityConditions', ActivityCondition::getTitleList());
                $this->setVars('aActivities', Activity::getTitleList());
                $this->setVars('aStatus', ActivityUserCondition::$aStatus);
                break;
        }
    }

}
