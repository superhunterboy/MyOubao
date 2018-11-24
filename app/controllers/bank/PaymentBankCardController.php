<?php

/**
 * BankcardController
 *
 * @author dev
 */
class PaymentBankCardController extends AdminBaseController {

//    protected $customViewPath = 'bank';
//    protected $customViews    = ['create', 'edit', 'feeView', 'feeEdit'];

    protected $modelName = 'PaymentBankCard';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $aBanks = & Bank::getAllBankArray();
        $this->setVars(compact('aBanks'));
        $this->setVars('aValidStatuses', PaymentBankCard::$validStatuses);
    }

}
