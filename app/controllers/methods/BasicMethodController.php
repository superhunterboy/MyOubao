<?php
class BasicMethodController extends AdminBaseController
{
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'BasicMethod';


    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oMethodType = new MethodType;
        $this->setVars('aLotteryTypes',Lottery::$validTypes);
        $aConditions = !empty($this->viewVars[ 'lottery_type' ]) ? [ 'lottery_type' => [ '=',$this->viewVars[ 'lottery_type' ]]] : [];
        $this->setVars('aMethodTypes',$oMethodType->getValueListArray('name',$aConditions,null,true));
    }

    /**
     * 设置投注说明和中奖说明
     * @param int $id
     */
    public function setNote($id){
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            $this->model->bet_rule = $this->params['bet_rule'];
            $this->model->bonus_note = $this->params['bonus_note'];
            $this->model->fillable(['bet_rule','bonus_note']);
            if ($bSucc = $this->model->save()) {
                return $this->goBackToIndex('success', __('_basicmethod.note-seted', $this->langVars));
            } else {
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basicmethod.note-set-failed', $this->langVars));
            }
        }
        else{
            $oSeries = Series::find($this->model->series_id);
            $this->setVars('data',$this->model);
            $this->setVars('series',$oSeries->name);
        }
        return $this->render();
    }
}