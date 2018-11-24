<?php
class PrizeLevelController extends AdminBaseController
{
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'PrizeLevel';

    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aLotteryTypes', Lottery::$validTypes);
        $this->setVars('aBasicMethods', BasicMethod::getTitleList());
    }

}