<?php
/**
 * 电子游戏模型
 */
class CasinoLottery extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'Lottery';

    protected $table = 'casino_lotteries';
    
    public static $titleColumn = 'name';

    const ERRNO_LOTTERY_MISSING = -704;
    const ERRNO_LOTTERY_CLOSED = -705;
    const ERRNO_LOTTERY_UNTABLE=-706;
    const ERRNO_LOTTERY_NOWAY=-707;
    const ERRNO_LOTTERY_TABLE_CLOSE=-708;


    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'model_name',
        'identifier',
        'open',

    ];
    public static $listColumnMaps = [
        'name' => 'friendly_name'
    ];

    protected $fillable = [
        'name',
        'model_name',
        'identifier',
        'open',

    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];


    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name'      => 'required|between:2,10',
        'open'                => 'in:0,1',
    ];





    private static function compileLotteryListCacheKey($bOpen = null){
        $sKey = get_called_class() . '-list';
        if (!is_null($bOpen)){
            $sKey .= $bOpen ? '-open' : '-close';
        }
        return $sKey;
    }

    private static function & _getLotteryListByOpen($bOpen = null){
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::compileLotteryListCacheKey($bOpen);
            if ($aLotteries = Cache::get($sCacheKey)) {
                $bReadDb = false;
            }
            else{
                $bPutCache = true;
            }
        }
        if ($bReadDb){
                $aLotteries = CasinoLottery::where('open', '=', $bOpen)->get();

        }
        if ($bPutCache){
            Cache::forever($sCacheKey, $aLotteries);
        }
        return $aLotteries;
    }

    /**
     *
     * @param  [Boolean] $bOpen  [open属性]
     * @param  [Array] $aColumns [要获取的数据列名]
     * @return [Array]           [结果数组]
     */
    public static function getAllLotteries($bOpen = null, $aColumns = null)
    {

        $aLotteries = self::_getLotteryListByOpen($bOpen);

        $data = [];
        foreach ($aLotteries as $key => $value) {
            $aTmpData         = $value->getAttributes(); // ['id' => $value->id, 'series_id' => $value->series_id, 'name' => $value->name];
            $aTmpData['name'] = $value->friendly_name;
            $data[]           = $aTmpData;
        }
        return $data;
    }



    protected function getFriendlyNameAttribute() {
        return __('_casino.' . strtolower($this->name), [], 1);
    }






}

