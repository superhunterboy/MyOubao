<?php

class ActivityPrizeController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityPrize';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch ($this->action) {
            case 'index':
                if (isset($this->params['action']) && $this->params['action'] == 'ajax') {
//                    echo 123;exit;
                    $activity_id = $this->params['activity_id'];
                    $prizes = ActivityPrize::where('activity_id',$activity_id)->get(['id','name'])->toArray();

                    if (!empty($prizes)) {
                        $aData = [];
                        $aData['data'] = $prizes;
                        echo json_encode($aData);
                    }
                    exit;
                }
            case 'view':
            case 'edit':
            case 'create':
                $this->setVars('aActivities', Activity::getTitleList());
                $this->setVars('aPrizeClasses', ActivityPrizeClass::getTitleList());
                break;
        }
    }

}
