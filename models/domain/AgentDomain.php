<?php

class AgentDomain extends BaseModel {

    protected $table = 'agent_domains';


    protected $fillable = [
        'id',
        'domain',
        'status',
        'group_id',
        'start_at',
        'end_at',
    ];
    public static $resourceName = 'AgentDomain';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'domain',
        'status',
        'group_name',
        'start_at',
        'end_at',
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
        'group_id' => 'required|integer',
        'status' => 'required|in:0,1',
        'start_at'=>'required|date',
        'end_at'=>'required|date',
    ];

    const IN_USE = 1;
    const NOT_IN_USE = 0;

    public static $aDomainStatus = [
        self::NOT_IN_USE => 'not-in-use',
        self::IN_USE     => 'in-use',
        // '2' => 'deleted',
    ];

    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'group_name'=> 'friendly_group_name',
    ];

    public static $htmlSelectColumns = [
        'status' => 'aDomainStatus',
        'group_id'=> 'aDomainGroup',
    ];

    public static $viewColumnMaps = [

        'status' => 'formatted_status',
    ];


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
    public static function getRandomDomainByGroupInPool($group_id) {
        $nowDate = date('Y-m-d H:i:s');
        $data = self::where('group_id',$group_id)
                    ->where('start_at','<=',$nowDate)
                    ->where('end_at', '>=', $nowDate)
                    ->where('status',self::IN_USE)
                    ->get(array('domain'));
        $data = $data->toArray();
        $iCount = count($data);
        $sDomain = '';
        if ($iCount) {
            $iIndex = $iCount > 1 ? mt_rand(0, $iCount - 1) : 0;
            $sDomain = $data[$iIndex]['domain'];
        }
        return $sDomain;
    }
    public function getFriendlyGroupNameAttribute(){
        $oGroup = AgentDomainGroup::find($this->group_id);
        if($oGroup){
            return $oGroup->group_name;
        }else{
            return '';
        }
    }


}
