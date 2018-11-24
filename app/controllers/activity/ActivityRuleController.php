<?php

class ActivityRuleController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityRule';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch ($this->action) {
            case 'view':
            case 'edit':
            case 'index':
            case 'create':
                $this->setVars('aActivities', Activity::getTitleList());
                $this->setVars('aPrizes', ActivityPrize::getTitleList());
                $aNewTypes = [];
                foreach (ActivityRule::$aTypes as $key => $val) {
                    $aNewTypes[$key] = __('_activityrule.' . $val);
                }
                $aNewGenerateTypes = [];
                foreach (ActivityRule::$aGenerateTypes as $key => $val) {
                    $aNewGenerateTypes[$key] = __('_activityrule.' . $val);
                }
                $this->setVars('aTypes', $aNewTypes);
                $this->setVars('aGenerateTypes', $aNewGenerateTypes);
                break;
        }
    }

}
