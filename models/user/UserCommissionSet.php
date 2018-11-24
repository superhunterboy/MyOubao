<?php

class UserCommissionSet extends BaseModel {

    public static $resourceName      = 'UserCommissionSet';
    protected $table                 = 'user_commission_sets';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'type_id',
        'username',
        'commission_rate',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'type_id'    => 'required|integer',
        'series_set_id'    => 'required|integer',
        'commission_rate'  => 'required',
    ];

    protected $fillable = [
        'type_id',
        'series_set_id',
        'parent_id',
        'forefather_ids',
        'username',
        'user_id',
        'commission_rate',
        'is_agent',
    ];

    public $oParent = null;


    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'type_id' => 'aTypeIds',
        'series_set_id' => 'aSeriesSets',
    ];

    /**
     * 获取用户返点
     * @param $iUserId
     * @param null $iSeriesId
     * @return bool
     */
    public static function getUserSeriesCommissionSets($iUserId, $iSeriesId = null){
        if (! $iUserId) return false;
        $oQuery = self::where('user_id', '=', $iUserId);

        if ($iSeriesId){
            $aSeriesSetSets = SeriesSet::whereRaw(' find_in_set(?, series_ids)', [$iSeriesId])->get()->toArray();
            $oQuery->whereIn('series_set_id', array_column($aSeriesSetSets, 'id'));
        }

        return $oQuery->orderBy('type_id', 'asc')->get();
    }
    
    public static function getSportUserCommissionSets($iUserId, $iSeriesId = null){
        $aCommissionSets = [];
        $oUserCommissionSets = self::getUserSeriesCommissionSets($iUserId, $iSeriesId);
        foreach($oUserCommissionSets as $oUserCommissionSet){
            if (in_array($oUserCommissionSet->series_set_id, SeriesSet::$aSingleIds)){
                $aCommissionSets['single'] = $oUserCommissionSet;
            }
            if (in_array($oUserCommissionSet->series_set_id, SeriesSet::$aMixIds)){
                $aCommissionSets['mix'] = $oUserCommissionSet;
            }
        }
        return $aCommissionSets;
    }


    /**
     *获取用户返点
     * @param $iUserId
     * @param $seriesSetId
     * @return mixed
     */
    public static function getUserCommissionSet($iUserId, $seriesSetId){
        return self::where('user_id', '=', $iUserId)->where('series_set_id', '=', $seriesSetId)->first();
    }
    public static function getUserCommissionSetList($aConditions,$aColumns=array('*'))
    {
        return self::doWhere($aConditions)->get($aColumns);
    }
    /**
     * 获取下级用户的返点
     * @param $iUserId
     * @param $seriesSetId
     * @return mixed
     */
    public static function getSubCommissionSets($iUserId, $seriesSetId){
        return self::where('parent_id', '=', $iUserId)->where('series_set_id', '=', $seriesSetId)->get();
    }

    /**
     * 创建用户返点
     * @param $oUser
     * @param array $aSeriesSet
     * @return array
     */
    public static function createCommissionRate($oUser, array $aSeriesSet){
        $failMsg = ['success' => false, 'msg' => __('_basic.created')];
        $sussMsg = ['success' => true, 'msg' => __('_basic.create-fail')];

        $aSeriesSetId = array_column(SeriesSet::all()->toArray(), 'type_id', 'id');
        if(count($aSeriesSetId) != count($aSeriesSet) || array_diff(array_keys($aSeriesSetId), array_keys($aSeriesSet))){
            //todo 设置的返点不存在
            return $failMsg;
        }

        foreach($aSeriesSet as $series_set_id => $commission_rate){

            if($aSeriesSetId[$series_set_id] == SeriesSet::TYPE_LOTTERY && $oUser->prize_group != self::getPrizeGroupByRate($commission_rate)){
                //todo 返点对应的奖金组和实际奖金组不相等
                return ['success' => false, 'msg' => '奖金组不一样'];
            }

            $data = [
                'type_id' => $aSeriesSetId[$series_set_id],
                'series_set_id' => $series_set_id,
                'parent_id' => $oUser->parent_id,
                'forefather_ids' => $oUser->forefather_ids,
                'username' => $oUser->username,
                'user_id' => $oUser->id,
                'is_agent' => $oUser->is_agent,
                'commission_rate' => (float)$commission_rate,
            ];
		

            $oUserCommissionSet  = new UserCommissionSet($data);

            $aReturnMsg = $oUserCommissionSet->validateData();
            if(!$aReturnMsg['success']) return $aReturnMsg;

            if(! $oUserCommissionSet->save()){
                return $failMsg;
            }
        }

        return $sussMsg;
    }

    /**
     * 修改用户返点
     * @param $rate
     * @return array
     */
    public function updateCommissionRate($rate){
        $this->commission_rate = $rate;
        $aReturnMsg = $this->validateData();
        if(!$aReturnMsg['success']) return $aReturnMsg;
        if(! $this->save()){
            return ['success' => false, 'msg' => __('_basic.update-fail')];
        }else{
            return ['success' => true, 'msg' => __('_basic.updated')];
        }
    }

    /**
     * 数据验证
     * @return array
     */
    public function validateData(){
        /*返点合法性验证开始*/
        $fMax = $fMin = 0;
        $sGameName = '';
        switch($this->series_set_id){
            case SeriesSet::ID_LOTTERY :
                $fMax = 8.1;
                $fMin = -15;
                $sGameName = '彩票';
                break;
            case SeriesSet::ID_DICE :
                $fMax = 2;
                $sGameName = '骰宝';
                break;
            case SeriesSet::ID_LHD :
                $fMax = 1.5;
                $sGameName = '龙虎斗';
                break;
            case SeriesSet::ID_BJL :
                $fMax = 1;
                $sGameName = '百家乐';
                break;
            case SeriesSet::ID_FOOTBALL_MIX :
                $fMax = 8;
                $sGameName = '串关';
                break;
        }

        if($this->commission_rate > $fMax && $this->commission_rate < $fMin && $this->series_set_id != SeriesSet::ID_FOOTBALL_SINGLE) return ['success' => false, 'msg' => $sGameName.'的返点设置不合法'];
        /*返点合法性验证结束*/
        if($this->parent_id)
        {
            if($oParentComm = self::getUserCommissionSet($this->parent_id, $this->series_set_id))
            {
                if($oParentComm->commission_rate < $this->commission_rate)
                {
                    return ['success' => false, 'msg' => '设置的返点大于上级返点'];
                }
            }
            else{
                return ['success' => false, 'msg' => '用户上级代理无返点'];
            }

            $this->commission_rate = strval(number_format($this->commission_rate, 1));
            //返点数必须有配额
            list($aRates, $highRates) = $this->getDiffCommissionRate();
            if(!in_array($this->commission_rate, array_keys($aRates)) && !in_array($this->commission_rate, array_keys($highRates)))
            {
                return ['success' => false, 'msg' => '此返点没配额'];
            }
        }

        //返点必须大于等于下级的返点
        $oSubCommissionSets = self::getSubCommissionSets($this->user_id, $this->series_set_id);

        if($oSubCommissionSets->count() > 0)
        {
            $iSubMaxRate = max(array_column($oSubCommissionSets->toArray(), 'commission_rate'));
            if($this->commission_rate < $iSubMaxRate){
                //todo
                return ['success' => false, 'msg' => '设置的返点小于下级返点'];
            }
        }

        //校验数据
        $oValidator = Validator::make($this->toArray(), self::$rules);
        $customAttributes = [
            "username"              => __('_user.username'),
            "commission_rate"       => '返点率', //todo
        ];
        $oValidator->setAttributeNames($customAttributes);

        if (!$oValidator->passes())
        {
            foreach ($oValidator->errors()->toArray() as $sColumn => $sMsg)
            {
                $sError = $sMsg[0];
                break;
            }
            return ['success' => false, 'msg' => $sError];
        }else{
            return ['success' => true, 'msg' => __('_basic.created')];
        }

    }

    /**
     * 由返点比例获取奖金组
     * @param $rate
     * @return float
     */
    public static function getPrizeGroupByRate($rate){
        $minPrizeGroup = PrizeSysConfig::minPrizeGroup(PrizeSysConfig::TYPE_AGENT);
        return $rate*2000/100 + $minPrizeGroup;
    }

    /**由奖金组获取返点比率
     * @param $prizeGroup
     * @return float
     */
    public static function getRateByPrizeGroup($prizeGroup){
        $minPrizeGroup = PrizeSysConfig::minPrizeGroup(PrizeSysConfig::TYPE_AGENT);
        return ($prizeGroup - $minPrizeGroup)*100/2000;
    }

    /**
     * 获取可使用的返点率=>返点数
     * @return array
     */
    public function getDiffCommissionRate(){
        $aRates = $highRates = [];

        if($this->type_id == SeriesSet::TYPE_LOTTERY)
        {
            $userType = PrizeSysConfig::TYPE_AGENT;
            $minPrizeGroup = PrizeSysConfig::minPrizeGroup($userType);
            $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($userType, true)) ? $aPrizeGroups : [];

            $oUser = $this->parent_id ? User::find($this->parent_id) : User::find($this->user_id);

            //低点
            $iMinLowPrizeGroup = $minPrizeGroup;
            $aUseLowPrizeGroupWhiteList = Config::get('useLowPrizeGroupWhiteList');
            if(!empty($aUseLowPrizeGroupWhiteList['user_list'])){
                if(in_array(Session::get('username'),$aUseLowPrizeGroupWhiteList['user_list'])){
                    $iMinLowPrizeGroup = $aUseLowPrizeGroupWhiteList['prize_group'];
                }
            }
            //基础返点
            foreach($aPrizeGroups as $iPrizeGroup)
            {

                if($iMinLowPrizeGroup != $minPrizeGroup && $iPrizeGroup >= $iMinLowPrizeGroup && $iPrizeGroup < $minPrizeGroup){
                    $irate = strval(number_format(($iPrizeGroup - $minPrizeGroup)/2000*100, 1));
                    $aRates[$irate] = 1;
                }

                if($iPrizeGroup > $oUser->prize_group || $iPrizeGroup < $minPrizeGroup) continue;
                $irate = strval(number_format(($iPrizeGroup - $minPrizeGroup)/2000*100, 1));
                $aRates[$irate] = 1;
            }

            //配额返点
            if($aOverPrizeGroups = OverlimitPrizeGroup::getPrizeGroupByTopAgentId(Session::get('user_id')))
            {
                foreach($aOverPrizeGroups as $aOverPrizeGroup)
                {
                    $iAvilibaleNum = $aOverPrizeGroup['limit_num'] - $aOverPrizeGroup['used_num'];
                    if($iAvilibaleNum > 0){
                        $irate = strval(number_format(($aOverPrizeGroup['prize_group'] - $minPrizeGroup)*100/2000, 1));
                        $highRates[$irate ] =  $iAvilibaleNum;
                    }
                }
            }
        }
        else{
            //基础返点
            $oParentCommission = self::getUserCommissionSet($this->parent_id, $this->series_set_id);
            $oSeriesSet = SeriesSet::find($this->series_set_id);

            $fCurRate = $oParentCommission->commission_rate;
            //todo 需要调整
            if($this->series_set_id == SeriesSet::ID_FOOTBALL_MIX && $this->parent_id){
                $fCurRate = min(8, $oParentCommission->commission_rate);
            }

            while($fCurRate >= 0){
                $aRates[strval($fCurRate) ] = 1;
                $fCurRate = bcsub($fCurRate, $oSeriesSet->diff,1);
            }

        }

        return [$aRates, $highRates];
    }

    public static function verifyCreate($ParentId, $aCommissionRate){

        $aSeriesSetId = array_column(SeriesSet::all()->toArray(), 'diff', 'id');
        if(count($aSeriesSetId) != count($aCommissionRate) || array_diff(array_keys($aSeriesSetId), array_keys($aCommissionRate))){
            //todo 设置的返点不存在
            return false;
        }

        $oParentRates = self::getUserSeriesCommissionSets($ParentId);

        if($oParentRates->count() != count($aSeriesSetId)) return false;

        foreach($oParentRates as $oParentRate)
        {
            if($oParentRate->commission_rate < $aCommissionRate[$oParentRate->series_set_id]){
                return false;
            }

            if(bcsub($oParentRate->commission_rate, $aCommissionRate[$oParentRate->series_set_id], 1) * 100 % ($aSeriesSetId[$oParentRate->series_set_id] * 100)){
                return false;
            }
        }

        return true;
    }

}
