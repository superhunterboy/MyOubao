<?php

class PassiveRecord extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'passive_records';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'PassiveRecord';
    public static $treeable = false;
    public static $sequencable = false;

    /**
     * the columns for list page
     * @var array
     */
    
    public static $columnForList = [
        'codecenter_id',
//        'lottery_id',
        'request_lottery',
        'issue',
//        'customer_key',
//        'codecenter_ip',
        'codecenter_log_id',
//        'safe_str',
        'request_time',
//        'accept_time',
        'finish_time',
        'spent_time',
        'code',
//        'response',
//        'status',
//        'request_data',
//        'verify_data',
//        'verify_result',
//        'created_at',
//        'updated_at',
    ];
    public static $listColumnMaps = [
        'request_time' => 'request_time_formatted',
        'accept_time'  => 'accept_time_formatted',
        'finish_time'  => 'finish_time_formatted',
        'spent_time'   => 'spent_time_formatted',
//        'status'       => 'status_formatted',
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

//    protected function getStatusFormattedAttribute(){
//        return __('_pushrecord.' . strtolower(Str::slug(static::$validStatuses[ $this->attributes[ 'status' ] ])));
//    }

}
