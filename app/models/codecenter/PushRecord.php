<?php

class PushRecord extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'push_records';

    const PUSH_SUCCESS                      = 4;
    const ERRNO_NEED_REPUSH                 = 0;
    const ERRNO_PUSH_SUCCESS                = 4;
    const ERRNO_REQUEST_NON_POST            = 8;
    const ERRNO_REQUEST_INVALID             = 16;
    const ERRNO_REQUEST_CUSTOMER_ERROR      = 32;
    const ERRNO_REQUEST_CUSTOMER_IP_ERROR   = 64;
    const ERRNO_REQUEST_CC_IP_ERROR         = 128;
    const ERRNO_REQUEST_CUSTOMER_HOST_ERROR = 256;
    const ERRNO_REQUEST_EXPIRED             = 512;
    const ERRNO_REQUEST_LOTTERY_ERROR       = 1024;
    const ERRNO_REQUEST_ISSUE_ERROR         = 2048;
    const ERRNO_SET_PROC_INVALID            = 4096;
    const ERRNO_SET_PROC_CODE_ERROR         = 8192;

    public static $validStatuses = [
        self::ERRNO_PUSH_SUCCESS                => 'Success',
        self::ERRNO_NEED_REPUSH                 => 'Repush...',
        self::ERRNO_REQUEST_NON_POST            => 'Not POST',
        self::ERRNO_REQUEST_INVALID             => 'Param Error',
        self::ERRNO_REQUEST_CUSTOMER_ERROR      => 'Key Error',
        self::ERRNO_REQUEST_CUSTOMER_IP_ERROR   => 'Customer IP Error',
        self::ERRNO_REQUEST_CC_IP_ERROR         => 'CC IP Error',
        self::ERRNO_REQUEST_CUSTOMER_HOST_ERROR => 'Customer Host Error',
        self::ERRNO_REQUEST_EXPIRED             => 'Expired',
        self::ERRNO_REQUEST_LOTTERY_ERROR       => 'Lottery Error',
        self::ERRNO_REQUEST_ISSUE_ERROR         => 'Draw # Error',
        self::ERRNO_SET_PROC_INVALID            => 'Verify Error',
        self::ERRNO_SET_PROC_CODE_ERROR         => 'Data Error',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'PushRecord';
    public static $treeable = false;
    public static $sequencable = false;
    /**
     * 视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $listColumnMaps = [
        'request_time' => 'request_time_formatted',
        'accept_time'  => 'accept_time_formatted',
        'finish_time'  => 'finish_time_formatted',
        'spent_time'   => 'spent_time_formatted',
        'status'       => 'status_formatted',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
//        'codecenter_id',
//        'lottery_id',
        'request_lottery',
        'issue',
//        'customer_key',
        'codecenter_ip',
        'codecenter_log_id',
//        'safe_str',
        'request_time',
        'accept_time',
        'finish_time',
        'spent_time',
        'code',
        'response',
        'status',
//        'request_data',
//        'verify_data',
//        'verify_result',
//        'created_at',
//        'updated_at',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'codecenter_id',
        'lottery_id',
        'request_lottery',
        'issue',
        'customer_key',
        'codecenter_ip',
        'codecenter_log_id',
        'safe_str',
        'request_time',
        'accept_time',
        'finish_time',
        'spent_time',
        'code',
        'response',
        'status',
        'request_data',
        'verify_data',
        'verify_result',
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

    protected function getRequestTimeFormattedAttribute(){
        return Carbon::createFromTimestamp($this->attributes[ 'request_time' ])->toDateTimeString();
    }

    protected function getAcceptTimeFormattedAttribute(){
        return Carbon::createFromTimestamp($this->attributes[ 'accept_time' ])->toDateTimeString();
    }

    protected function getFinishTimeFormattedAttribute(){
        return Carbon::createFromTimestamp($this->attributes[ 'finish_time' ])->toDateTimeString();
    }

    protected function getSpentTimeFormattedAttribute(){
        return number_format($this->attributes[ 'spent_time' ],3);
    }

    protected function getStatusFormattedAttribute(){
        return @__('_pushrecord.' . strtolower(Str::slug(static::$validStatuses[ $this->attributes[ 'status' ] ])));
    }
}
