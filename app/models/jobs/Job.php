<?php

class Job extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jobs';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'Job';

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable    = false;
    public static $sequencable = false;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'job_id',
        'command',
        'data_formatted',
        'times',
        'done_at',
        'status_formatted',
        'created_at',
        'updated_at',
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
        'job_id',
        'command',
        'data',
        'status',
        'times',
        'done_at',
        'created_at',
        'updated_at',
    ];

    public static $rules = [
        'job_id'  => 'required|integer|min:1',
        'command' => 'required|max:64',
        'data'    => 'required|max:10240',
        'times'   => 'required|integer|min:0',
        'done_at' => 'date_format:Y-m-d H:i:s'
    ];

    protected function getDataFormattedAttribute() {
        $object = json_decode($this->attributes['data']);
        return objectToArray($object);
    }

    protected function getStatusFormattedAttribute() {
        return __('_job.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }
}
