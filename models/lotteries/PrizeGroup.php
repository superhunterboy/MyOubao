<?php

class PrizeGroup extends BaseModel {

    public static $resourceName = 'Prize Group';
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'series_id',
        'type',
        'name',
        'classic_prize',
        'water',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'series_id' => 'required|integer',
//        'name' => 'required|max:20',
        'classic_prize' => 'required|numeric|max:2000',
        'water' => 'required|numeric|max:0.5',
    ];
    protected $fillable = [
        'series_id',
        'type',
        'name',
        'classic_prize',
        'water',
    ];
    protected $table = 'prize_groups';

    protected function getWaterFormattedAttribute() {
        return ($this->water * 100) . '%';
    }

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'series_id' => 'aSeries',
//        'type' => 'aLotteryTypes',
    ];
    public static $mainParamColumn = 'series_id';
    public static $titleColumn = 'name';

    protected function beforeValidate() {
        $oSeries = Series::find($this->series_id);
        if (!$this->water) {
            $this->water = self::countWater($oSeries->classic_amount, $this->classic_prize);
        } else {
            if (!self::checkWater($oSeries->classic_amount, $this->classic_prize, $this->water)) {
                $this->validationErrors->add('water', __('_prize.water-error'));
                return false;
            }
        }
        if (empty($this->classic_prize)) {
            $this->validationErrors->add('classic_prize', __('_prize.missing-classic-prize'));
            return false;
        }
//        pr($this->getAttributes());
//        die($this->water);
        if (empty($this->series_id)) {
            return false;
        }
        $this->type = $oSeries->type;
        if (empty($this->name)) {
            $this->name = $this->classic_prize;
        }
        return true;
    }

    public static function checkWater($iClassicAmount, $iClassicPrize, $fWater) {
        $fTrue = number_format(1 - $iClassicPrize / $iClassicAmount, 4);
        $fWater = number_format($fWater, 4);
        return $fTrue == $fWater;
    }

    public static function countWater($iClassicAmount, $iClassicPrize) {
        return number_format(1 - $iClassicPrize / $iClassicAmount, 4);
    }

    /**
     * run after save
     * @return boolean
     */
    protected function afterSave($bSucc, $bNew = false) {
        if (!$bSucc)
            return $bSucc;
        $aBasicMethodId = [];
//        $aBasicMethodId = array_column(BasicMethod::where('series_id','=', $this->series_id)->get()->toArray(), 'id');
        $aBasicMethods = SeriesWay::where('series_id', '=', $this->series_id)->get(['basic_methods']);
        foreach ($aBasicMethods as $sKey => $oValue) {
//           pr( $oValue->basic_method_ids);
            $aBasicMethodId = array_merge($aBasicMethodId, $oValue->basic_method_ids);
        }
        $aBasicMethodId = array_unique($aBasicMethodId);
        $aConditions = ['basic_method_id' => ['in', $aBasicMethodId]];
//         $aConditions = ['lottery_type_id' => ['=', $this->type]];
        $aFields = [
            'basic_method_id', 'level', 'probability', 'full_prize', 'max_prize', 'max_group', 'min_water', 'fixed_prize'
        ];

        $oPrizeLevel = new PrizeLevel;
        $aPrizeLevels = $oPrizeLevel->doWhere($aConditions)->get($aFields)->toArray();
//        pr($aPrizeLevels);
//        EXIT;
        $oSeries = Series::find($this->series_id);
//        pr($this->classic_prize);
//        pr($oSeries->classic_amount);
//        pr($aPrizeLevels);
//        exit;
        foreach ($aPrizeLevels as $aBasicLevel) {
//            pr($aBasicLevel);
//            $fPrize = formatNumber($this->classic_prize / $oSeries->classic_amount * $aBasicLevel['full_prize'], 4);
            $fPrize = $aBasicLevel['fixed_prize'] > 0 ? $aBasicLevel['fixed_prize'] : PrizeDetail::countPrize($oSeries, $this->classic_prize, 1960, $aBasicLevel);

//            if ($aBasicLevel['basic_method_id']== 53){
//                pr($aBasicLevel['full_prize']);
//                $fPrize = self::countPrize($oSeries,$this->classic_prize,1960,$aBasicLevel);
//                pr($fPrize);
//            exit;
//            }
//            $fPrize = $this->classic_prize / 2000 * $aBasicLevel[ 'full_prize' ],PrizeDetail::$amountAccuracy);
//            $fPrize <= $aBasicLevel['max_prize'] or $fPrize = $aBasicLevel['max_prize'];
//            $aBasicLevel['max_group'] >= 1960 or $fPrize *= $aBasicLevel['max_group'] / 1960;
//            if ($aBasicLevel['basic_method_id']== 53){
//                pr($fPrize);
//                exit;
//            }
//            $fPrize = formatNumber($fPrize, PrizeDetail::$amountAccuracy);
            $aAttributes = [
                'series_id' => $this->series_id,
                'group_id' => $this->id,
                'method_id' => $aBasicLevel['basic_method_id'],
                'level' => $aBasicLevel['level'],
                'probability' => $aBasicLevel['probability'],
                'classic_prize' => $this->classic_prize,
                'group_name' => $this->name,
                'prize' => $fPrize,
                'full_prize' => $aBasicLevel['full_prize'],
            ];
            $oPrizeDetail = new PrizeDetail;
            $aConditions = [
                'group_id' => ['=', $this->id],
                'method_id' => ['=', $aBasicLevel['basic_method_id']],
                'level' => ['=', $aBasicLevel['level']],
            ];
            $oExistsDetail = $oPrizeDetail->doWhere($aConditions)->get(['id'])->first();
            empty($oExistsDetail) or $oPrizeDetail = $oExistsDetail;
//            unset($oExistsDetail);
//            pr($aAttributes);
            $oPrizeDetail->fill($aAttributes);
//            pr($aAttributes['id']);
//            $oPrizeDetail->id = $aAttributes['id'];
//            $oPrizeDetail = new PrizeDetail($aAttributes);
//            pr($oPrizeDetail->getAttributes());
//            pr($oPrizeDetail->id);exit;
//            $aDetails[] = $aAttributes;
//            pr($aAttributes);
            if (!$bSucc = $oPrizeDetail->save(PrizeDetail::$rules)) {
                return false;
            }
//            pr($oPrizeDetail->getAttributes());
//            if ($i++ >= 5) break;
        }
//        pr($aDetails);
//        exit;
        return $bSucc;
    }

    /**
     * [getPrizeGroupByClassicPrize 根据奖金值获取奖金组详情]
     * @param  [Integer]  $iClassicPrize [经典奖金值]
     * @param  [Integer]  $iLotteryType  [彩种类型]
     * @return [Object]                  [奖金组详情]
     */
    public static function getPrizeGroupByClassicPrize($iClassicPrize, $iLotteryType) {
        if (!$iClassicPrize || !$iLotteryType)
            return false;
        return self::where('classic_prize', '=', $iClassicPrize)->where('type', '=', $iLotteryType)->get()->first();
    }

    /**
     * 根据奖金值获取奖金组详情
     *
     * @param  [Integer]  $iClassicPrize [经典奖金值]
     * @param  [Integer]  $series_id  [彩种系列]
     * @return bool
     */
    public static function getPrizeGroupByClassicPrizeAndSeries($iClassicPrize, $series_id) {
        if (!$iClassicPrize || !$series_id)
            return false;
        return self::where('classic_prize', '=', $iClassicPrize)->where('series_id', '=', $series_id)->get()->first();
    }

    /**
     * [getPrizeGroupByName 根据奖金名获取奖金组详情]
     * @param  [String]  $sPrizeGroup  [奖金组名称]
     * @param  [Integer] $iLotteryType [彩种类型]
     * @return [Object]                [奖金组详情]
     */
    public static function getPrizeGroupByName($sPrizeGroup) {
        if (!$sPrizeGroup)
            return false;
        return self::where('name', '=', $sPrizeGroup)->get(); // ->where('type', '=', $iLotteryType)
    }

    /**
     * 获得奖金设置详情数组
     * @param int $iClassicPrize
     * @return array &
     */
    public static function & getPrizeDetails($iGroupId) {
//        $iPrizeGroupId = PrizeGroup::where('classic_prize' , '=' , $iClassicPrize)->get(['id'])->first()->getAttribute('id');
//        $oPrizeDetails = PrizeDetail::where('group_id', '=', $iGroupId)->get();
////        $oBasicMethods = BasicMethod::all();
//        $aPrizes = [];
////        $aBasicMethods = [];
////        foreach($oBasicMethods as $oBasicMethod){
////            $aBasicMethods[$oBasicMethod->id] = $oBasicMethod;
////        }
//        // pr(json_encode($oPrizeDetails->toArray()));exit;
//        foreach ($oPrizeDetails as $oPrizeDetail) {
//            $aPrizes[$oPrizeDetail->method_id] = $oPrizeDetail;
//        }
//        return $aPrizes;
        return PrizeDetail::getDetails($iGroupId);
    }

    /**
     * [getPrizeGroupsBelowExistGroup 获取某个奖金组以下的n个奖金组]
     * @param  [integer] $iPrizeGroup [奖金组]
     * @param  [integer] $iSeriesId   [彩系id]
     * @return [Array]                [奖金组数组]
     */
    public static function & getPrizeGroupsBelowExistGroup($iPrizeGroup, $iSeriesId, $iCount = 6, $iPrizeGroupMin = 1800) {
        if (!$iPrizeGroup || !$iSeriesId)
            return false;
        $aColumns = ['id', 'type', 'name', 'classic_prize', 'water'];
        $oQuery = self::where('series_id', '=', $iSeriesId)
                        ->where('classic_prize', '<=', $iPrizeGroup)
                        ->where('classic_prize', '>=', $iPrizeGroupMin)
                        ->orderBy('classic_prize', 'desc')->limit($iCount);
        $data = $oQuery->get($aColumns);
        return $data;
    }

    public static function & getPrizeGroupWaterMap() {
        $aColumns = ['classic_prize', 'water'];
        $aPrizeGroupWaters = self::all($aColumns);
        $data = [];
        foreach ($aPrizeGroupWaters as $key => $value) {
            $data[$value->classic_prize] = ($value->water * 100) . '%';
        }
        return $data;
    }

    public static function getPrizeGroupsByParams($aParams, $iSeriesId = null, $aColumns = null) {
        $aColumns or $aColumns = ['id', 'series_id', 'name', 'classic_prize'];
        // foreach ($aParams as $aParam) {
        //     foreach ($aParam as $key => $value) {
        //         if (! isset($oQuery)) {
        //             $oQuery = self::where($key, '=', $value);
        //         } else {
        //             $oQuery->where($key, '=', $value);
        //         }
        //     }
        // }
        $oQuery = self::whereIn('classic_prize', $aParams);
        if ($iSeriesId)
            $oQuery->where('series_id', '=', $iSeriesId);
        $aData = $oQuery->get($aColumns);
//         $queries = DB::getQueryLog();
//         $last_query = end($queries);
//         pr($last_query);exit;
        return $aData;
    }

    public static function getPrizeGroupsWithOnlyKey($aParams, $iSeriesId = null, $aColumns = null) {
        $aGroups = [];
        $aPrizeGroups = self::getPrizeGroupsByParams($aParams);

        foreach ($aPrizeGroups as $value) {
            $key = $value->series_id . '_' . $value->classic_prize;
            $aGroups[$key] = $value;
        }
        return $aGroups;
    }

    /**
     * 根据系统配置参数，生成总代奖金组信息
     * @return array
     */
    public static function getTopAgentPrizeGroups() {
        $iMinGroup = SysConfig::readValue('top_agent_min_grize_group') + 1;
        $iMaxGroup = SysConfig::readValue('top_agent_max_grize_group');
        $aTopAgentPrizeGroups = range($iMinGroup, $iMaxGroup);
        return $aTopAgentPrizeGroups;
    }

    /**
     * 根据彩种系列id和奖金组名称获取奖金组信息
     * @param int $iSeriesId        彩种系列id
     * @param string $sName       奖金组名称
     * @return object PrizeGroup
     */
    public static function getPrizeGroupsBySeriesName($iSeriesId, $sName) {
        $aColumns = ['id', 'series_id', 'name', 'classic_prize'];
        $oSeries = Series::find($iSeriesId);
        if (!is_null($oSeries->link_to)) {
            $iSeriesId = $oSeries->link_to;
        }
        $oQuery = self::where('series_id', '=', $iSeriesId)->where('name', '=', $sName);
        $aData = $oQuery->get($aColumns)->first();
        return $aData;
    }

    /**
     * 工具方法,检验指定奖金组是否在指定范围内
     * @param int $prizeGroup               需验证的奖金组
     * @param int $minPrizeGroup        最小奖金组
     * @param int $maxPrizeGroup        最大奖金组
     * @return boolean                  是否在最小与最大之间
     */
    public static function checkExistPrizeGroup($prizeGroup, $minPrizeGroup, $maxPrizeGroup) {
        if ($minPrizeGroup <= $prizeGroup && $prizeGroup <= $maxPrizeGroup) {
            return true;
        } else {
            return false;
        }
    }

    public static function & getAllPrizeGroups($iSeriesId) {
        $bReadDb = true;
        $bPutCache = false;
        $aGroups = [];
        $oPrizeGroups = self::where('series_id', '=', $iSeriesId)->orderBy('name', 'asc')->get();

        foreach ($oPrizeGroups as $oPrizeGroup) {
            $aGroups[$oPrizeGroup->id] = $oPrizeGroup->toArray();
        }

        return $aGroups;
    }

}
