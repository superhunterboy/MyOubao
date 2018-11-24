<?php

/**
 * 代金券
 */
class VoucherController extends AdminBaseController {

    protected $modelName = 'Voucher';
    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        
        $aTypes = Voucher::$validTypes;
        Voucher::translateArray($aTypes);
        $this->setVars(compact('aTypes'));
    }

}
