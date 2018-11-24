<?php

/**
 * 代金券
 */
class UserVoucherController extends AdminBaseController {

    protected $modelName = 'UserVoucher';
    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aVouchers', Voucher::getTitleList());
    }

}
