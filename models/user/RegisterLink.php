<?php

# 链接开户管理

class RegisterLink extends BaseModel {

    protected $table = 'register_links';
    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'username',
        'valid_days',
        'is_admin',
        'is_agent',
        'is_tester',
        'keyword',
        'note',
        'channel',
        'agent_qqs',
        'prize_group_sets',
        'commission_sets',
        'created_count',
        'url',
        'status',
        'expired_at',
    ];
    public static $resourceName = 'RegisterLink';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'channel',
        'username',
        'created_count',
        'is_agent',
        'is_tester',
        'status',
        'created_at',
        'expired_at',
    ];

    /**
     * [$aNoNeedSortColumns 部分展现的字段是格式化后的值，不能用它们做排序]
     * @var [type]
     */
    public static $aNoNeedSortColumns = ['status_formatted'];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aStatuses',
            // 'is_agent' => 'aIsAgents',
    ];

    /**
     * 编辑框字段配置
     * @var array
     */
    public static $htmlTextAreaColumns = [
        'commission_sets',
    ];

    const STATUS_IN_USE = 0;
    const STATUS_CLOSED = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_VALID_FOREVER = 3;

    public static $aStatuses = [
        self::STATUS_IN_USE => 'In use',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_EXPIRED => 'expired',
        self::STATUS_VALID_FOREVER => 'valid forever',
    ];
    // public static $aIsAgents = ['No', 'Yes'];
    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'is_agent' => 'formatted_is_agent',
    ];
    // TODO 玩家，代理，总代的默认奖金组，应该由配置而来
    // 0: player, 1: agent, 2: top agent
    public static $aDefaultPrizeGroups = [
        ['prize_group' => '1950', 'classic_prize' => '1950'],
        ['prize_group' => '1950', 'classic_prize' => '1950'],
        ['prize_group' => '1955', 'classic_prize' => '1955'],
    ];
    public static $aDefaultMaxPrizeGroups = [
        ['prize_group' => '1955', 'classic_prize' => '1955'],
        ['prize_group' => '1955', 'classic_prize' => '1955'],
        ['prize_group' => '1960', 'classic_prize' => '1960'],
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'updated_at' => 'desc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required|between:4,32',
        'valid_days' => 'integer',
        'is_admin' => 'in:0,1',
        'is_agent' => 'in:0,1',
        'is_tester' => 'in:0,1',
        'keyword' => 'required',
        'note' => 'max:100',
        'channel' => 'max:50',
        'agent_qqs' => 'max:50',
        'created_count' => 'integer',
        'url' => 'required|max:255',
        'status' => 'in:0,1',
    ];

    public function users() {
        return $this->belongsToMany('User', 'register_link_users', 'register_link_id', 'user_id')->withTimeStamps();
    }

    protected function getFriendlyCreatedAtAttribute() {
        return friendly_date($this->created_at);
    }

    protected function getFriendlyExpiredAtAttribute() {

        if($this->status == RegisterLink::STATUS_CLOSED){
            return '关闭';
        }
        $iStatus = UserRegisterLink::getRegisterLinkByPrizeKeyword($this->keyword);

        if(is_null($this->expired_at) && $iStatus){
            return '永久有效';
        }
        if($iStatus){
            return '正常';
        }
        return '过期';
            //return friendly_date($this->expired_at);
    }

    protected function getLinkPrizeGroupAttribute() {
        $aPrizeGroupSets = json_decode(($this->prize_group_sets));
        return $aPrizeGroupSets[0]->prize_group;
    }

    protected function getValidDatesAttribute() {

    }


    /**
     * [getPrizeGroupSetsJsonAttribute 根据链接开户记录中的奖金组配置值，生成奖金组和返点率数据]
     * @return [Array] [奖金组和返点率数据]
     */
    protected function getPrizeGroupSetsJsonAttribute() {
        $aPrizeGroupSets = json_decode(($this->prize_group_sets));
        $aPrizeGroupWaters = PrizeGroup::getPrizeGroupWaterMap();
        // pr((($aPrizeGroupSets)));exit;
        $aPrizeGroups = [];
/*        $aPres = ['lottery_id', 'series_id'];
        $sPre = $aPres[$this->is_agent];*/
        foreach ($aPrizeGroupSets as $key => $value) {
            // TODO 返点率不是water字段，需要根据上下级奖金设置来计算，待定
            $sWater = $aPrizeGroupWaters[$value->prize_group];

            //$aPrizeGroups[$sPre . '_' . $value->{$sPre}] = ['prize_group' => $value->prize_group, 'water' => $sWater];
            $aPrizeGroups = ['prize_group' => $value->prize_group, 'water' => $sWater];
        }
        // pr($aPrizeGroups);exit;
        return $aPrizeGroups;
    }

    /**
     * [generateUserPrizeSetJson 生成json格式的奖金组设置信息]
     * @param  [Integer]  $iUserType         [开户类型是否代理, 0: 玩家, 1:代理, 2: top agent]
     * @param  [Integer]  $iPrizeGroupType   [奖金组类型, 1: 套餐, 2: 自定义]
     * @param  [Integer]  $iPrizeGroupId     [当是套餐奖金组数据时, 对应的奖金组id]
     * @param  [String]   $sLotteryPrizeJson [当是自定义奖金组时, 用户所选的彩种奖金组数据]
     * @param  [String]   $sSeriesPrizeJson  [当是自定义奖金组时, 用户所选的彩系奖金组数据]
     * @param  [Integer]  $iUserId           [父级用户id]
     * @return [String]                      [json格式的奖金组字符串]
     * @author Roy
     */
    public function generateUserPrizeSetJson($iUserType = 0, $iPrizeGroupType, $iPrizeGroupId, $sLotteryPrizeJson, $sSeriesPrizeJson, $iParentId = null, $bNeedDefault = true) {

        /*
        pr($iUserType);
        pr($iPrizeGroupType);
        pr($iPrizeGroupId);
        pr($sLotteryPrizeJson);
        pr($sSeriesPrizeJson);
        pr($iParentId);
        exit;
        */

        $sPrizeGroup      = null;
        $aCustomSeries    = null;
        $aCustomLotteries = null;

        // 套餐奖金组id, 彩系奖金组数据, 彩种奖金组数据必有其一
        if (!$iPrizeGroupId && !$sLotteryPrizeJson && !$sSeriesPrizeJson)
        {
            return false;
        }

        // 如果是套餐设置, 则必须有套餐奖金组id
        if ($iPrizeGroupType == 1 && !$iPrizeGroupId)
        {
            return false;
        }

        if ($iPrizeGroupType == 2)
        {
            $aCustomSeries    = objectToArray(json_decode($sSeriesPrizeJson));
            $aCustomLotteries = objectToArray(json_decode($sLotteryPrizeJson));
        }

        // 自定义玩家奖金组, 彩系奖金组数据和彩种奖金组数据必有其一
        if ($iUserType == 0 && $iPrizeGroupType == 2 && !$aCustomSeries && !$aCustomLotteries)
        {
            return false;
        }

        // 自定义代理奖金组, 必有彩系奖金组数据
        if ($iUserType == 1 && $iPrizeGroupType == 2 && !$aCustomSeries)
        {
            return false;
        }

        if ($iPrizeGroupType == 1 && $iPrizeGroupId)
        {
            $sPrizeGroup = PrizeGroup::find($iPrizeGroupId)->classic_prize;
        }

        if (!$this->judgePrizeGroupLimit($iUserType, $sPrizeGroup, $aCustomSeries, $aCustomLotteries, $iParentId))
        {
            return false;
        }

        $aPrizeGroupLimit = $this->generatePrizeGroupLimit($sPrizeGroup, $iUserType);

        $json = $this->generatePrizeGroupSetsJson($aPrizeGroupLimit, $iUserType, $aCustomSeries, $aCustomLotteries, $bNeedDefault);

        return $json;
    }

    /**
     * [generatePrizeGroupLimit 生成开户奖金组默认，最小，最大值]
     * @param  [String] $sPrizeGroup    [奖金组套餐]
     * @param  [Integer] $iUserType     [用户类型]
     * @return [Array]                  [开户奖金组默认，最小，最大值]
     */
    private function generatePrizeGroupLimit($sPrizeGroup, $iUserType) {
        switch ($iUserType) {
            case User::TYPE_USER:
                $sMaxPrizeGroup = SysConfig::readValue('player_max_grize_group');
                $sMinPrizeGroup = SysConfig::readValue('player_min_grize_group');
                break;
            case User::TYPE_AGENT:
                $sMaxPrizeGroup = SysConfig::readValue('agent_max_grize_group');
                $sMinPrizeGroup = SysConfig::readValue('agent_min_grize_group');
                break;
            case User::TYPE_TOP_AGENT:
                $sMaxPrizeGroup = SysConfig::readValue('top_agent_max_grize_group');
                $sMinPrizeGroup = SysConfig::readValue('top_agent_min_grize_group');
                break;
        }
        $aPrizeGroupLimit = [
            'min' => $sMinPrizeGroup,
            'default' => $sPrizeGroup,
            'max' => $sMaxPrizeGroup,
        ];
        return $aPrizeGroupLimit;
    }

    /**
     * [judgePrizeGroupLimit 判断设置的奖金组是否超过该代理自身的奖金组]
     * @param  [Integer] $iIsAgent         [开户类型是否代理, 0: 玩家, 1:代理]
     * @param  [String] $sPrizeGroup       [当是套餐奖金组时, 对应的奖金组]
     * @param  [Array] $aCustomSeries      [当是自定义奖金组时, 用户自定义的彩系奖金组数据]
     * @param  [Array] $aCustomLotteries   [当是自定义奖金组时, 用户自定义的彩种奖金组数据]
     * @return [Boolean]                   [是否超过]
     */
    private function judgePrizeGroupLimit($iIsAgent, $sPrizeGroup = null, $aCustomSeries = null, $aCustomLotteries = null, $iUserId = null)
    {
        // 没有用户id, 表示开总代，无需判断是否超限
        if (!$iUserId)
        {
            return true;
        }

        $oUser = User::find($iUserId);

        if (!$oUser)
        {
            return false;
        }

        $sCurrentAgentPrizeGroup = $sInputMaxPrizeGroup = $oUser->prize_group;

        // 如果没有任何奖金组, 返回false
        if (!$sCurrentAgentPrizeGroup)
        {
            return false;
        }

        if ($sPrizeGroup)
        {
            $sInputMaxPrizeGroup = $sPrizeGroup;
        }

        if (isset($aCustomSeries) && $aCustomSeries)
        {
            $sInputMaxPrizeGroup = max(array_values($aCustomSeries));
        }

        if ($iIsAgent == 0 && isset($aCustomLotteries) && $aCustomLotteries)
        {
            $sInputMaxPrizeGroup = max(array_values($aCustomLotteries));
        }

        return ($sCurrentAgentPrizeGroup >= $sInputMaxPrizeGroup);
    }

    /**
     * [generatePrizeGroupSetsJson 生成需要开户的用户的奖金组数据]
     * @param  [Array] $aPrizeGroupLimit    [开户奖金组默认，最小，最大值 ]
     * @param  [Integer] $iIsAgent          [开户类型是否代理, 0: 玩家, 1:代理]
     * @param  [Array] $aCustomSeries    [自定义的彩系奖金组]
     * @param  [Array] $aCustomLotteries [自定义的彩种奖金组]
     * @return [Array]                   [
     *                                     用户: [{'lottery_id' => '', 'prize_group' => ''}];
     *                                     代理: [{'series_id' => '', 'prize_group' => ''}]
     *                                   ]
     */
    public function generatePrizeGroupSetsJson($aPrizeGroupLimit, $iIsAgent, $aCustomSeries = null, $aCustomLotteries = null, $bNeedDefault = true) {
        $json = [];
        // 1:agent, 0:player
        // 链接开户用户的所有彩种的奖金组，不可超过其父级的奖金组
        if ($iIsAgent)
        {
            $oSeries = new Series;
            // 修改为根据彩系的link_to字段来重新组织彩系
            $aSeriesLotteries = & Series::getLotteriesGroupBySeries();
            // TIP 代理各彩系奖金组保持一致
            if (isset($aCustomSeries) && $aCustomSeries) {
                $sCustomSeriesPrize = (string) ($aCustomSeries['all_lotteries']);
            }
            foreach ($aSeriesLotteries as $value) {
                $key = $value['id'];
                $sPrizeGroup = null;
                // 如果是自定义奖金组，则该彩系设置为自定义的奖金组;
                // if (isset($aCustomSeries) && $aCustomSeries && array_key_exists($key, $aCustomSeries)) {
                //     $sPrizeGroup = $this->judgePrizeGroup($aPrizeGroupLimit, (string) $aCustomSeries[$key]);
                // }
                // TIP 代理各彩系奖金组保持一致
                if (isset($sCustomSeriesPrize) && $sCustomSeriesPrize) {
                    $sPrizeGroup = $this->judgePrizeGroup($aPrizeGroupLimit, $sCustomSeriesPrize);
                }
                // 否则使用默认的奖金组
                else if ($bNeedDefault) {
                    $sPrizeGroup = $this->judgePrizeGroup($aPrizeGroupLimit, $aPrizeGroupLimit['default']);
                }
                !$sPrizeGroup or $json[] = ['series_id' => $key, 'prize_group' => (string)($sPrizeGroup)];
            }
        }
        else
        {
            $aLotteries = [];
            $aAllLotteries = Lottery::getAllLotteries();

            foreach ($aAllLotteries as $key => $value)
            {
                $aLotteries[$value['id']] = arrayToObject($value);
            }

            if ($aCustomSeries)
            {
                $aSeriesLotteries = array_keys($aCustomSeries);
            }

            $sPrizeGroup = null;
            $oSeries = new Series;
            $aSeriesLinkTo = $oSeries->getValueListArray('link_to', [], [], true);

            foreach ($aLotteries as $lottery_id => $oLottery)
            {
                $iLinkTo = $aSeriesLinkTo[$oLottery->series_id] ? $aSeriesLinkTo[$oLottery->series_id] : $oLottery->series_id;

                // 如果设置了该彩种为自定义，则使用自定义奖金组;
                if (isset($aCustomLotteries) && $aCustomLotteries && array_key_exists($lottery_id, $aCustomLotteries))
                {
                    $sPrizeGroup = $this->judgePrizeGroup($aPrizeGroupLimit, $aCustomLotteries[$lottery_id]);
                }
                // 否则如果设置了彩系的自定义奖金组，且该彩种属于该彩系，则使用该彩系的奖金组设置;
                else if (isset($aSeriesLotteries) && $aSeriesLotteries && in_array($iLinkTo, $aSeriesLotteries))
                {
                    $sPrizeGroup = $this->judgePrizeGroup($aPrizeGroupLimit, (string) $aCustomSeries[$iLinkTo]);
                }
                // 否则，使用默认的奖金组;
                else if ($bNeedDefault)
                {
                    $sPrizeGroup = $this->judgePrizeGroup($aPrizeGroupLimit, $aPrizeGroupLimit['default']);
                }

                !$sPrizeGroup or $json[] = ['lottery_id' => $lottery_id, 'prize_group' => (string)($sPrizeGroup)];
            }
        }

        return json_encode($json);
    }

    /**
     * [judgePrizeGroup 确保奖金组的值在该开户类型的最大最小值区间内]
     * @param  [Array] $aPrizeGroupLimit  [该开户类型的奖金组的最大最小值区间]
     * @param  [String] $sPrizeGroup      [待判断的奖金组]
     * @return [String]                   [符合条件的奖金组的值]
     */
    private function judgePrizeGroup($aPrizeGroupLimit, $sPrizeGroup) {
        // if ($sPrizeGroup > $aPrizeGroupLimit['max']) $sPrizeGroup = $aPrizeGroupLimit['max'];
        // else if ($sPrizeGroup < $aPrizeGroupLimit['min']) $sPrizeGroup = $aPrizeGroupLimit['min'];
        // return $sPrizeGroup;
        //
        return min($aPrizeGroupLimit['max'], max($aPrizeGroupLimit['min'], $sPrizeGroup));
    }

    public static function getActiveLink($id) {
        // pr($sKeyword);exit;
        return self::where('id', '=', $id)
                        ->where('status', '=', 0)
                        // ->where('expired_at', '>', Carbon::now()->toDateTimeString())
                        // ->orWhereNull('expired_at')
                        ->first();
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
    }

    protected function getFormattedStatusAttribute() {
        if ($this->status == self::STATUS_IN_USE) {
            if ($this->expired_at) {
                if (Carbon::now()->toDateTimeString() > $this->expired_at) {
                    $sStatus = self::STATUS_EXPIRED;
                } else {
                    $sStatus = self::STATUS_IN_USE;
                }
            } else {
                $sStatus = self::STATUS_VALID_FOREVER;
            }
        } else {
            $sStatus = self::STATUS_CLOSED;
        }
        return __('_registerlink.' . strtolower(Str::slug(self::$aStatuses[$sStatus])));
    }

    protected function getFormattedIsAgentAttribute() {
        $sType = $this->is_agent;
        if ($this->is_admin) $sType = User::TYPE_TOP_AGENT;
        return __('_user.' . strtolower(Str::slug(User::$aUserTypes[$sType])));
    }

}
