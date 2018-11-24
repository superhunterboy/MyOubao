<?php

/**
 * 异常充值模型
 */
class ExceptionDeposit extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exception_deposits';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    public static $resourceName = 'ExceptionDeposit';

    /**
     * API: 接收异常
     * @var int
     */
    const EXCEPTION_API_RECEIVED = 1;

    /**
     * 状态：未处理（新订单）
     * @var int
     */
    const EXCEPTION_STATUS_NEW = 0;

    /**
     * 状态：申请成功
     * @var int
     */
    const EXCEPTION_STATUS_RECEIVED = 1;

    /**
     * 状态：申请失败
     * @var int
     */
    const EXCEPTION_STATUS_REFUSED = 2;

    /**
     * 状态：挂起（申请Mownecum退款时，未收到响应）
     * @var int
     */
    const EXCEPTION_STATUS_PENDING = 3;

    /**
     * 状态：退款成功
     * @var int
     */
    const EXCEPTION_STATUS_SUCCESS = 4;

    /**
     * 状态：退款失败
     * @var int
     */
    const EXCEPTION_STATUS_FAIL = 5;

    /**
     * 状态：已加币（手工给用户添加游戏币）
     * @var int
     */
    const EXCEPTION_STATUS_ADD_COIN = 6;

    /**
     * 状态：已没收
     * @var int
     */
    const EXCEPTION_STATUS_IGNORED = 7;

    /**
     * 状态：已提交（已经提交申请，防止重复提交）
     */
    const EXCEPTION_STATUS_ALREADY_APPLY = 8;

    /**
     * API响应状态：失败
     */
    const RESPONSE_STATUS_FAIL = 0;

    /**
     * API响应状态：成功
     */
    const RESPONSE_STATUS_SUCCESS = 1;

    /**
     * 退款：操作步骤一
     */
    const EXCEPTION_REFUND_STEP_ONE = 1;

    /**
     * 退款：操作步骤二
     */
    const EXCEPTION_REFUND_STEP_TWO = 2;

    /**
     * 退款方式：向mownecum发起
     */
    const EXCEPTION_REFUND_TYPE_MOWNECUM = 'mownecum';

    /**
     * 退款方式：线下支付
     */
    const EXCEPTION_REFUND_TYPE_OFFLINE = 'offline';

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';

    /**
     * 状态翻译
     * @var type
     */
    public static $validStatuses = [
        self::EXCEPTION_STATUS_NEW              => 'New',
        self::EXCEPTION_STATUS_RECEIVED         => 'Apply-Received',
        self::EXCEPTION_STATUS_REFUSED          => 'Apply-Refused',
        self::EXCEPTION_STATUS_PENDING          => 'Pending',
        self::EXCEPTION_STATUS_SUCCESS          => 'Success',
        self::EXCEPTION_STATUS_FAIL             => 'Failture',
        self::EXCEPTION_STATUS_ADD_COIN         => 'Add-Coin',
        self::EXCEPTION_STATUS_IGNORED          => 'Ignored',
        self::EXCEPTION_STATUS_ALREADY_APPLY    => 'exception-already-apply',
    ];
    public static $listColumnMaps = [
        'status' => 'formatted_status',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];
    protected $fillable = [
        'exception_order_num',
        'company_id',
        'exact_payment_bank',
        'pay_card_name',
        'pay_card_num',
        'receiving_bank',
        'receiving_account_name',
        'channel',
        'note',
        'area',
        'exact_time',
        'amount',
        'fee',
        'transaction_charge',
        'company_order_num',
        'mownecum_order_num',
        'status',
        'remark',
        'administrator',
        'admin_user_id',
        'process_at',
    ];
    public static $rules = [
        'exception_order_num' => 'required|between:1,64',
        'company_id' => 'required|integer',
        'exact_payment_bank' => 'between:1,16',
        'pay_card_name' => 'between:1,32',
        'pay_card_num' => 'between:1,32',
        'issue_bank_id' => 'integer',
        'issue_bank_address' => 'between:0,255',
        'receiving_bank' => 'required|integer',
        'receiving_account_name' => 'required|between:1,32',
        'channel' => 'between:1,16',
        'note' => 'between:1,32',
        'area' => 'between:1,32',
        'exact_time' => 'required|date',
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'fee' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'transaction_charge' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'company_order_num' => 'between:1,64',
        'mownecum_order_num' => 'between:1,64',
        'status' => 'in:0,1,2,3,4,5,6,7',
        'admin_user_id' => 'integer',
        'remark' => '',

    ];

    public static $totalColumns = [
        'amount',
        'fee'
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'exception_order_num',
        'company_order_num',
        'mownecum_order_num',
        'amount',
        'fee',
        'channel',
        'exact_payment_bank',
        'pay_card_name',
        'pay_card_num',
        'note',
        'status',
        'exact_time',
        'updated_at',
        'administrator',
        'process_at',
    ];
    public static $htmlNumberColumns = [
        'amount' => 2,
        'fee' => 2,
    ];
    public static $viewColumnMaps = [
        'amount' => 'amount_formatted',
        'fee' => 'fee_formatted',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'validStatuses', // see self::$validStatuses
        'issue_bank_id' => 'aBanks',
        'receiving_bank' => 'aBanks',
        'exact_payment_bank' => 'aBanks',
    ];
    public static $noOrderByColumns = [];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];

    protected function beforeValidate() {
        return parent::beforeValidate();
    }

    /**
     * _updateStatus 更新记录状态
     * @param  Int $iToStatus   将要改变的状态值
     * @param  Array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    private function _updateStatus($iToStatus, array $aExtraData = []) {
        // 以下是状态流
        // 0 => 4,6,7,8
        // 8 => 1,2,3
        // 1 => 4,5
        if (!$this->exists) {
            return FALSE;
        }
        if (!empty($aExtraData) && is_array($aExtraData)) {
            $this->fill($aExtraData);
        }
        $aExtraData['status'] = $iToStatus;
        $iAffectRows = self::where('id', '=', $this->id)->where('status', '=', $this->status)->update($aExtraData);
        $iAffectRows == 1 && $this->status = $iToStatus;
//        pr($this->validationErrors);
        return $iAffectRows == 1;
    }

    /**
     * 设置状态：订单申请成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setReceived(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_ALREADY_APPLY && $this->_updateStatus(self::EXCEPTION_STATUS_RECEIVED, $aExtraData);
    }

    /**
     * 设置状态：订单申请失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setRefused(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_ALREADY_APPLY && $this->_updateStatus(self::EXCEPTION_STATUS_REFUSED, $aExtraData);
    }

    /**
     * 设置状态：挂起申请订单
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setPending(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_ALREADY_APPLY && $this->_updateStatus(self::EXCEPTION_STATUS_PENDING, $aExtraData);
    }

    /**
     * 设置状态：订单完成，退款成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setSuccess(array $aExtraData = []) {
        if ($this->status == self::EXCEPTION_STATUS_NEW || $this->status == self::EXCEPTION_STATUS_RECEIVED) {
            return $this->_updateStatus(self::EXCEPTION_STATUS_SUCCESS, $aExtraData);
        }
        return false;
    }

    /**
     * 设置状态：退款失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setFail(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_RECEIVED && $this->_updateStatus(self::EXCEPTION_STATUS_FAIL, $aExtraData);
    }

    /**
     * 设置状态：己手工给用户添加游戏币
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setAddCoin(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_PENDING && $this->_updateStatus(self::EXCEPTION_STATUS_ADD_COIN, $aExtraData);
    }

    /**
     * 设置状态：没收异常充值订单
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setIgnored(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_PENDING && $this->_updateStatus(self::EXCEPTION_STATUS_IGNORED, $aExtraData);
    }

    /**
     * 设置状态：处理异常充值订单
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setProcess(array $aExtraData = []) {
        $this->_updateStatus(self::EXCEPTION_STATUS_NEW);
        return $this->status == self::EXCEPTION_STATUS_NEW && $this->_updateStatus(self::EXCEPTION_STATUS_PENDING, $aExtraData);
    }

    /**
     * 设置状态：未处理异常充值订单
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setExceptionStatusNew(array $aExtraData = []) {
        return $this->_updateStatus(self::EXCEPTION_STATUS_NEW, $aExtraData);
    }

    /**
     * 设置状态：设计订单状态为已提交（防重复提交）
     * @param array $aExtraData
     * @return type
     */
    public function setAlreadyApply(array $aExtraData = []) {
        return $this->status == self::EXCEPTION_STATUS_PENDING && $this->_updateStatus(self::EXCEPTION_STATUS_ALREADY_APPLY, $aExtraData);
    }


    /**
     * 生成通讯加密串
     * @param array $aPostData 通信数据包
     * @param int   $iType 通信类型
     * @return string|FALSE
     */
    public static function getApiKey($aPostData, $iType) {
        if (empty($aPostData) || !is_array($aPostData)) {
            return FALSE;
        }
        $aKeyRule = []; // 加密串拼接顺序
        switch ($iType) {
            case self::EXCEPTION_API_RECEIVED: // 接收到异常推送
                $aKeyRule = [
                    'exception_order_num', 'company_id', 'exact_payment_bank', 'pay_card_name', 'pay_card_num', 'receiving_bank',
                    'receiving_account_name', 'channel', 'note', 'area', 'exact_time', 'amount', 'fee', 'transaction_charge',
                ];
                break;
            default :
                return FALSE;
        }
        $sDataStr = '';
        foreach ($aKeyRule as $v) {
            $sDataStr .= array_get($aPostData, $v, '');
        }
        $oSysConfig = new SysConfig;
        $sKey = $oSysConfig->readValue('mc_company_key');
        return md5(md5($sKey) . $sDataStr);
    }
    public static function getApiKeyForVip($aPostData, $iType) {
        if (empty($aPostData) || !is_array($aPostData)) {
            return FALSE;
        }
        $aKeyRule = []; // 加密串拼接顺序
        switch ($iType) {
            case self::EXCEPTION_API_RECEIVED: // 接收到异常推送
                $aKeyRule = [
                    'exception_order_num', 'company_id', 'exact_payment_bank', 'pay_card_name', 'pay_card_num', 'receiving_bank',
                    'receiving_account_name', 'channel', 'note', 'area', 'exact_time', 'amount', 'fee', 'transaction_charge',
                ];
                break;
            default :
                return FALSE;
        }
        $sDataStr = '';
        foreach ($aKeyRule as $v) {
            $sDataStr .= array_get($aPostData, $v, '');
        }
        $oSysConfig = new SysConfig;
        $sKey = $oSysConfig->readValue('mc_company_key_vip');
        return md5(md5($sKey) . $sDataStr);
    }

    protected function getFormattedStatusAttribute() {
        return __('_exceptiondeposit.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    protected function getAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('amount');
    }

    protected function getFeetFormattedAttribute() {
        return $this->getFormattedNumberForHtml('fee');
    }

}
