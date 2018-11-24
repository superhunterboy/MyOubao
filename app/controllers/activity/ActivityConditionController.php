<?php

class ActivityConditionController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityCondition';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch ($this->action) {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
                $this->setVars('aTasks', ActivityTask::getTitleList());
                $this->setVars('aActivityConditionClasses', ActivityConditionClass::getTitleList());
                $this->setVars('aActivities', Activity::getTitleList());
                break;
        }
    }
}
