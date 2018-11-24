<?php

class District extends BaseModel {
    const ENABLED = 0;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'districts';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = false; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'id',
        'parent_id',
        'province_id',
        'lft',
        'rght',
        'name',
        'english_name',
        'fullname',
        'zipcode',
        'telecode',
        'ext',
        'disabled',
        'sequence',
    ];

    public static $resourceName = 'District';
    public static $treeable = true;
    public static $sequencable = true;
    public static $mainParamColumn = 'parent_id';
    public static $titleColumn = 'name';
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'parent_id',
        'province_id',
        'name',
        'english_name',
        'fullname',
        'zipcode',
        'telecode',
        'disabled',
        'sequence',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'parent_id'   => 'aProvinces',
        'province_id' => 'aProvinces',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];


    public static $rules = [
        'parent_id'     => 'integer',
        'province_id'   => 'integer',
        'name'          => 'required|between:1,50',
        'english_name'  => 'required|between:1,50',
        'fullname'  => 'between:1,255',
        'zipcode'   => 'max:6',
        'telecode'  => 'max:5',
        'ext'       => 'max:10',
        'disabled'  => 'in:0,1',
        'sequence'  => 'integer',
    ];


    public function parent()
    {
        return $this->belongsTo('District', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('District', 'parent_id');
    }

    public static function getAllProvinces($iEnabledType = null)
    {
        $oQuery = District::whereNull('parent_id');
        if (is_numeric($iEnabledType)) {
            $oQuery = $oQuery->where('disabled', '=', $iEnabledType);
        }
        $aProvinces = $oQuery->get(['id', 'province_id', 'parent_id', 'name']);
        $data = [];
        foreach ($aProvinces as $province) {
            $data[$province->id] = $province->name;
        }
        return $data;
    }
    public static function getCitiesByProvince($parent_id = null)
    {
        $aColumns = ['id', 'name'];
        $oQuery = $parent_id ? District::where('parent_id', '=', $oProvince->id) : District::whereNull('parent_id');
        $aProvinces = $oQuery->where('disabled', '=', District::ENABLED)->orderBy('sequence', 'asc')->get($aColumns);
        $provinceCities = [];
        // 格式为Json  {'id': {id: 'id', name:'name', children: [{id: 'id', name: 'name'}, ...]}, ...}
        foreach ($aProvinces as $oProvince) {
            $provinceCities[$oProvince->id] = $oProvince->getAttributes();
            $provinceCities[$oProvince->id]['children'] = [];
            $aCities = District::where('parent_id', '=', $oProvince->id)->orderBy('sequence', 'asc')->get($aColumns);
            foreach ($aCities as $oCity) {
                $provinceCities[$oProvince->id]['children'][] = $oCity->getAttributes();
            }
        }
        // 格式为Array  [{id: 'id', name:'name', children: [{id: 'id', name: 'name'}, ...]}, ...]
        // foreach ($aProvinces as $oProvince) {
        //     $item = $oProvince->getAttributes();
        //     $item['children'] = [];
        //     $aCities = District::where('parent_id', '=', $oProvince->id)->orderBy('sequence', 'asc')->get($aColumns);
        //     foreach ($aCities as $oCity) {
        //         $item['children'][] = $oCity->getAttributes();
        //     }
        //     $provinceCities[] = $item;
        // }
        unset($aProvinces, $aCities, $aColumns);
        return $provinceCities;

    }

}