<?php

class ActivityTaskController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityTask';

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
                $this->setVars('aActivities', Activity::getTitleList('name'));
                $aNewTypes = [];
                foreach (ActivityTask::$aTypes as $key => $val) {
                    $aNewTypes[$key] = __('_activitytask.' . $val);
                }
                $this->setVars('aTypes', $aNewTypes);
                break;
        }
    }

}
