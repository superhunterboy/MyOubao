<?php
# 一代限额奖金组
class OverlimitPrizeGroup extends BaseModel {
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'OverlimitPrizeGroup';
    public static $columnForList = [
        'id',
       // 'top_agent_id',
        'top_agent_name',
        'classic_prize_group',
        'limit_num',
        'used_num',
        'updated_at',
        'created_at',
    ];
    public static $htmlSelectColumns = [
        'classic_prize_group' => 'aLimitGroups',

    ];
//    public static $aLimitGroups = [
//        '1954'=>1954,
//        '1955'=>1955,
//        '1956'=>1956,
//        '1957'=>1957,
//        '1958'=>1958,
//        '1959'=>1959,
//        '1960'=>1960,
//    ];
    public static $rules = [
        'top_agent_id'   => 'required|integer',
        'top_agent_name' => 'required|between:0,16',
        'limit_num'      => 'required|integer',
    ];
    public $orderColumns = [
        'classic_prize_group' => 'asc'
    ];
     const ERROR_NO_QUOTA = -200;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'overlimit_prize_group';
    protected $fillable = [
        'id',
        'top_agent_id',
        'top_agent_name',
        'classic_prize_group',
        'limit_num',
        'used_num',
        'updated_at',
        'created_at',
    ];

    private   function delete_cache($top_agent_id,$classic_prize_group = null){
        $key = "hasOverLimitQuota_".$top_agent_id . "_".$classic_prize_group;
        $this->deleteCache($key);
        $key2 = "getPrizeGroupByTopAgentId_".$top_agent_id;
         $this->deleteCache($key2);
        
    }
    public static function getAvilibalePrizeGroups($top_agent_id)
    {
        $prize_group = self::where('top_agent_id', $top_agent_id)->get();
        $limitGroup = self::$aLimitGroups;
        foreach ($prize_group as $val) {
            unset($limitGroup[$val->classic_prize_group]);
        }
    }

    public static function getPrizeGroups($top_agent_id) {
        $data = [];
        if ($aPrizeGroup = self::where('top_agent_id', $top_agent_id)->get()->toArray()) {
            $data = array_column($aPrizeGroup, 'classic_prize_group');
        }
        return $data;
    }
    public function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        $this->delete_cache($this->top_agent_id,$this->classic_prize_group);
    }
    
    public static function getDatasByPrizeGroupAndTopAgentId($top_agent_id,$classic_prize_group) {
        $prize_group=self::where('top_agent_id',$top_agent_id)->where('classic_prize_group', $classic_prize_group)->first();
        if(is_object($prize_group)){
            return $prize_group;
        }
        return false;
    }
    /**
     * 根据user_id数组获取多个用户的高点配额
     * @param array $agent_ids
     * @param array $aColumns
     */
    public static function getPrizeGroupByAgentIds(Array $agent_ids=[],$aColumns=[]){
          $aColumns or $aColumns = ['top_agent_id','classic_prize_group', 'limit_num', 'used_num'];
          return  self::whereIn('top_agent_id', $agent_ids)->get($aColumns);
    }
    /**根据userID获取顶点将进组设置
     * @param $top_agent_id
     * @return bool
     */
    public static function getPrizeGroupByTopAgentId($top_agent_id){
        $datas = [];
        $oQuery=self::where('top_agent_id',$top_agent_id)->orderBy('classic_prize_group','asc');
        if($result=$oQuery->get()) {
            foreach ($result as $data) {
                $datas[$data['classic_prize_group']] = [
                    'prize_group' => $data['classic_prize_group'],
                    'user_id' => $data['top_agent_id'],
                    'limit_num' => $data['limit_num'],
                    'used_num' => $data['used_num'],
                ];
            }
        }
        return $datas;
/*        $key = "getPrizeGroupByTopAgentId_".$top_agent_id;
        if(!Cache::get($key)){
            $oQuery=self::where('top_agent_id',$top_agent_id)->orderBy('classic_prize_group','asc');
            if($result=$oQuery->get()){
                $datas=[];
               foreach($result as $data){
                   $datas[$data['classic_prize_group']]=[
                       'prize_group'=>$data['classic_prize_group'],
                       'user_id'=>$data['top_agent_id'],
                       'limit_num'=>$data['limit_num'],
                       'used_num'=>$data['used_num'],
                   ];
               }
               Cache::forever($key,$datas);
            }
        }
        return Cache::get($key);*/
    }
    /**
     * 是否有配额奖金组
     * @param $top_agent_id
     * @param null $classic_prize_group
     * @return bool
     */
    public static function isOverlimit($top_agent_id, $classic_prize_group = null)
    {

        $oQuery = self::where('top_agent_id',$top_agent_id)->where('classic_prize_group',$classic_prize_group);

        if ($agentPrizeGroup = $oQuery->first()) {
            return ($agentPrizeGroup->limit_num - $agentPrizeGroup->used_num) > 0;
        }
        return false;

/*        $key = "hasOverLimitQuota_".$top_agent_id . "_".$classic_prize_group;

        if(!Cache::get($key)){
            $bFlag  = false;
            $oQuery = self::where('top_agent_id',$top_agent_id)->where('classic_prize_group',$classic_prize_group);

            if ($results = $oQuery->get(['limit_num', 'used_num'])) {
                foreach ($results as $agentPrizeGroup) {
                    if(($agentPrizeGroup->limit_num - $agentPrizeGroup->used_num) > 0) {$bFlag = true;break;}
                }
            }

            Cache::forever($key,$bFlag);
        }
        return Cache::get($key);*/
    }

    /**
     * 增加/减少已使用数
     * @param $oParent
     * @param $oUser
     * @param $iSetPrizeGroup
     * @return bool
     */
    public static function setPrizeGroupNum(&$oParent, &$oUser, $iSetPrizeGroup, $isForever)
    {
        //没有配置高点奖金
        $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($oParent->is_agent, true)) ? $aPrizeGroups : [];
        $aHighPrizeGroups = ($aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups($oParent->is_agent, true)) ? $aHighPrizeGroups : [];
        if(!$aPrizeGroups || !$aHighPrizeGroups ){
            return false;
        }
        
        if(in_array($iSetPrizeGroup, $aHighPrizeGroups)){
            $oPrizeQuota = self::getDatasByPrizeGroupAndTopAgentId($oParent->id, $iSetPrizeGroup);
            if(!is_object($oPrizeQuota)){
                return false;
            }
            if(($oPrizeQuota->limit_num - $oPrizeQuota->used_num) < 1){
                return false;
            }
        }
//        if((in_array($iSetPrizeGroup, $aPrizeGroups) && in_array($oUser->prize_group, $aPrizeGroups))){
//            return true;
//        }

        $oPrizeGroup = self::where('top_agent_id',$oParent->id)->where('classic_prize_group',$oUser->prize_group)->first();
        $oSetPrizeGroup = self::where('top_agent_id', $oParent->id)->where('classic_prize_group', $iSetPrizeGroup)->first();
        $bExistTmpPrize = UserPrizeGroupTmp::existTmpPrize($oUser);

        if($iSetPrizeGroup == $oUser->prize_group && $isForever && $bExistTmpPrize){
            if ($oPrizeGroup = self::where('top_agent_id', $oParent->id)->where('classic_prize_group', UserPrizeGroupTmp::getForeverPrize($oUser))->first()) {
                $oPrizeGroup->used_num = $oPrizeGroup->used_num -1;
                return $oPrizeGroup->used_num >=0 && $oPrizeGroup->save();
            }else{
                return true;
            }
        }

        //临时到永久(临时减一，永久使用数不变)
        elseif($bExistTmpPrize && $isForever){
            $oPrizeGroup->used_num = $oPrizeGroup->used_num -1;
            return $oPrizeGroup->used_num >=0 && $oPrizeGroup->save();
        }

        //永久到临时(永久使用数不变，临时加一)
        elseif(!$bExistTmpPrize && !$isForever){
            if($oSetPrizeGroup){
                $oSetPrizeGroup->used_num = $oSetPrizeGroup->used_num +1;
                return $oSetPrizeGroup->save();
            }
        }

        //临时到临时 或 永久到永久
        else{
            $bPrizeRs = $bSetPrizeRs = true;

            //当前永久奖金组可能没有配额
//            if($oPrizeGroup){
//                $oPrizeGroup->used_num = $oPrizeGroup->used_num -1;
//                $bPrizeRs = $oPrizeGroup->used_num >=0 && $oPrizeGroup->save();
//            }
            if(in_array($iSetPrizeGroup, $aHighPrizeGroups)){
                $bSetPrizeRs = false;
                if($oSetPrizeGroup){
                    $oSetPrizeGroup->used_num = $oSetPrizeGroup->used_num +1;
                    $bSetPrizeRs = $oSetPrizeGroup->save();
                }
            }
            return $bPrizeRs && $bSetPrizeRs;
        }

/*        //三种情况：配额到基础，基础到配额，配额到配额
        if($oUser->prize_group > $iSetPrizeGroup && !in_array($iSetPrizeGroup, $aHighPrizeGroups) && $oPrizeGroup)
        {
                $oPrizeGroup->used_num = $oPrizeGroup->used_num -1;
                return $oPrizeGroup->save();
        }
        elseif ($oUser->prize_group < $iSetPrizeGroup && in_array($iSetPrizeGroup, $aHighPrizeGroups) && !in_array($oUser->prize_group, $aHighPrizeGroups) && $oSetPrizeGroup)
        {
                $oSetPrizeGroup->used_num = $oSetPrizeGroup->used_num + 1;
                return $oSetPrizeGroup->save();
        }
        elseif (in_array($oUser->prize_group, $aHighPrizeGroups) && in_array($iSetPrizeGroup, $aHighPrizeGroups) && ($oSetPrizeGroup && $oPrizeGroup))
        {
                $oPrizeGroup->used_num = $oPrizeGroup->used_num -1;
                $oSetPrizeGroup->used_num = $oSetPrizeGroup->used_num + 1;
                return $oPrizeGroup->save() && $oSetPrizeGroup->save();
        }*/

        return false;
    }

    /**
     * 增加减少用户的limit_num
     * @param array $aUpPrizeGroups 格式如 [12=>[1954=>2,1956=>1]]
     * @param array $aDownPrizeGroups
     * @return bool
     */
    public static function setPrizeGroupLimitNum(Array $aUpPrizeGroups=[], Array $aDownPrizeGroups=[]){

        if (empty($aUpPrizeGroups) && empty($aDownPrizeGroups)) return false;

        $aHighPrizeGroups = ($aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups(PrizeSysConfig::TYPE_AGENT, true)) ? $aHighPrizeGroups : [];

        $succ = true;

        if($aUpPrizeGroups)
        {
            foreach($aUpPrizeGroups as $userId => $aUser)
            {
                if(!is_array($aUser['change'])) {$succ = false;break;}

                foreach($aUser['change'] as $iPrizeGroup => $iNum)
                {
                    if(! in_array($iPrizeGroup, $aHighPrizeGroups)){ $succ = false; break 2; }
                    if(isset($aDownPrizeGroups[$userId]['change'][$iPrizeGroup]) && $aDownPrizeGroups[$userId]['change'][$iPrizeGroup] != 0){ $succ = false; break 2; }

                    $oPrizeGroup = self::where('top_agent_id',$userId)->where('classic_prize_group',$iPrizeGroup)->first();

                    if($oPrizeGroup){
                        $oPrizeGroup->limit_num = $oPrizeGroup->limit_num + $iNum;
                    }else{
                        $oPrizeGroup = new OverlimitPrizeGroup;
                        $oPrizeGroup->used_num = 0;
                        $oPrizeGroup->limit_num = $iNum;
                        $oPrizeGroup->top_agent_id = $userId;
                        $oPrizeGroup->top_agent_name = $aUser['username'];
                        $oPrizeGroup->classic_prize_group = $iPrizeGroup;
                    }
                    
                    if($oPrizeGroup->limit_num < $oPrizeGroup->used_num ||  !$oPrizeGroup->save()){$succ = false; break 2;}
                }
            }
        }
        if($aDownPrizeGroups && $succ)
        {
            foreach($aDownPrizeGroups as $userId => $aUser)
            {
                if(!is_array($aUser['change'])) {$succ = false;break;}
                foreach($aUser['change'] as $iPrizeGroup => $iNum)
                {
                    if(! in_array($iPrizeGroup, $aHighPrizeGroups)){ $succ = false; break 2; }

                    $oPrizeGroup = self::where('top_agent_id',$userId)->where('classic_prize_group',$iPrizeGroup)->first();
                    if(!$oPrizeGroup || $oPrizeGroup->limit_num < 1){ $succ = false; break 2; }
                    $oPrizeGroup->limit_num = $oPrizeGroup->limit_num - $iNum;
                    if($oPrizeGroup->limit_num < $oPrizeGroup->used_num ||  !$oPrizeGroup->save()) { $succ = false; break 2; }
                }
            }
        }

        return $succ;
    }

     /**
     * 增加减少用户的Used_num
     * @param array $aUpPrizeGroups 格式如 [12=>[1954=>2,1956=>1]]
     * @param array $aDownPrizeGroups
     * @return bool
     */
    public static function setPrizeGroupUsedNum(Array $aUpPrizeGroups=[], Array $aDownPrizeGroups=[]){
        
        if (empty($aUpPrizeGroups) && empty($aDownPrizeGroups)) return false;

        $aHighPrizeGroups = ($aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups(PrizeSysConfig::TYPE_AGENT, true)) ? $aHighPrizeGroups : [];

        $succ = true;
       
        if($aUpPrizeGroups)
        {
            foreach($aUpPrizeGroups as $userId => $aUser)
            {
                if(!is_array($aUser['change'])) {$succ = false;break;}

                foreach($aUser['change'] as $iPrizeGroup => $iNum)
                {
                    if(! in_array($iPrizeGroup, $aHighPrizeGroups)  || $iNum <=0){ $succ = false; break 2; }
                    
                    if(isset($aDownPrizeGroups[$userId]['change'][$iPrizeGroup]) && $aDownPrizeGroups[$userId]['change'][$iPrizeGroup] != 0){ $succ = false; break 2; }

                    $oPrizeGroup = self::where('top_agent_id',$userId)->where('classic_prize_group',$iPrizeGroup)->first();
                       
                    if($oPrizeGroup){
                        $oPrizeGroup->used_num = $oPrizeGroup->used_num + $iNum;
                    }else{
                       $succ = false; break 2;
                    }
                    if($oPrizeGroup->used_num > $oPrizeGroup->limit_num || !$oPrizeGroup->save()){$succ = false; break 2;}
                }
            }
        }
        if($aDownPrizeGroups && $succ)
        {
            foreach($aDownPrizeGroups as $userId => $aUser)
            {
               
                if(!is_array($aUser['change'])) {$succ = false;break;}
                foreach($aUser['change'] as $iPrizeGroup => $iNum)
                {
                     
                    if(! in_array($iPrizeGroup, $aHighPrizeGroups)){ $succ = false; break 2; }

                    $oPrizeGroup = self::where('top_agent_id',$userId)->where('classic_prize_group',$iPrizeGroup)->first();
                    if(!$oPrizeGroup || $oPrizeGroup->used_num < 1){ $succ = false; break 2; }
                    $oPrizeGroup->used_num = $oPrizeGroup->used_num - $iNum;
                    
                    if($oPrizeGroup->used_num < 0 || $oPrizeGroup->limit_num < $oPrizeGroup->used_num || ! $oPrizeGroup->save()) { $succ = false; break 2; }
                }
            }
        }

        return $succ;
    }
    
    /**
     * 获取用户奖金组可用数
     * @param $oUser
     * @return array
     */
    public static function getUserAvailableNum(&$oUser){
        $foreverPrize = UserPrizeGroupTmp::getForeverPrize($oUser);
        $aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups($oUser->is_agent, true);

        $aAvailableNum = [];
        foreach($aHighPrizeGroups as $highPrizeGroup){
            if($highPrizeGroup > $foreverPrize) break;
            $aAvailableNum[$highPrizeGroup] = 0;
        }

        if($olimitNums = self::where('top_agent_id',$oUser->id)->get(['classic_prize_group', 'limit_num', 'used_num'])){
            foreach ($olimitNums as $limitNum) {
                $aAvailableNum[$limitNum->classic_prize_group] = $limitNum->limit_num - $limitNum->used_num;
            }
        }

        return $aAvailableNum;
    }
    /**获取高额将进组
     * @return array
     */
    public static function getHighPrizeGroups()
    {
        $aLimitGroups = [];
        $aPrizeSysConfig = PrizeSysConfig::getHighPrizeGroups(PrizeSysConfig::TYPE_AGENT, true);

        foreach ((array)$aPrizeSysConfig as $iPrizeGroup) {
            $aLimitGroups[$iPrizeGroup] = $iPrizeGroup;
        }

        return $aLimitGroups;
    }

    /**
     * 检验是否可增加配额
     * 
     */
    public static function checkPlusNum($agent_id,$prize_group,$plus_num=1000){
        $oQuota = self::where('top_agent_id','=',$agent_id)->where('classic_prize_group','=',$prize_group)->first();
        if(!$oQuota) return false;
        if($plus_num > ($oQuota->limit_num - $oQuota->used_num)) return false;
        return true;
    }
    
        /**
     * 检验是否可回收配额
     * 
     */
    public static function checkSubtractNum($agent_id,$prize_group,$subtract_num=1000){
        $oQuota = self::where('top_agent_id','=',$agent_id)->where('classic_prize_group','=',$prize_group)->first();
        if(!$oQuota) return false;
        if($subtract_num > $oQuota->limit_num) return false;
        return true;
    }
    public static function checkIsOverLimitPrize($agent_id){
        $oQuoto=self::where('top_agent_id','=',$agent_id)->where('limit_num','>',0)->first();
        return $oQuoto?true:false;
    }

}