<?php

class SysModel extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sys_models';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'name',
        'table_name',
    ];

    public static $resourceName = 'Sys Model';
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'table_name',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'name' => 'asc'
    ];

    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'name';
    public static $titleColumn = 'name';

    public static $rules = [
        'name'   => 'required|max:64',
        'table_name'   => 'required|max:64',
    ];
    
}