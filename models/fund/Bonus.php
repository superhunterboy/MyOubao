<?php

/**
 * Description of Bonus
 *
 * @author abel
 */
class Bonus extends BaseModel {

    const STATUS_WAITING_AUDIT = 0;
    const STATUS_AUDIT_FINISH = 1;
    const STATUS_AUDIT_REJECT = 2;
    const STATUS_BONUS_SENT = 3;
    const TOP_AGENT = 0;
    const NORMAL_AGENT = 1;

    protected $table = 'bonuses';
    public static $resourceName = 'Bonus';
    public static $treeable = false;
    public static $sequencable = false;
    protected $softDelete = false;
    public static $aStatus = [
        self::STATUS_WAITING_AUDIT => 'waiting audit',
        self::STATUS_AUDIT_FINISH => 'audited',
        self::STATUS_AUDIT_REJECT => 'rejected',
        self::STATUS_BONUS_SENT => 'bonus sent',
    ];
    public static $aAgentLevel = [
        self::TOP_AGENT => 'top agent',
        self::NORMAL_AGENT => 'agent',
    ];
    public static $columnForList = [
        'begin_date',
        'end_date',
        'username',
        'parent_username',
        'agent_level',
        'turnover',
        'direct_profit',
        'rate',
        'bonus',
        'status',
        'auditor',
        'note',
        'verified_at',
        'sent_at',
    ];
    public static $listColumnMaps = [
        'rate' => 'rate_formatted',
        'turnover' => 'turnover_formatted',
        'status' => 'friendly_status',
        'agent_level' => 'friendly_agent_level',
    ];
    public static $rules = [
        'note' => 'between:0,100',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'rate' => 'asc',
    ];

    protected function getRateFormattedAttribute() {
        return $this->attributes['rate'] * 100 . '%';
    }

    protected function getTurnoverFormattedAttribute() {
        return $this->attributes['turnover'] / 10000 . ' 万';
    }

    protected function getFriendlyStatusAttribute() {
        return __('_bonus.' . self::$aStatus[$this->status]);
    }

    protected function getFriendlyAgentLevelAttribute() {
        return __('_bonus.' . self::$aAgentLevel[$this->agent_level]);
    }

    public static function getBonusByMonthUser($iUserId, $sBeginDate = null, $sEndDate = null) {
        $aConditions = [
            'user_id' => ['=', $iUserId],
        ];
        !$sBeginDate or $aConditions['begin_date'] = ['=', $sBeginDate];
        !$sEndDate or $aConditions['end_date'] = ['=', $sEndDate];
        $oBonus = self::doWhere($aConditions)->orderBy('end_date', 'desc')->get()->first();
        return $oBonus;
    }

}
