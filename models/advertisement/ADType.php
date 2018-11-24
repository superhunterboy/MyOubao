<?php

class AdType extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ad_types';
    public static $resourceName = 'AdType';
    public static $titleColumn = 'type_name';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'type_name',
        'created_at',
        'updated_at',
    ];
    public static $columnForList = [
        'id',
        'type_name',
        'created_at',
        'updated_at',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    // public static $htmlSelectColumns = [
    //     'type_id' => 'aAdminUsers',
    // ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];
    public static $rules = [
        // 'name'       => 'required|max:50',
        // 'type_id'    => 'max:50',
        // 'description'=> 'required|max:50',
        // 'text_length'=> 'max:50',
        // 'pic_width'  => 'max:50',
        // 'pic_height' => 'max:50',
        // 'is_closed'  => 'max:50',
        // 'roll_time'  => 'max:50',
        'type_name'  => 'required|max:50',
    ];

    public static function getAllAdTypeArray()
    {
        $data = [];
        $aAdType = AdType::all(['id', 'type_name']);

        foreach ($aAdType as $key => $value) {
            $data[$value->id] = $value->type_name;
        }
         // pr($data);exit;
        return $data;
    }
}