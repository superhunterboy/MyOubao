<?php
class TransactionTypeController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
//    protected $resourceView = 'default';

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'TransactionType';

    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aTransactionTypes', $this->model->getTitleList());
        $this->setVars('aFundFlows', FundFlow::getTitleList());
        $this->setVars('aFundActions',FundFlow::$validActions);
    }

}