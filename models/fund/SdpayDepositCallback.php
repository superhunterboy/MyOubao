<?php

class SdpayDepositCallback extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sdpay_deposit_callbacks';

    const DEPOSIT_STATUS_SUCCESS = 1;
    const DEPOSIT_STATUS_FAILURE = 1;

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'pay_time',
        'amount',
        'company_order_num',
        'result',
        'fee',
        'message',
        'username',
        'merchantid',
        'status',
        'cmd',
        'unit',
        
    ];
    public static $resourceName = 'DepositCallback';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';
    public static $titleColumn = '';
    public static $rules = [
        'pay_time' => 'required|date',
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'company_order_num' => 'required|between:1,64',
        'unit' => 'in:0,1,2',
        'result'=>'required|in:0,1,2',
        'fee' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'status'=>'required|in:0,1,2',
        'message' => '',
        'merchantid'=>'required',
        'cmd'=>'required',
        'username'=>'',
    ];

    /**
     * 平台响应状态：失败
     */
    const RESPONSE_STATUS_FAIL = 2;

    
    /***
     * 平台响应状态：等待
     */
    const RESPONSE_STATUS_WAITING = 0;
    
    /**
     * API响应状态：成功
     */
    const RESPONSE_STATUS_SUCCESS = 1;

    /**
     * 添加新记录，并返回实例
     * @param array $aInitData
     * @return DepositCallback
     */
    public static function createCallback(array $aInitData) {
        $oSdpayDepositCallback = new SdpayDepositCallback($aInitData);
        if (!$bSucc = $oSdpayDepositCallback->save()) {
//            pr($oSdpayDepositCallback->validationErrors->toArray());
//            exit;
            return false;
        }
        return $oSdpayDepositCallback;
    }

    /**
     * 设置响应的状态为成功
     * @return boolean
     */
    public function setResponseSuccessful() {
        $this->status = $this->status?$this->status: self::RESPONSE_STATUS_SUCCESS;
        return $this->save();
    }

    /**
     * 设置响应的状态为失败
     * @param type $sMsg 附带失败信息
     * @return boolean
     */
    public function setResponseFailed($sMsg = '') {
        $this->error_msg = $sMsg;
        $this->status =$this->status?$this->status: self::RESPONSE_STATUS_FAIL;
        return $this->save();
    }
/**
     * 设置响应的状态为等待
     * @param type $sMsg 附带失败信息
     * @return boolean
     */
    public function setResponseWaiting($sMsg = '') {
        $this->error_msg = $sMsg;
        $this->status = self::RESPONSE_STATUS_WAITING;
        return $this->save();
    }

}
