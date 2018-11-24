<?php

class ActivityRebateSettingController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityRebateSetting';

    protected $customViewPath = 'activityRebateSetting';
    protected $customViews = ['index'];
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
//        $sModelName = $this->modelName;
        switch ($this->action) {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
                $this->setVars('aOperators', ActivityRebateSetting::$aOperators);
                break;
        }
    }

    public function index(){
        $oModel=$this->model;
        if (Request::method() == 'POST') { // 处理提交的数据
            $this->saveSettings();
        }
        $rebateSettings=$oModel->all();
        $aRebateFeeRateSet = ActivityRebateSetting::$aRebateFeeRateSet;
        $this->setVars(compact('rebateSettings','aRebateFeeRateSet'));
        return $this->render();
    }
//保存设置
    protected  function saveSettings(){
            $aRequest = trimArray(Input::get());
            if (isset($aRequest['fee_set'])) {
//                return $this->goBack('error', '保存失败：SAVE ERROR #01');
                $aFeeSetRules = [
                    'left_amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
                    'right_amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
                    'operator' => 'required|in:%,=',
                    'rebate_value' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
                ];
                $aFeeSet = []; // 将要保存的公式
                foreach ($aRequest['fee_set'] as $fee_set) {
                    $validator = Validator::make($fee_set, $aFeeSetRules);
                    if (!$validator->passes()) {
//                    pr($validator->getMessageBag()->toArray());exit;
                        return $this->goBack('error', '保存失败：SAVE ERROR #02');
                    }
                    if (array_get($fee_set, 'right_amount') != '' && $fee_set['right_amount'] <= $fee_set['left_amount']) {
                        // 金额范围开始值必须小于结束值
                        return $this->goBack('error', '保存失败：SAVE ERROR #03');
                    }
                    if (isset($fLastAmount) && $fLastAmount > $fee_set['left_amount']) {
                        // 条件金额范围开始值要大于等于上一范围的结束值
                        return $this->goBack('error', '保存失败：SAVE ERROR #04');
                    }
                    $fLastAmount = array_get($fee_set, 'right_amount', 0);
                    if( !empty($fee_set['id'])){
                        $setting=$this->model->find($fee_set['id']);
                    }else{
                        $setting= $this->model;
                    }
                    $setting->right_amount=$fee_set['right_amount'];
                    $setting->left_amount=$fee_set['left_amount'];
                    $setting->rebate_value=$fee_set['rebate_value'];
                    $setting->operator=$fee_set['operator'];
    ////                $rebateSettings->setFeeExpressions($aFeeSet);
//                    var_dump($setting->toArray());
                    if (!$setting->save()) {
    //                    echo $setting->getValidationErrorString();exit;
                        return $this->goBack('error', '保存失败：SAVE ERROR #05');
                    }
                }

            }

            return Redirect::to(route($this->resource . '.index'));
        }



//    protected function beforSave(){
//    var_dump($this);exit;
//
//    }

}
