<?php

/**
 * 总代月分红
 */
class TopAgentMonthlyBonusController extends AdminBaseController {

    protected $modelName = 'TopAgentMonthlyBonus';
    protected $customViewPath = 'admin.dailySalary';
    protected $customViews = [
        'edit',
    ];

    /**
     * 资源编辑页面
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            $parent_id = $this->model->parent_id;

            $data = $this->model;
            $fMinBonusPercent = DailySalaryProtocal::getMinBonusPercentByParentId($data->user_id);
            $iMinBonusPercent = $fMinBonusPercent * 1000;
            $oParentProtocal = DailySalaryProtocal::getObjectByParams(['user_id' => $parent_id]);
            if (is_object($oParentProtocal)) {
                $fMaxBonusPercent = $oParentProtocal->bonus_percent;
            } else {
                $fMaxBonusPercent = 0.021;
            }
            $fMaxBonusPercent = $fMaxBonusPercent * 10000;
            $aBonusPercents = array();
            for ($i = $fMaxBonusPercent; $i > $iMinBonusPercent; $i--) {
                $key = $i / 10000.0;
                $aBonusPercents['' . $key] = number_format($i / 100, 2) . '%';
            }
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit', 'id', 'aBonusPercents'));
            return $this->render();
        }
    }

}
