<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-10-14
 * Time: 上午8:59
 */
class SdpayDepositSettingController extends AdminBaseController{

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'sdpaysetting';

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

        $sdpay_deposit_enable = SysConfig::readValue('sdpay_deposit_enable');
        $this->setVars('sdpay_deposit_enable', $sdpay_deposit_enable);

        $deposit_amount_min = SysConfig::readValue('deposit_amount_min');
        $this->setVars('deposit_amount_min', $deposit_amount_min);

        $single_deposit_amount_max = SysConfig::readValue('single_deposit_amount_max');
        $this->setVars('single_deposit_amount_max', $single_deposit_amount_max);

        $day_deposit_amount_max = SysConfig::readValue('day_deposit_amount_max');
        $this->setVars('day_deposit_amount_max', $day_deposit_amount_max);

        $deposit_fee = SysConfig::readValue('deposit_fee');
        $this->setVars('deposit_fee', $deposit_fee);

        $total_deposit_fee = SysConfig::readValue('total_deposit_fee');
        $this->setVars('total_deposit_fee', $total_deposit_fee);

        $deposit_channel_description = SysConfig::readValue('deposit_channel_description');
        $this->setVars('deposit_channel_description', $deposit_channel_description);


        if(Request::method() == 'POST'){
            $aData = Input::all();

            if(!isset($aData['sdpay_deposit_enable'])) $sdpay_deposit_enable = 0;
            else $sdpay_deposit_enable = 1;
            if(
                SysConfig::setValue('sdpay_deposit_enable',$sdpay_deposit_enable) &&
                SysConfig::setValue('deposit_amount_min',$aData['deposit_amount_min']) &&
                SysConfig::setValue('single_deposit_amount_max',$aData['single_deposit_amount_max']) &&
                SysConfig::setValue('day_deposit_amount_max',$aData['day_deposit_amount_max']) &&
                SysConfig::setValue('deposit_fee',$aData['deposit_fee']) &&
                SysConfig::setValue('total_deposit_fee',$aData['total_deposit_fee']) &&
                SysConfig::setValue('deposit_channel_description',$aData['deposit_channel_description'])
            )
                return $this->goBack('success', '设置成功！');
            else
                return $this->goBack('error', '设置失败！');
        }
        $this->render();
    }



}