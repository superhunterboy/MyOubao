<?php

/**
 * 后台管理操作日志
 *
 * @author frank
 */
class FunctionalityLog extends BaseModel {

    protected $table = 'functionality_logs';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'FunctionalityLog';

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;
    public static $sequencable = false;
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'title',
        'parent',
        'functionality_id',
        'controller',
        'action',
        'description',
        'params',
        'new_window',
        'disabled',
        'sequence',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'parent_id' => 'aMenuTree',
        'functionality_id' => 'aFunctionalities',
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;

    protected $fillable = [
        'title',
        'parent_id',
        'parent',
        'functionality_id',
        'controller',
        'action',
        'description',
        'params',
        'new_window',
        'disabled',
        'sequence',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'title'     => 'between:1,30',
        'params'  => 'between:1,100',
        'disabled'  => 'in:0,1',
        'new_window'   => 'in:0,1',
        'sequence'  => 'numeric'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];


}
