<?php

class ActiveRedEnvelopeRuleController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActiveRedEnvelopeRule';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aStatus = ActiveRedEnvelopeRule::$aStatus;
        $this->setVars(compact('aStatus'));
        switch ($this->action) {

            case 'index':
            case 'edit':
                break;
            case 'create':
                break;
        }
    }

    public function edit($id) {
        $this->params['admin'] = Session::get('admin_username');
        $this->params['admin_id'] = Session::get('admin_user_id');
        return parent::edit($id);
    }

    public function create($id = null) {
        $this->params['admin'] = Session::get('admin_username');
        $this->params['admin_id'] = Session::get('admin_user_id');
        //
        if (Request::method() == 'POST') {
            if (empty($this->params['min_turnover']) && empty($this->params['max_turnover']) && empty($this->params['max_bet_times']) && empty($this->params['min_bet_times'])) {

                return $this->goBack('error', 'turnover can not be empty!');
            }

            if ($this->params['min_turnover'] != 0) {
                if(ActiveRedEnvelopeRule::where('min_turnover', '<', $this->params['min_turnover'])->where('max_turnover', '>', $this->params['min_turnover'])->where('status',1)->count()){

                    return $this->goBack('error', 'min_turnover error!');
                }
            }

            if ($this->params['max_turnover'] != 0) {
                if(ActiveRedEnvelopeRule::where('min_turnover', '<', $this->params['max_turnover'])->where('max_turnover', '>', $this->params['max_turnover'])->where('status',1)->count()){
                    return $this->goBack('error', 'max_turnover error!');
                }
            }
            if ($this->params['max_turnover'] != 0 ||$this->params['min_turnover'] != 0) {

                if($a=ActiveRedEnvelopeRule::where('min_turnover', '>=', $this->params['min_turnover'])->where('max_turnover', '<=', $this->params['max_turnover'])->where('max_turnover','>',0)->where('min_turnover','>',0)->where('status',1)->count()){

                    return $this->goBack('error', $a.'max_turnover and min_turnover  error!');
                }
            }
        }
        return parent::create($id);
    }

    public function setStatus($id) {
        $this->params['admin'] = Session::get('admin_username');
        $this->params['admin_id'] = Session::get('admin_user_id');
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $this->model->status = $this->model->status == 1 ? 0 : 1;
        if ($bSucc = $this->model->save()) {
            return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
        } else {
            $this->langVars['reason'] = & $this->model->getValidationErrorString();
            return $this->goBack('error', __('_basic.update-fail', $this->langVars));
        }
        return parent::create($id);
    }

}
