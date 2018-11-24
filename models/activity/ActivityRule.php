<?php

/**
 * Class ActivityRule - 活动抽奖规则表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityRule extends BaseModel {
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel= self::CACHE_LEVEL_FIRST;

    /**
     * 抽奖类型：概率抽奖
     */
    const TYPE_PROBABILITY_LOTTERY = 1;
    
    /**
     * 抽奖类型：时间抽奖
     */
    const TYPE_TIME_LOTTERY = 2;
    
    /**
     * 生成奖品方式：总量
     */
    const GENTERATE_TYPE_COUNT = 1;
    
    /**
     * 生成奖品方式：按天
     */
    const GENTERATE_TYPE_TIME = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_rules';
    public static $aGenerateTypes = [
        self::GENTERATE_TYPE_COUNT => 'generate-type-count',
        self::GENTERATE_TYPE_TIME => 'generate-type-time',
    ];
    public static $aTypes = [
        self::TYPE_PROBABILITY_LOTTERY => 'type-probability-lottery',
        self::TYPE_TIME_LOTTERY => 'type-time-lottery',
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'activity_id',
        'activity_name',
        'prize_id',
        'prize_name',
        'name',
        'contribution',
        'contribution_cost',
        'user_limit',
        'type',
        'generate_rules',
        'generate_type',
        'probobility_expressions',
//        'total_count',
//        'left_count',
//        'locker',
    ];
    public static $resourceName = 'ActivityRule';
    public static $listColumnMaps = [
        'type' => 'friendly_type',
        'generate_type' => 'friendly_generate_type',
    ];
    public static $viewColumnMaps = [
        'type' => 'friendly_type',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'activity_name',
        'prize_name',
        'contribution',
        'contribution_cost',
        'user_limit',
        'type',
//        'generate_rules',
//        'generate_type',
//        'probobility_expressions',
        'total_count',
        'left_count',
//        'locker',
    ];
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'prize_id' => 'aPrizes',
        'type' => 'aTypes',
        'generate_type' => 'aGenerateTypes',
    ];
    public static $ignoreColumnsInEdit = ['activity_name', 'prize_name', 'left_count'];
    public static $rules = [
        'activity_id' => 'required|integer',
        'activity_name' => 'required|between:1,45',
        'prize_id' => 'required|integer',
        'prize_name' => 'required|between:1,45',
        'contribution' => 'integer',
        'contribution_cost' => 'integer',
        'user_limit' => 'integer',
        'type' => 'in:1,2',
        'generate_rules' => 'between:0,255',
        'generate_type' => 'in:1,2',
        'probobility_expressions' => 'between:0,255',
//        'total_count' => 'integer',
//        'left_count' => 'integer',
    ];

    protected function getFriendlyTypeAttribute() {
        return __('_activityrule.' . self::$aTypes[$this->type]) . '(' . __('_activityrule.' . self::$aGenerateTypes[$this->generate_type]) . ')';
    }

    protected function getFriendlyGenerateTypeAttribute() {
        return __('_activityrule.' . self::$aGenerateTypes[$this->generate_type]);
    }

    protected function beforeValidate() {
        $oActivity = Activity::find($this->activity_id);
        if (is_object($oActivity)) {
            $this->activity_name = $oActivity->name;
        } else {
            return false;
        }
        $oActivityPrize = ActivityPrize::find($this->prize_id);
        if (is_object($oActivityPrize)) {
            $this->prize_name = $oActivityPrize->name;
            $this->prize_value = $oActivityPrize->value;
        } else {
            return false;
        }
        return parent::beforeValidate();
    }
    
    
    /**
     * 
     * @param Activity $oActivity 活动对象
     * @param int $iLocker 使用的锁ID
     * @return ActivityRule[]
     */
    public static function findAvailableRules(Activity $oActivity, $iLocker) {
        if(!is_object($oActivity) || empty($oActivity->id) || !is_numeric($iLocker)) {
            return false;
        }
        $aActivityRule = self::where('activity_id', '=', $oActivity->id)
                ->where('locker', '=', "$iLocker") // 已被锁定
                ->where('left_count', '>', 0) // 已被锁定
                ->orderBy('prize_value', 'DESC')
                ->orderBy('type', 'DESC')
                ->get();
//        dd(DB::getQueryLog());
        return $aActivityRule;
    }
    
    
    /**
     * 扣减抽奖规则奖品数量
     * @param type $iLocker
     * @param type $iQuantity
     * @return boolean
     */
    public function deduct($iLocker, $iQuantity = 1) {
        $iAffectRows = self::where('id', '=', $this->id)
                ->where('locker', '=', "$iLocker") // 已被锁定
                ->decrement('left_count', $iQuantity);
//        dd(DB::getQueryLog());
        $iAffectRows == 1 && $this->left_count -= $iQuantity;
        return $iAffectRows == 1;
    }
    
    
    /**
     * 锁定当前活动所有抽奖规则
     * @param Activity $oActivity 活动对象
     * @return int|false 锁定成功返回锁ID，失败则返回false
     */
    public static function lock(Activity $oActivity) {
        if(!is_object($oActivity) || empty($oActivity->id)) {
            return false;
        }
        $iThreadId = DbTool::getDbThreadId();
        $iAffectRows = self::where('activity_id', '=', $oActivity->id)
                ->whereNull('locker') // 未被锁定
                ->update(['locker' => $iThreadId]);
        if($iAffectRows == 0) {
            return false;
        }
        return $iThreadId;
    }
    
    
    /**
     * 释放当前活动所有抽奖规则
     * @param Activity $oActivity 活动对象
     * @param type $iLocker 锁ID
     * @return boolean 解锁是否成功
     */
    public static function unlock(Activity $oActivity, $iLocker) {
        if(!is_object($oActivity) || empty($oActivity->id) || !is_numeric($iLocker)) {
            return false;
        }
        $iAffectRows = self::where('activity_id', '=', $oActivity->id)
                ->where('locker', '=', "$iLocker") // 已被锁定
                ->update(['locker' => null]);
        return $iAffectRows > 0;
    }
    
    
    
    /**
     * 生成中奖概率表达式「公式」（需要以下格式数组作为条件）：
     * <pre>array(
     *  ['x'=>['<'=>'200'], 'y'=>1],
     *  ['x'=>['>='=>200, '<'=>'500'], 'y'=>5],
     * )</pre>
     * @param array $aConditions 条件数据
     * @return string 公式（示例：x<200&&y=1;x>=200&&x<500&&y=5）
     */
    public function setProbobilityExpressions($aConditions = []) {
        $aResult = [];
        if (empty($aConditions)) {
            $this->probobility_expressions = '';
            return $this->probobility_expressions;
        }
        if (!is_array($aConditions)) {
            return false;
        }
        foreach ($aConditions as $ct) {
            if (!empty($ct['x']) && !empty($ct['y'])) {
                $aTemp = [];
                foreach ($ct['x'] as $k => $v) {
                    $v = floatval($v);
                    switch ($k) {
                        case '>':
                            $aTemp[] = 'x>' . $v;
                            break;
                        case '>=':
                            $aTemp[] = 'x>=' . $v;
                            break;
                        case '<':
                            $aTemp[] = 'x<' . $v;
                            break;
                        case '<=':
                            $aTemp[] = 'x<=' . $v;
                            break;
                        default :
                            break;
                    }
                }
                $aTemp[] = 'y=' . $ct['y'];
                $aResult[] = implode('&&', $aTemp);
            }
        }
        $this->probobility_expressions = implode(';', $aResult);
        return $this->probobility_expressions;
    }

    /**
     * 解析中奖概率表达式为数组形式，返回数据格式如下：
     * <pre>array(
     *  ['x'=>['<'=>'200'], 'y'=>1],
     *  ['x'=>['>='=>200, '<'=>'500'], 'y'=>5],
     * )</pre>
     * @return array
     */
    public function getProbobilityExpressionsArray() {
        $aResult = [];
        if (empty($this->probobility_expressions)) {
            return $aResult;
        }
        // x<200&&y=1;x>=200&&x<500&&y=5
        $aConditions = explode(';', $this->probobility_expressions);
        foreach ($aConditions as $ct) {
            $aTemp = [];
            preg_match_all('/x([><]=?)(\d+(?:\.\d+)?)/', $ct, $matches);
            foreach ($matches[1] as $k => $v) {
                $aTemp['x'][$v] = $matches[2][$k];
            }
            preg_match_all('/y=(x\*)?(\d+(?:\.\d+)?)(\/100)?/', $ct, $matches);
            if (!empty($matches[1][0]) && !empty($matches[3][0])) {
                $aTemp['y']['%'] = $matches[2][0];
            } else {
                
            }
            $aTemp['y'] = substr($ct, strpos($ct, 'y=') + 2);
            $aResult[] = $aTemp;
        }
        return $aResult;
    }

    /**
     * 根据公式计算中奖概率
     * @param float $fContribution 充值金额
     * @return int
     */
    public function calculateProbobility($fContribution = 0) {
        $iProbobility = 0;
        if (empty($this->probobility_expressions)) {
            return $iProbobility;
        }
        // x<200&&y=1;x>=200&&x<500&&y=5
        $sFeeExpressions = str_replace('x', '$fContribution', $this->probobility_expressions);
        $sFeeExpressions = str_replace('y', '$iProbobility', $sFeeExpressions);
        eval($sFeeExpressions . ';');
        return $iProbobility;
    }
    
    
    /**
     * 设置数据初始化规则<br/>
     * <b>
     * 注意:该方法未save当前对象
     * </b>
     * <p>
     * 数组规则要求：
     * array(
     *  ['start'=>?, 'end'=>?, 'amount'=>?],
     *  ...
     * )
     * </p>
     * @param array $aGenerateRules 规则数组
     * @return type
     */
    public function setGenerateRules($aGenerateRules) {
        if(!is_array($aGenerateRules) || empty($aGenerateRules)) {
            return false;
        }
        foreach($aGenerateRules as $key => $rule) {
            if(!isset($rule['start']) || !isset($rule['end']) || !isset($rule['amount'])) {
                return false;
            }
            $sFormatType = 'Y-m-d H:i:s';
            if($this->generate_type == self::GENTERATE_TYPE_COUNT) { // 按总量生成（日期区间）
                $sFormatType = 'Y-m-d';
            } else if($this->generate_type == self::GENTERATE_TYPE_TIME) { // 按天生成（时间区间）
                $sFormatType = 'H:i:s';
            }
            $aGenerateRules[$key] = [
                'start' => date($sFormatType, strtotime($rule['start'])),
                'end' => date($sFormatType, strtotime($rule['end'])),
                'amount' => intval($rule['amount']),
            ];
        }
        return $this->generate_rules = json_encode($aGenerateRules);
    }
    
    
    /**
     * 解析奖品生成规则为数组
     * <p>
     * 返回的数组格式:
     * array(
     *  ['start'=>?, 'end'=>?, 'amount'=>?],
     *  ...
     * )
     * </p>
     * @return array 
     */
    public function getGenerateRulesArray() {
        return json_decode($this->generate_rules, true);
    }
    
    
    /**
     * 更新奖品数量
     * @return boolean
     */
    public function updateCount() {
        $this->total_count = ActivityRulePrizeTime::countRulezPrizeTime($this);
        $this->left_count = ActivityRulePrizeTime::countRulezPrizeTime($this, ActivityRulePrizeTime::STATUS_NOT_USED);;
        return $this->save();
    }
    
    
    public function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        if($this->generate_rules && $this->total_count == 0) {
            BaseTask::addTask('CreateRulePrizeTimeData', ['id' => $this->id], 'activity');
        }
    }
}
