<?php

/**
 * 彩票系列模型
 */
class Series extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'Series';
    protected $table = 'series';

    const LOTTERY_SERIES_SSC = 1;
    const LOTTERY_SERIES_11Y = 2;
    const LOTTERY_SERIES_K3 = 15;
    const LOTTERY_SERIES_K3_DICE = 16;
    const LOTTERY_SERIES_LHD = 17;
    const LOTTERY_SERIES_BJL = 18;
    const LOTTERY_SERIES_PK10 = 19;
    const ERRNO_SERIES_MISSING = -3000;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'type',
        'lotto_type',
        'name',
        'identifier',
        'sort_winning_number',
        'bet_commission',
        'digital_count',
        'classic_amount',
        'max_prize_group',
        'buy_length',
        'wn_length',
        'valid_nums',
        'lotteries',
        'default_way_id',
        'link_to',
        'min_commission_prize_group',
        'delay_issue_start_time',
        'is_muti_games',
    ];
    protected $fillable = [
        'type',
        'lotto_type',
        'name',
        'identifier',
        'lotteries',
        'buy_length',
        'wn_length',
        'digital_count',
        'valid_nums',
        'classic_amount',
        'max_prize_group',
        'sort_winning_number',
        'default_way_id',
        'link_to',
        'bet_commission',
        'min_commission_prize_group',
        'delay_issue_start_time',
        'is_muti_games',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [
        'link_to' => 'aSeries'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'type' => 'required|integer',
        'lotto_type' => 'integer',
        'name' => 'required|max:20',
        'identifier' => 'required|max:20',
        'digital_count' => 'required|numeric',
        'classic_amount' => 'required|integer',
        'max_prize_group' => 'required|integer',
        'buy_length' => 'required|numeric',
        'wn_length' => 'required|numeric',
        'valid_nums' => 'required|max:100',
        'lotteries' => 'max:200',
        'default_way_id' => 'required|integer',
        'sort_winning_number' => 'in:0,1',
        'link_to' => 'integer',
        'bet_commission' => 'in:0,1',
        'min_commission_prize_group' => '',
        'delay_issue_start_time' => 'integer',
        'is_muti_games' => 'in:0,1',
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';
    public $timestamps = false;

//    public function lotteries()
//    {
//        return $this->hasMany('Lottery');
//    }

    protected function getFriendlyNameAttribute() {
        return __('_series.' . strtolower($this->name));
    }

    /**
     * 检查是否存在相同的游戏名称
     *
     * @return boolean
     */
    private function _existName() {
        
    }

    protected function beforeValidate() {
        if (strpos($this->valid_nums, '-')) {
            list($iMin, $iMax) = explode('-', $this->valid_nums);
            $aValidNums = [];
            for ($i = $iMin; $i <= $iMax; $i++) {
                $aValidNums[] = $i;
            }
            $this->valid_nums = implode(',', $aValidNums);
        }
        !empty($this->link_to) or $this->link_to = null;
        !empty($this->lotto_type) or $this->lotto_type = null;
        if ($this->type == Lottery::LOTTERY_TYPE_DIGITAL) {
            $this->sort_winning_number = null;
        } else {
            in_array($this->sort_winning_number, [0, 1]) or $this->sort_winning_number = null;
        }
        return parent::beforeValidate();
    }

    private static function compileAllSeriesCacheKey() {
        return get_called_class() . '-all';
    }

    private static function & getAllSeries() {
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::compileAllSeriesCacheKey();
            if ($aSeires = Cache::get($sCacheKey)) {
                $bReadDb = false;
            } else {
                $bPutCache = true;
            }
        }
        if ($bReadDb) {
            $oSeires = self::all();
            $aSeires = [];
            foreach ($oSeires as $oSeires) {
                $aSeires[] = $oSeires->getAttributes();
            }
        }
        if ($bPutCache) {
            Cache::forever($sCacheKey, $aSeires);
        }

        return $aSeires;
    }

    /**
     * [getLotteriesGroupBySeries 获取带彩系信息的彩种数据]
     * @param  [Integer] $iOpen  [open属性]
     * @param  [Boolean] $bNeedLink  [是否需要判断彩系的link_to属性]
     * @param  [Array] $aColumns [要得到的数据列数组]
     * @return [Array]           [彩种数据]
     */
    public static function & getLotteriesGroupBySeries($iOpen = null, $bNeedLink = true, $aColumns = null) {
//        $aColumns or $aColumns = ['id', 'name', 'identifier', 'link_to'];
//        $aSeires = self::all($aColumns);
        $aAllSeires = & self::getAllSeries();
        $data = [];
        $aLotteriesArray = Lottery::getAllLotteriesGroupBySeries($iOpen, $bNeedLink);
        // pr($aLotteriesArray);exit;
        // pr($aSeires->toArray());exit;
        foreach ($aAllSeires as $aSeries) {
            if (isset($aLotteriesArray[$aSeries['id']])) {
                $oSeries = self::find($aSeries['id']);
                $oSeries->children = $aLotteriesArray[$oSeries->id];
                // 将模型的虚拟属性固定为对象的属性
                $oSeries->friendly_name = $oSeries->friendly_name;
                // if ($bNeedLink) {
                $data[] = $oSeries->getAttributes();
                // }
            }
        }
        // pr($data);exit;
        return $data; // $bNeedLink ? $data : $aSeires;
    }

    /**
     * [getAllSeriesWithLinkTo 获取所有带link_to属性的彩系信息]
     * @param  [Collection] $aSeires [彩系数据集合]
     * @return [Array]          [彩系之间关联数据数组]
     */
    public static function getAllSeriesWithLinkTo($aSeires = null) {
        $aAllSeires = self::getAllSeries();
//        if (!$aSeires) {
//            $aColumns = ['id', 'name', 'identifier', 'link_to'];
//            $aSeires = self::all($aColumns);
//        }
        $aLinkTo = [];
        foreach ($aAllSeires as $key => $aSeries) {
            $aLinkTo[$aSeries['id']] = $aSeries['link_to'];
        }
        return $aLinkTo;
    }

    /**
     * 获取所有linkto指定的彩种系列
     * @param int $iSeriesId  彩种系列id
     * @return [Array]          [彩种系列数组]
     */
    public static function getSeriesByLinkTo($iSeriesId) {
        $aColumns = ['id', 'lotteries'];
        $results = self::doWhere(['link_to' => ['=', $iSeriesId]])->get($aColumns);
        return $results;
    }

    public function deleteOtherCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sKey = self::compileAllSeriesCacheKey();
        !Cache::has($sKey) or Cache::forget($sKey);
    }

}
