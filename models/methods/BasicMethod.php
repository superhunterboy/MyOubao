<?php

class BasicMethod extends BaseModel
{
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'basic_methods';
    public $sPosition = null;
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'lottery_type',
        'type',
        'name',
        'price',
        'sequencing',
        'digital_count',
        'unique_count',
        'max_repeat_time',
        'min_repeat_time',
        'span',
        'min_span',
        'choose_count',
        'special_count',
        'fixed_number',
//        'shape',
        'valid_nums',
        'buy_length',
        'wn_length',
        'wn_count',
        'all_count',
        'wn_function',
//        'sequence',
    ];

    public static $resourceName = 'Basic Method';
    public static $sequencable = false;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'lottery_type',
        'type',
        'name',
        'sequencing',
        'digital_count',
        'unique_count',
        'max_repeat_time',
        'min_repeat_time',
        'span',
        'min_span',
        'special_count',
        'choose_count',
        'fixed_number',
//        'shape',
        'valid_nums',
        'buy_length',
        'wn_length',
        'wn_count',
//        'sequence',
    ];

    public static $titleColumn = 'name';
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_type' => 'aLotteryTypes',
        'type' => 'aMethodTypes',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'digital_count' => 'asc',
//        'sequence' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'lottery_type';
    public $digitalCounts = [];
    public static $rules = [
        'lottery_type' => 'required|integer',
        'type' => 'required|integer',
        'name' => 'required|max:10',
        'digital_count' => 'required|numeric',
        'sequencing' => 'required|in:0,1',
        'unique_count' => 'integer|min:0|max:5',
        'max_repeat_time' => 'integer|min:0|max:5',
        'min_repeat_time' => 'integer|min:0|max:5',
        'span' => 'integer|min:0|max:9',
        'min_span' => 'integer|min:0|max:9',
        'choose_count' => 'integer|min:0|max:9',
        'special_count' => 'integer|min:0|max:9',
        'fixed_number' => 'integer|min:0|max:9',
//        'shape'           => 'required|numeric',
        'price' => 'numeric',
        'buy_length' => 'required|numeric',
        'wn_length' => 'required|numeric',
        'wn_count' => 'required|numeric',
        'valid_nums' => 'required|max:50',
        'all_count' => 'required|numeric',
//        'sequence'      => 'numeric',
    ];

    protected $splitChar;
    protected $splitCharSumDigital;
    protected $splitCharInArea;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 设置splitChar属性
     * @return void
     */
    protected function init()
    {
        $this->splitChar = Config::get('bet.split_char') or $this->splitChar = '|';
        $this->splitCharSumDigital = Config::get('bet.split_sum_char') or $this->splitCharSumDigital = ',';
        if (!$this->lottery_type) {
            return;
        }
//        if ($this->lottery_type == Lottery::LOTTERY_TYPE_LOTTO){
        $this->splitCharInArea = Config::get('bet.split_char_lotto_in_area') or $this->splitCharInArea = '';
//        }
    }

    protected function beforeValidate()
    {
        $this->price or $this->price = Config::get('price.default');
//        $this->indexs or $this->indexs = $this->max('indexs') + 1;
        $this->sequencing or $this->sequencing = 0;
//        $this->digital_count or $this->digital_count   = null;
        $this->unique_count or $this->unique_count = null;
        $this->max_repeat_time or $this->max_repeat_time = null;
        $this->min_repeat_time or $this->min_repeat_time = null;
        $this->span or $this->span = null;
        $this->min_span or $this->min_span = null;
        $this->choose_count or $this->choose_count = null;
        $this->special_count or $this->special_count = null;
        $this->fixed_number or $this->fixed_number = null;
//        $this->shape or $this->shape           = null;
        if (!$this->type) {
            return false;
        }
        $oMethodType = MethodType::find($this->type);
        $this->wn_function = $oMethodType->wn_function;
        return parent::beforeValidate();
    }

    /**
     * 分析中奖号码
     * @param string $sWinningNumber
     * @return string | array
     */
    public function getWinningNumber($sWinningNumber)
    {
        $this->init();
        $sFunction = $this->getWnFunction();
        return $this->$sFunction($sWinningNumber);
    }

    /**
     * 计算投注码的中奖注数
     * @param string $sWayFunction
     * @param string $sBetNumber
     * @return int
     */
    public function countBetNumber($sWayFunction, & $sBetNumber)
    {

        $this->init();

        $sFunction = $this->getCheckFunction($sWayFunction);

//        die($sFunction);
//        file_put_contents('/tmp/check',$sFunction);
        return $this->$sFunction($sBetNumber);
    }

    /**
     * 计奖方法，返回中奖注数或false
     * @param SeriesWay $oSeriesWay
     * @param BasicWay $oBasicWay
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int | false
     */
    public function checkPrize($oSeriesWay, $oBasicWay, $sWnNumber, $sBetNumber)
    {
        $this->init();
        $sFunction = $this->getPrizeFunction($oBasicWay->function);

        return $this->$sFunction($oSeriesWay, $sWnNumber, $sBetNumber);
    }

    /**
     * 返回直选中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberEqual($sWinningNumber)
    {
        if (!is_null($this->span)) {
            $aDigitals = str_split($sWinningNumber, 1);
            $iSpan = max($aDigitals) - min($aDigitals);
            if ($iSpan == $this->span) {
                if ($this->min_span) {
                    $iDigitalCount = count($aDigitals);
                    $aSpan = [];
                    for ($i = 1; $i < $iDigitalCount; $aSpan[] = abs($aDigitals[$i] - $aDigitals[$i++ - 1])) ;
                    $aDigitals[] = abs($aDigitals[0] - $aDigitals[$iDigitalCount - 1]);
                    min($aSpan) == $this->min_span or $sWinningNumber = '';
                }
            } else {
                $sWinningNumber = '';
            }
        }
        return $sWinningNumber;
    }

    /**
     * 返回组选中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberCombin($sWinningNumber)
    {
        return $this->checkCombinValid($sWinningNumber) ? $sWinningNumber : false;
    }

    /**
     *
     * @param type $sWinningNumber
     * @return type
     */
    public function getWnNumberLottoCombin($sWinningNumber)
    {
        return $sWinningNumber;
    }

    /**
     * get middle
     *
     * @param type $sWinningNumber
     * @return string | number
     */
    public function getWnNumberLottoMiddle($sWinningNumber)
    {
        $aBalls = explode($this->splitCharInArea, $sWinningNumber);
        sort($aBalls);
        return $aBalls[2];
    }

    /**
     * 返回和尾中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberSumTail($sWinningNumber)
    {
        return array_sum(str_split($sWinningNumber, 1)) % 10;
    }

    /**
     * 返回特殊中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberSpecial($sWinningNumber)
    {
        $aWnDigitals = array_unique(str_split($sWinningNumber));
        $bWin = count($aWnDigitals) == $this->unique_count;
        if ($bWin && $this->unique_count == 3) {
            $iSpan = max($aWnDigitals) - min($aWnDigitals);
            if (!$bWin = $iSpan == $this->span) {
                if ($iSpan == 9) {
                    rsort($aWnDigitals);
                    $iSpanAB = $aWnDigitals[0] - $aWnDigitals[1];
                    $iSpanBC = $aWnDigitals[1] - $aWnDigitals[2];
                    $iMinSpan = min($iSpanAB, $iSpanBC);
                    $bWin = $iMinSpan == $this->min_span;
                }
            }
        }
        return $bWin ? $this->fixed_number : false;
    }

    /**
     * 返回不定位中奖号码
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberContain($sWinningNumber)
    {
        $aDigitals = str_split($sWinningNumber, 1);
        $aDigitalCount = array_count_values($aDigitals);
        $aUniqueDigitals = array_keys($aDigitalCount);
        $aWnNumber = [];
        if ($this->min_repeat_time) {
            if (count($aDigitalCount) >= $this->choose_count && max($aDigitalCount) >= $this->min_repeat_time) {
                foreach ($aDigitalCount as $iDigital => $iCount) {
                    $iCount < $this->min_repeat_time or $aWnNumber[] = $iDigital;
                }
            }
        } else {
            (count($aDigitalCount) < $this->choose_count) or $aWnNumber = $aUniqueDigitals;
        }
        return $aWnNumber ? $aWnNumber : false;
    }

    /**
     * 返回11选5不定位中奖号码
     * @param string $sWinningNumber
     * @return array
     */
    public function getWnNumberLottoContain($sWinningNumber)
    {
        $aDigitals = explode($this->splitCharInArea, $sWinningNumber);
        $aDigitalCount = array_count_values($aDigitals);
        $aUniqueDigitals = array_keys($aDigitalCount);
        $aWnNumber = [];
        if ($this->min_repeat_time) {
            if (count($aDigitalCount) >= $this->choose_count && max($aDigitalCount) >= $this->min_repeat_time) {
                foreach ($aDigitalCount as $iDigital => $iCount) {
                    $iCount < $this->min_repeat_time or $aWnNumber[] = $iDigital;
                }
            }
        } else {
            (count($aDigitalCount) < $this->choose_count) or $aWnNumber = $aUniqueDigitals;
        }
//        Log::info(var_export($aWnNumber,1));
//        exit;
        return $aWnNumber ? $aWnNumber : false;
    }

    /**
     * 返回11选5直选中奖号码
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberLottoEqual($sWinningNumber)
    {
        return $sWinningNumber;
//        pr($sWinningNumber);
//        exit;
    }

    /**
     * 返回猜单双
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberLottoOddEven($sWinningNumber)
    {
        $aBalls = explode($this->splitCharInArea, $sWinningNumber);
        $iOddCount = 0;
        foreach ($aBalls as $iBall) {
            $iOddCount += $iBall % 2;
        }
        return $iOddCount;
    }

    /**
     * 返回大小单双中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberBsde($sWinningNumber)
    {
        $validNums = explode(',', Series::find($this->series_id)->valid_nums);

        $minBigNumber = intval(count($validNums) / 2);
        $aDigitals = str_split($sWinningNumber, 1);

        $aWnNumbers = [];
        foreach ($aDigitals as $i => $iDigital) {
            $sNumberOfPosition = intval($iDigital >= $minBigNumber); // 大小
            $sNumberOfPosition .= $iDigital % 2 + 2; // 单双
            $aWnNumbers[$i] = $sNumberOfPosition;
        }
        return implode('|', $aWnNumbers);
    }

    /**
     * 返回趣味中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberInterest($sWinningNumber)
    {
        $aDigitals = str_split($sWinningNumber, 1);
        $aWnNumbers = [];
        foreach ($aDigitals as $i => $iDigital) {
            $aWnNumbers[] = $i < $this->special_count ? intval($iDigital > 4) : $iDigital;
        }
        return implode($aWnNumbers);
    }

    /**
     * 返回区间中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberArea($sWinningNumber)
    {
        $aDigitals = str_split($sWinningNumber, 1);
        $aWnNumbers = [];
        foreach ($aDigitals as $i => $iDigital) {
            $aWnNumbers[] = $i < $this->special_count ? floor($iDigital / 2) : $iDigital;
        }
        return implode($aWnNumbers);
    }

    /**
     * 返回合适的计算中奖号码的方法
     * @return string
     */
    public function getWnFunction()
    {
        return 'getWnNumber' . ucfirst(Str::camel($this->wn_function));
    }

    /**
     * 返回合适的检查投注码是否正确与投注注数的方法
     * @param string $sWayFunction
     * @return string
     */
    public function getCheckFunction($sWayFunction)
    {
        return 'count' . $sWayFunction . ucfirst(Str::camel($this->wn_function));
    }

    /**
     * 返回合适的判断是否中奖的方法
     * @param string $sWayFunction
     * @return string
     */
    public function getPrizeFunction($sWayFunction)
    {
        return 'prize' . $sWayFunction . ucfirst(Str::camel($this->wn_function));
    }

    /**
     * 返回直选单式的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeEnumEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aKeys = array_keys($aBetNumbers, $sWnNumber);
        return count($aKeys);
    }

    /**
     * 返回组选单式的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeEnumCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aKeys = array_keys($aBetNumbers, $sWnNumber);
        return count($aKeys);
    }

    /**
     * 返回混合组选的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeMixCombinCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aKeys = array_keys($aBetNumbers, $sWnNumber);
        return count($aKeys);
    }

    /**
     * 返回直选组合的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeMultiSequencingEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($sWnNumber);
//        pr($sBetNumber);
//        exit;
//        $aWnDigitals = str_split($sWnNumber);
//        $aBetDigitals = explode($this->splitChar, $sBetNumber);
//        $iCount       = 1;
//        foreach ($aBetDigitals as $i => $sBetDigitals) {
//            $iHit = preg_match("/{$aWnDigitals[$i]}/", $sBetDigitals);
//            if (!$iCount *= strlen($sBetDigitals)) {
//                break;
//            }
//        }
        $iCount = $this->prizeSeparatedConstitutedEqual($oSeriesWay, $sWnNumber, $sBetNumber);
        // 此处可能不对
        return $iCount;
    }

    /**
     * 返回直选和值的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSumEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iSum = DigitalNumber::getSum($sWnNumber);
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        return intval(in_array($iSum, $aBetNumbers));
    }

    /**
     * 返回三星特殊的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSpecialConstitutedSpecial($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        return preg_match("/$sWnNumber/", $sBetNumber);
    }

    /**
     * 返回直选跨度的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSpanEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iSpan = DigitalNumber::getSpan($sWnNumber);
        $aBetNumbers = str_split($sBetNumber);
        return intval(in_array($iSpan, $aBetNumbers));
    }

    /**
     * 返回组选和值的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSumCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iSum = DigitalNumber::getSum($sWnNumber);
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        return intval(in_array($iSum, $aBetNumbers));
    }

    /**
     * 返回和尾的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSumTailSumTail($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iSumTail = DigitalNumber::getSumTail($sWnNumber);
        $aBetNumbers = str_split($sBetNumber);
        return intval(in_array($iSumTail, $aBetNumbers));
    }

    /**
     * 返回组选包胆的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeNecessaryCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnDigitals = array_unique(str_split($sWnNumber));
        return intval(in_array($sBetNumber, $aWnDigitals));
    }

    /**
     * 返回趣味玩法的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeFunSeparatedConstitutedInterest($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        return $this->prizeSeparatedConstitutedEqual($oSeriesWay, $sWnNumber, $sBetNumber);
    }

    /**
     * 返回区间玩法的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSectionalizedSeparatedConstitutedArea($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        return $this->prizeSeparatedConstitutedEqual($oSeriesWay, $sWnNumber, $sBetNumber);
    }

    /**
     * 返回不定位的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeConstitutedContain($oSeriesWay, $aWnNumber, $sBetNumber)
    {
//        pr($aWnNumber);
//        pr($sBetNumber);
//        exit;
        $aBetDigitals = array_unique(str_split($sBetNumber));
        $aBoth = array_intersect($aWnNumber, $aBetDigitals);
//        pr($aBetDigitals);
//        pr($aBoth);
        $iHitCount = count($aBoth);
        return $iHitCount >= $this->choose_count ? Math::combin($iHitCount, $this->choose_count) : 0;
    }

    /**
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
//    public function prizeSpecialConstitutedCombin($oSeriesWay,$aWnNumber,$sBetNumber){
//        pr($this->name);
//        pr($aWnNumber);
//        pr($sBetNumber);
//        exit;
//        $aBetDigitals = array_unique(str_split($sBetNumber));
//        $aBoth        = array_intersect($aWnNumber,$aBetDigitals);
////        pr($aBetDigitals);
////        pr($aBoth);
//        $iHitCount    = count($aBoth);
//        return $iHitCount >= $this->choose_count ? Math::combin($iHitCount,$this->choose_count) : 0;
//    }

    /**
     * 返回大小单双的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeBigSmallOddEvenBsde($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($sWnNumber);
//        pr($sBetNumber);
//        exit;
        $aWnDigitals = explode($this->splitChar, $sWnNumber);
        $aBetDigitals = explode($this->splitChar, $sBetNumber);
        $iWonCount = 1;
        foreach ($aWnDigitals as $i => $sWnDigitals) {
            $aWnDigitalsOfWei = str_split($sWnDigitals);
            $aBetDigitalsOfWei = str_split($aBetDigitals[$i]);
            $aBoth = array_intersect($aWnDigitalsOfWei, $aBetDigitalsOfWei);
            if (!$iWonCount *= count($aBoth)) {
                break;
            }
        }
        return $iWonCount;
    }

    /**
     * 返回直选复式的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSeparatedConstitutedEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnDigitals = str_split($sWnNumber);
        $p = [];
        foreach ($aWnDigitals as $iDigital) {
            $p[] = '[\d]*' . $iDigital . '[\d]*';
        }
        $pattern = '/^' . implode('\|', $p) . '$/';
        return preg_match($pattern, $sBetNumber);
    }

    /**
     * 返回定位胆的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeMultiOneEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($sWnNumber);
//        pr($sBetNumber);
//        pr(preg_match("/$sWnNumber/",$sBetNumber));
//        exit;
        return preg_match("/$sWnNumber/", $sBetNumber);
    }

    /**
     * 计算双区型组选复式的中奖注数
     * @param string $sNumber
     * @return int
     */
    public function prizeConstitutedDoubleAreaCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumber = explode($this->splitChar, $sBetNumber);
        $aWnDigitals = array_count_values(str_split($sWnNumber));
        $aWnMaxs = array_keys($aWnDigitals, $this->max_repeat_time);
        $aWnMins = array_keys($aWnDigitals, $this->min_repeat_time);
        $aDiffMax = array_diff($aWnMaxs, str_split($aBetNumber[0]));
        $aDiffMin = isset($aBetNumber[1]) ? array_diff($aWnMins, str_split($aBetNumber[1])) : array_diff($aWnMins, str_split($aBetNumber[0]));
        return intval(empty($aDiffMax) && empty($aDiffMin));
    }

    /**
     * 计算双区型组选复式的中奖注数
     * @param string $sNumber
     * @return int
     */
    public function prizeConstitutedForCombin30Combin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        return $this->prizeConstitutedDoubleAreaCombin($oSeriesWay, $sWnNumber, $sBetNumber);
    }

    /**
     * 计算单区型组选复式的中奖注数
     * @param string $sNumber
     * @return int
     */
    public function prizeConstitutedCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        if ($this->max_repeat_time == 1) {
            $aBetDigitals = str_split($sBetNumber);
            $aWnDigitals = str_split($sWnNumber);
            $aDiff = array_diff($aWnDigitals, $aBetDigitals);
            return intval(empty($aDiff));
        } else {
            $aBetNumber = explode($this->splitChar, $sBetNumber);
            $aWnDigitals = array_count_values(str_split($sWnNumber));
            $aWnMaxs = array_keys($aWnDigitals, $this->max_repeat_time);
            $aWnMins = array_keys($aWnDigitals, $this->min_repeat_time);
            $aDiffMax = array_diff($aWnMaxs, str_split($aBetNumber[0]));
            $aDiffMin = isset($aBetNumber[1]) ? array_diff($aWnMins, str_split($aBetNumber[1])) : array_diff($aWnMins, str_split($aBetNumber[0]));
            return intval(empty($aDiffMax) && empty($aDiffMin));
        }
    }

    /**
     * 计算11选5直选单式的中奖注数
     * @param string $sNumber
     * @return int
     */
    private function prizeLottoEqualLottoEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBets = explode($this->splitChar, $sBetNumber);
        return intval(in_array($sWnNumber, $aBets));
    }

    /**
     * 计算11选5任选6中五至任选8中5单式和组选单式的中奖注数
     * @param string $sNumber
     * @return int
     */
    private function prizeLottoEqualLottoCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnBalls = explode($this->splitCharInArea, $sWnNumber);
        $aBets = explode($this->splitChar, $sBetNumber);
        $iCount = 0;
        foreach ($aBets as $sBet) {
            $aTmpBalls = explode($this->splitCharInArea, $sBet);
            $aHitBalls = array_intersect($aTmpBalls, $aWnBalls);
            if ($bWon = count($aHitBalls) == $this->wn_length) {
                $iCount++;
            }
        }
//        pr(intval($bWon));
//        exit;
        return $iCount;
    }

    /**
     * 计算11选5任选1至任选5单式的中奖注数
     * @param string $sNumber
     * @return int
     */
    private function prizeLottoEqualLottoContain($oSeriesWay, $aWnNumber, $sBetNumber)
    {
//        pr($sBetNumber);
        sort($aWnNumber);
//        $sWnNumber = implode($this->splitCharInArea, $aWnNumber);
//        pr($sWnNumber);
        $aBets = explode($this->splitChar, $sBetNumber);
        $iCount = 0;
//        pr($aWnNumber);
//        pr($aBets);
        foreach ($aBets as $sBet) {
            $aBetBalls = explode($this->splitCharInArea, $sBet);
            $aHits = array_intersect($aBetBalls, $aWnNumber);
            $iHitNumber = count($aHits);
            $iCount += intval(count($aHits) == $this->choose_count);
        }
//        pr($iCount);
//        exit;
        return $iCount;
    }

    /**
     * 计算11选5任选一至五复式的中奖注数
     * @param string $sNumber
     * @return int
     */
    private function prizeLottoConstitutedLottoContain($oSeriesWay, $aWnNumber, $sBetNumber)
    {
//        pr($oSeriesWay->name);
//        pr($aWnNumber);
//        pr($sBetNumber);
        $iHitCount = $this->_getHitNumbersOfLotto($sBetNumber, $aWnNumber, $iBetBallCount);
        return Math::combin($iHitCount, $this->choose_count);
    }

    /**
     * 计算11选5任选五至八复式的中奖注数
     * @param string $sNumber
     * @return int
     */
    private function prizeLottoConstitutedLottoCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iHitCount = $this->_getHitNumbersOfLotto($sBetNumber, $sWnNumber, $iBetBallCount);
        if ($iHitCount < $this->wn_length) return 0;
        $iNeedOtherBallCount = $this->buy_length - $this->wn_length;
        $iUnHitCount = $iBetBallCount - $iHitCount;
        return Math::combin($iUnHitCount, $iNeedOtherBallCount);
    }

    /**
     * 计算11选5任选五至八复式的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    private function prizeLottoSeparatedConstitutedLottoEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($oSeriesWay->name);
//        pr($sWnNumber);
//        pr($sBetNumber);
        $aWnBalls = explode($this->splitCharInArea, $sWnNumber);
        $aBetBalls = explode($this->splitChar, $sBetNumber);
        $iHitPosCount = 0;
        if (count($aWnBalls) != count($aBetBalls)) return 0;
        foreach ($aBetBalls as $i => $sBetNumberOfPos) {
            $aBetBallsOfPos = explode($this->splitCharInArea, $sBetNumberOfPos);
            if (!in_array($aWnBalls[$i], $aBetBallsOfPos)) {
                break;
            }
            $iHitPosCount++;
        }
//        pr($iHitPosCount);
//        pr($this->getAttributes());
        return intval($iHitPosCount == $this->wn_length);
    }

    /**
     * 计算11选5定单双的中奖数字
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    private function prizeLottoConstitutedLottoOddEven($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($sWnNumber);
//        pr($sBetNumber);
        $aBetDigitals = explode($this->splitCharInArea, $sBetNumber);
        return intval(in_array($sWnNumber, $aBetDigitals));
//        exit;
    }

    /**
     * 计算11选5任选二至五胆胆拖的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    private function prizeLottoNecessaryConstitutedLottoContain($oSeriesWay, $aWnNumber, $sBetNumber)
    {
//        pr($oSeriesWay->name);
//        pr($aWnNumber);
//        pr($sBetNumber);
//        pr($this->getAttributes());exit;

        list($sBetNecessaried, $sBetConstituted) = explode($this->splitChar, $sBetNumber);
        $aBetNecessaried = explode($this->splitCharInArea, $sBetNecessaried);
        $aHitNecessaried = array_intersect($aBetNecessaried, $aWnNumber);
        $iHitNessariedCount = count($aHitNecessaried);
        if ($iHitNessariedCount != count($aBetNecessaried)) return 0;
        $iNeedOfNecessariedCount = $this->wn_length - $iHitNessariedCount;
        if ($iNeedOfNecessariedCount == 0) return 1;
        $aBetConstituted = explode($this->splitCharInArea, $sBetConstituted);
        $aHitConstituted = array_intersect($aBetConstituted, $aWnNumber);
        $iHitConstitutedCount = count($aHitConstituted);
        if ($iHitConstitutedCount < $iNeedOfNecessariedCount) return 0;

        return Math::combin($iHitConstitutedCount, $iNeedOfNecessariedCount);
    }

    /**
     * 计算11选5任选六至八胆胆拖的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    private function prizeLottoNecessaryConstitutedLottoCombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($oSeriesWay->name);
//        pr($sWnNumber);
//        pr($sBetNumber);
//        pr($this->getAttributes());
//        exit;
        $aWnNumber = explode($this->splitCharInArea, $sWnNumber);
        list($sBetNecessaried, $sBetConstituted) = explode($this->splitChar, $sBetNumber);
        $aBetNecessaried = explode($this->splitCharInArea, $sBetNecessaried);
        $aHitNecessaried = array_intersect($aBetNecessaried, $aWnNumber);
        $iBetNecessariedCount = count($aBetNecessaried);

        $iHitNessariedCount = count($aHitNecessaried);
//        if (count($aHitNecessaried) < $this->wn_length) return 0;
//        pr($iHitNessariedCount);
        $iNeedOfBetBallsCount = $this->buy_length - $iBetNecessariedCount;        // 凑足一注投注码还需要的复式码个数
//        if (!$iNeedOfNecessariedCount == 0) return 1;
        $aBetConstituted = explode($this->splitCharInArea, $sBetConstituted);
        $iBetConstitutedCount = count($aBetConstituted);
        if ($iNeedOfBetBallsCount > $iBetConstitutedCount) return 0;                 // 如果复式码个数不足, 则不中奖

        $aHitConstituted = array_intersect($aBetConstituted, $aWnNumber);        // 求出中得的复式码个数
        $iHitConstitutedCount = count($aHitConstituted);
        if ($iBetNecessariedCount + $iHitConstitutedCount > $this->buy_length) return 0;                 // 如果胆码个数+中得的复式码个数,则不中奖

        $iNonHitConstitutedCount = $iBetConstitutedCount - $iHitConstitutedCount;     // 求出未中得的复式码个数
//        pr($iHitConstitutedCount);
//        pr($iHitNessariedCount);
//        pr($iHitConstitutedCount + $iHitNessariedCount);
//        pr($this->wn_length);
//        exit;
        if ($iHitConstitutedCount + $iHitNessariedCount < $this->wn_length) return 0;
//        pr($iHitConstitutedCount);
//        pr($iNeedOfBetBallsCount);
//        exit;
//        if ($iHitConstitutedCount < $iNeedOfNecessariedCount) return 0;
        $iNeedNonHitCount = $iNeedOfBetBallsCount - $iHitConstitutedCount;
//        pr($iNeedNonHitCount);
//        $iCount = Math::combin($iNonHitConstitutedCount, $iNeedNonHitCount);
//        pr($iCount);
//        exit;
        return Math::combin($iNonHitConstitutedCount, $iNeedNonHitCount);
    }

    /**
     * 计算11选5定位胆的中奖注数
     *
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    private function prizeLottoMultiOneLottoEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($this->name);
//        pr($sWnNumber);
//        pr($sBetNumber);
//        pr($this->getAttributes());
        $aBetBalls = explode($this->splitCharInArea, $sBetNumber);
//        pr($aBetBalls);
        return intval(in_array($sWnNumber, $aBetBalls));
    }

    /**
     * 11选5猜中位的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    private function prizeLottoConstitutedLottoMiddle($oSeriesWay, $sWnNumber, $sBetNumber)
    {
//        pr($this->name);
//        pr($sWnNumber);
//        pr($sBetNumber);
        $aBetBalls = explode($this->splitCharInArea, $sBetNumber);
        return intval(in_array($sWnNumber, $aBetBalls));
    }

    private function _getHitNumbersOfLotto($sBetNumber, $mWnNumber, & $iBetBallCount)
    {
        $aWnBalls = is_array($mWnNumber) ? $mWnNumber : explode($this->splitCharInArea, $mWnNumber);
        $aBetBalls = explode($this->splitCharInArea, $sBetNumber);
        $iBetBallCount = count($aBetBalls);
        return count(array_intersect($aBetBalls, $aWnBalls));
    }

    /**
     * 检查直选单式投注号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkEqualValid($sNumber)
    {
        $pattern = '/^[\d]{' . $this->digital_count . '}$/';
        return preg_match($pattern, $sNumber);
    }

    /**
     * 检查直选单式投注号码是否合法,返回注数.
     * @param string $sNumber
     * @return int
     */
    public function countEnumEqual(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aValidNumbers = [];
        $iCount = 0;
        foreach ($aNumbers as $sSNumber) {
            !$this->checkEqualValid($sSNumber) or $aValidNumbers[] = $sSNumber;
        }
        $aValidNumbers = array_unique($aValidNumbers);
        $sNumber = implode($this->splitChar, $aValidNumbers);
        return count($aValidNumbers);
    }

    /**
     * 返回直选复式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSeparatedConstitutedEqual(& $sNumber)
    {
        return $this->_countSeparatedConstituted($sNumber);
    }

    /**
     * 返回直选跨度的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSpanEqual(& $sNumber)
    {
        $countArray = [
            2 => [10, 18, 16, 14, 12, 10, 8, 6, 4, 2],
            3 => [10, 54, 96, 126, 144, 150, 144, 126, 96, 54],
        ];
        $validDigitals = [2, 3];
        if (!in_array($this->digital_count, $validDigitals) || !isset($countArray[$this->digital_count])) {
            return 0;
        }
        $aNumbers = str_split($sNumber);
        $iCount = 0;
        foreach ($aNumbers as $iSpan) {
            $iCount += isset($countArray[$this->digital_count][$iSpan]) ? $countArray[$this->digital_count][$iSpan] : 0;
        }
        return $iCount;
    }

    /**
     * 返回直选和值的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSumEqual(& $sNumber)
    {
        $countArray = [
            2 => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
            3 => [1, 3, 6, 10, 15, 21, 28, 36, 45, 55, 63, 69, 73, 75, 75, 73, 69, 63, 55, 45, 36, 28, 21, 15, 10, 6, 3, 1],
        ];
        $validDigitals = [2, 3];
        if (!in_array($this->digital_count, $validDigitals) || !isset($countArray[$this->digital_count])) {
            return 0;
        }
        $aNumbers = explode($this->splitChar, $sNumber);
        $iCount = 0;
        foreach ($aNumbers as $iSum) {
            $iCount += isset($countArray[$this->digital_count][$iSum]) ? $countArray[$this->digital_count][$iSum] : 0;
        }
        return $iCount;
    }

    public function countSumCombin(& $sNumber)
    {
        $countArray = [
            2 => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 4, 4, 3, 3, 2, 2, 1, 1, 0],
            3 => [0, 1, 2, 2, 4, 5, 6, 8, 10, 11, 13, 14, 14, 15, 15, 14, 14, 13, 11, 10, 8, 6, 5, 4, 2, 2, 1, 0]
        ];
        $validDigitals = [2, 3];
        if (!in_array($this->digital_count, $validDigitals) || !isset($countArray[$this->digital_count])) {
            return 0;
        }
        $aNumbers = explode($this->splitChar, $sNumber);
        $iCount = 0;
        foreach ($aNumbers as $iSum) {
            $iCount += isset($countArray[$this->digital_count][$iSum]) ? $countArray[$this->digital_count][$iSum] : 0;
        }
        return $iCount;
    }

    /**
     * 返回不定位的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countConstitutedContain(& $sNumber)
    {
        $aDigitals = array_unique(str_split($sNumber));
        $sNumber = implode($aDigitals);
        return Math::combin(count($aDigitals), $this->choose_count);
    }

    /**
     * 返回大小单双的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countBigSmallOddEvenBsde(& $sNumber)
    {
        return $this->_countSeparatedConstituted($sNumber);
    }

    /**
     * 返回趣味玩法的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countFunSeparatedConstitutedInterest(& $sNumber)
    {
        $aValidNums = explode($this->splitChar, $this->valid_nums);
        return $this->_countSeparatedConstituted($sNumber, $aValidNums);
    }

    /**
     * 返回区间玩法的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSectionalizedSeparatedConstitutedArea(& $sNumber)
    {
        $aValidNums = explode($this->splitChar, $this->valid_nums);
        return $this->_countSeparatedConstituted($sNumber, $aValidNums);
    }

    /**
     * 返回直选定位复式的注数
     * @param string $sNumber
     * @return int
     */
    private function _countSeparatedConstituted(& $sNumber, $mValidNums = null)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aBetNumbers = [];
        if ($mValidNums) {
            if (!is_array($mValidNums)) {
                $mValidNums = array_fill(0, $this->digital_count, $mValidNums);
            }
        } else {
            $mValidNums = array_fill(0, $this->digital_count, $this->valid_nums);
        }

        $iCount = 1;
        foreach ($aNumbers as $i => $sPartNumber) {
            if (!preg_match('/^[' . $mValidNums[$i] . ']+$/', $sPartNumber)) {
                return 0;
            }
            $aDigitals = array_unique(str_split($sPartNumber));
            sort($aDigitals);
            $iCount *= count($aDigitals);
            $aBetNumbers[] = implode($aDigitals);
        }
        $sNumber = implode($this->splitChar, $aBetNumbers);

        return $iCount;
    }

    /**
     * 返回定位胆组合的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countMultiOneEqual(& $sNumber)
    {
        //if(!preg_match('/^[0-9]{1,10}\|[0-9]{1,10}\|[0-9]{1,10}\|[0-9]{1,10}\|[0-9]{1,10}$/',$sNumber)) return 0;
        $aNumbers = explode($this->splitChar, $sNumber);
        if (!in_array(count($aNumbers), [3, 5])) return 0;

        $iCount = 0;
        $aBetNumbers = [];
        foreach ($aNumbers as $sNumberOfPos) {
            if (!strlen($sNumberOfPos)) {
                $aBetNumbers[] = '';
                continue;
            }
            $aNums = array_unique(str_split($sNumberOfPos));
            $iCount += count(array_count_values($aNums));
            sort($aNums);
            $aBetNumbers[] = implode($aNums);
        }
        $sNumber = implode($this->splitChar, $aBetNumbers);
        return $iCount;
    }

    /**
     * 返回组选包胆的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countNecessaryCombin(& $sNumber)
    {
        $countArray = [
            2 => 9,
            3 => 54,
        ];
        $validDigitals = [2, 3];
        if (!in_array($this->digital_count, $validDigitals)) {
            return 0;
        }
        if (isset($countArray[$this->digital_count])) {
            $aNetNumbers = array_unique(explode($this->splitChar, $sNumber));
            $iNumberCount = count($aNetNumbers);
            $iCount = $iNumberCount * $countArray[$this->digital_count];
            $sNumber = implode($aNetNumbers);
        } else {
            $iCount = 0;
        }
        return $iCount;
    }

    /**
     * 返回组选单式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countEnumCombin(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aValidNumbers = [];
        $iCount = 0;
        foreach ($aNumbers as $sSNumber) {
            !$this->checkCombinValid($sSNumber) or $aValidNumbers[] = $sSNumber;
        }
        $aValidNumbers = array_unique($aValidNumbers);
        sort($aValidNumbers);
        $sNumber = implode($this->splitChar, $aValidNumbers);
        return count($aValidNumbers);
    }

    /**
     * 返回组选单式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countMixCombinCombin(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aValidNumbers = [];
        $iCount = 0;
        foreach ($aNumbers as $sSNumber) {
            !DigitalNumber::getSpan($sSNumber) or $aValidNumbers[] = DigitalNumber::getCombinNumber($sSNumber);
        }
        $aValidNumbers = array_unique($aValidNumbers);
        sort($aValidNumbers);
        $sNumber = implode($this->splitChar, $aValidNumbers);
        return count($aValidNumbers);
    }

    /**
     * 返回和尾复式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSumTailSumTail(& $sNumber)
    {
        if (preg_match('/^[\d]+$/', $sNumber)) {
            $aDigitals = array_unique(str_split($sNumber));
            $sNumber = implode($aDigitals);
            $iCount = count($aDigitals);
        } else {
            $iCount = 0;
        }
        return $iCount;
    }

    /**
     * 返回五星组选30复式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countConstitutedForCombin30Combin(& $sNumber)
    {
        $aNums = explode($this->splitChar, $sNumber);
        $aValidNums = [];
        foreach ($aNums as $k => $sNums) {
            $aNumOfArea[$k] = str_split($sNums, 1);
            sort($aNumOfArea[$k]);
            $aValidNums[$k] = implode($aNumOfArea[$k]);
        }
        $aRepeatN = array_intersect($aNumOfArea[1], $aNumOfArea[0]);
        $iRepeatCountN = count($aRepeatN);
        $iNonRepeatCountN = count($aNumOfArea[1]) - $iRepeatCountN;
        $iCountM = count($aNumOfArea[0]);
        $iCount = Math::combin($iRepeatCountN, 1) * Math::combin($iCountM - 1, 2) + Math::combin($iNonRepeatCountN, 1) * Math::combin($iCountM, 2);
        $sNumber = implode($this->splitChar, $aValidNums);
        return $iCount;
    }

    /**
     * 返回双区组选复式的投注码数量，不含五星组选30
     * @param string $sNumber
     * @return int
     */
    public function countConstitutedDoubleAreaCombin(& $sNumber)
    {
        $aNums = explode($this->splitChar, $sNumber);
        $aValidNums = [];
        foreach ($aNums as $k => $sNums) {
            $aNumOfArea[$k] = str_split($sNums, 1);
            sort($aNumOfArea[$k]);
            $aValidNums[$k] = implode($aNumOfArea[$k]);
        }

        $iNeedNumCountOfN = ($this->digital_count - $this->max_repeat_time) / $this->min_repeat_time;

        $aRepeatM = array_intersect($aNumOfArea[0], $aNumOfArea[1]);
        $iRepeatCountM = count($aRepeatM);
        $iNonRepeatCountM = count($aNumOfArea[0]) - $iRepeatCountM;
        $iCountN = count($aNumOfArea[1]);
        $iCount = Math::combin($iRepeatCountM, 1) * Math::combin($iCountN - 1, $iNeedNumCountOfN) + Math::combin($iNonRepeatCountM, 1) * Math::combin($iCountN, $iNeedNumCountOfN);

        $sNumber = implode($this->splitChar, $aValidNums);
        return $iCount;
    }

    /**
     * 返回单区组选复式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSpecialConstitutedSpecial(& $sNumber)
    {
        $pattern = '/^[012]+$/';
        if (preg_match($pattern, $sNumber)) {
            $aNumbers = array_unique(str_split($sNumber));
            sort($aNumbers);
            $sNumber = implode($aNumbers);
            $iCount = count($aNumbers);
        } else {
            $iCount = 0;
        }
        return $iCount;
    }

    /**
     * 返回单区组选复式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countConstitutedCombin(& $sNumber)
    {
        $aDigitals = array_unique(str_split($sNumber));
        $iDigitalCount = count($aDigitals);
        $iCount = Math::combin($iDigitalCount, $this->unique_count);
        if ($this->digital_count == 3 && $this->unique_count == 2) {
            $iCount *= 2;
        }
        sort($aDigitals);
        $sNumber = implode($aDigitals);
        return $iCount;
    }

    /**
     * 11选5:前三直选复式注数计算
     * @param string & $sNumber
     * @return int
     */
    public function countLottoSeparatedConstitutedLottoEqual(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aChoosedBalls = $aInterSectionCounts = $aChoosedCounts = [];
        foreach ($aNumbers as $i => $sNumberOfPos) {
            $aChoosedBalls[$i] = explode(' ', $sNumberOfPos);
            sort($aChoosedBalls[$i]);
            $aNumbers[$i] = implode(' ', $aChoosedBalls[$i]);
        }
        $sNumber = implode($this->splitChar, $aNumbers);
        unset($aNumbers, $sNumberOfPos);

        $iPosCount = count($aChoosedBalls);
        $aAllChoosedBalls = $aChoosedBalls[0];
        switch ($iPosCount) {
            case 3:
                $a = 1;
                for ($i = 0; $i < $iPosCount; $i++) {
                    $a *= $aChoosedCounts[$i] = count($aChoosedBalls[$i]);
                    if ($i + 1 < $iPosCount) {
                        $aAllChoosedBalls = array_intersect($aAllChoosedBalls, $aChoosedBalls[$i + 1]);
                        $aInterSectionCounts[$i . ($i + 1)] = count(array_intersect($aChoosedBalls[$i], $aChoosedBalls[$i + 1]));
                    }
                    if ($i + 2 < $iPosCount) {
                        $aInterSectionCounts[$i . ($i + 2)] = count(array_intersect($aChoosedBalls[$i], $aChoosedBalls[$i + 2]));
                    }
                }
                $c = count($aAllChoosedBalls);
                $b = $aInterSectionCounts['01'] * $aChoosedCounts[2] + $aInterSectionCounts['02'] * $aChoosedCounts[1] + $aInterSectionCounts['12'] * $aChoosedCounts[0];
                $iCount = $a - $b + $c * 2;
                break;
            case 2:
                $iCount = count($aChoosedBalls[0]) * count($aChoosedBalls[1]) - count(array_intersect($aChoosedBalls[0], $aChoosedBalls[1]));
                break;
        }
//        pr($iCount);
//        exit;
        return $iCount;
//        计算总注数	a*b*c-(D12*c+D13*b+D23*a)+T123*2
    }

    /**
     * 11选5定单双注数计算
     * @param string $sNumber
     */
    public function countLottoConstitutedLottoOddEven(& $sNumber)
    {
        $aDigitals = array_unique(explode($this->splitCharInArea, $sNumber));
        if (max($aDigitals) > 5 || min($aDigitals) < 0) {
            return 0;
        }
        $sNumber = implode($this->splitCharInArea, $aDigitals);
        return count($aDigitals);
    }

    /**
     * 11选5:组选复式注数计算
     * @param string & $sNumber
     * @return int
     */
    public function countLottoConstitutedLottoCombin(& $sNumber)
    {
        $aChoosedNumbers = array_unique(explode($this->splitCharInArea, $sNumber));
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aChoosedNumbers as $i => $sChoosedNumber) {
            if ($sChoosedNumber < $iMin || $sChoosedNumber > $iMax) {
                return 0;
            }
            $aChoosedNumbers[$i] = str_pad($sChoosedNumber, 2, '0', STR_PAD_LEFT);
        }
        sort($aChoosedNumbers);
        $sNumber = implode($this->splitCharInArea, $aChoosedNumbers);
        $iChoosedCount = count($aChoosedNumbers);
        return Math::combin($iChoosedCount, $this->buy_length);
    }

    /**
     * 11选5:猜中位注数计算
     * @param string & $sNumber
     * @return int
     */
    private function countLottoConstitutedLottoMiddle(& $sNumber)
    {
        $aChoosedNumbers = array_unique(explode($this->splitCharInArea, $sNumber));
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aChoosedNumbers as $i => $sChoosedNumber) {
            if ($sChoosedNumber < $iMin || $sChoosedNumber > $iMax) {
                return 0;
            }
            $aChoosedNumbers[$i] = str_pad($sChoosedNumber, 2, '0', STR_PAD_LEFT);
        }
        sort($aChoosedNumbers);
        $sNumber = implode($this->splitCharInArea, $aChoosedNumbers);
        return count($aChoosedNumbers);
    }

    /**
     * 返回11选5任选包胆的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countLottoNecessaryConstitutedLottoContain(& $sNumber)
    {
        @list($sNecessary, $sConstituted) = explode($this->splitChar, $sNumber);
        if (!isset($sConstituted)) {
            return 0;
        }
        $aNecessaries = array_unique(explode($this->splitCharInArea, $sNecessary));
        $aConstituteds = array_unique(explode($this->splitCharInArea, $sConstituted));
        if (array_intersect($aNecessaries, $aConstituteds)) {
            return 0;
        }
        $iNecessaryCount = count($aNecessaries);
        $iConstitutedCount = count($aConstituteds);
        if ($iNecessaryCount >= $this->buy_length) {
            return 0;
        }
        $iNeedConstitutedCount = $this->buy_length - $iNecessaryCount;
        $iCount = Math::combin($iConstitutedCount, $iNeedConstitutedCount);
        $sNumber = implode($this->splitCharInArea, $aNecessaries) . $this->splitChar . implode($this->splitCharInArea, $aConstituteds);
        return $iCount;
    }

    /**
     * 11选5:任选单式注数计算
     * @param string & $sNumber
     * @return int
     */
    public function countLottoEqualLottoContain(& $sNumber)
    {
//        pr($sNumber);
        $aBetNumbers = explode($this->splitChar, $sNumber);
//        pr($this->attributes);
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        $iCount = 0;
        $aTrueNumbers = [];
        foreach ($aBetNumbers as $sBetNumber) {
            if (!$this->checkLottoEqualValid($sBetNumber, $iMin, $iMax, true)) {
                return 0;
            }
            $aTrueNumbers[] = $sBetNumber;
            $iCount++;
        }
        $sNumber = implode($this->splitChar, $aTrueNumbers);
        return $iCount;
    }

//    private function checkLotto(& $sBetNumber,$iMin,$iMax){
//        $aBalls = array_unique(explode($this->splitCharInArea,$sBetNumber));
//        sort($aBalls);
//        if (!$bValid = count($aBalls) == $this->choose_count){
//            return false;
//        }
//        $aTrueBalls = [];
//        foreach($aBalls as $iBall){
//            if (!$bValid = $iBall >= $iMin && $iBall <= $iMax){
//                break;
//            }
//            $aTrueBalls[] = str_pad($iBall,2,'0',STR_PAD_LEFT);
//        }
//        $sBetNumber = implode($this->splitCharInArea, $aTrueBalls);
//        return $bValid;
//    }
    /**
     * 返回11选5不定位的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countLottoConstitutedLottoContain(& $sNumber)
    {
        $aDigitals = array_unique(explode($this->splitCharInArea, $sNumber));
        $sNumber = implode($this->splitCharInArea, $aDigitals);
        return Math::combin(count($aDigitals), $this->choose_count);
    }

    /**
     * 返回11选5定位胆组合的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countLottoMultiOneLottoEqual(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $iCount = 0;
        $aBetNumbers = [];
        foreach ($aNumbers as $sNumberOfPos) {
            if (!strlen($sNumberOfPos)) {
                $aBetNumbers[] = '';
                continue;
            }
            $aNums = array_unique(explode($this->splitCharInArea, $sNumberOfPos));
            $iCount += count(array_count_values($aNums));
            sort($aNums);
            $aBetNumbers[] = implode($this->splitCharInArea, $aNums);
        }
        $sNumber = implode($this->splitChar, $aBetNumbers);
        return $iCount;
    }

    /**
     * 11选5:直选单式注数计算
     * @param string & $sNumber
     * @return int
     */
    public function countLottoEqualLottoEqual(& $sNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sNumber);
        $iCount = 0;
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aBetNumbers as $i => $sEnumNumber) {
            if (!$this->checkLottoEqualValid($sEnumNumber, $iMin, $iMax)) {
                return 0;
            }
            $aBetNumbers[$i] = $sEnumNumber;
            $iCount++;
        }
        $sNumber = implode($this->splitChar, $aBetNumbers);
        return $iCount;
    }

    /**
     * 11选5:组选单式注数计算
     * @param string & $sNumber
     * @return int
     */
    public function countLottoEqualLottoCombin(& $sNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sNumber);
        $iCount = 0;
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aBetNumbers as $i => $sEnumNumber) {
            if (!$this->checkLottoEqualValid($sEnumNumber, $iMin, $iMax, true)) {
                return 0;
            }
            $aBetNumbers[$i] = $sEnumNumber;
            $iCount++;
        }
        $sNumber = implode($this->splitChar, $aBetNumbers);
        return $iCount;
    }

    /**
     * 11选5组选胆拖注数计算
     * @param string $sNumber
     * @return int
     */
    public function countLottoNecessaryConstitutedLottoCombin(& $sNumber)
    {
        return $this->countLottoNecessaryConstitutedLottoContain($sNumber);
//        $aArea = explode($this->splitChar, $sNumber);
//        if (count($aArea) != 2){
//            return 0;
//        }
//        $aNecessaries = array_unique(explode($this->splitCharInArea,$aArea[0]));
//        $aConstitues = array_unique(explode($this->splitCharInArea,$aArea[1]));
//        $iNecessary = count($aNecessaries);
//        $iConstitue = count($aConstitues);
//        if ($iNecessary >= $this->digital_count || $iNecessary + $iConstitue < $this->digital_count){
//            return 0;
//        }
//        $aBoth = array_intersect($aNecessaries,$aConstitues);
//        if (count($aBoth)){
//            return 0;
//        }
//        list($iMin,$iMax) = explode('-',$this->valid_nums);
//        foreach($aNecessaries as $i => $iDigital){
//            if ($iDigital < $iMin || $iDigital > $iMax){
//                return 0;
//            }
//        }
//        foreach($aConstitues as $i => $iDigital){
//            if ($iDigital < $iMin || $iDigital > $iMax){
//                return 0;
//            }
//        }
//        sort($aNecessaries);
//        sort($aConstitues);
//        $sNumber = implode($this->splitCharInArea,$aNecessaries) . $this->splitChar . implode($this->splitCharInArea,$aConstitues);
//        return Math::combin($iConstitue,$this->digital_count - $iNecessary);
    }

    /**
     * 检查乐透型直选单式码是否合法并格式化
     * @param type $sNumber
     * @param type $iMin
     * @param type $iMax
     * @return int
     */
    public function checkLottoEqualValid(& $sNumber, $iMin, $iMax, $bCombin = false)
    {
        $aNumber = explode($this->splitCharInArea, $sNumber);
        foreach ($aNumber as $d) {
            if (strlen($d) != 2) {
                return 0;
                break;
            }
        }
        $aDigitals = array_unique(explode($this->splitCharInArea, $sNumber));

        if (count($aDigitals) != $this->buy_length) {
            return 0;
        }
        foreach ($aDigitals as $i => $iDigital) {
            if ($iDigital < $iMin || $iDigital > $iMax) {
                return 0;
            }
            $aDigitals[$i] = str_pad($iDigital, 2, '0', STR_PAD_LEFT);
        }
        !$bCombin or sort($aDigitals);
        $sNumber = implode($this->splitCharInArea, $aDigitals);
        return 1;
    }

    /**
     * 检查组选单式号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkCombinValid(& $sNumber)
    {
        $aDigitals = str_split($sNumber, 1);
        $aDigitalCount = array_count_values($aDigitals);
        $iMaxRepeatCount = max($aDigitalCount);
        $iMinRepeatCount = min($aDigitalCount);
        $iUniqueCount = count($aDigitalCount);
        $iCount = 0;
        if ($iUniqueCount == $this->unique_count && $iMaxRepeatCount == $this->max_repeat_time && $iMinRepeatCount == $this->min_repeat_time) {
            $aUniqueDigitals = array_keys($aDigitalCount);
            sort($aDigitals);
            $sNumber = implode($aDigitals);
            return true;
        }
        return false;
    }


    /**
     * 检查大小单双号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkBsde(& $sNumber)
    {
        $aParts = explode($this->splitChar, $sNumber);
        if (count($aParts) != $this->digital_count) {
            return false;
        }
        $aAllowDigitals = [0, 1, 2, 3];
        $aNumberOfParts = [];
        foreach ($aParts as $sPartNumber) {
            $aDigitals = array_unique(str_split($sPartNumber, 1));
            $aDiff = array_diff($aDigitals, $aAllowDigitals);
            if (!empty($aDiff)) {
                return false;
            }
            sort($aDigitals);
            $aNumberOfParts = $aDigitals;
        }
        $sNumber = implode($this->splitChar, $aNumberOfParts);
        return true;
    }

    /**
     * 检查趣味号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkInterest(& $sNumber)
    {
        return $this->_checkInterestAndArea($sNumber, true);
    }

    /**
     * 检查区间号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkArea(& $sNumber)
    {
        return $this->_checkInterestAndArea($sNumber, false);
    }

    /**
     * 检查不定位号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkContain(& $sNumber)
    {
        return $this->_checkOriginalSingArea($sNumber);
    }

    /**
     * 检查和尾号码是否合法
     * @param string $sNumber
     * @return bool
     */
    public function checkSumTail(& $sNumber)
    {
        return $this->_checkOriginalSingArea($sNumber);
    }

    /**
     * 检查跨度是否合法
     * @param $sNumber
     * @return string
     */
    public function checkSpan(& $sNumber)
    {
        if (!is_null($this->span)) {
            $aDigitals = str_split($sNumber, 1);
            if ($this->min_span && (max($aDigitals) - min($aDigitals)) == $this->span) {
                $aSpan = [];
                for ($i = 1; $i < count($aDigitals); $aSpan[] = abs($aDigitals[$i] - $aDigitals[$i++ - 1])) ;
                min($aSpan) == $this->min_span or $sNumber = '';
            } else {
                $sNumber = '';
            }
        }

        return $sNumber ? true : false;
    }

    /**
     * 检查区间和趣味玩法投注码的合法性
     * @param string $sNumber
     * @param bool $bInterest
     * @return boolean
     */
    private function _checkInterestAndArea(& $sNumber, $bInterest)
    {
        $aParts = explode($this->splitChar, $sNumber);
        $aWnNumbers = [];
        $aPatterns = [
            0 => '/^[\d]+$/',
            1 => $bInterest ? '/^[01]+$/' : '/^[01234]+$/',
        ];
        foreach ($aParts as $i => $sPartNumber) {
            $sPatternKey = intval($i < $this->special_count);
            if (!preg_match($aPatterns[$sPatternKey], $sPartNumber)) {
                return false;
            }
            $aWnNumbers[] = implode(array_unique(str_split($sPartNumber)));
        }
        $sNumber = implode($this->splitChar, $aWnNumbers);
        return true;
    }

    /**
     * 检查单区复式投注码的合法性
     * @param string $sNumber
     * @return boolean
     */
    private function _checkOriginalSingArea(& $sNumber)
    {
        if (!preg_match('/^[\d]+$/', $sNumber)) {
            return false;
        }
        $aParts = array_unique(str_split($sNumber));
        sort($aParts);
        $sNumber = implode($aParts);
        return true;
    }

    /**
     * 按offset来截取中奖号码
     * @param string $sFullWinningNumber
     * @param int $iOffset
     * @return string
     */
    public function getWnNumber($sFullWinningNumber, $iOffset)
    {
        switch ($this->lottery_type) {
            case Lottery::LOTTERY_TYPE_DIGITAL:
                $this->init();
                if (strpos($sFullWinningNumber, $this->splitChar)) {
                    $sWnNumber = $sFullWinningNumber;
                } elseif (strpos($sFullWinningNumber, $this->splitCharInArea)) {
                    $aBalls = explode($this->splitCharInArea, $sFullWinningNumber);
                    $aNeedBalls = [];
                    for ($i = $iOffset, $j = 0; $j < $this->digital_count; $aNeedBalls[$j++] = $aBalls[$i++]) ;
                    $sWnNumber = implode($this->splitCharInArea, $aNeedBalls);
                } else if(strpos($sFullWinningNumber, $this->splitCharSumDigital)){
                    $sWnNumber = explode($this->splitCharSumDigital,$sFullWinningNumber);
                    $sWnNumber = array_slice($sWnNumber, intval($iOffset),$this->digital_count);
                }else{
                    $sWnNumber = substr($sFullWinningNumber, intval($iOffset), $this->digital_count);
                }
                break;
            case Lottery::LOTTERY_TYPE_LOTTO:
                $this->init();
                $aBalls = explode($this->splitCharInArea, $sFullWinningNumber);
                $aNeedBalls = [];
                for ($i = $iOffset, $j = 0; $j < $this->digital_count; $aNeedBalls[$j++] = $aBalls[$i++]) ;
                $sWnNumber = implode($this->splitCharInArea, $aNeedBalls);
                break;
        }
        return $sWnNumber;
    }

    /**
     * 获取奖级列表,键为规则,值为奖级
     * @return array
     */
    public function getPrizeLevels()
    {
        $aConditions = [
            'basic_method_id' => ['=', $this->id]
        ];
        $oLevels = PrizeLevel::doWhere($aConditions)->orderBy('level', 'asc')->get(['id', 'level', 'rule']);
        $aLevels = [];
        foreach ($oLevels as $oLevel) {
            $a = explode(',', $oLevel->rule);
            foreach ($a as $sRule) {
                $aLevels[$sRule] = $oLevel->level;
            }
        }
        return $aLevels;
    }

    /*************************任选******************************/
    /**
     * 返回任选的中奖号码数字。
     * @param type $sWinningNumber
     * @return type
     */
    public function getWnNumberOptionalequal($sWinningNumber)
    {
        return $sWinningNumber;
    }

    public function countMixCombinOptionalcombin(& $sNumber)
    {

        $aNumber = explode($this->splitChar, $sNumber);
        foreach ($aNumber as $i => $sPartNumber) {
            if (!preg_match('/^[' . $this->valid_nums . ']+$/', $sPartNumber)) {
                return 0;
            }
            // if(strlen($sPartNumber)!=$this->digital_count) return 0;
            // if(!$this->checkNumbers(str_split($sPartNumber))) return 0;
            preg_match_all('/[\d]{1}/U', $sPartNumber, $out, PREG_SET_ORDER);
            if ($this->choose_count != count($out)) return 0;
        }

        return count($aNumber) * Math::combin(count(str_split($this->sPosition)), $this->choose_count);

    }


    /**
     * 返回任选复式(不重号)的投注码数量
     * @param type $sNumber
     * @return int
     */
    public function countSeparatedConstitutedOptionalequal(& $sNumber)
    {

        $mValidNums = explode($this->splitChar, $this->valid_nums);
        $aNumbers = explode($this->splitChar, $sNumber);

        $aBetNumbers = [];
        if ($mValidNums) {
            if (!is_array($mValidNums)) {
                $mValidNums = array_fill(0, $this->digital_count, $mValidNums);
            }
        } else {
            $mValidNums = array_fill(0, $this->digital_count, $this->valid_nums);
        }

        $iCount = [];
        foreach ($aNumbers as $i => $sPartNumber) {
            if (!strlen($sPartNumber)) {
                $iCount[$i] = 0;
                continue;
            }
            if (!preg_match('/^[' . $mValidNums[$i] . ']+$/', $sPartNumber)) {
                return 0;
            }
            $aDigitals = array_unique(str_split($sPartNumber));

            sort($aDigitals);

            $iCount [$i] = count($aDigitals);

            $aBetNumbers[] = implode($aDigitals);
        }

        $tCount = Math::getCombin4Renxun($this->choose_count, $iCount);

        return $tCount;


    }

    /**
     * 任直选单式计算
     * @param type $sNumber
     */
    public function countEnumOptionalequal(& $sNumber)
    {

        $aNumber = explode($this->splitChar, $sNumber);

        foreach ($aNumber as $i => $sPartNumber) {
            if (!preg_match('/[\d{' . $this->choose_count . '}]+$/', $sPartNumber)) {
                return 0;
            }

        }
        $aPosition = str_split($this->sPosition);
        if (count($aPosition) < $this->choose_count) return 0;

        return count($aNumber) * Math::combin(count($aPosition), $this->choose_count);

    }

    /**
     * 返回任选复式中奖号码
     * @param type $sWinningNumber
     */
    public function getWnConstitutedOptionalequal($sWinningNumber)
    {

        return $sWinningNumber;
    }


    /**
     * 返回任选单式号码
     * @param type $sWinningNumber
     */
    public function getWnEnumOptionalequal($sWinningNumber)
    {
        return $sWinningNumber;
    }

    /**
     * 返回任选直选单式的中奖煮熟
     * @param type $oSeriesWay
     * @param type $aWnNumber
     * @param type $sBetNumber
     * @return int
     */
    public function prizeEnumOptionalequal($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $nWnCount = 0;
        $aWnNumber = str_split($sWnNumber);
        $aBetDigitals = explode($this->splitChar, $sBetNumber);
        $aPostions = str_split($this->sPosition);

        $allPositions = Math::getCombinationToString($aPostions, $this->choose_count);
        foreach ($aBetDigitals as $sDigits) {
            $aDigits = str_split($sDigits);
            foreach ($allPositions as $sP) {
                $current_index = 0;
                $aSingleBet = [];
                $aP = explode(",", $sP);
                for ($i = 0; $i < 5; $i++) {
                    if (!in_array($i, $aP)) $aSingleBet[$i] = "";
                    else {
                        $aSingleBet[$i] = isset($aDigits[$current_index]) ? $aDigits[$current_index] : "";
                        $current_index++;
                    }
                }
                $aBoth = array_intersect_assoc($aWnNumber, $aSingleBet);
                if (count($aBoth) === $this->choose_count) $nWnCount++;
            }
        }
        return $nWnCount;

    }

    /**
     * 返回任选直选复式的中奖注数
     * @param type $oSeriesWay
     * @param type $aWnNumber
     * @param type $sBetNumber
     */
    public function prizeSeparatedConstitutedOptionalequal($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnNumber = str_split($sWnNumber);
        $aBetDigitals = explode($this->splitChar, $sBetNumber);
        $aBetDigitsByPostion = [];
        foreach ($aBetDigitals as $i => $sBetDigits) {
            $aBetDigitsByPostion[$i] = str_split($sBetDigits);
        }
        $iHitCount = 0;
        for ($i = 0; $i < count($aBetDigitsByPostion); $i++) {
            if ($aWnNumber[$i] === 0) $aWnNumber[$i] = "0";
            if (in_array($aWnNumber[$i], $aBetDigitsByPostion[$i])) {
                $iHitCount++;
            }
        }
        return $iHitCount >= $this->choose_count ? Math::combin($iHitCount, $this->choose_count) : 0;
    }

    /**
     * 任组选单式计算
     * @param type $sNumber
     */
    public function countEnumOptionalcombin(& $sNumber)
    {

        $aNumber = explode($this->splitChar, $sNumber);
        foreach ($aNumber as $i => $sPartNumber) {
            if (!preg_match('/^[' . $this->valid_nums . ']+$/', $sPartNumber)) {
                return 0;
            }
//                if(strlen($sPartNumber)!=$this->digital_count) return 0;
            // if(!$this->checkNumbers(str_split($sPartNumber))) return 0;
            preg_match_all('/[\d]{1}/U', $sPartNumber, $out, PREG_SET_ORDER);
            if ($this->choose_count != count($out)) return 0;
        }
        return count($aNumber) * Math::combin(count(str_split($this->sPosition)), $this->choose_count);

    }


    public function countConstitutedDoubleAreaOptionalcombin(& $sNumber)
    {
//          $aBetData=  explode($this->splitChar, $sNumber);
//          $aBetDigitals         =  str_split($aBetData[0]);
//          $aBetPostions         =  str_split($aBetData[1]);
//          $combin_digits_counts =  $this->unique_count ? $this->unique_count : $this->choose_count;
//          if(count($aBetDigitals) < $combin_digits_counts || count($aBetPostions) < $combin_digits_counts) return 0;
//          $count =  Math::combin(count($aBetDigitals),$combin_digits_counts) * Math::combin(count($aBetPostions), $this->choose_count);
//          if($combin_digits_counts==2) $count = $count *2;
//          return $count;

        $aNums = explode($this->splitChar, $sNumber);
        $aValidNums = [];
        foreach ($aNums as $k => $sNums) {
            $aNumOfArea[$k] = str_split($sNums, 1);
            sort($aNumOfArea[$k]);
            $aValidNums[$k] = implode($aNumOfArea[$k]);
        }

        $iNeedNumCountOfN = ($this->choose_count - $this->max_repeat_time) / $this->min_repeat_time;

        $aRepeatM = array_intersect($aNumOfArea[0], $aNumOfArea[1]);
        $iRepeatCountM = count($aRepeatM);
        $iNonRepeatCountM = count($aNumOfArea[0]) - $iRepeatCountM;
        $iCountN = count($aNumOfArea[1]);
        $iCount = Math::combin($iRepeatCountM, 1) * Math::combin($iCountN - 1, $iNeedNumCountOfN) + Math::combin($iNonRepeatCountM, 1) * Math::combin($iCountN, $iNeedNumCountOfN);
        $iCount *= Math::combin(strlen($this->sPosition), $this->choose_count);
        $sNumber = implode($this->splitChar, $aValidNums);
        return $iCount;
    }

    /**
     * 返回单区任选组选复式的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countConstitutedOptionalcombin(& $sNumber)
    {
        $aDigitals = array_unique(str_split($sNumber));
        $iDigitalCount = count($aDigitals);
        $iCount = Math::combin($iDigitalCount, $this->unique_count);
        if ($this->choose_count == 3 && $this->unique_count == 2) {
            $iCount *= 2;
        }
        $iCount *= Math::combin(strlen($this->sPosition), $this->choose_count);
        sort($aDigitals);
        $sNumber = implode($aDigitals);
        return $iCount;
    }

    //返回任选组选中奖号码getWnNumberOptionalcombin
    public function getWnNumberOptionalcombin($sWinningNumber)
    {
        return $sWinningNumber;
    }

    //返回任选组选复式的中奖注数
    public function prizeConstitutedOptionalcombin_bak($oSeriesWay, $aWnNumber, $sBetNumber)
    {
        $aBetNumber = str_split($sBetNumber);
        $aWnNumber = array_intersect($this->getOptionalWinNumber($aWnNumber), $aBetNumber);

        $prizeCount = 0;
        $filterDigital = [];
        $aWinDigital = array_count_values($aWnNumber);

        $aWnNumber = [];
        foreach ($aWinDigital as $digital => $count) {
            if ($count < $this->min_repeat_time) unset($aWinDigital[$digital]);
            if ($count >= $this->min_repeat_time) $aWnNumber = array_merge(array_fill(0, $count, $digital), $aWnNumber);
        }

        foreach ($aWinDigital as $digital => $count) {
            if ($count >= $this->max_repeat_time) {
                if ($this->max_repeat_time == $this->min_repeat_time) {
                    $filterDigital[] = $digital;
                    $aWnMins = array_diff($aWnNumber, $filterDigital);

                    $iDiffCount = count($aWnMins);

                    foreach (array_count_values($aWnMins) as $minDigital => $minCount) {
                        if ($minCount > $this->min_repeat_time) $iDiffCount -= $minCount - $this->min_repeat_time;
                    }
                } else {
                    $iDiffCount = count(array_diff($aWnNumber, [$digital]));
                }

                $prizeCount += Math::combin($count, $this->max_repeat_time) * Math::combin($iDiffCount, $this->choose_count - $this->max_repeat_time);
            }
        }
        return $prizeCount;
    }

    //任选组选复式的中奖注数

    public function prizeConstitutedOptionalcombin($oSeriesWay, $aWnNumber, $sBetNumber)
    {

        $aBetDigits = str_split($sBetNumber);

        $aPosition = str_split($this->sPosition);
        $aWnDigits = [];
        foreach ($aPosition as $p) {
            if (!isset($aWnNumber)) return 0;
            $aWnDigits[] = $aWnNumber[$p];
        }

        $aIntersects = array_intersect($aWnDigits, $aBetDigits);
        if (count($aIntersects) < $this->choose_count) return 0;
        $aWnDigitals = array_count_values($aIntersects);
        $aRepeateCount = [];
        if ($this->max_repeat_time == $this->min_repeat_time) {
            $iSumHits = 0;
            $aCombins = Math::getCombinationToString(array_values($aWnDigitals), $this->unique_count);
            foreach ($aCombins as $sComb) {
                $iHits = 1;
                $aScomb = explode(",", $sComb);
                foreach ($aScomb as $n)
                    $iHits *= Math::combin($n, $this->max_repeat_time);

                $iSumHits += $iHits;
            }
            return $iSumHits;
        }
        if ($this->max_repeat_time == 2 && $this->min_repeat_time == 1 && $this->choose_count == 3) {//AAB
            $iSumHits = 0;
            $aCombins = Math::getCombinationToString(array_values($aWnDigitals), $this->unique_count);
            foreach ($aCombins as $sComb) {

                $aScomb = explode(",", $sComb);
                $iSumHits += Math::combin($aScomb[0], $this->max_repeat_time) * Math::combin($aScomb[1], $this->min_repeat_time)
                    + Math::combin($aScomb[1], $this->max_repeat_time) * Math::combin($aScomb[0], $this->min_repeat_time);

            }
            return $iSumHits;
        }

        return 0;
    }


    /**
     * 任选组选选重号与不重号的复式注数计算
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeConstitutedDoubleAreaOptionalcombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {

        $aBetNumber = explode($this->splitChar, $sBetNumber);
        $aWnNumber = $this->getOptionalWinNumber($sWnNumber);

        $aDoubleNumber = array_intersect($aWnNumber, str_split($aBetNumber[0]));
        $aSingleNumber = array_intersect($aWnNumber, str_split($aBetNumber[1]));

        $aDoubleDigitals = array_count_values($aDoubleNumber);
        $aSingleDigitals = array_count_values($aSingleNumber);

        $uniqueCount = ($this->choose_count - $this->max_repeat_time) / $this->min_repeat_time;
        $aMinCombin = Math::getCombinationToString(array_keys($aSingleDigitals), $uniqueCount);

        $aMinCombins = [];
        foreach ($aMinCombin as $key => $sMinCombin) {
            $aMinCombins[] = explode(',', $sMinCombin);
        }

        $iSumHits = 0;
        foreach ($aDoubleDigitals as $digital => $count) {
            if ($count < $this->max_repeat_time) continue;
            $combinCount = Math::combin($count, $this->max_repeat_time);

            $iMinCombinCount = 0;
            foreach ($aMinCombins as $aDigtal) {
                if (in_array($digital, $aDigtal)) continue;
                $iMinCombinCount += $uniqueCount == 1 ? $aSingleDigitals[$aDigtal[0]] : $aSingleDigitals[$aDigtal[0]] * $aSingleDigitals[$aDigtal[1]];
            }
            $iSumHits += $combinCount * $iMinCombinCount;
        }

        return $iSumHits;
    }


    //任直选和值算注数
    public function countSumOptionalequal(& $sNumber)
    {
        $countArray = [
            2 => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
            3 => [1, 3, 6, 10, 15, 21, 28, 36, 45, 55, 63, 69, 73, 75, 75, 73, 69, 63, 55, 45, 36, 28, 21, 15, 10, 6, 3, 1],
        ];
        $validDigitals = [2, 3];
        if (!in_array($this->choose_count, $validDigitals) || !isset($countArray[$this->choose_count])) {
            return 0;
        }
        $aNumbers = explode($this->splitChar, $sNumber);

        $iPCount = strlen($this->sPosition);
        $iCount = 0;
        foreach ($aNumbers as $iSum) {
            $iCount += isset($countArray[$this->choose_count][$iSum]) ? $countArray[$this->choose_count][$iSum] : 0;
        }

        return $iCount * Math::combin($iPCount, $this->choose_count);
    }

    //任组选和值算注数
    public function countSumOptionalcombin(& $sNumber)
    {
        $countArray = [
            2 => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 4, 4, 3, 3, 2, 2, 1, 1, 0],
            3 => [0, 1, 2, 2, 4, 5, 6, 8, 10, 11, 13, 14, 14, 15, 15, 14, 14, 13, 11, 10, 8, 6, 5, 4, 2, 2, 1, 0]
        ];
        $validDigitals = [2, 3];
        if (!in_array($this->choose_count, $validDigitals) || !isset($countArray[$this->choose_count])) {
            return 0;
        }
        $aNumbers = explode($this->splitChar, $sNumber);

        $iPCount = strlen($this->sPosition);
        $iCount = 0;
        foreach ($aNumbers as $iSum) {
            $iCount += isset($countArray[$this->choose_count][$iSum]) ? $countArray[$this->choose_count][$iSum] : 0;
        }
        return $iCount * Math::combin($iPCount, $this->choose_count);
    }

    /**
     * 任选和值计奖
     * @param type $oSeriesWay
     * @param type $aWnNumber
     * @param type $sBetNumber
     * @return int
     */
    public function prizeSumOptionalequal($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnNumber = str_split($sWnNumber);

        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aPosition = str_split($this->sPosition);
        $aSelectWnNumber = [];
        foreach ($aPosition as $p) {
            if (!isset($aWnNumber[$p])) return 0;
            $aSelectWnNumber[] = $aWnNumber[$p];
        }


        $digitsCounts = count($aSelectWnNumber);
        if ($digitsCounts < $this->choose_count) return 0;

        $aCombins = Math::getCombinationToString($aSelectWnNumber, $this->choose_count);
        print_r($aSelectWnNumber);
        $aSumValues = [];
        foreach ($aCombins as $sComb) {

            $asComb = explode(",", $sComb);

            if (count($asComb) != $this->choose_count) continue;
            $iSum = DigitalNumber::getSum($sComb);
            array_push($aSumValues, $iSum);
        }
        $aBoth = array_intersect($aSumValues, $aBetNumbers);


        return count($aBoth);
    }

    public function prizeSumOptionalcombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnNumber = str_split($sWnNumber);

        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aPosition = str_split($this->sPosition);
        $aSelectWnNumber = [];
        foreach ($aPosition as $p) {
            if (!isset($aWnNumber[$p])) return 0;
            $aSelectWnNumber[] = $aWnNumber[$p];
        }

//        //get rid of pair from 2 digits
//        if($this->choose_count == 2){
//            $aSelectWnNumber=  array_unique($aSelectWnNumber);
//        }

        $digitsCounts = count($aSelectWnNumber);
        if ($digitsCounts < $this->choose_count) return 0;

        $aCombins = Math::getCombinationToString($aSelectWnNumber, $this->choose_count);

        $aSumValues = [];
        foreach ($aCombins as $sComb) {

            $asComb = explode(",", $sComb);
            if ($this->choose_count == 2)
                if (count(array_unique($asComb)) == 1)
                    continue;
            if (count($asComb) != $this->choose_count) continue;
            if (count(array_unique($asComb)) != $this->unique_count) continue;
            $aCountComb = array_flip(array_count_values($asComb));
            if (!isset($aCountComb[$this->max_repeat_time])) continue;
            if (!isset($aCountComb[$this->min_repeat_time])) continue;
            $iSum = DigitalNumber::getSum($sComb);
            array_push($aSumValues, $iSum);
        }
        $aBoth = array_intersect($aSumValues, $aBetNumbers);


        return count($aBoth);
    }

    /**
     * 组选单式计奖
     * @param type $oSeriesWay
     * @param type $sWnNumber
     * @param type $sBetNumber
     * @return int
     */
    function prizeEnumOptionalcombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnNumber = str_split($sWnNumber);

        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aPosition = str_split($this->sPosition);

        $aSelectWnNumber = [];
        foreach ($aPosition as $p) {
            if (!isset($aWnNumber[$p])) return 0;
            $aSelectWnNumber[] = $aWnNumber[$p];
        }


        $iHitCounts = 0;
        foreach ($aBetNumbers as $sNumber) {
            if (strlen($sNumber) != $this->choose_count) {
                continue;
            }
            $aBetDigits = str_split($sNumber);
            $aCountValueBetDigits = array_count_values($aBetDigits);

            if (!$this->_verifyRepeatTimes($aBetDigits)) {
                continue;
            }
            $aBoth = array_intersect($aSelectWnNumber, array_unique(str_split($sNumber)));
            if (count(array_unique($aBoth)) != $this->unique_count) continue;

            if ($this->max_repeat_time == $this->min_repeat_time) { //AB  ABC ABCD AABB
                $iHits = 1;
                foreach ($aCountValueBetDigits as $digit => $count) {
                    $iHitInWn = count(array_intersect($aSelectWnNumber, [$digit]));//当前数字在开奖号里出现几次

                    $iHits *= Math::combin($iHitInWn, $this->max_repeat_time);
                }

                $iHitCounts += $iHits;
            } else {//AAB
                $iHits = 1;
                foreach ($aCountValueBetDigits as $digit => $count) {
                    if (!in_array($count, [1, 2])) continue 2;
                    $iHitInWn = count(array_intersect($aSelectWnNumber, [$digit]));
                    $chooseCount = ($count == $this->max_repeat_time) ? $this->max_repeat_time : $this->min_repeat_time;
                    $iHits *= Math::combin($iHitInWn, $chooseCount);
                }
                $iHitCounts += $iHits;
            }


        }
        return $iHitCounts;
    }

    public function prizeMixCombinOptionalcombin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnNumber = str_split($sWnNumber);

        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aPosition = str_split($this->sPosition);

        $aSelectWnNumber = [];
        foreach ($aPosition as $p) {
            if (!isset($aWnNumber[$p])) return 0;
            $aSelectWnNumber[] = $aWnNumber[$p];
        }


        $iHitCounts = 0;
        foreach ($aBetNumbers as $sNumber) {
            if (strlen($sNumber) != $this->choose_count) {
                continue;
            }
            $aBetDigits = str_split($sNumber);
            $aCountValueBetDigits = array_count_values($aBetDigits);

            if (!$this->_verifyRepeatTimes($aBetDigits)) {
                continue;
            }
            $aBoth = array_intersect($aSelectWnNumber, array_unique(str_split($sNumber)));
            if (count(array_unique($aBoth)) != $this->unique_count) continue;

            if ($this->max_repeat_time == $this->min_repeat_time) { //AB  ABC ABCD AABB
                $iHits = 1;
                foreach ($aCountValueBetDigits as $digit => $count) {
                    $iHitInWn = count(array_intersect($aSelectWnNumber, [$digit]));//当前数字在开奖号里出现几次

                    $iHits *= Math::combin($iHitInWn, $this->max_repeat_time);
                }

                $iHitCounts += $iHits;
            } else {//AAB
                $iHits = 1;
                foreach ($aCountValueBetDigits as $digit => $count) {
                    if (!in_array($count, [1, 2])) continue 2;
                    $iHitInWn = count(array_intersect($aSelectWnNumber, [$digit]));
                    $chooseCount = ($count == $this->max_repeat_time) ? $this->max_repeat_time : $this->min_repeat_time;
                    $iHits *= Math::combin($iHitInWn, $chooseCount);
                }
                $iHitCounts += $iHits;
            }


        }
        return $iHitCounts;
    }

    private function _verifyRepeatTimes($aNumber = [])
    {
        $arrCounts = array_count_values($aNumber);

        asort($arrCounts, SORT_NUMERIC);
        //print_r($arrCounts);exit;
        if ($this->max_repeat_time) {
            if (end($arrCounts) != $this->max_repeat_time) {
                return false;
            }
        }
        if ($this->min_repeat_time) {
            $a = array_reverse($arrCounts);
            if (end($a) != $this->min_repeat_time) {
                return false;
            }
        }
        return true;
    }


    /**
     * 返回数据列表
     * @param boolean $bOrderByTitle
     * @param boolean $iLotteryType
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getTitleList($bOrderByTitle = true, $iLotteryType = null)
    {
        $aColumns = ['id', static::$titleColumn];
        $sOrderColumn = $bOrderByTitle ? static::$titleColumn : 'id';

        $oQuery = self::orderBy($sOrderColumn, 'asc');

        !$iLotteryType or $oQuery = $oQuery->where('lottery_type', '=', $iLotteryType);

        $oModels = $oQuery->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->{static::$titleColumn};
        }
        return $data;
    }

    private function getOptionalWinNumber($sWnNumber)
    {
        $aWnNumber = str_split($sWnNumber);
        $aPosition = str_split($this->sPosition);
        $aWnDigitals = [];
        foreach ($aPosition as $iPosition) $aWnDigitals[] = $aWnNumber[$iPosition];
        return $aWnDigitals;
    }

    /**
     * 返回二星大小的中奖号码
     * @param type $sWinningNumber
     * @return type
     */
    public function getWnNumberTsbs($sWinningNumber)
    {
        return $sWinningNumber;
    }

    /**
     * 返回二星大小的中奖注数
     * @param type $sNumber
     * @return type
     */
    public function prizeTwoStarBigSmallTsbs($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumber = str_split($sBetNumber);

        $aBetNumber = array_unique(array_intersect([0, 1], $aBetNumber));

        $iWnDigital = $this->getTsbslWinNumber($oSeriesWay->area_position, $sWnNumber);

        return intval(in_array($iWnDigital, $aBetNumber));
    }

    /**
     * 返回二星相等的中奖
     * @param type $sWinningNumber
     * @return type
     */
    public function getWnNumberTsEqual($sWinningNumber)
    {
        return $sWinningNumber;
    }

    /**
     * 返回二星相等的中奖注数
     * @param type $sNumber
     * @return type
     */
    public function prizeTwoStarBigSmallTsEqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {

        $aBetNumber = str_split($sBetNumber);

        $aBetNumber = array_unique(array_intersect([2], $aBetNumber));

        $iWnDigital = $this->getTsbslWinNumber($oSeriesWay->area_position, $sWnNumber);

        return intval(in_array($iWnDigital, $aBetNumber));
    }

    /**
     * 返回二星大小的中奖号码
     * @param $areaPosition
     * @param $sWnNumber
     * @return int
     */
    private function getTsbslWinNumber($areaPosition, $sWnNumber)
    {

        $aWnNumber = str_split($sWnNumber);
        $aPosition = str_split($areaPosition);

        $aWnDigital = [];
        foreach ($aPosition as $iPosition) $aWnDigital[] = $aWnNumber[$iPosition];

        if ($aWnDigital[0] > $aWnDigital[1]) {
            return 0; //龙
        } elseif ($aWnDigital[0] < $aWnDigital[1]) {
            return 1; //虎
        } elseif ($aWnDigital[0] == $aWnDigital[1]) {
            return 2; //和
        }
    }

    /**
     * 返回快3组选的中奖号
     * @param $sWinningNumber
     * @return mixed
     */
    public function getWnNumberK3combin($sWinningNumber)
    {
        $sWinningNumber = str_split($sWinningNumber, 1);
        sort($sWinningNumber);
        $sWinningNumber = implode($sWinningNumber);
        return $this->checkSpan($sWinningNumber) ? $sWinningNumber : '';
    }

    /**
     * 返回快3任选的中奖号
     * @param $sWinningNumber
     * @return string
     */
    public function getWnNumberK3contain($sWinningNumber)
    {
        $sWinningNumber = str_split($sWinningNumber, 1);
        sort($sWinningNumber);
        return implode($sWinningNumber);
    }

    /**
     * 返回快3大小单双的中奖号
     * @param $sWinningNumber
     * @return string
     */
    public function getWnNumberK3bsde($sWinningNumber)
    {
        if (1 == count(array_unique(str_split($sWinningNumber)))) return '';

        $iSum = DigitalNumber::getSum($sWinningNumber);
        if ($iSum <= 3 || $iSum >= 18) return '';
        $sWnNumber = intval($iSum >= 11); // 大小
        $sWnNumber .= $iSum % 2 + 2; // 单双
        return $sWnNumber;
    }


    /**
     * 返回快3组选单式的投注数
     * @param type $sNumber
     * @return type
     */
    public function countEnumK3combin(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        list($iMin, $iMax) = explode('-', $this->valid_nums);

        $aValidNumbers = [];
        $iCount = 0;
        foreach ($aNumbers as $sSNumber) {
            $aChoosedNumber = str_split($sSNumber);
            if (min($aChoosedNumber) < $iMin || max($aChoosedNumber) > $iMax) {
                return 0;
            }

            if (strlen($sSNumber) == $this->choose_count && $this->checkCombinValid($sSNumber) && $this->checkSpan($sSNumber)) {
                $aValidNumbers[] = $sSNumber;
            }
        }
        $aValidNumbers = array_unique($aValidNumbers);
        sort($aValidNumbers);
        $sNumber = implode($this->splitChar, $aValidNumbers);
        return count($aValidNumbers);
    }

    /**
     * 返回的k3任选单式投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countEnumK3contain(& $sNumber)
    {
        return $this->countEnumK3combin($sNumber);
    }

    /**
     * 返回快3组选和值的投注数
     * @param type $sNumber
     * @return type
     */
    public function countSumK3combin(& $sNumber)
    {
        $aChoosedNumbers = array_unique(explode($this->splitChar, $sNumber));
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aChoosedNumbers as $i => $sChoosedNumber) {
            if ($sChoosedNumber < $iMin || $sChoosedNumber > $iMax) {
                return 0;
            }
        }
        sort($aChoosedNumbers);
        $sNumber = implode($this->splitChar, $aChoosedNumbers);
        return count($aChoosedNumbers);
    }

    /**
     * 返回K3大小单双和值的投注数
     * @param type $sNumber
     * @return type
     */
    public function countBigSmallOddEvenK3bsde(& $sNumber)
    {
        $aNumber = explode($this->splitChar, $sNumber);
        list($iMin, $iMax) = explode('-', $this->valid_nums);

        if (min($aNumber) < $iMin || max($aNumber) > $iMax) return 0;
        sort($aNumber);
        return count(array_unique($aNumber));
    }

    /**
     * 返回快3组选单式的中奖注数
     * @param type $sNumber
     * @return type
     */
    public function prizeEnumK3combin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        return intval(array_keys($aBetNumbers, $sWnNumber));
    }

    /**
     * 返回快3任选单式的中奖注数
     * @param type $sNumber
     * @return type
     */
    public function prizeEnumK3contain($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $winCount = 0;
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aWnNumber = str_split($sWnNumber);

        $aCombinations = Math::getCombinationToString($aWnNumber, $this->choose_count);

        $aDigitals = [];
        foreach ($aCombinations as $sCombination) {
            $aDigital = explode(',', $sCombination);
            sort($aDigital);
            $aDigitals[] = implode($aDigital);
        }

        foreach ($aBetNumbers as $sBetNumber) {
            if (in_array($sBetNumber, $aDigitals)) $winCount++;
        }
        return $winCount;
    }

    /**
     * 返回K3组选和值的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSumK3combin($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iSum = DigitalNumber::getSum($sWnNumber);
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        return intval(in_array($iSum, $aBetNumbers));
    }


    /**
     * 返回K3大小单双和值的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeBigSmallOddEvenK3bsde($oSeriesWay, $sWnNumber, $sBetNumber)
    {

        $aWnNumber = str_split($sWnNumber);
        $aBetNumber = str_split($sBetNumber);
        $aBoth = array_intersect($aWnNumber, $aBetNumber);

        return count($aBoth);
    }


    /**
     * 返回特殊中奖号码
     * @param SeriesMethod $oSeriesMethod
     * @param string $sWinningNumber
     * @return string
     */
    public function getWnNumberK3special($sWinningNumber)
    {
        $aWnDigitals = array_unique(str_split($sWinningNumber));
        $bWin = count($aWnDigitals) == $this->unique_count;
        if ($bWin && $this->unique_count == 3) {
            $iSpan = max($aWnDigitals) - min($aWnDigitals);
            if (!$bWin = $iSpan == $this->span) {
                if ($iSpan == 9) {
                    rsort($aWnDigitals);
                    $iSpanAB = $aWnDigitals[0] - $aWnDigitals[1];
                    $iSpanBC = $aWnDigitals[1] - $aWnDigitals[2];
                    $iMinSpan = min($iSpanAB, $iSpanBC);
                    $bWin = $iMinSpan == $this->min_span;
                }
            }
        }
        return $bWin ? $this->fixed_number : false;
    }

    /**
     * 返回k3特殊的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeSpecialConstitutedK3special($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        return preg_match("/$sWnNumber/", $sBetNumber);
    }

    /**
     * 返回K3的投注码数量
     * @param string $sNumber
     * @return int
     */
    public function countSpecialConstitutedK3special(& $sNumber)
    {

        $aNumbers = explode($this->splitChar, $sNumber);
        $aBetNumbers = [];

        $mValidNums = array_fill(0, $this->digital_count, $this->valid_nums);

        $iCount = 1;
        foreach ($aNumbers as $i => $sPartNumber) {
            if (!preg_match('/^[' . $mValidNums[$i] . ']+$/', $sPartNumber)) {
                return 0;
            }
            $aDigitals = array_unique(str_split($sPartNumber));
            sort($aDigitals);
            $iCount *= count($aDigitals);
            $aBetNumbers[] = implode($aDigitals);
        }
        $sNumber = implode($this->splitChar, $aBetNumbers);
        return $iCount;

    }

    /**
     * 返回乐透二星大小的中奖号码
     * @param type $sWinningNumber
     * @return type
     */
    public function getWnNumberTsSpecial($sWinningNumber)
    {

        list($longNumber, $huNumber) = explode($this->splitCharInArea, $sWinningNumber);

        $SLongNumber = $longNumber <= 13 ? $longNumber : ($longNumber % 13 ? $longNumber % 13 : 13);
        $SHuNumber = $huNumber <= 13 ? $huNumber : ($huNumber % 13 ? $huNumber % 13 : 13);

        if ($SLongNumber > $SHuNumber) {
            $bs = 0; //龙
        } elseif ($SLongNumber < $SHuNumber) {
            $bs = 1; //虎
        } elseif ($SLongNumber == $SHuNumber) {
            $bs = 2; //和
        }

        $sd = ($SLongNumber % 2 ? 3 : 5) . $this->splitCharInArea . ($SHuNumber % 2 ? 4 : 6);//单双
        $rb = (($longNumber >= 1 && $longNumber <= 26) ? 7 : 9) . $this->splitCharInArea . (($huNumber >= 1 && $huNumber <= 26) ? 8 : 10);

        return $bs . $this->splitCharInArea . $sd . $this->splitCharInArea . $rb;
    }

    /**
     * 返回乐透二星大小的中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeTwoStarSpecialTsSpecial($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumber = explode($this->splitChar, $sBetNumber);
        $aWnNumber = explode($this->splitCharInArea, $sWnNumber);

        if (in_array($sBetNumber, ['0', '1']) && in_array('2', $aWnNumber)) {
            return 1;
        }

        return count(array_intersect($aWnNumber, $aBetNumber));
    }

    /**
     * 返回乐透二星大小的投注注数
     * @param type $sNumber
     * @return type
     */
    public function countTwoStarSpecialTsSpecial(& $sNumber)
    {

        $aNumber = array_unique(explode($this->splitChar, $sNumber));

        if (is_numeric($this->valid_nums)) {
            foreach ($aNumber as $i => $iNumber) {
                if ($iNumber != $this->valid_nums) return 0;
            }
        } else {
            list($iMin, $iMax) = explode('-', $this->valid_nums);
            foreach ($aNumber as $i => $iNumber) {
                if ($iNumber < $iMin || $iNumber > $iMax) {
                    return 0;
                }
            }
        }
        $sNumber = implode($this->splitChar, $aNumber);
        return count($aNumber);
    }


    /**
     * 返回百家乐中奖号码（除闲龙宝、庄龙宝）
     * @param $sWinningNumber
     * @return string
     */
    public function getWnNumberBjlSum($sWinningNumber)
    {
        list($aXNumber, $aZNumber, $iXSum, $iZSum) = $this->getWnNumberBjl($sWinningNumber);

        $aWnNumber[] = $iXSum == $iZSum ? 2 : ($iXSum > $iZSum ? 0 : 1);//闲、庄、和 0、1、2
        $aWnNumber[] = count($aXNumber) + count($aZNumber) > 4 ? 3 : 4; //大、小 3、4

//        count(array_unique($aXNumber)) == count($aXNumber) or $aWnNumber[] = 5; //闲对 5
//        count(array_unique($aZNumber)) == count($aZNumber) or $aWnNumber[] = 6; //庄对 6

        if ($aXNumber[0] == $aXNumber[1]) $aWnNumber[] = 5;
        if ($aZNumber[0] == $aZNumber[1]) $aWnNumber[] = 6;

        if ($iZSum == 6 && $iZSum > $iXSum) $aWnNumber[] = 7;//super6  7

        return implode($this->splitCharInArea, $aWnNumber);
    }

    /**
     * 返回百家乐闲龙宝、庄龙宝的中奖号码
     * @param $sWinningNumber
     * @return string
     */
    public function getWnNumberBjlSumSpecial($sWinningNumber)
    {

        $aWnNumber = $this->getWnNumberBjlSpecial($sWinningNumber);
//        return $sWnNumber ? implode($this->splitCharInArea, $sWnNumber) : false;

        return $aWnNumber ? $sWinningNumber : false;
    }


    /**
     * 返回百家乐闲龙宝、庄龙宝的中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeBjlEnumBjlSumSpecial($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumber = explode($this->splitChar, $sBetNumber);
        $aWnNumber = $this->getWnNumberBjlSpecial($sWnNumber);

        $count = count(array_intersect($aBetNumber, ['8', '9']));

        if ($count && in_array('0', $aWnNumber)) {
            return $count;
        } else {
            return count(array_intersect($aWnNumber, $aBetNumber));
        }
    }

    /**
     * 返回百家乐的中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeBjlEnumBjlSum($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumber = explode($this->splitChar, $sBetNumber);
        $aWnNumber = explode($this->splitCharInArea, $sWnNumber);

        //庄家6点获胜，则只赔一半。 //押庄或闲，结果为和，则投注金额返还给玩家
        if (in_array('7', $aWnNumber) && array_intersect($aBetNumber, ['1'])) {
            return 1;
        } elseif (($count = count(array_intersect($aBetNumber, ['0', '1']))) && in_array('2', $aWnNumber)) {
            return $count;
        } else {
            return count(array_intersect($aWnNumber, $aBetNumber));
        }
    }


    /**
     *返回百家乐闲龙宝、庄龙宝的投注注数
     * @param type $sNumber
     * @return type
     */
    public function countBjlEnumBjlSumSpecial(& $sNumber)
    {
        return $this->countBjlEnumBjlSum($sNumber);
    }

    /**
     *返回百家乐的投注注数
     * @param type $sNumber
     * @return type
     */
    public function countBjlEnumBjlSum(& $sNumber)
    {
        $aNumber = array_unique(explode($this->splitChar, $sNumber));

        if (is_numeric($this->valid_nums)) {
            foreach ($aNumber as $i => $iNumber) {
                if ($iNumber != $this->valid_nums) return 0;
            }
        } else {
            list($iMin, $iMax) = explode('-', $this->valid_nums);
            foreach ($aNumber as $i => $iNumber) {
                if ($iNumber < $iMin || $iNumber > $iMax) {
                    return 0;
                }
            }
        }
        $sNumber = implode($this->splitChar, $aNumber);
        return count($aNumber) > 1 ? 0 : 1;//一注单里只能有一单注
    }


    public function getWnNumberBjl($sWinningNumber)
    {

        list($sXNumber, $sZNumber) = explode($this->splitChar, $sWinningNumber);

        $aXNumber = explode($this->splitCharInArea, $sXNumber);
        $aZNumber = explode($this->splitCharInArea, $sZNumber);

        foreach ($aXNumber as $key => $iWnNumber) {
            $aXNumber[$key] = $iWnNumber <= 13 ? intval($iWnNumber) : ($iWnNumber % 13 ? $iWnNumber % 13 : 13);
            $aXDigital[$key] = $aXNumber[$key] < 10 ? $aXNumber[$key] : 0;
        }
        foreach ($aZNumber as $key => $iWnNumber) {
            $aZNumber[$key] = $iWnNumber <= 13 ? intval($iWnNumber) : ($iWnNumber % 13 ? $iWnNumber % 13 : 13);
            $aZDigital[$key] = $aZNumber[$key] < 10 ? $aZNumber[$key] : 0;
        }

        $iXSum = array_sum($aXDigital) % 10;
        $iZSum = array_sum($aZDigital) % 10;

        return [$aXNumber, $aZNumber, $iXSum, $iZSum];
    }

    public function getWnNumberBjlSpecial($sWinningNumber)
    {

        list($aXNumber, $aZNumber, $iXSum, $iZSum) = $this->getWnNumberBjl($sWinningNumber);

        $aWnNumber = [];
        //例牌(两张牌，且和为8或9)
        if ((count($aXNumber) == 2 && in_array($iXSum, [8, 9])) || (count($aZNumber) == 2 && in_array($iZSum, [8, 9]))) {
            if ($iXSum == $iZSum) $aWnNumber[] = 0;
            else $aWnNumber[] = $iXSum > $iZSum ? 8 : 9;
        } //非例牌(计算差)
        else {
            if (abs($iXSum - $iZSum) > 3) {
                $aWnNumber[] = $iXSum > $iZSum ? 8 : 9;
            }
        }

        return $aWnNumber;
    }

    /**
     *  和值玩法大小单双投注注数
     * @param $sNumber
     */
    public function countBigSmallOddEvenSum(& $sNumber)
    {
        $aNumber = str_split(trim($sNumber));
        $aNumbers = [];
        foreach ($aNumber as $sBetNumber) {
            if (preg_match('/[' . $this->valid_nums . ']/', $sBetNumber)) {
                $aNumbers[] = $sBetNumber;
            }
        }
        return count($aNumbers);
    }

    /**
     * 和值大小单双中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     */
    public function prizeBigSmallOddEvenSum($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aWnNumber = str_split($sWnNumber);
        $iSum = array_sum($aWnNumber);
        $aBetNumber = str_split(trim($sBetNumber));
        foreach ($aBetNumber as $sNumber) {
            if (preg_match('/[' . $this->valid_nums . ']/', $sNumber)) {
                $aBetNumbers[] = $sNumber;
            }
        }
        $iNumberCount = $this->digital_count * 9 + 1;
        $aWnNumbers[] = $iSum < $iNumberCount / 2 ? 0 : 1;
        $aWnNumbers[] = $iSum % 2 == 0 ? 2 : 3;
        $aBoth = array_intersect($aWnNumbers, $aBetNumbers);
        file_put_contents('/tmp/debug4sum','$this->digital_count->'.$this->digital_count."\n\r".'$iNumberCount->'.$iNumberCount."\n\r".'$iSum->'.$iSum."\n\r".'$iNumberCount / 2 ='.($iNumberCount / 2)."\n\r");
        return count($aBoth);
    }

    /**
     * 返回和值和值的投注数
     * @param type $sNumber
     * @return type
     */
    public function countSumSum(& $sNumber)
    {
        $aChoosedNumbers = array_unique(explode($this->splitChar, $sNumber));
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aChoosedNumbers as $i => $sChoosedNumber) {
            if (!is_numeric($sChoosedNumber) || ($sChoosedNumber < $iMin || $sChoosedNumber > $iMax)) {
                return 0;
            }
        }
        sort($aChoosedNumbers);
        $sNumber = implode($this->splitChar, $aChoosedNumbers);
        return count($aChoosedNumbers);
    }
    public function prizeSumSum($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $iSum = DigitalNumber::getSum($sWnNumber);
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        return intval(in_array($iSum, $aBetNumbers));
    }

    public function getWnNumberSum($sWinningNumber)
    {
        $sWinningNumber = str_split($sWinningNumber, 1);
        sort($sWinningNumber);
        $sWinningNumber = implode($sWinningNumber);
        return $this->checkSpan($sWinningNumber) ? $sWinningNumber : '';
    }


    /*
     * 处理PK10中奖号码
     */
    public function getPk10WinNumber($iFullWinningNumber,$subtract=0){
        //$sWnNumber = explode($this->splitCharSumDigital,$iFullWinningNumber);
        if($subtract !== 0)
            foreach($iFullWinningNumber as $i=>$num){
                $iFullWinningNumber[$i] -= $subtract;
            }

        return $iFullWinningNumber;
    }
    /**
     * pk10danshi
     * 检查直选单式投注号码是否合法,返回注数.
     * @param string $sNumber
     * @return int
     */
    public function countEnumPkqual(& $sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aValidNumbers = [];
        $iCount = 0;
        foreach ($aNumbers as $sSNumber) {
            !$this->checkEqualValid($sSNumber) or $aValidNumbers[] = $sSNumber;
        }
        $aValidNumbers = array_unique($aValidNumbers);
        $sNumber = implode($this->splitChar, $aValidNumbers);
        return count($aValidNumbers);
    }
    /**
     * pk10danshi
     * 返回直选单式的中奖注数
     * @param string $sWnNumber
     * @param string $sBetNumber
     * @return int
     */
    public function prizeEnumPkqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $aBetNumbers = explode($this->splitChar, $sBetNumber);
        $aKeys = array_keys($aBetNumbers, $sWnNumber);
        return count($aKeys);
    }
    /*
    * 北京PK10计算注数 和值大小单双
     * $sNumber = 1032|1032|||1032
    */
    public function countPkBigSmallOddEvenPksum(&$sNumber)
    {
        $aNumbers = explode($this->splitChar, $sNumber);
        $aBetNumbers = [];
        $iCount = 0;
        if (count($aNumbers) != $this->buy_length) return 0;
        foreach ($aNumbers as $i => $sPartNumber) {
            if ($sPartNumber === '') {
                $aBetNumbers[] = '';
                continue;
            }
            if (!preg_match('/^[' . $this->valid_nums . ']+$/', $sPartNumber) || count($sPartNumber) > 4) {
                return 0;
            }
            $aDigitals = array_unique(str_split($sPartNumber));
            $iCount += count($aDigitals);
            $aBetNumbers[] = implode($aDigitals);
        }
        $sNumber = implode($this->splitChar,$aBetNumbers);
        return $iCount;
    }
    public function getWnNumberPksum($sWinningNumber)
    {
        $this->init();
        $validNums = explode(',', Series::find($this->series_id)->valid_nums);
        $headSum = array_sum(array_slice($validNums, 0, $this->span));
        $tailSum = array_sum(array_slice($validNums, -$this->span));
        $midNum = intval(($headSum + $tailSum) / 2);
        $sWinningNumber = $this->getPk10WinNumber($sWinningNumber);
        $sNumberOfPosition = [];
        for ($i = 0; $i < $this->buy_length; $i += $this->span) {
            $aWinNum = array_slice($sWinningNumber, $i, $this->span);
            $aDigitalSum = array_sum($aWinNum);
            $sNumberOfPosition[$i] = intval($aDigitalSum > $midNum);       //大小
            $sNumberOfPosition[$i] .= $aDigitalSum % 2 + 2; // 单双
        }
        return implode($this->splitChar, $sNumberOfPosition);


    }
    public function prizePkBigSmallOddEvenPksum($oSeriesWay, $sWnNumber, $sBetNumber)
    {

        $aWnDigitals = explode($this->splitChar, $sWnNumber);
        $aBetDigitals = explode($this->splitChar, $sBetNumber);
        $iWonCount = 0;
        foreach ($aWnDigitals as $i => $sWnDigitals) {
            $aWnDigitalsOfWei = str_split($sWnDigitals);

            if (!isset($aBetDigitals[$i]))
                continue;
            $aBetDigitalsOfWei = str_split($aBetDigitals[$i]);
            $aBoth = array_intersect($aWnDigitalsOfWei, $aBetDigitalsOfWei);
            $iWonCount += count($aBoth);
        }
        return $iWonCount;
    }



    /*
    *北京PK10计算注数 直选龙虎
     * $sNumber = '0..9|0..9||||0..9';
    */
    public function countDragonwithtigerPkqual(&$sNumber)
    {
        $this->init();
        $aNumbers = explode($this->splitChar, $sNumber);
        if(count($aNumbers)!=$this->digital_count)
            return 0;
        $aBetNumbers = [];
        $iCount = 0;
        foreach ($aNumbers as $i => $sPartNumber) {
            if ($sPartNumber === '') {
                $aBetNumbers[]='';
                continue;
            }
            if (!preg_match('/^[' . $this->valid_nums . ']+$/', $sPartNumber) || count($sPartNumber)>10) {
                return 0;
            }
            $aDigitals = array_unique(str_split($sPartNumber));
            $iCount += count($aDigitals);
            $aBetNumbers[] = implode($aDigitals);
        }
        $sNumber =implode($this->splitChar,$aBetNumbers);
        return $iCount;
    }

    public function getWnNumberPkqual($sWinningNumber)
    {
        $winNumber = implode($this->getPk10WinNumber($sWinningNumber,$subtract=1));
        return $winNumber;
    }
    public function prizeDragonwithtigerPkqual($oSeriesWay, $sWnNumber, $sBetNumbers)
    {

        $this->init();
        $aWnDigitals = str_split($sWnNumber,1);
        $aBetNumbers = explode($this->splitChar, $sBetNumbers);
        $count = 0;
        foreach ($aBetNumbers as $row => $aBetNumber) {
            if ($aBetNumber === '') continue;
            $aBetNumber = str_split($aBetNumber);
            foreach ($aBetNumber as $column) {
                $dragon = $aWnDigitals[$row];
                $tiger = $aWnDigitals[$column];
                if ($dragon > $tiger) {
                    $count++;
                }
            }
        }
        return $count;
    }



    /*
     * 北京PK10计算投注数  和值直选
     */
    public function countSumPksumsum(&$sNumber)
    {

        $aChoosedNumbers = array_unique(explode($this->splitChar, $sNumber));
        if(count($aChoosedNumbers)>$this->all_count) return 0;
        list($iMin, $iMax) = explode('-', $this->valid_nums);
        foreach ($aChoosedNumbers as $i => $sChoosedNumber) {
            if ($sChoosedNumber === '') continue;
            if (!is_numeric($sChoosedNumber) || ($sChoosedNumber < $iMin || $sChoosedNumber > $iMax)) {
                return 0;
            }
        }
        $sNumber = implode($this->splitChar, $aChoosedNumbers);

        return count($aChoosedNumbers);
    }

    public function prizeSumPksumsum($oSeriesWay, $sWnNumber, $sBetNumber)
    {

        $iWinCount = 0;
        if ($sBetNumber) {
            $sBetNumber = explode($this->splitChar, $sBetNumber);

            list($iMin, $iMax) = explode('-', $this->valid_nums);
            foreach ($sBetNumber as $betNumber) {
                if (!is_numeric($betNumber) || ($betNumber < $iMin || $betNumber > $iMax)) {
                    continue;
                }
                if ($betNumber == $sWnNumber)
                    ++$iWinCount;
            }
        }
        return $iWinCount;
    }

    public function getWnNumberPksumsum($sWinningNumber)
    {
        $winNumber = $this->getPk10WinNumber($sWinningNumber);
        return array_sum($winNumber);
    }





    /*
    * PK10直选复式
    * $sNumber = '0..9|0..9||||0..9';
    */
    public function countPkconstitutedPkqual(&$sNumber)
    {
        return $this->countDragonwithtigerPkqual($sNumber);
    }
    /*
     * $sWinNumber = '0123456789'
     */
    public function prizePkconstitutedPkqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $betNumbers = explode($this->splitChar, $sBetNumber);
        $winNumbers = str_split($sWnNumber);

        $iCount = 0;
        for ($i = 0; $i < count($betNumbers); $i++) {
            $bNumbers = str_split($betNumbers[$i]);
            if (isset($winNumbers[$i]) && in_array((string)$winNumbers[$i], $bNumbers,true)) {

                ++$iCount;
            }
        }
        return $iCount;
    }


    /*
     * PK10 zhixuan
     */
    public function countPKSeparatedConstitutedPkqual(&$sNumber)
    {
        $betNumbers = explode($this->splitChar,$sNumber);
        if(count($betNumbers) != $this->digital_count)
            return 0;
        foreach($betNumbers as $i=>$betNumber){
            if(!preg_match('/^[' . $this->valid_nums . ']+$/', $betNumber) || count($betNumber) > 10) {
                return 0;
            }
            $bNumber = str_split($betNumber,1);
            $betNumbers[$i] = implode('',array_unique($bNumber));
        }

        $sNumber = implode($this->splitChar,$betNumbers);
        $iBetNumber=array_reduce($betNumbers,function($a1,$a2){
                                        $a2 = str_split($a2,1);
                                        if(is_null($a1)) {
                                            return $a2;
                                        }
                                        $result = [];
                                        foreach($a1 as $v1){
                                            foreach($a2 as $v2){
                                                $result[] = $v1.$v2;
                                            }
                                        }
                                        return $result;

                                    });

        unset($betNumbers);
        foreach($iBetNumber as $i => $bNumber){
            $bet = str_split($bNumber,1);
            if(count($bet) != count(array_unique($bet))){
                unset($iBetNumber[$i]);
            }
        }
        return count($iBetNumber);
//      return $this->_countSeparatedConstituted($sNumber);
    }


    public function prizePkSeparatedConstitutedPkqual($oSeriesWay, $sWnNumber, $sBetNumber)
    {

        $betNumbers = explode($this->splitChar, $sBetNumber);
        $winNumbers = str_split($sWnNumber);
        $p = [];
        foreach ($winNumbers as $iDigital) {
            $p[] = '[\d]*' . $iDigital . '[\d]*';
        }
        $pattern = '/^' . implode('\|', $p) . '$/';

        return preg_match($pattern, $sBetNumber);
    }

    /*
     *
     * PK10 组选
     */
    public function countConstitutedPkconstituted(&$sNumber)
    {
        return $this->countConstitutedContain($sNumber);
    }
    public function getWnNumberPkconstituted($sWinningNumber)
    {
        //$winNumbers = explode($this->splitCharInArea, $sWinningNumber);
        $winNumber = implode($this->getPk10WinNumber($sWinningNumber,$subtract=1));
        return $winNumber;

    }

    public function prizeConstitutedPkconstituted($oSeriesWay, $sWnNumber, $sBetNumber)
    {
        $winNumbers = str_split($sWnNumber);
        $aBetDigitals = array_unique(str_split($sBetNumber));
        $aBoth = array_intersect($winNumbers, $aBetDigitals);
        $iHitCount = count($aBoth);
        return $iHitCount >= $this->choose_count ? Math::combin($iHitCount, $this->choose_count) : 0;
    }


    /*
     * PK10 hezhi daxiao
     */
    public function countPkBigSmallOddEvenPkBigSmall(&$sNumber){
        return $this->countPkBigSmallOddEvenPksum($sNumber);
    }

    public function getWnNumberPkBigSmall($sWinningNumber){
        $winNumber = $this ->getWnNumberPksum($sWinningNumber);
        if(!empty($winNumber)){
            $winNumber = str_split($winNumber,1);
            $winNumber = $winNumber[0];
        }
        return $winNumber;
    }
    public function prizePkBigSmallOddEvenPkBigSmall($oSeriesWay, $sWnNumber, $sBetNumber){
        $allowBet = ['1','0'];
        $sBetNumber = str_split($sBetNumber,1);

        $iCount = 0;
        if(is_array($sBetNumber)){
            foreach($sBetNumber as $bet){
                if(in_array($bet,$allowBet,true) && $bet === $sWnNumber){
                    ++$iCount;
                }
            }
        }
        return $iCount;
    }

    /*
     * PK10 hezhi danshuang
     */
    public function countPkBigSmallOddEvenPkOddEven(){}
    public function getWnNumberPkOddEven($sWinningNumber){
        $winNumber = $this ->getWnNumberPksum($sWinningNumber);
        if(!empty($winNumber)){
            $winNumber = str_split($winNumber,1);
            $winNumber = $winNumber[1];
        }
        return $winNumber;
    }
    public function prizePkBigSmallOddEvenPkOddEven($oSeriesWay, $sWnNumber, $sBetNumber){
        $allowBet = ['3','2'];
        $sBetNumber = str_split($sBetNumber,1);
        $iCount = 0;
        if(is_array($sBetNumber)){
            foreach($sBetNumber as $bet){
                if(in_array($bet,$allowBet,true) && $bet === $sWnNumber){
                    ++$iCount;
                }
            }
        }
        return $iCount;
    }
    
    
    
    
    
    //--------------幸运28开始-------------------------
    // 大：1， 小：0，
    // 单：1， 双：0，
    // 极大：1， 极小：0，
    // 小单：01， 小双：00， 大单：11， 大双：10
    /**
     * 获取中奖号码
     * @param $sWinningNumber
     * @return string
     *
     */
    public function getWnNumberKl28Sum($sWinningNumber){
        $sWinningNumber = str_split($sWinningNumber, 1);
        sort($sWinningNumber);
        $sWinningNumber = implode($sWinningNumber);
        return $this->checkSpan($sWinningNumber) ? $sWinningNumber : '';
    }

    /**
     * 幸运28和值玩法投注注数
     * @param $sNumber
     * @return int
     */
    public function countSumKl28Sum(& $sNumber){
        $sNumber = intval($sNumber);
        list($iMin,$iMax) = explode('-',$this->valid_nums);
            if ($sNumber < $iMin || $sNumber > $iMax || !is_numeric($sNumber)){
                return 0;
            }

        return 1;
    }

    /**
     * 幸运28和值玩法中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeSumKl28Sum($oSeriesWay,$sWnNumber,$sBetNumber){
        file_put_contents('/tmp/kl','sWnNumber->'.$sWnNumber."\n\r".'sBetNumber->'.$sBetNumber."\n\r",FILE_APPEND);
        $aWinningNumber = str_split($sWnNumber);
        $iSum = array_sum($aWinningNumber);


        return $iSum == $sBetNumber;
    }

    /**
     * 大小玩法注数
     * @param $sNumber
     * @return int
     */
    public function countBigSmallKl28Sum(& $sNumber){
        if(!preg_match('/['.$this->valid_nums.']{1}/',$sNumber,$a) || strlen($sNumber) != 1){
            return 0;
        }
        return 1;
    }

    /**
     * 大小玩法中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeBigSmallKl28Sum($oSeriesWay,$sWnNumber,$sBetNumber){
        if(!preg_match('/['.$this->valid_nums.']/',$sBetNumber)){
            return 0;
        }
        $aWinningNumber = str_split($sWnNumber);
        $iSum = array_sum($aWinningNumber);
        $iNumberCount = $this->digital_count * 9 + 1;
        $aWnNumbers[] = $iSum < $iNumberCount / 2 ? '0':'1';//1:大 0:小
        $aBetNumber[] = $sBetNumber;
        $aBoth = array_intersect($aWnNumbers,$aBetNumber);
        return count($aBoth);
    }

    /**
     * 单双玩法注数
     * @param $sNumber
     * @return int
     */
    public function countOddEvenKl28Sum(& $sNumber){
        if(!preg_match('/['.$this->valid_nums.']{1}/',$sNumber) || strlen($sNumber) != 1){
            return 0;
        }

        return 1;
    }

    /**
     * 大小玩法中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeOddEvenKl28Sum($oSeriesWay,$sWnNumber,$sBetNumber){
        if(preg_match('/['.$this->valid_nums.']/',$sBetNumber)){
            $aBetNumbers[] = $sBetNumber;
        }
        $aWinningNumber = str_split($sWnNumber);
        $iSum = array_sum($aWinningNumber);
        $aWnNumbers[] = $iSum % 2 == 0 ? '0' : '1';//0:双 1:单
        $aBoth = array_intersect($aWnNumbers,$aBetNumbers);
        return count($aBoth);
    }

    /**
     * 串关注数
     * @param $sNumber
     * @return int
     */
    public function countMultipleKl28Sum(& $sNumber){


        if(!preg_match('/[01]{2}/',$sNumber) || strlen($sNumber) != 2){
            return 0;
        }

        return 1;
    }

    /**
     * 串关中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeMultipleKl28Sum($oSeriesWay,$sWnNumber,$sBetNumber){
        if(preg_match('/['.$this->valid_nums.']/',$sBetNumber)){
            $aBetNumbers[] = $sBetNumber;
        }
        $aWinningNumber = str_split($sWnNumber);
        $iSum = array_sum($aWinningNumber);
        $iNumberCount = $this->digital_count * 9 + 1;
        $sBigSmall = $iSum < $iNumberCount / 2 ? '0' : '1';//1:大 0:小
        $sOddEven = $iSum % 2 == 0 ? '0' : '1';//0:双 1:单
        $aWnNumbers[] = $sBigSmall.$sOddEven;
        $aBoth = array_intersect($aWnNumbers,$aBetNumbers);
        return count($aBoth);
    }

    /**
     * 两极玩法注数
     * @param $sNumber
     * @return int
     */
    public function countExtremumKl28Sum(& $sNumber){

        if(!preg_match('/['.$this->valid_nums.']{1}/',$sNumber) || strlen($sNumber) != 1){
            return 0;
        }

        return 1;
    }

    /**
     * 两极玩法中奖注数
     * @param $oSeriesWay
     * @param $sWnNumber
     * @param $sBetNumber
     * @return int
     */
    public function prizeExtremumKl28Sum($oSeriesWay,$sWnNumber,$sBetNumber){
        $aBetNumbers = [];
        if(preg_match('/['.$this->valid_nums.']/',$sBetNumber)){
            $aBetNumbers[] = $sBetNumber;
        }
        $aWinningNumber = str_split($sWnNumber);
        $iSum = array_sum($aWinningNumber);
        if($iSum >= 0 &&  $iSum <= 5){
            $aWnNumbers[] = '0';//极小
        }elseif($iSum >= 22 &&  $iSum <= 27){
            $aWnNumbers[] = '1';//极大
        }else{
            $aWnNumbers[] = '2';//非极值
        }
        $aBoth = array_intersect($aWnNumbers,$aBetNumbers);
        return count($aBoth);
    }
    //--------------幸运28结束-------------------------

}