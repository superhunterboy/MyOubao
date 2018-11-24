<?php
class AuditTypeController extends AdminBaseController {
    protected $modelName = 'AuditType';
    // protected $resourceView = 'admin.audit';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        // $sModelName = $this->modelName;
        // $aAuditTypes = AuditType::getAuditTypes();
        // $aStatus = $sModelName::$statusDesc;
        // switch($this->action){
        //     case 'index':
        //         break;
        // }
        // $this->setVars(compact('aAuditTypes', 'aStatus'));
    }
}