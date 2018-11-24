<?php

class Domain extends BaseModel {

    protected $table = 'domains';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'domain',
        'status',
        'type',
    ];
    public static $resourceName = 'Domain';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'domain',
        'status',
        'type',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'id';
    public static $rules = [
        'domain' => 'required|max:60|unique:domains,domain,',
        'status' => 'in:0,1',
        'type'   => 'between:0, 10',
    ];

    const TYPE_USER = 2;
    const TYPE_AGENT = 1;
    const TYPE_TOP_AGENT = 0;

    const IN_USE = 1;
    const NOT_IN_USE = 0;

    public static $aDomainTypes = [
        self::TYPE_USER      => 'user',
        self::TYPE_AGENT     => 'agent',
        self::TYPE_TOP_AGENT => 'top-agent',
    ];

    public static $aDomainStatus = [
        self::NOT_IN_USE => 'not-in-use',
        self::IN_USE     => 'in-use',
        // '2' => 'deleted',
    ];

    public static $listColumnMaps = [
        'type'   => 'formatted_type',
        'status' => 'formatted_status',
    ];

    public static $htmlSelectColumns = [
        'type'   => 'aDomainTypes',
        'status' => 'aDomainStatus',
    ];

    public static $viewColumnMaps = [
        'type'   => 'formatted_type',
        'status' => 'formatted_status',
    ];

    protected function getFormattedTypeAttribute() {
        $aTypes = explode(',', $this->type);
        $aTypeDesc = [];
        // pr($aTypes);exit;
        foreach($aTypes as $key => $value) {
            // pr(static::$aDomainTypes[$value]);exit;
            $aTypeDesc[$key] = __('_domain.' . static::$aDomainTypes[$value]);
        }
        return implode(',', $aTypeDesc);
    }
    protected function getFormattedStatusAttribute() {
        return __('_domain.' . static::$aDomainStatus[$this->status]);
    }

    protected function beforeValidate() {
        if ($this->id) {
            self::$rules['domain'] = 'required|max:60|unique:domains,domain,' . $this->id;
        }
        if (is_array($this->type)) {
            $this->type = implode(',', $this->type);
        }
        // pr($this->type);exit;
        // pr(self::$rules);exit;
        // pr($this->toArray());exit;
        return parent::beforeValidate();
    }

    public static function getDomainsByType($iType = 0, $aColumns = ['*']) {
        return self::where('status', '=', self::IN_USE)->whereRaw('find_in_set(?, type)',[$iType])->get($aColumns);
    }
    /**
     * [getRandomDomainInPool 根据域名类型获取可用域名]
     * @param  integer $iType [域名类型]
     * @return [String]       [随机域名]
     */
    public static function getRandomDomainInPool($iType = 0) {
        $data = self::getDomainsByType($iType, ['domain']);
        $data = $data->toArray();
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        $iCount = count($data);
        $sDomain = '';
        if ($iCount) {
            $iIndex = $iCount > 1 ? rand(0, $iCount - 1) : 0;
            $sDomain = $data[$iIndex]['domain'];
        }
        return $sDomain;
    }

}
