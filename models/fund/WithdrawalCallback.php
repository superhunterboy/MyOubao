<?php

class WithdrawalCallback extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'withdraw_callbacks';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    /*
        id
        call_url
        post_data
        mownecum_order_num
        company_order_num
     */
    protected $fillable = [
        'call_url',
        'post_data',
        'mownecum_order_num',
        'company_order_num',
        'amount',
        'status',
    ];
    public static $resourceName = 'WithdrawCallback';

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
    
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = []; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)

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
        $oDepositCallback = new WithdrawalCallback($aInitData);
        if (!$bSucc = $oDepositCallback->save()) {
//            pr($oDepositCallback->validationErrors->toArray());
//            exit;
            return false;
        }
        return $oDepositCallback;
    }


}
