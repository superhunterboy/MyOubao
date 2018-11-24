<?php

class UserPrizeSetFloat extends BaseModel {

    const STATUS_NOT_USED = 0;
    const STATUS_USED = 1;
    const UP_POINT = 1;
    const DOWN_POINT = 0;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_prize_set_floats';

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
        'day',
        'begin_date',
        'end_date',
        'old_prize_group',
        'new_prize_group',
        'standard_turnover',
        'total_team_turnover',
        'is_up',
        'description',
    ];
    public static $resourceName = 'User Prize Set Float';
    public static $aStatus = [
        self::STATUS_NOT_USED => 'not used',
        self::STATUS_USED => 'used',
    ];
    public static $aUpDownType = [
        self::UP_POINT => 'up point',
        self::DOWN_POINT => 'down point',
    ];
    public static $listColumnMaps = [
        'status' => 'friendly_status',
        'is_up' => 'friendly_isup',
    ];
    public static $viewColumnMaps = [
        'status' => 'friendly_status',
        'is_up' => 'friendly_isup',
        'standard_turnover' => 'standard_turnover_formatted',
        'total_team_turnover' => 'total_team_turnover_formatted',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'created_at',
        'is_up',
        'username',
        'old_prize_group',
        'new_prize_group',
        //'status',
        'description',
    ];
    public static $htmlNumberColumns = [
        'standard_turnover' => 2,
        'total_team_turnover' => 2,
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
        'status' => 'aStatus',
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
        'user_id' => 'required|integer',
    ];
    public static $aUserTypes = ['Top Agent', 'Agent'];

    protected function getUserTypeFormattedAttribute() {
        return static::$aUserTypes[intval($this->parent_id != null)];
    }

    public static function getMaxDateByUserId($iUserid) {
        return DB::table('user_prize_set_floats')->where('user_id', '=', $iUserid)->max('created_at');
    }

    public static function getUnusedPrizeSet() {
        return DB::table('user_prize_set_floats')->where('status', '=', self::STATUS_NOT_USED)->orderBy('created_at', 'asc')->get();
    }

    protected function getFriendlyStatusAttribute() {
        return __('_userprizesetfloat.' . self::$aStatus[$this->status], [], 1);
    }

    protected function getFriendlyIsupAttribute() {
        return __('_userprizesetfloat.' . self::$aUpDownType[$this->is_up], [], 1);
    }

    /**
     * 获取指定用户最后一次升降点计算的日期，第一次取用户的注册时间
     * @param int $iUserId  用户id
     * @return string  日期字符串，如：2014-01-01
     */
    public static function getLastCalculateFloatDate($iUserId) {
        $sLastCalculateFloatDate = UserPrizeSetFloat::getMaxDateByUserId($iUserId);
        if ($sLastCalculateFloatDate) {
            $sLastCalculateFloatDate = date('Y-m-d', strtotime($sLastCalculateFloatDate));
        } else {
            $oUser = User::find($iUserId);
            $sLastCalculateFloatDate = $oUser->created_at->toDateString();
        }
        return $sLastCalculateFloatDate;
    }

    protected function getStandardTurnoverFormattedAttribute() {
        return $this->getFormattedNumberForHtml('standard_turnover');
    }

    protected function getTotalTeamTurnoverFormattedAttribute() {
        return $this->getFormattedNumberForHtml('total_team_turnover');
    }

}
