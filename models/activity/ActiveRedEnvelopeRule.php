<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActiveRedEnvelopeRule extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'active_red_envelopes_rules';
    public $orderColumns = ['status' => 'desc', 'id' => 'desc'];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'min_turnover',
        'max_turnover',
        'max_bet_times',
        'min_bet_times',
        'amount',
        'admin',
        'admin_id',
        'status',
    ];
    public static $resourceName = 'ActiveRedEnvelopeRule';
    public static $ignoreColumnsInEdit = ['admin_id', 'admin'];

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'min_turnover',
        'max_turnover',
        'min_bet_times',
        'max_bet_times',
        'amount',
        'admin',
        'admin_id',
        'status',
        'created_at',
        'updated_at',
    ];
    public static $titleColumn = 'id';
    public static $rules = [
        'min_turnover' => 'numeric',
        'max_turnover' => 'numeric',
        'max_bet_times' => 'numeric',
        'min_bet_times' => 'numeric',
        'amount' => 'required|regex:/^\d+(,\d+)*$/', //^\d+(,\d+)*$
        'admin' => 'required',
        'admin_id' => 'required',
        'status' => 'required|in:0,1,2',
    ];/**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];
    public static $aStatus = [
        '1' => '启用',
        '0' => '停用',
    ];

    /**
     * 获取适合的规则
     * @param type $currentRedEnvolopeUser
     * @return boolean
     */
    public static function getRules($currentRedEnvolopeUser) {
        if (!is_object($currentRedEnvolopeUser)) {
            return false;
        }
        $oRules = self::getAllRules();

        foreach ($oRules as $rule) {
            if (($rule->max_turnover > 0 || $rule->min_turnover > 0 || $rule->min_bet_times > 0 || $rule->max_bet_times > 0) &&
                    $currentRedEnvolopeUser->turnover >= $rule->min_turnover &&
                    ($currentRedEnvolopeUser->turnover < $rule->max_turnover || $rule->max_turnover == 0) &&
                    $currentRedEnvolopeUser->bets_times >= $rule->min_bet_times &&
                    ($currentRedEnvolopeUser->bets_times < $rule->max_bet_times || $rule->max_bet_times == 0)
            ) {

                return $rule;
            }
        }
        return false;
    }

    /**
     * 检查金额是否符合规定否
     */
    public function getAmountArray() {

        $aAmounts = explode(',', $this->amount);
//        $amount_array = [];
//        foreach ($aAmounts as $amount) {
//            if ($amount <= $balnce) {
//                $amount_array[] = $amount;
//            }
//        }
        return $aAmounts;
    }

    /**
     * 获得有效的活动
     *
     * @return mixed
     */
    public static function getAllRules() {

        $oRules = self::where('status', 1)->get();
        return $oRules;
    }

}
