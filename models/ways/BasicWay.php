<?php

class BasicWay extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'basic_ways';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'lottery_type',
        'name',
        'description',
        'function',
    ];

    public static $resourceName = 'BasicWay';
    public static $sequencable  = false;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'lottery_type',
        'name',
        'description',
        'function',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_type' => 'aLotteryTypes',
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
    public static $mainParamColumn = 'lottery_type';
    public static $titleColumn = 'name';
    public static $rules = [
        'lottery_type' => 'required|integer',
        'name'          => 'required|max:10',
        'description'   => 'max:255',
        'function'     => 'required|max:64',
        'sequence'      => 'integer',
    ];

    /**
     * 注数计算
     * @param SeriesWay $oSeriesWay
     * @param string     $sNumber
     * @return int
     */
    public function count(& $sNumber,$oSeriesWay ,$sPosition=null){
        $oBasicMethod = BasicMethod::find($oSeriesWay->basic_methods);
        $oBasicMethod->sPosition = $sPosition;
        $iCount       = $oBasicMethod->countBetNumber($this->function,$sNumber);
        return $iCount;
    }


    /**
     * 检验是否中奖,返回中奖注数数组
     * @param SeriesWay $oSeriesWay
     * @param string $sBetNumber
     * @return array
     */
    public function checkPrize($oSeriesWay,$sBetNumber,$sPosition=null){
        $aPrized = [];
        if ($this->function == 'MultiOne' || $this->function == 'LottoMultiOne' || $this->function == 'MultiSequencing') {
            $sSplitChar  = Config::get('bet.split_char');
            $aBetNumbers = explode($sSplitChar,$sBetNumber);
        }
//        pr($oSeriesWay->WinningNumber);
//        pr($sBetNumber);
//        pr($oSeriesWay->toArray());
//        exit;
        foreach ($oSeriesWay->WinningNumber as $iSeriesMethodId => $sWnNumber){
            $oSeriesMethod = SeriesMethod::find($iSeriesMethodId);
            $oBasicMethod  = BasicMethod::find($oSeriesMethod->basic_method_id);
            $oBasicMethod->sPosition = $sPosition;
//            pr($oSeriesMethod->offset);
//            pr($oBasicMethod->toArray());
//            continue;
            switch ($this->function) {
                case 'MultiOne':
                case 'LottoMultiOne':
                    $iOffset = $oSeriesMethod->offset >= 0 ? $oSeriesMethod->offset : $oSeriesMethod->offset + $oSeriesWay->digital_count;
                    $sBetNumberOfMethod = $aBetNumbers[$iOffset];
                    break;
                case 'MultiSequencing':
                    $iWidthOfWnNumber   = strlen($sWnNumber);
//                    pr($iWidthOfWnNumber);
                    $aBetNumbers        = explode($sSplitChar, $sBetNumber);
                    $aMultiples         = [];
                    $iMultiple         = 1;
//                    pr($aBetNumbers[0]);
                    foreach ($aBetNumbers as $i => $tmp) {
                        $aMultiples[ $i ] = strlen($aBetNumbers[ $i ]);
                    }
                    foreach ($aBetNumbers as $i => $tmp){
                        if ($i < $oSeriesWay->digital_count - $iWidthOfWnNumber){
//                        $iMultiple *= strlen($aBetNumbers[$i]);
                            unset($aBetNumbers[ $i ]);
                        }
                    }
                    $sBetNumberOfMethod = implode($sSplitChar,$aBetNumbers);
                    break;
                default:
                    $sBetNumberOfMethod = $sBetNumber;
            }
//            pr('bet: ' . $sBetNumberOfMethod);
//            pr('wn: ' . $sWnNumber);
//            continue;
//            pr($aMultiples);
//            exit;
            $aPrizeLevels = $oBasicMethod->getPrizeLevels();
//            pr($aPrizeLevels);
//            exit;
            if ($iCount = $oBasicMethod->checkPrize($oSeriesWay, $this, $sWnNumber, $sBetNumberOfMethod)) {
                if ($this->function == 'MultiSequencing') {
                    for ($i = 0; $i < $oSeriesWay->digital_count - $iWidthOfWnNumber; $iCount *= $aMultiples[ $i++ ]) ;
//                    $iCount *= $iMultiple;
                }
                if(($iLevel = count($aPrizeLevels)) > 1)
                {
                    if($oBasicMethod->wn_function == 'k3contain'){
                        $iLevelIndex = 0;
                        foreach($aPrizeLevels as $sRule=>$sLevel){
                            if(strstr($sWnNumber,strval($sRule)) && strstr(strval($sRule), $sBetNumberOfMethod) && ($iLevelIndex == 0 || $sLevel < $iLevelIndex)) $iLevelIndex = $sLevel;
                        }
                        $iLevel = $iLevelIndex;
                    }
                    elseif($oBasicMethod->wn_function == 'TsSpecial'){
                        $splitCharInArea = Config::get('bet.split_char_lotto_in_area');
                        $aWnNumber = explode($splitCharInArea, $sWnNumber);

                        if(in_array($sBetNumber, ['0','1']) && in_array('2', $aWnNumber)){
                            $iLevel = 2;
                        }else{
                            $iLevel = 1;
                        }
                    }
                    elseif($oBasicMethod->wn_function == 'BjlSum'){
                        $splitChar = Config::get('bet.split_char');
                        $splitCharInArea = Config::get('bet.split_char_lotto_in_area');

                        $aWnNumber = explode($splitCharInArea, $sWnNumber);
                        $aBetNumber = explode($splitChar, $sBetNumberOfMethod);
                        //闲庄,赔率一半
                        if(in_array('7', $aWnNumber) && array_intersect($aBetNumber, ['1'])){
                            $iLevel = 2;
                        }
                        //闲庄,返还投注金额
                        elseif(($count = count(array_intersect($aBetNumber, ['0', '1']))) && in_array('2', $aWnNumber)){
                            $iLevel = 3;
                        }else{
                            $iLevel = 1;
                        }
                    }
                    elseif($oBasicMethod->wn_function == 'BjlSumSpecial'){
                        list($aXNumber, $aZNumber, $iXSum, $iZSum) = $oBasicMethod->getWnNumberBjl($sWnNumber);
                        if((count($aXNumber) == 2 && in_array($iXSum, [8,9])) || (count($aZNumber) == 2 && in_array($iZSum, [8,9])))
                        {
                            if($iXSum == $iZSum) $iLevelIndex = 0;
                            else $iLevelIndex = 4;
                        }else{
                            $iLevelIndex = abs($iXSum - $iZSum);
                        }
                        $iLevel = $aPrizeLevels[$iLevelIndex];
                    }elseif($oBasicMethod->wn_function == 'Pksumsum'){
                        !is_array($sWnNumber) or $sWnNumber = implode($sWnNumber);
                        $iLevelIndex =  intval($sWnNumber);
                        $iLevel = $aPrizeLevels[$iLevelIndex];
                    }elseif($oBasicMethod->wn_function == 'Kl28Sum'){
                        !is_array($sWnNumber) or $sWnNumber = implode($sWnNumber);
                        $iLevelIndex = $oBasicMethod->lottery_type == 2 ? intval($sWnNumber) : DigitalNumber::getSum($sWnNumber);
                        if($oBasicMethod->id == 115) {
                            $sBigSmall = $iLevelIndex < 28 / 2 ? '0' : '1';//1:大 0:小
                            $sOddEven = $iLevelIndex % 2 == 0 ? '0' : '1';//0:双 1:单
                            $iLevelIndex = $sBigSmall.$sOddEven;
                        }
                        $iLevel = $aPrizeLevels[$iLevelIndex];
                    }
                    else{
                        !is_array($sWnNumber) or $sWnNumber = implode($sWnNumber);
                        $iLevelIndex = $oBasicMethod->lottery_type == 2 ? intval($sWnNumber) : DigitalNumber::getSum($sWnNumber);
                        $iLevel = $aPrizeLevels[$iLevelIndex];
                    }
                }

                if (isset($aPrized[ $oSeriesMethod->basic_method_id ])){
                    $aPrized[ $oSeriesMethod->basic_method_id ][ $iLevel ] += $iCount;
                }
                else{
                    $aPrized[ $oSeriesMethod->basic_method_id ][ $iLevel ] = $iCount;
                }
            }
        }
//        pr($aPrized);
//        exit;
        return $aPrized ? $aPrized : false;
    }

    /**
     * 返回中奖号码
     *
     * @param string    $sNumber        full number
     * @param int       $iShape
     * @return string
     */
    public function getWinningNumber($sNumber, $oSeriesWay){
        $sModel = $this->getModel();
//        pr($sModel);
//        pr($oSeriesWay->wn_count);
//        pr($oSeriesWay->digital_count);
        if ($oSeriesWay->wn_count == 1){
            $sWinningNumber = substr($sNumber, intval($oSeriesWay->offset), $oSeriesWay->digital_count);
//            pr($sWinningNumber);
            return $sModel::getWinningNumber($oSeriesWay, $sWinningNumber);
        }
        else{
//            pr($oSeriesWay->basic_methods);
            $aBasicMethods = explode(',', $oSeriesWay->basic_methods);
//            pr($aBasicMethods);
            $aOffsets = explode(',', $oSeriesWay->offset);
            foreach($aBasicMethods as $k => $iBasicMethodId){
                $oBasicMethod = BasicMethod::find($iBasicMethodId);
                $sTmpWnNumber = substr($sNumber, intval($aOffsets[$k]), $oBasicMethod->digital_count);
                $aWnNumber[$iBasicMethodId] = $sModel::getWinningNumber($oSeriesWay, $sTmpWnNumber);
//                $sWinningNumber = substr($sNumber, intval($this->offset), $this->digital_count);
//                $sWinningNumber = substr($sNumber, intval($this->offset), $this->digital_count);
            }
            return $aWnNumber;
        }
    }

    function getFunction($sAction){
        return $sAction . $this->function;
    }

    /**
     * 返回对应的工具类名称
     *
     * @return string
     */
    function getModel(){
        return 'Way' . $this->function;
    }

    /**
     * 分析生成大小单双玩法的投注号码
     * @param string $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfBsde($sBetNumber){
        $a = ['小','大','双','单'];
        $sSplitChar  = Config::get('bet.split_char');
        $aNumbers = explode($sSplitChar,$sBetNumber);
        $aStrings = array_fill(0,count($aNumbers),'');
        foreach($aNumbers as $i => $sNumberOfPosition){
            $aNumberOfPos = str_split($sNumberOfPosition);
            foreach($aNumberOfPos as $iDigital){
                if(isset($a[$iDigital]))
                    $aStrings[$i] .= $a[$iDigital];
                else
                    $aStrings[$i] = '';
            }
        }
        return implode($sSplitChar,$aStrings);
    }

/*
 * $type=1 longhu others is normal number
 *
 */
    function getDisplayBetNumberOfPk10($sBetNumber,$type=1){
        $number = ['1','2','3','4','5','6','7','8','9','10'];
        $sSplitChar = Config::get('bet.split_char');
        $sBetNumber = explode($sSplitChar,$sBetNumber);
        $iDisplayNumber=[];
        if($sBetNumber)
            foreach($sBetNumber as $row=>$sBet){
                if($sBet !== ''){
                    $line = '';
                    $bets = str_split($sBet,1);
                    if($bets)
                        foreach($bets as $bet){
                            if($type === 1)
                                $line .= $number[$row].'-'.$number[$bet].',';
                            else
                                $line .= $number[$bet].',';
                        }
                    $line=rtrim($line,',');
                    $iDisplayNumber[$row]=$line;
                }else{
                    $iDisplayNumber[$row]='';
                }
            }
        return implode($sSplitChar,$iDisplayNumber);
    }
    /**
     * 分析三星特殊玩法的投注号码
     * @param string $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfSpecial($sBetNumber){
        $a = ['豹子','顺子','对子'];
        $aStrings = [];
        $sSplitChar  = Config::get('bet.split_char');
        $aNumbers = str_split($sBetNumber);
        foreach($aNumbers as $iDigital){
            $aStrings[] = $a[$iDigital];
        }
        return implode($sSplitChar,$aStrings);
    }

    /**
     * 分析生成趣味或区间玩法的投注号码
     * @param string $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfAreaOrInterest($bInterest,$sValidNums,$sBetNumber){
        $a = $bInterest ? ['小','大'] : ['一区','二区','三区','四区','五区'];
        $sSplitChar  = Config::get('bet.split_char');
        $aNumbers = explode($sSplitChar,$sBetNumber);
        $aStrings = array_fill(0,count($aNumbers),'');
        $aValidDigitals = explode($sSplitChar,$sValidNums);
        foreach($aValidDigitals as $i => $sValidNums){
            if ($sValidNums != '0-9'){
                $aNumberOfPos = str_split($aNumbers[$i]);
                $aStrings = [];
                foreach($aNumberOfPos as $iDigital){
                    $aStrings[] = $a[$iDigital];
                }
                $aNumbers[$i] = implode($aStrings);
            }
        }
        return implode($sSplitChar,$aNumbers);
    }

    /**
     * 分析生成定单双玩法的显示用的投注号码
     * @param string $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfLottoOddEven($sBetNumber){
        $sSplitChar  = Config::get('bet.split_char_lotto_in_area');
        $aNumbers = explode($sSplitChar, $sBetNumber);
        $aDisplays = array_map(function ($iNumber) {
            return $iNumber . '单' . (5 - $iNumber) . '双';
        },$aNumbers);
        return implode($sSplitChar,$aDisplays);
    }

    /**
     * 分析生成大小单双玩法的投注号码
     * @param string $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfTsbs($sBetNumber){
        $a = ['龙','虎','和'];
        $aNumbers = str_split($sBetNumber);
        $aStrings = array_fill(0,count($aNumbers),'');

        foreach($aNumbers as $i => $iDigital) $aStrings[$i] .= $a[$iDigital];
        return implode('',$aStrings);
    }

    /**
     * 龙虎斗
     * @param $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfTsSpecial($sBetNumber){
        $a = ['龙','虎','和','龙单','虎单','龙双','虎双','龙红','虎红','龙黑','虎黑'];

        $sSplitChar  = Config::get('bet.split_char');
        $aNumbers = explode($sSplitChar,$sBetNumber);
        $aStrings = array_fill(0,count($aNumbers),'');

        foreach($aNumbers as $i => $iDigital) $aStrings[$i] .= $a[$iDigital];
        return implode('|',$aStrings);
    }


    /**
     * 百家乐
     * @param $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfBjlEnum($sBetNumber){
        $a = ['闲','庄','和','大','小','闲对','庄对','SUPER6','闲龙宝','庄龙宝'];

        $sSplitChar  = Config::get('bet.split_char');
        $aNumbers = explode($sSplitChar,$sBetNumber);
        $aStrings = array_fill(0,count($aNumbers),'');

        foreach($aNumbers as $i => $iDigital) $aStrings[$i] .= $a[$iDigital];
        return implode('|',$aStrings);
    }

    /**
     * kl28大小
     * @param $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfKl28BigSmall($sBetNumber){
        switch($sBetNumber){
            case '0' : $sDisplayBetNumber = '小';break;
            case '1' : $sDisplayBetNumber = '大';break;
        }
        return $sDisplayBetNumber;
    }

    /**
     * kl28极值
     * @param $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfKl28Extremum($sBetNumber){
        switch($sBetNumber){
            case '0' : $sDisplayBetNumber = '极小';break;
            case '1' : $sDisplayBetNumber = '极大';break;
        }
        return $sDisplayBetNumber;
    }

    /**
     * kl28串关
     * @param $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfKl28Multiple($sBetNumber){
        switch($sBetNumber){
            case '00' : $sDisplayBetNumber = '小双';break;
            case '01' : $sDisplayBetNumber = '小单';break;
            case '10' : $sDisplayBetNumber = '大双';break;
            case '11' : $sDisplayBetNumber = '大单';break;
        }
        return $sDisplayBetNumber;
    }

    /**
     * kl28单双
     * @param $sBetNumber
     * @return string
     */
    function getDisplayBetNumberOfKl28OddEven($sBetNumber){
        switch($sBetNumber){
            case '0' : $sDisplayBetNumber = '双';break;
            case '1' : $sDisplayBetNumber = '单';break;
        }
        return $sDisplayBetNumber;
    }
}