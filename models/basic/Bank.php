<?php

class Bank extends BaseModel {

    const ERRNO_MISSING_DATA = -2501;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'banks';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = false; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'id',
        'name',
        'identifier',
        'identifier_sdpay',
        'mode',
        'card_type',
        'code_length',
        'url',
        'logo',
        'status',
        'min_load',
        'max_load',
        'help_url',
        'notice',
        'deposit_notice',
        'fee_switch',
        'bank_code'
    ];
    public static $resourceName = 'Bank';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'identifier',
        'identifier_sdpay',
        'card_type',
        // 'logo',
        'status',
        'min_load',
        'max_load',
        'help_url',
        'notice',
        'deposit_notice',
        'bank_code'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'mode' => 'aMode',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'name' => 'asc'
    ];
    public static $titleColumn = 'name';
    public static $aMode = [
        self::BANK_MODE_BANK_CARD => 'bank-card-mode',
        self::BANK_MODE_THIRD_PART => 'third-part',
        self::BANK_MODE_ALL => 'all-mode',
        self::DEPOSIT_MODE_SDPAY => 'sdpay',
    ];
    public static $aPayMode = [
        self::PAY_MODE_BANK => 'pay-mode-bank',
        self::PAY_MODE_BANKKJ => 'pay-mode-bankkj',
        self::PAY_MODE_YLZF => 'pay-mode-ylzf',
        self::PAY_MODE_TENPAY => 'pay-mode-tenpay',
        self::PAY_MODE_ALIPAY => 'pay-mode-alipay',
        self::PAY_MODE_WEIXIN => 'pay-mode-weixin',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'name';
    public static $rules = [
        'name' => 'required|max:50',
        'identifier' => 'max:10',
        'identifier_sdpay' => 'max:10',
        'card_type' => 'max:20',
        'code_length' => 'max:20',
        'url' => 'max:200',
        'mode' => 'in:1,2,3,4',
        // 'logo'        => 'max:100',
        'status' => 'in:0,1',
        'fee_switch' => 'in:0,1',
        'min_load' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'max_load' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'help_url' => 'max:200|url',
        'notice' => 'max:100',
        'deposit_notice' => '',
        'bank_code' => ''
    ];

    /**
     * 状态：可用
     */
    const BANK_STATUS_AVAILABLE = 1;

    /**
     * 状态：不可用
     */
    const BANK_STATUS_NOT_AVAILABLE = 0;

    /**
     * 手续费开关：关闭
     */
    const BANK_FEE_SWITCH_OFF = 0;

    /**
     * 手续费开关：开启
     */
    const BANK_FEE_SWITCH_ON = 1;

    /**
     * 模式：银行卡转账
     */
    const BANK_MODE_BANK_CARD = 1;

    /**
     * 模式：第三方
     */
    const BANK_MODE_THIRD_PART = 2;

    /**
     * 模式：兼容所有
     */
    const BANK_MODE_ALL = 3;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const DEPOSIT_MODE_SDPAY = 4;

    /**
     * 充值渠道：
     * @var int
     */
    const PAY_MODE_BANK = 1;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const PAY_MODE_BANKKJ = 2;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const PAY_MODE_YLZF = 3;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const PAY_MODE_TENPAY = 4;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const PAY_MODE_ALIPAY = 5;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const PAY_MODE_WEIXIN = 6;

    /**
     * 手续费返还百分比取值集合
     * @var array
     */
    public static $aBankFeeRateSet = [0.1, 0.15, 0.2, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5];

    // public function user()
    // {
    //     return $this->BelongsTo('User', 'user_bank_cards', 'bank_id', 'user_id')->withTimestamps();
    // }
    // public static function getAllBankNameArray()
    // {
    //     $data = [];
    //     $aUsers = Bank::all(['id', 'name']);
    //     foreach ($aUsers as $key => $value) {
    //         $data[$value->id] = $value->name;
    //     }
    //     return $data;
    // }

    /**
     * To get all of bank's information
     * @param boolean $bAvailable TRUE: available only | FALSE: all (default: TRUE)
     * @return Bank[]
     */
    public static function getAllBankInfo($bAvailable = TRUE) {
        $aData = [];
        if ($bAvailable) {
            $aData = Bank::where('status', '=', BANK::BANK_STATUS_AVAILABLE)->get();
        } else {
            $aData = Bank::all();
        }
        return $aData;
    }

    public static function getAllBankIdentifier() {
        $aData = [];
        if (Cache::has('bank_identifier')) {
            $aData = Cache::get('bank_identifier');
        } else {
            $aBanks = Bank::where('status', '=', BANK::BANK_STATUS_AVAILABLE)->get(['id', 'identifier']);

            foreach ($aBanks as $oBank) {
                $aData[$oBank->id] = $oBank->identifier;
            }

            Cache::forever('bank_identifier', $aData);
        }
        return $aData;
    }

    /**
     * 获取支持银行卡转账的银行
     * @return array
     */
    public static function getSupportCardBank() {
        $oQuery = Bank::whereIn('mode', [Bank::BANK_MODE_BANK_CARD, Bank::BANK_MODE_ALL]);
        $oQuery->where('status', '=', BANK::BANK_STATUS_AVAILABLE);
        return $oQuery->get();
    }

    /**
     * 获取银行信息，供绑卡使用
     * @return array
     */
    public static function getAllBank() {
        $oQuery = Bank::where('status', '=', BANK::BANK_STATUS_AVAILABLE);
        return $oQuery->get();
    }
    
        public static function & getAllBankArray($bAvailable = TRUE) {
        $oBanks = self::getAllBankInfo($bAvailable);
        $aBanks = [];
        foreach ($oBanks as $oBank) {
            $aBanks[$oBank->id] = $oBank->name;
        }
        return $aBanks;
    }

    /**
     * 获取支持第三方充值的银行
     * @return array
     */
    public static function getSupportThirdPartBank() {
        $oQuery = Bank::whereIn('mode', [Bank::BANK_MODE_THIRD_PART, Bank::BANK_MODE_ALL]);
        $oQuery->where('status', '=', BANK::BANK_STATUS_AVAILABLE)->whereNotIn('mc_bank_id', [30, 40, 51]);
        return $oQuery->get();
    }

    /**
     * 获取支持sdpay的银行卡
     * 
     */
    public static function getSupportSdpayBank() {
        $oQuery = Bank::where('mode', Bank::BANK_MODE_THIRD_PART);
        $oQuery->where('status', '=', BANK::BANK_STATUS_AVAILABLE)->where('mc_bank_id', '=', 51);
        return $oQuery->first();
    }

    /**
     * 获取支持财付通的银行卡
     *
     */
    public static function getSupportCaifuTongBank() {
        $oQuery = Bank::where('mode', Bank::BANK_MODE_BANK_CARD);
        $oQuery = $oQuery->where('identifier', '=', 'TENPAY');
        $oQuery->where('status', '=', BANK::BANK_STATUS_AVAILABLE);
        return $oQuery->first();
    }

    /**
     * 获取支付宝配置信息
     *
     */
    public static function getSupportAlipay() {
        $oQuery = Bank::where('mode', Bank::BANK_MODE_THIRD_PART);
        $oQuery = $oQuery->where('identifier', '=', 'ALIPAY');
        $oQuery->where('status', '=', BANK::BANK_STATUS_AVAILABLE);
        return $oQuery->first();
    }

    /**
     * 获取微信配置信息
     *
     */
    public static function getSupportWeixin() {
        $oQuery = Bank::where('mode', Bank::BANK_MODE_THIRD_PART);
        $oQuery = $oQuery->where('identifier', '=', 'WEIXIN');
        $oQuery->where('status', '=', BANK::BANK_STATUS_AVAILABLE);
        return $oQuery->first();
    }

    /**
     * 根据Mownecum银行编号获取平台银行对象
     * @param type $iMcBankId MC银行编号
     * @return Bank
     */
    public static function findBankByMcBankId($iMcBankId) {
        return Bank::where('mc_bank_id', '=', $iMcBankId)->first();
    }

    /**
     * 生成手续费表达式「公式」（需要以下格式数组作为条件）：
     * <pre>array(
     *  ['x'=>['>='=>100, '<'=>'200'], 'y'=>['='=>5]],
     *  ['x'=>['>='=>200, '<'=>'500'], 'y'=>['%'=>5]],
     * )</pre>
     * @param array $aConditions 条件数据
     * @return string 公式（示例：x>=100&&x<200&&y=5;x>=200&&y=x*5/100）
     */
    public function setFeeExpressions($aConditions = []) {
        $aResult = [];
        if (empty($aConditions)) {
            $this->fee_valve = 0;
            $this->fee_expressions = '';
            return $this->fee_expressions;
        }
        if (!is_array($aConditions)) {
            return false;
        }
        foreach ($aConditions as $ct) {
            if (!empty($ct['x']) && !empty($ct['y'])) {
                $aTemp = [];
                foreach ($ct['x'] as $k => $v) {
                    $v = floatval($v);
                    switch ($k) {
                        case '>':
                            $aTemp[] = 'x>' . $v;
                            break;
                        case '>=':
                            $aTemp[] = 'x>=' . $v;
                            break;
                        case '<':
                            $aTemp[] = 'x<' . $v;
                            break;
                        case '<=':
                            $aTemp[] = 'x<=' . $v;
                            break;
                        default :
                            break;
                    }
                    if ($this->fee_valve == 0 || $this->fee_valve > $v) {
                        $this->fee_valve = $v;
                    }
                }
                foreach ($ct['y'] as $k => $v) {
                    switch ($k) {
                        case '=':
                            $aTemp[] = 'y=' . floatval($v);
                            break;
                        case '%':
                            $aTemp[] = 'y=x*' . floatval($v) . '/100';
                            break;
                        default :
                            break;
                    }
                }
                $aResult[] = implode('&&', $aTemp);
            }
        }
        $this->fee_expressions = implode(';', $aResult);
        return $this->fee_expressions;
    }

    /**
     * 解析手续费表达式为数组形式，返回数据格式如下：
     * <pre>array(
     *  ['x'=>['>='=>100, '<'=>'200'], 'y'=>['='=>5]],
     *  ['x'=>['>='=>200, '<'=>'500'], 'y'=>['%'=>5]],
     * )</pre>
     * @return array
     */
    public function getFeeExpressionsArray() {
        $aResult = [];
        if (empty($this->fee_expressions)) {
            return $aResult;
        }
        // x>=100&&x<200&&y=5;x>=200&&y=x*5/100
        $aConditions = explode(';', $this->fee_expressions);
        foreach ($aConditions as $ct) {
            $aTemp = [];
            preg_match_all('/x([><]=?)(\d+(?:\.\d+)?)/', $ct, $matches);
            foreach ($matches[1] as $k => $v) {
                $aTemp['x'][$v] = $matches[2][$k];
            }
            preg_match_all('/y=(x\*)?(\d+(?:\.\d+)?)(\/100)?/', $ct, $matches);
            if (!empty($matches[1][0]) && !empty($matches[3][0])) {
                $aTemp['y']['%'] = $matches[2][0];
            } else {
                $aTemp['y']['='] = $matches[2][0];
            }
            $aResult[] = $aTemp;
        }
        return $aResult;
    }

    /**
     * 根据公式计算手续费
     * @param float $fAmount 充值金额
     * @return float
     */
    public function calculateBankFee($fAmount) {
        $fAmount = floatval($fAmount);
        $fBankFee = 0;
        if (empty($this->fee_expressions)) {
            return $fBankFee;
        }
        // x>=100&&x<200&&y=5;x>=200&&y=x*5/100
        $sFeeExpressions = str_replace('x', '$fAmount', $this->fee_expressions);
        $sFeeExpressions = str_replace('y', '$fBankFee', $sFeeExpressions);
        eval($sFeeExpressions . ';');
        return number_format($fBankFee, 2, '.', ''); // 保留两位小数，四舍五入
    }

    /**
     * 根据公式计算sdpay手续费
     * @param float $fAmount 充值金额
     * @return float
     */
    public static function calculateSdpayBankFee($fAmount) {
        $fAmount = floatval($fAmount);
        $deposit_fee = SysConfig::readValue('deposit_fee');
        $fBankFee = $fAmount / 100 * $deposit_fee;
        return number_format($fBankFee, 2, '.', ''); // 保留两位小数，四舍五入
    }

    /**
     * 根据bank_id获取identifier
     * @param type $bank_id
     */
    public static function getBankIdentifier($bank_id) {
        return self::where('id', $bank_id)->first();
    }

    /**
     * 获取paymode
     * @param type $identifier
     * @param type $deposit_mode
     * @return boolean
     */
    public static function getPayMode($identifier, $deposit_mode) {
        if (empty($identifier) || empty($deposit_mode)) {
            return false;
        }
        if ($identifier) {
            switch ($identifier) {
                case 'TENPAY':
                    $iPayMode = self::PAY_MODE_TENPAY;      //财付通支付
                    break;
                case 'YLZF':
                    $iPayMode = self::PAY_MODE_YLZF;      //银联快捷支付
                    break;
                case 'WEIXIN':
                    $iPayMode = self::PAY_MODE_WEIXIN;      //微信支付
                    break;
                case 'ALIPAY':
                    $iPayMode = self::PAY_MODE_ALIPAY;      //微信支付
                    break;
            }
            if (empty($iPayMode)) {
                if (1 == $deposit_mode) {
                    $iPayMode = self::PAY_MODE_BANK;     //银行卡转账
                } else {
                    $iPayMode = self::PAY_MODE_BANKKJ;     //网银快捷支付
                }
            }
            return $iPayMode;
        }
        return false;
    }

}
