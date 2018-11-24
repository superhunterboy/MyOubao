<?php
/**
 * 系列投注方式模型类
 */
class SeriesWay extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    const ERRNO_SERIES_WAY_MISSING          = -930;
    const ERRNO_SERIES_WAY_CLOSED           = -931;
    const ERRNO_SERIES_BET_NUMBER_WRONG      = -932;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'series_ways';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'series_id',
        'name',
        'short_name',
        'series_way_method_id',
        'basic_way_id',
        'basic_methods',
        'series_methods',
        'digital_count',
        'price',
//        'shape',
        'offset',
        'buy_length',
        'wn_length',
        'wn_count',
        'area_count',
        'area_config',
        'area_position',
        'valid_nums',
        'rule',
        'all_count',
        'bet_note',
        'bonus_note',
        'is_enable_extra'
    ];

    public static $resourceName = 'Series Way';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'short_name',
        'basic_way_id',
//        'basic_methods',
        'series_methods',
        'digital_count',
//        'price',
//        'shape',
        'offset',
        'buy_length',
        'wn_length',
        'wn_count',
        'area_count',
        'area_config',
        'area_position',
        'valid_nums',
        'is_enable_extra'
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'series_id' => 'aSeries',
        'basic_way_id' => 'aBasicWays',
//        'basic_methods' => 'aBasicMethods',
        'series_methods' => 'aSeriesMethods',
        'series_way_method_id' => 'aSeriesWayMethods',
    ];

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
    public static $mainParamColumn = 'series_id';
    public static $titleColumn            = 'name';
    public static $rules = [
        'series_id' => 'required|integer',
        'series_way_method_id' => 'required|integer',
//        'basic_methods' => 'required|max:200',
        'name' => 'required|max:30',
        'short_name' => 'required|max:30',
        'digital_count' => 'required|numeric',
        'price' => 'required|numeric',
//        'shape' => 'max:100',
        'offset' => 'required|max:30',
        'buy_length' => 'required|numeric',
        'wn_length' => 'required|numeric',
        'wn_count' => 'required|numeric',
        'area_count' => 'integer',
        'area_config' => 'max:20',
        'area_position' => 'max:10',
        'valid_nums' => 'max:50',
        'rule' => 'max:50',
        'all_count' => 'max:100',
        'is_enable_extra' => 'in:0,1'
    ];

    public $WinningNumber;

    protected function beforeValidate() {
        parent::beforeValidate();
        $oBasicWay = BasicWay::find($this->basic_way_id);
        $oWayMethod = SeriesWayMethod::find($this->series_way_method_id);
        $this->basic_way_id = $oWayMethod->basic_way_id;
//        if (!strlen($this->shape)){
        $aSeriesMethodId = explode(',',$this->series_methods);
        $aOffsets = $aAllCount = $aBasicMethods = [];
        foreach($aSeriesMethodId as $iSeriesMethodId){
            $oSeriesMethod = SeriesMethod::find($iSeriesMethodId);
            if (empty($iSeriesMethodId)){
                return false;
            }
            $oBasicMethod    = BasicMethod::find($oSeriesMethod->basic_method_id);
            $aBasicMethods[] = $oSeriesMethod->basic_method_id;
//            $aShapes[] = $oBasicMethod->shape;
            $aOffsets[] = $oSeriesMethod->offset;
            $aAllCount[] = $oBasicMethod->all_count;
        }
        $this->basic_methods = implode(',',$aBasicMethods);
        $this->all_count     = implode(',',$aAllCount);
        $this->offset = implode(',', $aOffsets);
//        $this->shape = implode(',', $aShapes);
        $this->price or $this->price = $oBasicMethod->price;
        $this->buy_length or $this->buy_length = $oBasicMethod->buy_length;
        $this->wn_length or $this->wn_length = $oBasicMethod->wn_length;
        $this->wn_count or $this->wn_count = $oBasicMethod->wn_count;
        strlen($this->valid_nums) or $this->valid_nums = $oBasicMethod->valid_nums;
        strlen($this->rule) or $this->rule          = $oBasicMethod->rule;
        $this->area_count or $this->area_count    = null;
        //        }
        unset($aSeriesMethodId,$iSeriesMethodId);
        return true;
    }

    /**
     * 返回数据列表
     * @return array
     */
    public static function & getTitle() {
        $data = [];
        $oModels = self::all([ 'id', 'basic_way_id', static::$titleColumn]);
        foreach($oModels as $oModel){
            $data[$oModel->id] = $oModel->compileDisplayName();
        }
        return $data;
    }

    /**
     * 从基础方式、基础玩法、方式与玩法的关联、系列与玩法的关联等四个模型，进行数据转换生成系列方式数据并保存
     *
     * 本方法主要用于本对象的数据初始化，一旦数据整理完成后，请慎用
     *
     * @param int $iSeriesId
     * @return true | validationErrors->toArray
     */
    function makeSeriesWayData($iSeriesId = 1){
        $oSeriesMethods = SeriesMethod::where('series_id','=',$iSeriesId)->get();
        $oSeries        = Series::find($iSeriesId);
        $aBasicMethods = $aSeriesMethods = [];
        foreach($oSeriesMethods as $oSeriesMethod){
            $aSeriesMethods[$oSeriesMethod->id] = $oSeriesMethod->getAttributes();
        }
        $oBasicMethods = BasicMethod::where('lottery_type','=',$oSeries->type)->get();
        foreach($oBasicMethods as $oMethod){
            $aBasicMethods[$oMethod->id] = $oMethod->getAttributes();
        }
        $oWayMethods = SeriesWayMethod::where('series_id', '=', $iSeriesId)->get();
        $oBasicWays = BasicWay::all();
        $aBasicWays = [];
        foreach($oBasicWays as $oBasicWay){
            $aBasicWays[$oBasicWay->id] = $oBasicWay->getAttributes();
        }

//        pr($aBasicMethods);
//        exit;
        foreach($oWayMethods as $oWayMethod){
//            if (!$oWayMethod->single){
//                continue;
//            }
//            pr($oWayMethod->getAttributes());
            $aSeriesMethodId = explode(',', $oWayMethod->series_methods);
            if ($oWayMethod->single){
//                continue;
//                pr($aSeriesMethodId);
                $aAllCount = $aOffset = $aDigitalCount = $aBasicMethodID = [];
                foreach($aSeriesMethodId as $iSeriesMethodId){
//                    pr($aBasicMethods);
//                    exit;
                    $aSeriesMethod = & $aSeriesMethods[$iSeriesMethodId];
//                    pr($aSeriesMethod);
                    $iBasicMethodId = $aSeriesMethod['basic_method_id'];
//                    $aShape[] = $aBasicMethods[$iBasicMethodId]['shape'];
                    $aAllCount[] = $aBasicMethods[$iBasicMethodId]['all_count'];
                    $aOffset[] = $aSeriesMethod['offset'];
                    $aDigitalCount[] = $aBasicMethods[$iBasicMethodId]['digital_count'];
                    $aBasicMethodID[] = $iBasicMethodId;
                }

                $aSeriesWays[] = [
                    'series_id' => $oWayMethod->series_id,
                    'basic_way_id' => $oWayMethod->basic_way_id,
                    'series_way_method_id' => $oWayMethod->id,
//                            'name' => $aName[$i] . $aBasicWays[$oWayMethod->basic_way_id]['name'],
                    'name'           => $oWayMethod->name,
                    'short_name'     => $aBasicMethods[ $iBasicMethodId ][ 'name' ],
//                    'digital_count' => $aBasicMethods[$iBasicMethodId]['digital_count'],
                    'digital_count'  => max($aDigitalCount),
                    'price'          => $aBasicMethods[ $iBasicMethodId ][ 'price' ],
//                    'shape'          => implode(',',$aShape),
                    'offset'         => implode(',',$aOffset),
//                    'offset' => $aSeriesMethod['offset'],
                    'buy_length'     => $aBasicMethods[ $iBasicMethodId ][ 'buy_length' ],
                    'wn_length'      => $aBasicMethods[ $iBasicMethodId ][ 'wn_length' ],
                    'wn_count'       => $aBasicMethods[ $iBasicMethodId ][ 'wn_count' ],
                    'valid_nums'     => $aBasicMethods[ $iBasicMethodId ][ 'valid_nums' ],
                    'rule'           => $aBasicMethods[ $iBasicMethodId ][ 'rule' ],
                    'all_count'      => implode(',',$aAllCount),
//                    'all_count' =>  $aBasicMethods[$iBasicMethodId]['all_count'],
                    'basic_methods'  => implode(',',$aBasicMethodID),
                    'series_methods' => $oWayMethod->series_methods,
                ];
//                pr($aShape);
//                pr($aAllCount);
//                pr($aName);
//                break;
            }
            else{
                foreach($aSeriesMethodId as $iSeriesMethodId){
                    $aSeriesMethod = & $aSeriesMethods[$iSeriesMethodId];
//                    pr($aSeriesMethod);
                    $iBasicMethodId = $aSeriesMethod['basic_method_id'];
//                    continue;
                    $aSeriesWays[] = [
                        'series_id' => $oWayMethod->series_id,
                        'basic_way_id' => $oWayMethod->basic_way_id,
                        'series_way_method_id' => $oWayMethod->id,
//                            'name' => $aName[$i] . $aBasicWays[$oWayMethod->basic_way_id]['name'],
                        'name' => $aSeriesMethod['name'] . $aBasicWays[$oWayMethod->basic_way_id]['name'],
//                        'name' => $oWayMethod->name,
//                        'short_name' => $aBasicWays[$oWayMethod->basic_way_id]['name'],
                        'short_name' => $aBasicMethods[$iBasicMethodId]['name'],
                        'digital_count' => $aBasicMethods[$iBasicMethodId]['digital_count'],
                        'price' => $aBasicMethods[$iBasicMethodId]['price'],
//                        'shape' => $aBasicMethods[$iBasicMethodId]['shape'],
                        'offset' => $aSeriesMethod['offset'],
                        'buy_length' => $aBasicMethods[$iBasicMethodId]['buy_length'],
                        'wn_length' => $aBasicMethods[$iBasicMethodId]['wn_length'],
                        'wn_count' => $aBasicMethods[$iBasicMethodId]['wn_count'],
                        'valid_nums' => $aBasicMethods[$iBasicMethodId]['valid_nums'],
                        'rule' => $aBasicMethods[$iBasicMethodId]['rule'],
                        'all_count' => $aBasicMethods[$iBasicMethodId]['all_count'],
                        'basic_methods' => $iBasicMethodId,
                        'series_methods' => $iSeriesMethodId,
                    ];
                }
            }
        }
//        pr($aSeriesWays);
//        exit;
        $bSucc = true;
        foreach($aSeriesWays as $aSeriesWay){
            $oSeriesWay = new SeriesWay($aSeriesWay);
            $aConditions = [
                'series_id' => ['=', $aSeriesWay['series_id']],
                'basic_way_id' => ['=', $aSeriesWay['basic_way_id']],
                'basic_methods' => ['=', $aSeriesWay['basic_methods']],
                'series_methods' => ['=', $aSeriesWay['series_methods']],
                'offset' => ['=', $aSeriesWay['offset']],
            ];
            if ($oSeriesWay->doWhere($aConditions)->get(['id'])->first()){
                echo 'pass:' . $oSeriesWay->name . '<br>';
                continue;
//                $oSeriesWay->exists = true;
            }
            if (!$bSucc = $oSeriesWay->save()){
                break;
            }
        }
        return $bSucc ? $bSucc : var_export($oSeriesWay->validationErrors->toArray(),true);
    }

    /**
     * 整理投注号码，将不必要的分隔符及占位符删除
     *
     * @param string $sBetNumber
     * @return string
     */
    public function compileBetNumber($sBetNumber){
        $sSplitChar = Config::get('bet.split_char') or $sSplitChar = '|';

        if ($this->area_count == 1){
            $sBetNumber = str_replace(str_split(Config::get('bet.possible_split_chars')),'',$sBetNumber);
        }
        else{
            if ($this->area_count > -1){
                $aParts = explode($sSplitChar, $sBetNumber);
                if (count($aParts) > $this->area_count){
//                    if (count($aParts) > $this->digital_count){
                    foreach($aParts as $i => $sNumber){
                        if (!preg_match('/^\d*$/', $sNumber)){
                            unset($aParts[ $i ]);
                        }
                        else{
                            $aParts = DigitalNumber::getCombinNumber($sNumber);
                        }
                    }
//                    }
                    $sBetNumber = implode($sSplitChar , $aParts);
                }
                if (count($aParts) != $this->area_count){
                    $sBetNumber = '';
                }
            }
        }
        return $sBetNumber;
    }

    /**
     * 计算注数
     *
     * @param array $aOrder betNumber
     * @return int
     */
    public function count(& $aOrder){
        $oBasicWay = BasicWay::find($this->basic_way_id);
        $oBasicMethod=BasicMethod::find($this->basic_methods);
        if ($this->area_count == 1){
            $aOrder['bet_number'] = str_replace('|', '', $aOrder['bet_number']);
        }

        switch($oBasicWay->function)
        {
            case 'MultiSequencing': // 直选组合

                $sSplitChar = Config::get('bet.split_char') or $sSplitChar = '|';
                $aNumbers   = explode($sSplitChar,$aOrder[ 'bet_number' ]);
                $iCount      = $this->digital_count;
                $aNetNumbers = [];
                foreach ($aNumbers as $sNumber){
                    $aNums      = array_unique(str_split($sNumber));
                    $aNumbers[] = implode($aNums);
                    $iCount *= count($aNums);
                }
                $sNumber = implode($sSplitChar,$aNumbers);
                break;

            case 'TwoStarBigSmall': //二星大小
                $iCount = strlen($aOrder['bet_number']);
                break;

            default :
                $optional = isset($aOrder['position']) ? $aOrder['position'] : null;
                $iCount = $oBasicWay->count($aOrder['bet_number'], $this,$optional);
                break;
        }
        switch($oBasicWay->function){
            case 'PkBigSmallOddEven':
            case 'BigSmallOddEven':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfBsde($aOrder['bet_number']);
                break;

            case 'SpecialConstituted':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfSpecial($aOrder['bet_number']);
                break;

            case 'FunSeparatedConstituted':
                $bInterest = true;
                break;

            case 'SectionalizedSeparatedConstituted':
                isset($bInterest) or $bInterest = false;
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfAreaOrInterest($bInterest, $this->valid_nums, $aOrder['bet_number']);
                break;

            case 'LottoConstituted':
                $oBasicMethod = BasicMethod::find($this->basic_methods);
                if ($oBasicMethod->wn_function == 'LottoOddEven'){
                    $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfLottoOddEven($aOrder['bet_number']);
                }
                break;

            case 'TwoStarBigSmall':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfTsbs($aOrder['bet_number']).' '.$this->name;
                $this->name = '龙虎和';
                break;

            case 'TwoStarSpecial':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfTsSpecial($aOrder['bet_number']);
                break;

            case 'BjlEnum':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfBjlEnum($aOrder['bet_number']);
                break;
            case 'Dragonwithtiger':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfPk10($aOrder['bet_number']);
                break;
            case 'Pkconstituted':
            case 'PKSeparatedConstituted':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfPk10($aOrder['bet_number'],2);
                break;
//            default:
//                $aOrder['display_bet_number'] = $aOrder['bet_number']
            case 'BigSmall':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfKl28BigSmall($aOrder['bet_number']);
                break;
            case 'Extremum':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfKl28Extremum($aOrder['bet_number']);
                break;
            case 'Multiple':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfKl28Multiple($aOrder['bet_number']);
                break;
            case 'OddEven':
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfKl28OddEven($aOrder['bet_number']);
                break;
        }
//        pr($oBasicWay->function.$oBasicMethod->wn_function);
//        if($oBasicWay->function.$oBasicMethod->wn_function=='ConstitutedPkconstituted'){
//            $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfPk10($aOrder['bet_number'],2);
//        }
        switch($oBasicWay->function.$oBasicMethod->wn_function){
            case 'EnumPkqual';
            case 'ConstitutedPkconstituted';
                $aOrder['display_bet_number'] = $oBasicWay->getDisplayBetNumberOfPk10($aOrder['bet_number'],2);
                break;

        }

        return $iCount;

//        pr($aOrder);
//        exit;

    }

    /**
     * 获取中奖号码
     *
     * @param string $sBaseWinningNumber
     * @return string|array
     */
//    public function getWinningNumber($sBaseWinningNumber){
//        $oBasicWay = BasicWay::find($this->basic_way_id);
//        $sWinningNumber = substr($sBaseWinningNumber, intval($this->offset), $this->digital_count);
//        return $oBasicWay->getWinningNumber($sWinningNumber);
//    }

    public function & getWinningNumber(& $aWnNumberOfMethods){
        $aWnNumbers = [];
        foreach ($this->series_method_ids as $iSeriesMethodId){
            if ($aWnNumberOfMethods[ $iSeriesMethodId ] === false){
                continue;
            }
            $aWnNumbers[ $iSeriesMethodId ] = $aWnNumberOfMethods[ $iSeriesMethodId ];
        }
        $this->setWinningNumber($aWnNumbers);
        return $aWnNumbers;
//        $oBasicWay      = BasicWay::find($this->basic_way_id);
//        return $oBasicWay->getWinningNumber($sWinningNumber);
    }

    public function compileDisplayName(){
        $oBasicWay = BasicWay::find($this->basic_way_id);

        switch($oBasicWay->function)
        {
            case 'TwoStarBigSmall':
                $name = '龙虎和';
                break;
            default:
                $name = $this->name;
                break;
        }

        return $name;
    }


    public function checkPrize($sBetNumber,$sPostion=null){
        $oBasicWay = BasicWay::find($this->basic_way_id);
        return $oBasicWay->checkPrize($this,$sBetNumber,$sPostion);
    }

    public function setWinningNumber($aWinningNumber){
        $this->WinningNumber = count($aWinningNumber) > 0 ? $aWinningNumber : false;
    }

    protected function getBasicMethodIdsAttribute(){
        return explode(',',$this->attributes[ 'basic_methods' ]);
    }

    protected function getSeriesMethodIdsAttribute(){
        return explode(',',$this->attributes[ 'series_methods' ]);
    }

    /**
     * 重写方法，支持竞彩玩法
     * @param type $bOrderByTitle
     * @return type
     */
    public static function & getTitleList($bOrderByTitle = true) {
        $aList = & parent::getTitleList($bOrderByTitle);
        $aList += \JcModel\JcMethodGroup::getTitleList($bOrderByTitle);
        $aList += BlackJackWay::getTitleList($bOrderByTitle);
        return $aList;
    }
}
