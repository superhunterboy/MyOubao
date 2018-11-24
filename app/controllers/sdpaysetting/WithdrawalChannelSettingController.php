<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-10-14
 * Time: 上午8:59
 */
class WithdrawalChannelSettingController extends AdminBaseController{

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'withdrawalchannelsetting';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'SysConfig';

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;

        switch($this->action){
            case 'settings':
                $this->setVars('isEdit',true);
                $this->setVars('aColumnForList', $sModelName::$columnForList);
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
        }
    }

    public function settings(){
        $payment_amount_setting = SysConfig::readValue('payment_amount_setting');
        $sdpay_enable = SysConfig::readValue('sdpay_enable');
        $dashpay_enable = SysConfig::readValue('dashpay_enable');
        $aPayment_amount_setting = json_decode($payment_amount_setting);
        $sdpay_amount_max = $aPayment_amount_setting[0]->sdpay_amount_max;
        $sdpay_sort = $aPayment_amount_setting[0]->sort;
        $dashpay_amount_max = $aPayment_amount_setting[1]->dashpay_amount_max;
        $dashpay_sort = $aPayment_amount_setting[1]->sort;
        $this->setVars('sdpay_enable',$sdpay_enable);
        $this->setVars('dashpay_enable',$dashpay_enable);
        $this->setVars('sdpay_amount_max',$sdpay_amount_max);
        $this->setVars('sdpay_sort',$sdpay_sort);
        $this->setVars('dashpay_amount_max',$dashpay_amount_max);
        $this->setVars('dashpay_sort',$dashpay_sort);
        if(Request::method() == 'POST'){
            $aData = Input::all();
            if(
                SysConfig::setValue('payment_amount_setting','[{"sdpay_amount_max":'.$aData['sdpay_amount_max'].',"sort":'.$aData['sdpay_sort'].'},{"dashpay_amount_max":'.$aData['dashpay_amount_max'].',"sort":'.$aData['dashpay_sort'].'}]') &&
                SysConfig::setValue('sdpay_enable',isset($aData['sdpay_enable']) ? 1 : 0) &&
                SysConfig::setValue('dashpay_enable',isset($aData['dashpay_enable']) ? 1 : 0)
            )
                return $this->goBack('success', '设置成功！');
            else
                return $this->goBack('error', '设置失败！');
        }

        $this->render();
    }

}