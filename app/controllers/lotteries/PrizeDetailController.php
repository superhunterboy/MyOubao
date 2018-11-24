<?php
class PrizeDetailController extends AdminBaseController
{
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'PrizeDetail';

    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aLotteryTypes', Lottery::$validTypes);
    }

    public function index() {
        $this->setVars('aBasicMethods', BasicMethod::getTitleList(true, PrizeGroup::find($this->params['group_id'])->type));
        parent::index();
    }

    public function edit($id) {
        $oBasicMethod =  $this->model->find($id)->belongsTo('BasicMethod','method_id')->first();
        $this->setVars('aBasicMethods', BasicMethod::getTitleList(true, $oBasicMethod->lottery_type));
        parent::edit($id);
    }

    public function view($id) {
        $oBasicMethod =  $this->model->find($id)->belongsTo('BasicMethod','method_id')->first();
        $this->setVars('aBasicMethods', BasicMethod::getTitleList(true, $oBasicMethod->lottery_type));
        parent::view($id);
    }

}