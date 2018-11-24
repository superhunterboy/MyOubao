<?php

class BankController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    // protected $resourceView = 'admin.admin';
    protected $customViewPath = 'bank';
    protected $customViews = ['create', 'edit', 'feeView', 'feeEdit','index'];

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'Bank';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'edit':
            case 'create':
            case 'view':
                $this->setVars('aMode', Bank::$aMode);
                break;
        }
    }

    /**
     * 手续费编辑页
     * @param type $id 银行ID
     * @return Response
     */
    public function feeEdit($id) {
        $oBank = Bank::find($id);
        $aFeeExpressions = $oBank->getFeeExpressionsArray();
        $aBankFeeRateSet = Bank::$aBankFeeRateSet;
        if (Request::method() == 'POST') { // 处理提交的数据
            $aRequest = trimArray(Input::get());
            if (isset($aRequest['fee_set'])) {
//                return $this->goBack('error', '保存失败：SAVE ERROR #01');

                $aFeeSetRules = [
                    'amount_left' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
                    'amount_right' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
                    'operator' => 'required|in:%,=',
                    'value' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
                ];
                $aFeeSet = []; // 将要保存的公式
                foreach ($aRequest['fee_set'] as $fee_set) {
                    $validator = Validator::make($fee_set, $aFeeSetRules);
                    if (!$validator->passes()) {
//                    pr($validator->getMessageBag()->toArray());exit;
                        return $this->goBack('error', '保存失败：SAVE ERROR #02');
                    }
                    if (array_get($fee_set, 'amount_right') != '' && $fee_set['amount_right'] <= $fee_set['amount_left']) {
                        // 金额范围开始值必须小于结束值
                        return $this->goBack('error', '保存失败：SAVE ERROR #03');
                    }
                    if (isset($fLastAmount) && $fLastAmount > $fee_set['amount_left']) {
                        // 条件金额范围开始值要大于等于上一范围的结束值
                        return $this->goBack('error', '保存失败：SAVE ERROR #04');
                    }
                    /**
                     * ['x'=>['>='=>100, '<'=>'200'], 'y'=>['='=>5]],
                     * ['x'=>['>='=>200, '<'=>'500'], 'y'=>['%'=>5]],
                     */
                    $aXSet = ['>=' => $fee_set['amount_left']];
                    if (array_get($fee_set, 'amount_right') != '') {
                        $aXSet['<'] = $fee_set['amount_right'];
                    }
                    $aFeeSet[] = [
                        'x' => $aXSet,
                        'y' => [$fee_set['operator'] => $fee_set['value']]
                    ];
                    $fLastAmount = array_get($fee_set, 'amount_right', 0);
                }
                $oBank->setFeeExpressions($aFeeSet);
            } else {
                $oBank->setFeeExpressions();
            }
            if (!$oBank->save()) {
                return $this->goBack('error', '保存失败：SAVE ERROR #05');
            }
            return Redirect::to(route($this->resource . '.fee_view', $id));
        }
        $this->setVars(compact('oBank', 'id', 'aFeeExpressions', 'aBankFeeRateSet'));
        return $this->render();
    }

    /**
     * 显示银行手续费返还公式详情
     * @param type $id 银行ID
     * @return Response
     */
    public function feeView($id) {
        $oBank = Bank::find($id);
        $aFeeExpressions = $oBank->getFeeExpressionsArray();
        $this->setVars(compact('oBank', 'id', 'aFeeExpressions'));
        return $this->render();
    }

    
    
    /**
     * 是否禁用存提
     */
    public function prohibitedWithdraw(){
        $value = SysConfig::readValue('prohibited_withdraw');
        if($value == 0){
            $typeStr = '禁用';
        }elseif($value == 1){
            $typeStr = '启用';
        }else{
            return $this->goBack('error', '保存失败：非法参数');
        }

        $status = SysConfig::setValue('prohibited_withdraw',!$value);
        if($status){
            return $this->goBack('success', $typeStr.'成功');
        }else{
            return $this->goBack('error', $typeStr.'失败');
        }
    }

    
    
    /**
     * 提款通道设置
     */
    
    public function withdrawalChannel(){
        
        
        
    }
}
