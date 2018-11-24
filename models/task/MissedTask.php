<?php

class MissedTask extends BaseModel {
    protected $table = 'missed_tasks';

    const STATUS_WAITING   = 0;
    const STATUS_RESTORING = 1;
    const STATUS_RESTORED  = 2;

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete       = false;
    public $timestamps          = true; // 取消自动维护新增/编辑时间
    protected $fillable         = [
        'queue',
        'command',
        'data',
        'status',
        'restored_at',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'MissedTask';

    public static $columnForList     = [
        'queue',
        'command',
//        'data',
        'status',
        'created_at',
        'restored_at',
    ];
    public static $listColumnMaps = [];
    public static $viewColumnMaps = [];
    public static $htmlSelectColumns = [];
    public static $noOrderByColumns  = [];
    public static $validStatuses = [
        self::STATUS_WAITING   => 'Waiting',
        self::STATUS_RESTORING => 'Restoring',
        self::STATUS_RESTORED  => 'Restored',
    ];

    public $orderColumns = [
        'id' => 'desc',
    ];

    public static $mainParamColumn     = 'command';
    public static $titleColumn         = 'command';
    public static $rules               = [
        'queue'       => 'required|max:32',
        'command'     => 'required|max:64',
        'data'        => 'required',
        'status'      => 'in:0,1,2',
        'restored_at' => 'date_format:Y-m-d H:i:s',
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns      = [];
    // 表单只读字段
    public static $aReadonlyInputs     = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = [];

    public static function addMissedTask($sCommand, & $aData, $sQueue) {
        $data = [
            'command' => $sCommand,
            'queue'   => $sQueue,
            'data'    => json_encode($aData),
            'status'  => self::STATUS_WAITING
        ];
        $obj  = new MissedTask($data);
        return $obj->save();
    }

}
