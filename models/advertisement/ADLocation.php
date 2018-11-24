<?php

class AdLocation extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ad_locations';
    public static $resourceName = 'AdLocation';
    public static $titleColumn = 'name';

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
    public static $mainParamColumn = 'type_id';
    protected $fillable = [
        'id',
        'name',
        'type_id',
        'description',
        'text_length',
        'pic_width',
        'pic_height',
        'is_closed',
        'roll_time',
        'created_at',
        'updated_at',
    ];
    public static $columnForList = [
        'id',
        'name',
        'type_id',
        'description',
        'text_length',
        'pic_width',
        'pic_height',
        'is_closed',
        'roll_time',
        'created_at',
        'updated_at',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'type_id' => 'aTypes',
    ];
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $rules = [
        'name'       => 'required|max:50',
        'type_id'    => 'required|max:10',
        'description'=> 'required|max:100',
        'text_length'=> 'max:3',
        'pic_width'  => 'max:4',
        'pic_height' => 'max:3',
        'is_closed'  => 'in:0,1',
        'roll_time'  => 'max:3',
    ];

    protected function beforeValidate(){

        $this->text_length or $this->text_length = 0;
        $this->pic_width or $this->pic_width = 0;
        $this->pic_height or $this->pic_height = 0;
        $this->roll_time or $this->roll_time =0;
        if (isset($this->type_id) && $this->type_id) {
            $oType = AdType::find($this->type_id);
            $this->type_name = $oType->type_name;
        }
        //pr($this);exit;
        return parent::beforeValidate();
    }

    public static function getAllAdLocationArray()
    {
        $data = [];
        $aAdlocation = AdLocation::all(['id', 'name']);
        foreach ($aAdlocation as $key => $value) {
            $data[$value->id] = $value->name;
        }
         // pr($data);exit;
        return $data;
    }

}