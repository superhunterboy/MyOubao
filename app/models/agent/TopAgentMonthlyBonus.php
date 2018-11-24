<?php

class TopAgentMonthlyBonus extends BaseModel {

    protected $table    = 'top_agent_monthly_bonus';
    public static $resourceName = 'TopAgentMonthlyBonus';
    protected $fillable = [
        'bonus',
        'bonus_date',
        'status',
        'admin',
    ];
    public static $columnForList = [
        'username',
        'bonus',
        'status',
        'bonus_date',
        'admin',
        'created_at',
    ];
    public static $htmlSelectColumns = [
//        'parent_id' => 'aParentIds',
        'bonus_percent' => 'aBonusPercents',
    ];
    public static $rules = [
        'bonus' => 'min:0',
    ];
    public $orderColumns = [
        'created_at' => 'desc',
    ];

    /**
     * 不显示orderby按钮的列，供列表页使用
     * @var array
     */
    public static $noOrderByColumns = [
        'bonus_percent_number',
    ];

    protected function getFormattedIsTesterAttribute() {
        if ($this->attributes['is_tester'] !== null) {
            return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
        } else {
            return '';
        }
    }

    protected function setParentIdAttribute($iParentId) {
        $this->attributes['parent_id'] = $iParentId;
    }

    protected function beforeValidate() {
        if ($this->bonus_percent == 0) {
            $this->basic_percent = $this->bonus_amount = $this->bonus_percent = 0;
        }
        if (intval($this->user_id)) {
            $oUser = User::find($this->user_id);
            // 如果更新用户上下级关系，需要更新该部分数据
            $this->parent_id = $oUser->parent_id;
            $this->parent_name = $oUser->parent;
            $this->user_forefather_ids = $oUser->forefather_ids;
            if (!$this->username) {
                $this->username = $oUser->username;
            }
            if (!$this->is_tester) {
                $this->is_tester = $oUser->is_tester;
            }
        }
        return parent::beforeValidate();
    }

    public function getBonusPercentNumberAttribute() {
        return number_format($this->attributes['bonus_percent'] * 100, 2) . '%';
    }

    public static function getMinBonusPercentByParentId($parentId) {
        return DB::table('daily_salary_protocals')->where('parent_id', '=', $parentId)->max('bonus_percent');
    }

    public static function createDailySalaryProtocal($oUser) {
        $oDSP = new DailySalaryProtocal();
        $oDSP->user_id = $oUser->id;
        $oDSP->username = $oUser->username;
        $oDSP->is_tester = $oUser->is_tester;
        $oDSP->status = DailySalaryProtocal::STATUS_NEW_CREATE;
        return $oDSP->save();
    }

}
