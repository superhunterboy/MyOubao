<?php
class DepositNotify extends BaseModel {
    protected $table = 'deposit_notifies';

    const STATUS_RECEIVED = 0;
    const STATUS_MATCHED = 1;
    const STATUS_DEPOSIT_UPDATED = 2;

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 自动维护新增/编辑时间
    protected $fillable = [
        'deposit_id',
        'platform_id',
        'platform',
        'merchant_code',
        'order_no',
        'deposit_amount',
        'notify_amount',
        'notify_data',
        'service_order_no',
        'service_time',
        'service_bank_seq_no',
        'sign',
        'status',
        'pay_time',
        'notify_time',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'DepositNotify';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'deposit_id',
        'order_no',
        'deposit_amount',
        'platform',
        'merchant_code',
        'notify_amount',
        'service_order_no',
        'service_time',
        'service_bank_seq_no',
        'sign',
        'pay_time',
        'notify_time',
        'status',
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
        'deposit_id',
        'platform_id',
        'platform',
        'merchant_code',
        'order_no' => 'required|between:1,64',
        'deposit_amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'notify_amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'notify_data',
        'service_order_no' => 'required|between:1,64',
        'service_time',
        'service_bank_seq_no',
        'sign' => 'required|max:128',
        'status' => 'integer',
        'pay_time' => 'required|datetime',
        'notify_time' => 'required|datetime',
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = []; 

    /**
     * 平台响应状态：失败
     */
    const RESPONSE_STATUS_FAIL = 0;

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
        $oDepositCallback = new DepositCallback($aInitData);
        if (!$bSucc = $oDepositCallback->save()) {
//            pr($oDepositCallback->validationErrors->toArray());
//            exit;
            return false;
        }
        return $oDepositCallback;
    }

    /**
     * 设置响应的状态为成功
     * @return boolean
     */
    public function setResponseSuccessful() {
        $this->status = self::RESPONSE_STATUS_SUCCESS;
        return $this->save();
    }

    /**
     * 设置响应的状态为失败
     * @param type $sMsg 附带失败信息
     * @return boolean
     */
    public function setResponseFailed($sMsg = '') {
        $this->error_msg = $sMsg;
        $this->status = self::RESPONSE_STATUS_FAIL;
        return $this->save();
    }


}
