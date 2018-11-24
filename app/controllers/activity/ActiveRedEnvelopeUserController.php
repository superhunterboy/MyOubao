<?php

class ActiveRedEnvelopeUserController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActiveRedEnvelopeUser';
    

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aStatus = ActiveRedEnvelopeUser::$aStatus;
        $this->setVars(compact('aStatus'));
        switch ($this->action) {
            case 'index':
         
        }
    }

    
}
