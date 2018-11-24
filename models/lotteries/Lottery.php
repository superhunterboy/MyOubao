<?php

/**
 * 彩票模型
 */
class Lottery extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * 数字排列类型
     */
    const LOTTERY_TYPE_DIGITAL = 1;

    /**
     * 乐透类型
     */
    const LOTTERY_TYPE_LOTTO = 2;

    /**
     * 单区乐透类型
     */
    const LOTTERY_TYPE_LOTTO_SINGLE = 1;

    /**
     * 双区乐透类型
     */
    const LOTTERY_TYPE_LOTTO_DOUBLE = 2;
    const WINNING_SPLIT_FOR_DOUBLE_LOTTO = '+';

    /**
     * 彩票开售状态
     */
    const LOTTERY_STATUS_OPEN = 1;

    /**
     * 彩票状态：不可用
     */
    const STATUS_NOT_AVAILABLE = 0;

    /**
     * 彩票状态：测试用户可用
     */
    const STATUS_AVAILABLE_FOR_TESTER = 1;

    /**
     * 彩票状态：所有用户可用
     */
    const STATUS_AVAILABLE = 3;

    /**
     * 彩票关闭状态
     */
    const LOTTERY_STATUS_CLOSE = 0;
    const ERRNO_LOTTERY_MISSING = -900;
    const ERRNO_LOTTERY_CLOSED = -901;

    /**
     * 彩票展示分组类型
     */
    const LOTTERY_CATEGORY_SSC = 0;
    const LOTTERY_CATEGORY_11Y = 1;
    const LOTTERY_CATEGORY_OFFICIAL = 2;
    const LOTTERY_CATEGORY_K3 = 3;
    const LOTTERY_CATEGORY_OTHER = 4;
    const LOTTERY_CATEGORY_PK10 = 5;

    /**
     * all types
     * @var array
     */
    public static $validTypes = [
        self::LOTTERY_TYPE_DIGITAL => 'Digital',
        self::LOTTERY_TYPE_LOTTO => 'Lotto',
    ];

    /**
     * all lotto types
     * @var array
     */
    public static $validLottoTypes = [
        self::LOTTERY_TYPE_LOTTO_SINGLE => 'Single',
        self::LOTTERY_TYPE_LOTTO_DOUBLE => 'Double',
    ];
    public static $validStatus = [
        self::STATUS_NOT_AVAILABLE => 'Closed',
        self::STATUS_AVAILABLE_FOR_TESTER => 'Testing',
        self::STATUS_AVAILABLE => 'Available'
    ];
    public static $aLotteryCategories = [
        self::LOTTERY_CATEGORY_SSC => 'ssc',
        self::LOTTERY_CATEGORY_11Y => 'l115',
        self::LOTTERY_CATEGORY_OFFICIAL => 'own',
        self::LOTTERY_CATEGORY_OTHER => 'other',
        self::LOTTERY_CATEGORY_K3 => 'k3',
        self::LOTTERY_CATEGORY_PK10 => 'pk10',
    ];

    /**
     * 一个奖期内只能投注一次
     * @var array
     */
    public static $validLotteriesForOnce = [
        'DYDICE', 'DYDICEA1', 'DYDICEA2', 'DYDICEA3', 'DYDICEB1', 'DYDICEB2', 'DYDICEB3', 'DYDICEC1', 'DYDICEC2', 'DYDICEC3',
        'BMDICE', 'BMDICEA1', 'BMDICEA2', 'BMDICEA3', 'BMDICEB1', 'BMDICEB2', 'BMDICEB3', 'BMDICEC1', 'BMDICEC2', 'BMDICEC3',
        'BMLHD', 'BMLHDA1', 'BMLHDA2', 'BMLHDA3', 'BMLHDB1', 'BMLHDB2', 'BMLHDB3', 'BMLHDC1', 'BMLHDC2', 'BMLHDC3',
        'DYLHD', 'DYLHDA1', 'DYLHDA2', 'DYLHDA3', 'DYLHDB1', 'DYLHDB2', 'DYLHDB3', 'DYLHDC1', 'DYLHDC2', 'DYLHDC3',
        'BMBJL', 'BMBJLA1', 'BMBJLA2', 'BMBJLA3', 'BMBJLB1', 'BMBJLB2', 'BMBJLB3', 'BMBJLC1', 'BMBJLC2', 'BMBJLC3',
        'DYBJL', 'DYBJLA1', 'DYBJLA2', 'DYBJLA3', 'DYBJLB1', 'DYBJLB2', 'DYBJLB3', 'DYBJLC1', 'DYBJLC2', 'DYBJLC3',
    ];

    /**
     * 不检查投注倍数
     * @var array
     */
    public static $noCheckBetMultiple = [
        'DYDICE', 'DYDICEA1', 'DYDICEA2', 'DYDICEA3', 'DYDICEB1', 'DYDICEB2', 'DYDICEB3', 'DYDICEC1', 'DYDICEC2', 'DYDICEC3',
        'BMDICE', 'BMDICEA1', 'BMDICEA2', 'BMDICEA3', 'BMDICEB1', 'BMDICEB2', 'BMDICEB3', 'BMDICEC1', 'BMDICEC2', 'BMDICEC3',
        'BMLHD', 'BMLHDA1', 'BMLHDA2', 'BMLHDA3', 'BMLHDB1', 'BMLHDB2', 'BMLHDB3', 'BMLHDC1', 'BMLHDC2', 'BMLHDC3',
        'DYLHD', 'DYLHDA1', 'DYLHDA2', 'DYLHDA3', 'DYLHDB1', 'DYLHDB2', 'DYLHDB3', 'DYLHDC1', 'DYLHDC2', 'DYLHDC3',
        'BMBJL', 'BMBJLA1', 'BMBJLA2', 'BMBJLA3', 'BMBJLB1', 'BMBJLB2', 'BMBJLB3', 'BMBJLC1', 'BMBJLC2', 'BMBJLC3',
        'DYBJL', 'DYBJLA1', 'DYBJLA2', 'DYBJLA3', 'DYBJLB1', 'DYBJLB2', 'DYBJLB3', 'DYBJLC1', 'DYBJLC2', 'DYBJLC3',
    ];
    public static $bmLotteryIds = [11, 12, 20, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52];
    public static $aKl28Lotteries = [54, 55, 56, 57, 58, 59];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'Lottery';
    protected $table = 'lotteries';

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'series_id' => 'aSeries',
        'type' => 'aValidTypes',
        'status' => 'aValidStatus',
        'category' => 'aLotteryCategories',
        'lotto_type' => 'aValidLottoTypes',
    ];
    public static $sequencable = true;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'type',
        'lotto_type',
        'identifier',
        'days',
        'begin_time',
        'end_time',
        'daily_issue_count',
        'trace_issue_count',
        'open',
        'sequence',
        'max_prize',
        'is_trace_issue',
        'entertained_time',
    ];
    public static $listColumnMaps = [
        'name' => 'friendly_name'
    ];
    protected $fillable = [
        'series_id',
        'name',
        'type',
        'lotto_type',
        'high_frequency',
        'sort_winning_number',
        'valid_nums',
        'buy_length',
        'wn_length',
        'identifier',
        'days',
        'issue_over_midnight',
        'issue_format',
        'begin_time',
        'end_time',
        'open',
        'need_draw',
        'series_ways',
        'sequence',
        'daily_issue_count',
        'trace_issue_count',
        'max_prize',
        'is_trace_issue',
        'entertained_time',
        'introduction',
        'category',
        'status',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'type';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'series_id' => 'required|integer',
        'name' => 'required|between:2,10',
        'type' => 'required|numeric',
//        'lotto_type'     => 'numeric',
        'high_frequency' => 'in:0,1',
        'sort_winning_number' => 'in:0,1',
//        'valid_nums' => 'required',
        'buy_length' => 'required',
        'wn_length' => 'required',
        'identifier' => 'required|between:3,10',
        'days' => 'numeric',
        'issue_over_midnight' => 'in:0,1',
        'issue_format' => 'required',
        'daily_issue_count' => 'integer',
        'trace_issue_count' => 'integer',
//        'begin_time' => 'required',
//        'end_time' => 'required',
//        'need_draw' => 'in:0,1',
        'open' => 'in:0,1',
        'sequence' => 'integer',
        'max_prize' => 'required|integer',
        'is_trace_issue' => 'in:0,1',
        'entertained_time' => 'integer',
        'status' => 'in:0,1,3',
        'category' => 'in:0,1,2,3,4,5',
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

    /**
     * 奖级设置表名称
     *
     * @var string
     */
    private $tableBonusSet;

    /**
     * 奖金明细表名称
     *
     * @var string
     */
    private $tableBonusDetails;

    public function series() {
        return $this->belongsTo('Series');
    }

    /**
     * 检查是否存在相同的游戏名称
     *
     * @return boolean
     */
    private function _existName() {
        
    }

    /**
     * 检查是否存在相同的游戏代码
     *
     * @return boolean
     */
    private function _existCode() {
        
    }

    /**
     * 检验号码是否正确, move in from ec,need modify
     *
     * @param string $sWinningNumber
     * @return boolean
     */
    public function checkWinningNumber($sWinningNumber) {
        switch ($this->type) {
            case self::LOTTERY_TYPE_DIGITAL:    // num type
                if ($this->id == 53) {
                    $sWinningNumber = trim($sWinningNumber);
                    $wNumbers = explode(',', $sWinningNumber);
                    if (count(array_unique($wNumbers)) != $this->wn_length) {
                        return false;
                    } else {
                        $oSeries = Series::find($this->series_id);
                        $valid_nums = explode(',', $oSeries->valid_nums);
                        foreach ($wNumbers as $num) {
                            if (!in_array($num, $valid_nums, true)) {
                                return false;
                            }
                        }
                    }
                    return true;
                } else if (strpos($sWinningNumber, ' ')) {
                    foreach (explode(' ', $sWinningNumber) as $sWinNumber) {
                        if (!is_numeric($sWinNumber))
                            return false;
                    }
                    return true;
                }else if ($this->series_id == Series::LOTTERY_SERIES_PK10) {
                    $aValidBalls = ['01','02','03','04','05','06','07','08','09','10'];
                    return $this->_checkWinningNumberForSingleLotto($sWinningNumber, $aValidBalls, $this->wn_length);
                } else {
                    $sPattern = '/^\d{' . ($this->wn_length) . '}$/uis';
                    return preg_match($sPattern, $sWinningNumber);
                }

                break;
            case self::LOTTERY_TYPE_LOTTO:
                switch ($this->lotto_type) {
                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
                        $aValidBalls = $this->getValidNums($this->valid_nums, $this->type, $this->lotto_type);
                        return $this->_checkWinningNumberForSingleLotto($sWinningNumber, $aValidBalls, $this->wn_length);
                        break;
                    case self::LOTTERY_TYPE_LOTTO_DOUBLE:
                        $aBonusCode = explode(self::BONUS_CODE_SPLIT_CHAR_FOR_DOUBLE_LOTTO, $sWinningNumber);
                        if (count($aBonusCode) != 2) {
                            return false;
                        }
                        $aValidBalls = $this->getValidNums($this->valid_nums, $this->type, $this->lotto_type);
                        $aCodeLen = explode('|', $this->wn_length);
                        $bValid = true;
                        foreach ($aBonusCode as $iArea => $sBonusCodeOfArea) {
                            if (!$bValid = $this->_checkWinningNumberForSingleLotto($sBonusCodeOfArea, $aValidBalls[$iArea], $aCodeLen[$iArea])) {
                                break;
                            }
                        }
                        return $bValid;
                        break;
                    default :       // 尚不支持多区乐透型
                        return false;
                }
                break;
            default:  // 尚不支持其他类型
                return false;
        }
    }

    /**
     * 检验号码是否正确,用于单区乐透型
     *
     * @param string $sWinningNumber
     * @param array $aValidBalls
     * @param int $iCodeLen
     * @return bool
     */
    private function _checkWinningNumberForSingleLotto($sWinningNumber, & $aValidBalls, $iCodeLen) {
        Log::info($aValidBalls);
        if ($this->series_id == Series::LOTTERY_SERIES_PK10) {
            $aBalls = array_unique(explode(',', $sWinningNumber));
        } else {
            $aBalls = array_unique(explode(' ', $sWinningNumber));
        }
        foreach ($aBalls as $i => $iBall) {
            $iBall = $this->formatBall($iBall, self::LOTTERY_TYPE_LOTTO, self::LOTTERY_TYPE_LOTTO_SINGLE);
            if (!in_array($iBall, $aValidBalls)) {
                return false;
            }
            $aBalls[$i] = $iBall;
        }
        $aDiff = array_diff($aBalls, $aValidBalls);
//        $sWinningNumber = implode(' ',$aBalls);
        return empty($aDiff) && count($aBalls) == $iCodeLen;
    }

    /**
     * 返回期号规则
     *
     * @return string
     */
    public function getIssueFormat() {
        return $this->issue_format;
    }

    /**
     * 设置开售停售状态
     *
     * @return type
     */
    public function setOpenClose() {
        if ($this->open == self::LOTTERY_STATUS_CLOSE) {
            $this->open = self::LOTTERY_STATUS_OPEN;
        } else {
            $this->open = self::LOTTERY_STATUS_CLOSE;
        }
        return $this->save();
    }

    protected function beforeValidate() {
        $this->lotto_type or $this->lotto_type = null;
        return parent::beforeValidate();
    }

//    public static function getAllLotteryNameArray($aColumns = null)
//    {
//        $aColumns or $aColumns = ['id', 'name'];
//        $aLotteries = Lottery::all($aColumns);
//        $data = [];
//        foreach ($aLotteries as $key => $value) {
//            $data[$value->id] = $value->name;
//        }
//        return $data;
//    }
    private static function compileLotteryListCacheKey($bOpen = null) {
        $sKey = get_called_class() . '-list';
        if (!is_null($bOpen)) {
            $sKey .= $bOpen ? '-open' : '-close';
        }
        return $sKey;
    }

    private static function & _getLotteryListByOpen($bOpen = null) {
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::compileLotteryListCacheKey($bOpen);
            if ($aLotteries = Cache::get($sCacheKey)) {
                $bReadDb = false;
            } else {
                $bPutCache = true;
            }
        }
        if ($bReadDb) {
            if (!is_null($bOpen)) {
                $aLotteries = Lottery::where('open', '=', $bOpen)->orderBy('sequence')->get();
            } else {
                $aLotteries = Lottery::orderBy('sequence')->get();
            }
        }
        if ($bPutCache) {
            Cache::forever($sCacheKey, $aLotteries);
        }
        return $aLotteries;
    }

    /**
     * [getAllLotteries 获取所有彩种信息]
     * @param  [Boolean] $bOpen  [open属性]
     * @param  [Array] $aColumns [要获取的数据列名]
     * @return [Array]           [结果数组]
     */
    public static function getAllLotteries($iStatus = null, $aColumns = null) {
//        $aColumns or $aColumns = ['id', 'series_id', 'name'];
//        if (! is_null($bOpen)) {
//            $aLotteries = Lottery::where('open', '=', $bOpen)->orderBy('sequence')->get($aColumns);
//        } else {
//            $aLotteries = Lottery::orderBy('sequence')->get($aColumns);
//        }
        $aLotteries = self::getLotteryListByStatus($iStatus);
        $data = [];
        foreach ($aLotteries as $key => $value) {
            $aTmpData = $value->getAttributes(); // ['id' => $value->id, 'series_id' => $value->series_id, 'name' => $value->name];
            $aTmpData['name'] = $value->friendly_name;
            $data[] = $aTmpData;
        }
        return $data;
    }

    protected static function & getLotteryListByStatus($iStatus = null) {
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::compileLotteryListCacheKey($iStatus);
            if ($aLotteries = Cache::get($sCacheKey)) {
                $bReadDb = false;
            } else {
                $bPutCache = true;
            }
        }
//        $bReadDb = $bPutCache = true;
        if ($bReadDb) {
            if (!is_null($iStatus)) {
                $aStatus = self::_getStatusArray($iStatus);
//                file_put_contents('/tmp/kkkkkk', var_export($aStatus, true));
                $aLotteries = Lottery::whereIn('status', $aStatus)->orderBy('sequence')->get();
            } else {
                $aLotteries = Lottery::orderBy('sequence')->get();
            }
        }
        if ($bPutCache) {
            Cache::forever($sCacheKey, $aLotteries);
        }
        return $aLotteries;
    }

    protected static function _getStatusArray($iNeedStatus) {
        $aStatus = [];
        foreach (static::$validStatus as $iStatus => $sTmp) {
            if (($iStatus & $iNeedStatus) == $iNeedStatus) {
                $aStatus[] = $iStatus;
            }
        }
        return $aStatus;
    }

    /**
     * generate select widget
     * @return int or false   -1: path not writeable
     */
    public static function generateWidget() {
        $sCacheDataPath = Config::get('widget.data_path');
        if (!is_writeable($sCacheDataPath)) {
            return [
                'code' => -1,
                'message' => __('_basic.file-write-fail-path', ['path' => $sCacheDataPath]),
            ];
        }
        $sFile = $sCacheDataPath . '/' . 'lotteries.blade.php';
        if (file_exists($sFile) && !is_writeable($sFile)) {
            return [
                'code' => -1,
                'message' => __('_basic.file-write-fail-file', ['file' => $sFile]),
            ];
        }
        $aLotterys = self::getAllLotteryNameArray();
//        pr(json_encode($aLotterys));
        $iCode = @file_put_contents($sFile, 'var lotteries = ' . json_encode($aLotterys));
        $sLangKey = '_basic.' . ($iCode ? 'file-writed' : 'file-write-fail');
        return [
            'code' => $iCode,
            'message' => __($sLangKey, ['resource' => $sFile]),
        ];
    }

    /**
     * 返回可用的数字数组
     *
     * @param string $sString
     * @param int $iLotteryType
     * @param int $iLottoType
     * @return array
     */
    public function & getValidNums($sString, $iLotteryType = self::LOTTERY_TYPE_DIGITAL, $iLottoType = self::LOTTERY_TYPE_LOTTO_SINGLE) {
        $data = [];
        if ($iLotteryType == self::LOTTERY_TYPE_LOTTO && $iLottoType != self::LOTTERY_TYPE_LOTTO_SINGLE) {
//            echo "$iLotteryType   New...\n";
            $aStringOfAreas = explode('|', $sString);
            $data = [];
            foreach ($aStringOfAreas as $iArea => $sStr) {
                $data[$iArea] = & $this->getValidNums($sStr, self::LOTTERY_TYPE_LOTTO, self::LOTTERY_TYPE_LOTTO_SINGLE);
            }
//            return $data;
        } else {
            $a = explode(',', $sString);
            foreach ($a as $part) {
                $aPart = explode('-', $part);
                if (count($aPart) == 1) {
                    $data[] = $this->formatBall($aPart[0], $iLotteryType, $iLottoType);
                } else {
                    for ($i = $aPart[0]; $i <= $aPart[1]; $i++) {
                        $data[] = $this->formatBall($i, $iLotteryType, $iLottoType);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 格式化数字
     *
     * @param int $iNum
     * @param int $iLotteryType
     * @param int $iLottoType
     * @return string
     */
    public function formatBall($iNum, $iLotteryType = self::LOTTERY_TYPE_DIGITAL, $iLottoType = self::LOTTERY_TYPE_LOTTO_SINGLE) {
        switch ($iLotteryType) {
            case self::LOTTERY_TYPE_DIGITAL:
                return $iNum + 0;
                break;
            case self::LOTTERY_TYPE_LOTTO:
                switch ($iLottoType) {
                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
                    case self::LOTTERY_TYPE_LOTTO_DOUBLE:
                    case self::LOTTERY_TYPE_LOTTO_MIXED:
                        return str_pad($iNum, 2, '0', STR_PAD_LEFT);
                        break;
                }
        }
    }

    /**
     * 格式化中奖号码，返回规范化的中奖号码
     *
     * @param string $sWinningNumber
     * @param $sSplitChar 双区乐透类型时的区分隔符，非双区乐透型时无效
     * @return string
     */
    public function formatWinningNumber($sWinningNumber, $sSplitChar = '+') {
        $this->recursive = -1;
        switch ($this->type) {
            case self::LOTTERY_TYPE_DIGITAL:    // num type
                $pattern = '/[^ |,\d]/';
                return preg_replace($pattern, '', $sWinningNumber);
                break;
            case self::LOTTERY_TYPE_LOTTO:
                switch ($this->lotto_type) {
                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
                        return $this->_formatWinningNumberForSingleLotto($sWinningNumber, $this->sort_winning_number);
                        break;
                    case self::LOTTERY_TYPE_LOTTO_DOUBLE:
                        $aAreas = explode($sSplitChar, $sWinningNumber);
                        $aBonusCode = [];
                        foreach ($aAreas as $iKey => $sBonusCodeForArea) {
                            $aBonusCode[$iKey] = $this->_formatWinningNumberForSingleLotto($sBonusCodeForArea, $this->sort_winning_number);
                        }
                        return implode(self::WINNING_SPLIT_FOR_DOUBLE_LOTTO, $aBonusCode);
                    default :       // 尚不支持多区乐透型
                        return false;
                }
                break;
            default:  // 尚不支持其他类型
                return false;
        }
    }

    /**
     * 格式化单区乐透型的号码
     * @param string $sWinningNumber
     * @param bool $bSort
     * @return string
     */
    private function _formatWinningNumberForSingleLotto($sWinningNumber, $bSort) {
        $sWinningNumber = preg_replace('/[^\d]/', ' ', $sWinningNumber);
        $aNums = array_unique(explode(' ', $sWinningNumber));
        $aNums = array_diff($aNums, ['']);
        !$bSort or sort($aNums);
        $aBalls = [];
        foreach ($aNums as $iNum) {
            $aBalls[] = $this->formatBall($iNum, self::LOTTERY_TYPE_LOTTO, self::LOTTERY_TYPE_LOTTO_SINGLE);
        }
        return implode(' ', $aBalls);
    }

    protected function getFriendlyNameAttribute() {
        return __('_lotteries.' . strtolower($this->name), [], 1);
    }

    /**
     * 返回数据列表
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getTitleList($bOrderByTitle = false) {
        $aColumns = [ 'id', 'name'];
        $sOrderColumn = $bOrderByTitle ? 'name' : 'sequence';
        $oModels = self::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->friendly_name;
        }
        $data += \JcModel\JcLotteries::getTitleList($bOrderByTitle);
        $data += CasinoLottery::getTitleList($bOrderByTitle);
        return $data;
    }

    /**
     * 返回人性化的游戏列表，游戏名称为已翻译的
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getLotteryList() {
        $bReadDb = false;
        $sLocale = App::getLocale();
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $key = self::compileListCaheKey($sLocale);
            if (!$aLotteries = Cache::get($key)) {
                $bReadDb = true;
            }
        }
        if ($bReadDb) {
            $aLotteries = self::getTitleList();
            !$key or Cache::forever($key, $aLotteries);
        }

        return $aLotteries;
    }

    /**
     * 从数据库提取游戏列表
     * @param bool $bOrderByTitle   是否按名字排序
     * @return array
     */
    private static function & _getLotteryList($bOrderByTitle = true) {
        $aColumns = [ 'id', 'name'];
        $sOrderColumn = $bOrderByTitle ? 'name' : 'sequence';
        $oModels = self::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->name;
        }
        return $data;
    }

    public static function & getIdentifierList($bOrderByTitle = false) {
        $aColumns = [ 'id', 'identifier'];
        $sOrderColumn = $bOrderByTitle ? 'name' : 'sequence';
        $oModels = self::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->identifier;
        }
        return $data;
    }

    /**
     * 更新游戏列表配置
     * @return int  1: 成功 0:失败 -1: 文件不可写
     */
    public static function updateLotteryConfigs() {
        $aLotteries = & self::getIdentifierList();
//        pr($aLotteries);
        $sString = "<?php\nreturn " . var_export($aLotteries, true) . ";\n";
        $sPath = app_path('config');
        $sFile = $sPath . DIRECTORY_SEPARATOR . 'lotteries.php';
        if (!is_writeable($sFile)) {
            return -1;
        }
        return file_put_contents($sFile, $sString) ? 1 : 0;
    }

    public static function updateLotteryListCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sLanguageSource = SysConfig::readDataSource('sys_support_languages');
        // pr($sLanguageSource);
        $aLanguages = SysConfig::getSource($sLanguageSource);
        $aLotteries = & self::_getLotteryList();
        foreach ($aLanguages as $sLocale => $sLanguage) {
            $aLotteriesOfLocale = array_map(function($value) use ($sLocale) {
                return __('_lotteries.' . strtolower($value), [], 1, $sLocale);
            }, $aLotteries);
            $key = self::compileListCaheKey($sLocale);
            Cache::forever($key, $aLotteriesOfLocale);
        }
        return true;
    }

    protected static function compileListCaheKey($sLocate) {
        return 'lottery-list-' . $sLocate;
    }

    private static function deleteOtherCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sKey = self::compileLotteryListCacheKey();
        !Cache::has($sKey) or Cache::forget($sKey);
        $sKey = self::compileLotteryListCacheKey(1);
        !Cache::has($sKey) or Cache::forget($sKey);
        $sKey = self::compileLotteryListCacheKey(0);
        !Cache::has($sKey) or Cache::forget($sKey);
    }

    protected function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        $this->updateLotteryListCache();
        $this->deleteOtherCache();
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        parent::afterDelete($oDeletedModel);
        $this->updateLotteryListCache();
        $this->deleteOtherCache();
        return true;
    }

    /**
     * 根据代码返回游戏对象
     * @param string $sIdentifier
     * @return Lottery | false
     */
    public static function getByIdentifier($sIdentifier) {
        $bReadDb = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $key = self::compileCacheKeyByIdentifier($sIdentifier);
            if ($aAttributes = Cache::get($key)) {
                $obj = new static;
                $obj = $obj->newFromBuilder($aAttributes);
            } else {
                $bReadDb = true;
            }
        }
        if ($bReadDb) {
            $obj = self::where('identifier', '=', $sIdentifier)->get()->first();
            if (!is_object($obj)) {
                return false;
            }
            !$key or Cache::forever($key, $obj->getAttributes());
        }

        return $obj;
    }

    protected static function compileCacheKeyByIdentifier($sIdentifier) {
        return 'lottery-identifier-' . $sIdentifier;
    }

    /**
     * [getAllLotteriesGroupBySeries 根据彩系组织彩种]
     * @param  [Integer] $iOpen     [open属性]
     * @param  [boolean] $bNeedLink [是否需要判断彩系的link_to属性]
     * @return [Array]           [彩种数据]
     */
    public static function getAllLotteriesGroupBySeries($iOpen = null, $bNeedLink = true, $aLotteryColumns = null) {
        $aLotteries = self::getAllLotteries($iOpen, $aLotteryColumns);
        $aLinkTo = Series::getAllSeriesWithLinkTo();
        $aLotteriesArray = [];
        foreach ($aLotteries as $key => $aLottery) {
            if ($bNeedLink && $aLinkTo[$aLottery['series_id']]) {
                $aLottery['series_id'] = $aLinkTo[$aLottery['series_id']];
            }
            if (!isset($aLotteriesArray[$aLottery['series_id']])) {
                $aLotteriesArray[$aLottery['series_id']] = [];
            }
            $aLotteriesArray[$aLottery['series_id']][] = $aLottery;
        }
        return $aLotteriesArray;
    }

    /**
     * [getAllLotteryIdsGroupBySeries 生成彩种--彩系的映射数组, 彩系以linkTo属性为准]
     * @return [Array] [彩种--彩系的映射数组]
     */
    public static function getAllLotteryIdsGroupBySeries() {
        $aLotteries = self::getAllLotteries();
        $aLinkTo = Series::getAllSeriesWithLinkTo();
        $aLotteriesArray = [];
        foreach ($aLotteries as $key => $aLottery) {
            if ($aLinkTo[$aLottery['series_id']]) {
                $aLottery['series_id'] = $aLinkTo[$aLottery['series_id']];
            }
            $aLotteriesArray[$aLottery['id']] = $aLottery['series_id'];
        }
        return $aLotteriesArray;
    }

    /**
     * 根据经典奖金组获取奖金组信息
     *
     * @param $prize
     * @return bool
     */
    public function getGroupByClassicPrize($prize) {
        return PrizeGroup::getPrizeGroupByClassicPrizeAndSeries($prize, $this->series_id);
    }

    public static function getBmLotteries() {
        $aConditions = [
            'id' => ['in', self::$bmLotteryIds]
        ];
        return self::doWhere($aConditions)->get();
    }

    public static function getLotteriesBySeriesId($iSeriesId) {
        $aConditions = [
            'series_id' => ['=', $iSeriesId]
        ];
        return self::doWhere($aConditions)->get();
    }

    public static function getLotteriesByLotteryIds($aLotteryIds) {
        $aConditions = [
            'id' => ['in', $aLotteryIds],
            'open' => self::LOTTERY_STATUS_OPEN
        ];
        return self::doWhere($aConditions)->orderBy('sequence')->get();
    }

    public static function getAllLotteryId() {
        return self::where('open', self::LOTTERY_STATUS_OPEN)->get();
    }

}
