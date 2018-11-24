<?php

class IssueWarning extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'issue_warnings';

    const ISSUE_ADVANCED_SALE_CLOSE_TIME = '102001';
    const ISSUE__ADVANCED_DRAW_TIME = '104001';
    const ISSUE_CANCELLED = '105001';
    const ISSUE_REVISE_CODE = '103001';
    const STATUS_RESOLVED = 1;
    const STATUS_NOT_RESOLVED = 0;

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'IssueWarning';
    public static $treeable = false;
    public static $sequencable = false;
    public static $listColumnMaps = [
        'status' => 'status_formatted',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
//        'codecenter_id',
        'lottery_id',
        'issue',
        'number',
        'warning_type',
        'err_code',
        'err_msg',
        'earliest_draw_time',
        'record_id',
        'correct_time',
        'status',
//        'created_at',
//        'updated_at',
    ];
    public static $aStatus = [
        self::STATUS_RESOLVED => 'resolved',
        self::STATUS_NOT_RESOLVED => 'not-resolved',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
        'status' => 'aStatus',
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'codecenter_id',
        'lottery_id',
        'issue',
        'number',
        'err_code',
        'err_msg',
        'earliest_draw_time',
        'record_id',
        'correct_time',
        'created_at',
        'updated_at',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
//        'customer_id' => 'required|max:20',
//        'domain' => 'required|max:50',
//        'version' => 'required|integer|min:1|max:2',
//        'ip' => 'required|max:100',
//        'set_url' => 'required|url|max:200',
//        'get_url' => 'url|max:200',
//        'set_verify_url' => 'required|url|max:200',
//        'customer_key' => 'required|size:32',
//        'default' => 'required|in:0,1',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    public static function setStatusToResolved($id) {
        $aConditions = [
            'id' => ['=', $id],
            'status' => ['=', self::STATUS_NOT_RESOLVED],
        ];
        $data = [
            'status' => self::STATUS_RESOLVED,
        ];
        return IssueWarning::doWhere($aConditions)->update($data) > 0;
    }

    protected function getStatusFormattedAttribute() {
        return __('_issuewarning.' . self::$aStatus[$this->status]);
    }

}
