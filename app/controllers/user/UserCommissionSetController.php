<?php

/**
 * 用户管理
 */
class UserCommissionSetController extends AdminBaseController {
    protected $modelName = 'UserCommissionSet';

    protected function beforeRender() {
        parent::beforeRender();
        $aSeriesSets = & SeriesSet::getTitleList();
        $aTypeIds = SeriesSet::getTypeList();
        $this->setVars(compact('aSeriesSets', 'aTypeIds'));
    }

    public function edit($id) {

        if (Request::method() == 'PUT')
        {
            $oUserCommissionSet = $this->model->find($id);
            if($oUserCommissionSet->series_set_id == SeriesSet::ID_LOTTERY && $this->params['commission_rate'] != $oUserCommissionSet->commission_rate){
                return Redirect::back()->withInput()->with('error', '请通过修改奖金组调整此返点');
            }

            $oUserCommissionSet->commission_rate = $this->params['commission_rate'];
            $aReturnMsg = $oUserCommissionSet->validateData();

            if (!$aReturnMsg['success']) {
                return Redirect::back()->withInput()->with('error', $aReturnMsg['msg']);
            }
        }

        return parent::edit($id);
    }


    public function diffCommissionRate($id){
        $rates = $this->model->find($id)->getDiffCommissionRate();
        $this->halt(true, 'info', null, $a, $a, $rates);
    }
}