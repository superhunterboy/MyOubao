<?php

/**
 * 抽奖规则-奖品时间分布
 *
 */
class ActivityRulePrizeTime extends BaseModel {
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel= self::CACHE_LEVEL_FIRST;

    /**
     * 状态：未被使用
     */
    const STATUS_NOT_USED = 0;
    
    /**
     * 状态：已被使用
     */
    const STATUS_IS_USED = 1;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_rule_prize_time';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public static $resourceName = 'ActivityRulePrizeTime';
    public static $mainParamColumn = 'rule_id';
    public static $titleColumn = null;

    protected $fillable = [
        'rule_id',
        'rand_time',
        'start_time',
        'end_time',
        'expiratoin_time',
        'status',
        'user_id',
    ];
    
    public static $rules = [
        'rule_id' => 'required|integer',
        'rand_time' => 'required|date',
        'start_time' => 'required|date',
        'end_time' => 'required|date',
        'expiratoin_time' => 'date',
        'status' => 'in:0,1',
        'user_id' => 'integer',
    ];
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [];
    
    public static $htmlSelectColumns = [];
    public static $ignoreColumnsInEdit = [];
    

    protected function beforeValidate() {
        return parent::beforeValidate();
    }
    
    
    /**
     * 锁定当前对象（弃用）
     * @param User $oUser
     * @return boolean
     */
//    public function lock(User $oUser) {
//        if(!is_object($oUser) || empty($oUser->id)) {
//            return false;
//        }
//        $iAffectRows = self::where('id', '=', $this->id)
//                ->where('status', '=', self::STATUS_NOT_USED)
//                ->whereNull('user_id')
//                ->update(['user_id' => $oUser->id]);
//        return $iAffectRows == 1;
//    }
    
    
    /**
     * 释放当前对象（弃用）
     * @return boolean
     */
//    public function unlock() {
//        $iAffectRows = self::where('id', '=', $this->id)
//                ->where('status', '=', self::STATUS_NOT_USED)
//                ->whereNotNull('user_id')
//                ->update(['user_id' => null]);
//        return $iAffectRows == 1;
//    }
    
    
    /**
     * 更新状态为已使用
     * @param User $oUser
     * @return boolean
     */
    public function used(User $oUser) {
        if(!is_object($oUser) || empty($oUser->id)) {
            return false;
        }
        $aExtraData = [
            'status' => self::STATUS_IS_USED,
            'user_id' => $oUser->id,
        ];
        $iAffectRows = self::where('id', '=', $this->id)
                ->where('status', '=', $this->status)
                ->update($aExtraData);
        $iAffectRows == 1 && $this->fill($aExtraData);
        return $iAffectRows == 1;
    }
    
    
    /**
     * 获取指定规则下可用的一条时间抽奖信息
     * @param ActivityRule $oActivityRule
     * @return boolean
     */
    public static function findAvailableOne(ActivityRule $oActivityRule) {
        if(!is_object($oActivityRule) || empty($oActivityRule->id)) {
            return false;
        }
        $sTime = date('Y-m-d H:i:s');
        $oPrizeTime = self::where('rule_id', '=', $oActivityRule->id)
                ->where('status', '=', self::STATUS_NOT_USED)
                ->whereNull('user_id') // 未被锁定
                ->where('rand_time', '<=', $sTime)
                ->where(function($oQuery) use($sTime){
                    $oQuery->where('expiration_time', '>=', $sTime) // 未过期
                           ->orWhereNull('expiration_time'); // 或者不会过期
                })
                ->orderBy('rand_time', 'ASC')
                ->first();
//        dd(DB::getQueryLog());
        return $oPrizeTime;
    }
    
    
    /**
     * 批量初始化
     * @param ActivityRule $oActivityRule
     * @return int|false 生成的记录数（数据已存在、无生成规则或者初始化过程中出错则返回false）
     */
    public static function init(ActivityRule $oActivityRule) {
        if(!$oActivityRule || !is_array($oActivityRule->getGenerateRulesArray())) {
            return false;
        }
        // ------------ 判断是否数据已存在 --------------
        $iCount = self::where('rule_id', '=', $oActivityRule->id)->count();
        if($iCount > 0) { // 数据已存在
            return false;
        }
        // ------------ 前置条件信息 --------------
        $aGenerateRules = $oActivityRule->getGenerateRulesArray();
        $bNeedRandomTime = $oActivityRule->type == ActivityRule::TYPE_TIME_LOTTERY; // 时间抽奖，需要随机时间
        
        if($oActivityRule->generate_type == ActivityRule::GENTERATE_TYPE_COUNT) { // 按总量生成（日期区间）
            $iTotalDays = 1; // 不按照活动日期跨度为参考
        } else if($oActivityRule->generate_type == ActivityRule::GENTERATE_TYPE_TIME) { // 按天生成（时间区间）
            // 计算一共多少天
            $oActivity = Activity::find($oActivityRule->activity_id);
            $iStart = strtotime(array_get(explode(' ', $oActivity->start_time), 0));
            $iEnd = strtotime(array_get(explode(' ', $oActivity->end_time), 0));
            $iTotalDays = intval(($iEnd - $iStart) / 86400) + 1; // 活动跨度天数
        }
        // ------------ 生成初始化数据 --------------
        for($i = 0; $i < $iTotalDays; $i++) {
            $aData = [];
            foreach($aGenerateRules as $aRule) {
                $aRandom = [];
                $t1 = strtotime($aRule['start']);
                $t2 = strtotime($aRule['end']);
                if($bNeedRandomTime) { // 需要随机时间
                    for($j = 0; $j < $aRule['amount']; $j++) {
                        $aRandom[] = mt_rand(0, $t2 - $t1); // 存储时间差随机偏移量
                    }
                    sort($aRandom); // 排序
                }
                if($oActivityRule->generate_type == ActivityRule::GENTERATE_TYPE_COUNT) { // 按总量生成（日期区间）
                    $sTempStart = date('Y-m-d H:i:s', ($t1 + $i * 86400)); // 拼接每个时间段的开始时间
                    $sTempEnd = date('Y-m-d H:i:s', ($t2 + $i * 86400)); // 拼接每个时间段的开始时间
                } else if($oActivityRule->generate_type == ActivityRule::GENTERATE_TYPE_TIME) { // 按天生成（时间区间）
                    $sTempStart = date('Y-m-d ', ($iStart + $i * 86400)) . $aRule['start']; // 拼接每个时间段的开始时间
                    $sTempEnd = date('Y-m-d ', ($iStart + $i * 86400)) . $aRule['end']; // 拼接每个时间段的开始时间
                }
                for($j = 0; $j < $aRule['amount']; $j++) {
                    $aData[] = [
                        'rule_id' => $oActivityRule->id,
                        'rand_time' => $bNeedRandomTime ? date('Y-m-d H:i:s', strtotime($sTempStart) + $aRandom[$j]) : $sTempStart,
                        'start_time' => $sTempStart,
                        'end_time' => $sTempEnd,
                    ];
                }
            }
            $bSuccess = DB::table('activity_rule_prize_time')->insert($aData);
            if(!$bSuccess) { // 出错中断
                return false;
            }
        }
//        file_put_contents('/tmp/debug', "\n". __FILE__. " L".__LINE__."\n". var_export([$iCount], true). "\n\n", FILE_APPEND);
        return true;
    }
    
    
    /**
     * 统计指定规则下存在的奖品时间记录数
     * @param ActivityRule $oActivityRule 指定的规则对象 
     * @param int $iStatus 状态（默认为null查询所有记录）
     * @return array|false
     */
    public static function countRulezPrizeTime(ActivityRule $oActivityRule, $iStatus = null) {
        if(!$oActivityRule || !$oActivityRule->id) {
            return false;
        }
        $oQuery = self::where('rule_id', '=', $oActivityRule->id);
        if($iStatus === self::STATUS_NOT_USED) {
            $oQuery->where('status', '=', self::STATUS_NOT_USED);
        } else if($iStatus === self::STATUS_IS_USED) {
            $oQuery->where('status', '=', self::STATUS_IS_USED);
        }
        return $oQuery->count();
    }

}
