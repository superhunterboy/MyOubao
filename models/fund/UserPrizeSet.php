<?php

class UserPrizeSet extends BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 1440;
    protected $table = 'user_prize_sets';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'username',
        'user_parent_id',
        'user_parent',
        'lottery_id',
        'group_id',
        'prize_group',
        'classic_prize',
        'valid',
        'is_agent',
        'description'
    ];

    public static $resourceName = 'User Prize Set';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'lottery_id',
        'prize_group',
        'classic_prize',
        'valid',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
//        'group_id' => 'aPrizeGroups',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'user_id' => 'asc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    public static $rules = [
        'user_id'   => 'required|integer',
        'group_id'  => 'required|integer',
    ];

    public static $aUserTypes = ['top-agent', 'Agent'];

    const ERRNO_MISSING_PRIZE_SET = -940;

    protected function getUserTypeFormattedAttribute()
    {
        return static::$aUserTypes[intval($this->user_parent_id != null)];
    }

    /**
     * 获取游戏信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lottery()
    {
        return $this->belongsTo('Lottery', 'lottery_id');
    }

    /**
     * 获得用户的最大奖金组
     *
     * @param $iUserId
     * @return mixed
     */
    public static function getMaxGroup($iUserId)
    {
        return self::where('user_id', '=', $iUserId)
                    ->orderBy('prize_group', 'DESC')
                    ->first();
    }

    public static function getGroupId($iUserId, $iLotteryId, & $sGroupName){
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::makeCacheKeyOfUserLottery($iUserId, $iLotteryId);
            if ($sGroupInfo = Cache::get($sCacheKey)) {
                $bReadDb = false;
                list($iGroupId,$sGroupName) = explode('-',$sGroupInfo);
            }
            else{
                $bPutCache = true;
            }
        }
        if ($bReadDb){
            $oUserPrizeSet = self::where('user_id','=',$iUserId)->where('lottery_id','=',$iLotteryId)->get(['group_id','prize_group'])->first();
//        $oBasicMethods = BasicMethod::all();
            if (is_object($oUserPrizeSet)){
                $sGroupName = $oUserPrizeSet->prize_group;
                $iGroupId = $oUserPrizeSet->getAttribute('group_id');
            }
            else{
                return false;
            }
        }
    //        $aBasicMethods = [];
    //        foreach($oBasicMethods as $oBasicMethod){
    //            $aBasicMethods[$oBasicMethod->id] = $oBasicMethod;
    //        }

        if ($bPutCache){
            $sGroupInfo = $iGroupId . '-' . $sGroupName;
            Cache::forever($sCacheKey, $sGroupInfo);
        }
        return $iGroupId;
    }

    public static function getGroupIdOfUsers($aUsers, $iLotteryId){
        $aGroups = [];
        $oSettings = self::whereIn('user_id', $aUsers)->where('lottery_id' , '=', $iLotteryId)->get(['user_id','group_id','prize_group']);
        if ($oSettings){
            foreach($oSettings as $oSet){
                $aGroups[$oSet->user_id] = [$oSet->group_id,$oSet->prize_group];
            }
        }
        return $aGroups;
    }

    public static function getPrizeSetOfUsers($aUsers, $iLotteryId, $iWayId, & $aGroupNames){
        $aGroupIds = self::getGroupIdOfUsers($aUsers, $iLotteryId, $aGroupNames);
        $oSeriesWay = SeriesWay::find($iWayId);
        $aMethodIds = explode(',', $oSeriesWay->basic_methods);
//        $aPrizeSettingOfMethods = [];
        $data =[];
        $aGroupNames = [];
        foreach($aGroupIds as $iUserId => $aGroupInfo){
            list($iGroupId, $sGroupName) = $aGroupInfo;
            $aGroupNames[$iUserId] = $sGroupName;
            foreach($aMethodIds as $iMethodId){
                $data[$iUserId][$iMethodId] = PrizeDetail::getPrizes($iGroupId, $iMethodId);
            }
        }
        return $data;
    }

    /**
     * [getUserLotteriesPrizeSets 获取用户的彩种奖金组]
     * @param  [Integer] $iUserId    [用户id]
     * @param  [Integer] $iLotteryId [彩种id]
     * @return [Array]               [彩种奖金组]
     */
    public static function getUserLotteriesPrizeSets ($iUserId, $iLotteryId = null, $aColumns = null)
    {
        if (! $iUserId) return false;
        $aColumns or $aColumns = ['id', 'user_id', 'user_parent_id', 'lottery_id', 'group_id', 'prize_group', 'classic_prize', 'username', 'description'];
        $oQuery = self::where('user_id', '=', $iUserId);
        if ($iLotteryId) $oUserPrizeSets = $oQuery->where('lottery_id', '=', $iLotteryId)->first($aColumns);
        else $oUserPrizeSets = $oQuery->get($aColumns);
        return $oUserPrizeSets;
    }

    /**
     * [generateLotteriesPrizeWithSeries 生成用户的彩系->彩种奖金组数据]
     * @param  [Integer] $iUserId [用户id]
     * @return [Array]            [彩系->彩种奖金组数据]
     */
    public static function generateLotteriesPrizeWithSeries($iUserId = null)
    {
        $iUserId or $iUserId = Session::get('user_id');
        $oUserPrizeSets = self::getUserLotteriesPrizeSets($iUserId);
        $aLotteriesPrize = [];
        if ($oUserPrizeSets) {
            foreach ($oUserPrizeSets as $key => $oUserPrizeSet) {
                $aLotteriesPrize[$oUserPrizeSet->lottery_id] = $oUserPrizeSet->getAttributes();
            }
        }
        // pr($aLotteriesPrize);
        $aSeriesLotteries = & Series::getLotteriesGroupBySeries();
        $result = [];
        // pr($aSeriesLotteries[0]->children);exit;
        foreach ($aSeriesLotteries as $key => $aSeries) {
            $aNewChildren = [];
            $aChildren = $aSeries['children'];
            // pr(count($aChildren));exit;
            for ($i = 0, $l = count($aChildren); $i < $l; $i++) {
                // pr($oLottery['id']);exit;
                $data = $aLottery = $aChildren[$i];
                if (isset($aLotteriesPrize[$aLottery['id']]) && $aLotteriesPrize[$aLottery['id']]) {
                    $data['prize_group']   = $aLotteriesPrize[$aLottery['id']]['prize_group'];
                    $data['classic_prize'] = $aLotteriesPrize[$aLottery['id']]['classic_prize'];
                    $data['group_id']      = $aLotteriesPrize[$aLottery['id']]['group_id'];
                    $aNewChildren[]        = $data;
                }
            }
            $aSeries['children'] = $aNewChildren;
            $result[] = $aSeries;
        }
        // pr($result);exit;
        return $result;
    }

    /**
     * [createUserPrizeGroup 创建用户奖金组, 必须在数据库事务中进行]
     * @param  [Object] $oUser      [新建的用户对象]
     * @param  [Array] $aPrizeGroup [奖金组数组]
     * @param  [Array] $aExistUserPrizeGroups [用户已存在的奖金组数组, 代理设置下级时使用]
     * @return [Array]            [成功/失败信息]
     */
    public static function createUserPrizeGroup($oUser, $aPrizeGroup, $aExistUserPrizeGroups = null)
    {
        // pr($aExistUserPrizeGroups);
        $aLotteryPrizeGroups = $oUser->generateLotteryPrizeGroup($aPrizeGroup);
        // pr($aLotteryPrizeGroups);
        $aUserPrizeGroups    = $oUser->generateUserPrizeGroups($aLotteryPrizeGroups);
        // pr($aUserPrizeGroups);exit;
        $aReturnMsg = ['success' => true, 'msg' => __('_basic.updated')];
        foreach($aUserPrizeGroups as $value) {
            $bSucc = true;
            if ($aExistUserPrizeGroups && $aExistUserPrizeGroups[$value['lottery_id']]) {
                $oUserPrizeSet = $aExistUserPrizeGroups[$value['lottery_id']];
/*                if ($oUserPrizeSet->classic_prize > $value['classic_prize'] && $oUserPrizeSet->classic_prize <= Sysconfig::readValue('agent_max_grize_group')) {
                    $bSucc = false;
                    $aReturnMsg = ['success' => $bSucc, 'msg' => __('_userprizeset.less-than-exist-prize-group')];
                    break;
                }*/
            } else {
                $oUserPrizeSet = new UserPrizeSet;
            }
            if ($bSucc) {
                $oUserPrizeSet->fill($value);
                if (! $bSucc = $oUserPrizeSet->save()) {
                    $aReturnMsg = ['success' => $bSucc, 'msg' => __('_basic.update-fail')];
                    break;
                }
            }
        }
        // pr($bSucc);exit;
        return $aReturnMsg;
    }

    private static function makeCacheKeyOfUserLottery($iUserId,$iLotteryId){
        $sClass = get_called_class();
        !static::$cacheUseParentClass or $sClass = get_parent_class($sClass);
        return $sClass . '_' . $iUserId . '_' . $iLotteryId;
    }

    protected function afterSave($oSavedModel){
        $this->deleteOtherCache();
        return parent::afterSave($oSavedModel);
    }

    protected function deleteOtherCache(){
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
             Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
             $sCacheKey = $this->makeCacheKeyOfUserLottery($this->user_id,$this->lottery_id);
            !Cache::has($sCacheKey) or Cache::forget($sCacheKey);
        }
    }
    // public static function getTopAgentPrizeGroupDistribution()
    // {
    //     $aColumns = ['prize_group', 'num'];
    //     $oQuery = self::selectRaw(' *, count(distinct user_id) as num ')->where('valid', '=', 1)->whereNull('parent_id')->orWhere('parent_id', '=', '')->groupBy('prize_group');
    //     return $oQuery->get($aColumns);
    // }

    // public static function getAllAgentPrizeGroup($sUsername = null, $sPrizeGroupStart = null, $sPrizeGroupEnd = null, $sAgentType = null)
    // {
    //     $oQuery = self::where('valid', '=', 1)->where('is_agent', '=', 1);
    //     if ($sUsername) {
    //         $oQuery->where('username', 'like', $sUsername);
    //     }
    //     $oQuery->groupBy('user_id')->orderByRaw(' parent_id, username ');
    //     return $oQuery->get();
    // }

    /**
     * [generateUserPrizeSetData 生成用户的彩种奖金组数组]
     * @param $isAgent
     * @param $setPrizeGroup
     * @param null $iCurPrizeGroup [当前奖金组] 如果存在则是调整，否则为创建
     * @return array|bool [彩种奖金组数组]
     */
    public static function generateUserPrizeSetData($isAgent, $setPrizeGroup, $iCurPrizeGroup = null) {

        $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($isAgent, true)) ? $aPrizeGroups : [];
        $aHighPrizeGroups = ($aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups($isAgent, true)) ? $aHighPrizeGroups : [];

        //不在基础奖金组和配额奖金组内
        if( !in_array($setPrizeGroup, $aPrizeGroups) && !in_array($setPrizeGroup, $aHighPrizeGroups)){
            return false;
        }
        //基础奖金不能下调
        if(in_array($setPrizeGroup, $aPrizeGroups) && $iCurPrizeGroup && in_array($iCurPrizeGroup, $aPrizeGroups) && $setPrizeGroup < $iCurPrizeGroup){
            return false;
        }
        $aPrizeGroup = [];
        $oLotteries = Lottery::all(['id']);
        foreach($oLotteries as $key => $oLottery) {
            // $aPrizeGroup[$oLottery->id] = $iValidPrizeGroup;
            $aPrizeGroup[] = ['lottery_id' => $oLottery->id, 'prize_group' => (string)($setPrizeGroup)];
        }
        return $aPrizeGroup;
    }

}