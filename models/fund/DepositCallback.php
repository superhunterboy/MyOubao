<?php

class DepositCallback extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deposit_callbacks';

    const STATUS_CALLED = 0;
    const STATUS_FAILURE = 1;
    const STATUS_NO_DATA = 2;
    const STATUS_IP_ERROR = 3;
    const STATUS_SIGN_ERROR = 4;
    const STATUS_MERCODE_ERROR = 5;
    const STATUS_PAYMENT_FAILURE  = 6;
    const STATUS_DEPOSIT_STATUS_ERROR  = 7;
    const STATUS_AMOUNT_ERROR  = 8;
    const STATUS_SET_DEPOSIT_STATUS_ERROR  = 9;
    const STATUS_ADD_TASK_ERROR  = 10;
    const STATUS_DATA_ERROR = 11;
    const STATUS_SUCCESS = 32;

    public static $validStatus = [
        self::STATUS_CALLED => 'new',
        self::STATUS_FAILURE => 'failed',
        self::STATUS_NO_DATA => 'missing deposit',
        self::STATUS_IP_ERROR => 'IP error',
        self::STATUS_SIGN_ERROR => 'sign error',
        self::STATUS_MERCODE_ERROR => 'mercode error',
        self::STATUS_PAYMENT_FAILURE => 'payment error',
        self::STATUS_DEPOSIT_STATUS_ERROR => 'deposit status error',
        self::STATUS_AMOUNT_ERROR => 'amount error',
        self::STATUS_SET_DEPOSIT_STATUS_ERROR => 'set status error',
        self::STATUS_ADD_TASK_ERROR => 'add task error',
        self::STATUS_DATA_ERROR => 'data error',
        self::STATUS_SUCCESS => 'success'
    ];
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'order_no',
        'platform_id',
        'platform',
        'platform_identifier',
        'service_order_no',
        'merchant_code',
        'amount',
        'ip',
        'status',
        'post_data',
        'referer',
        'http_user_agent',
        'callback_time',
        'callback_at',
//        'created_at',
//        'updated_at',
    ];
    public static $resourceName = 'DepositCallback';

    public static $columnForList = [
        'callback_at',
        'platform',
        'order_no',
        'service_order_no',
        'merchant_code',
        'amount',
        'ip',
        'status',
    ];
    public static $listColumnMaps = [
        'status' => 'status_formatted',
    ];
    public static $viewColumnMaps = [
        'status' => 'status_formatted',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aValidStatus'
    ];

    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';
    public static $titleColumn = '';
    public static $rules = [
        'order_no' => 'required|between:1,64',
        'service_order_no' => 'required|between:1,64',
        'platform_id' => 'integer',
        'platform' => 'max:50',
        'platform_identifier' => 'max:16',
        'merchant_code' => 'required|max:16',
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'ip' => 'required|ip',
        'status' => 'required|min:0|max:32',
        'post_data' => 'required',
        'referer' => 'max:2048',
        'http_user_agent' => 'max:10240',
        'callback_time' => 'required|integer',
        'callback_at' => 'required',
    ];

    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = [];

    /**
     * 添加新记录，并返回实例
     * @param array $aInitData
     * @return DepositCallback
     */
    public static function createCallback(array $aInitData) {
        $oDepositCallback = new static ($aInitData);
        if (!$bSucc = $oDepositCallback->save()) {
            pr($oDepositCallback->validationErrors->toArray());
            exit;
            return false;
        }
        return $oDepositCallback;
    }

    /**
     * 设置响应的状态为成功
     * @return boolean
     */
    public function setSuccessful() {
        $this->status = self::STATUS_SUCCESS;
        return $this->save();
    }

    /**
     * 设置响应的状态为失败
     * @param string $sMsg 附带失败信息
     * @return boolean
     */
    public function setFailed($sMsg = '') {
        $this->error_msg = $sMsg;
        $this->status = self::STATUS_FAILURE;
        return $this->save();
    }
    
    public function setIpError(){
        $this->status = self::STATUS_IP_ERROR;
        return $this->save();
    }
    
    public function setDataError(){
        $this->status = self::STATUS_DATA_ERROR;
        return $this->save();
    }

    public function setMissingData(){
        $this->status = self::STATUS_NO_DATA;
        return $this->save();
    }

    public function setSignError(){
        $this->status = self::STATUS_SIGN_ERROR;
        return $this->save();
    }

    public function setMercodeError(){
        $this->status = self::STATUS_MERCODE_ERROR;
        return $this->save();
    }

    public function setPaymentError(){
        $this->status = self::STATUS_PAYMENT_FAILURE;
        return $this->save();
    }

    public function setDepositStatusError(){
        $this->status = self::STATUS_DEPOSIT_STATUS_ERROR;
        return $this->save();
    }

    public function setAmountError(){
        $this->status = self::STATUS_AMOUNT_ERROR;
        return $this->save();
    }

    public function setDepositStatusSetError(){
        $this->status = self::STATUS_SET_DEPOSIT_STATUS_ERROR;
        return $this->save();
    }

    public function setAddTaskError(){
        $this->status = self::STATUS_ADD_TASK_ERROR;
        return $this->save();
    }
    
    protected function getStatusFormattedAttribute() {
        return __('_depositcallback.' . strtolower(Str::slug(static::$validStatus[$this->attributes['status']])));
    }

}
