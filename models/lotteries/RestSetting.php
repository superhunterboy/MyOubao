<?php

/*
 * 休市管理模型类
 * 作用：生成彩种奖期时，按照休市信息配置奖期生成的逻辑(奖期是否连续，指定时间段不需要生成奖期等)
 */

class RestSetting extends BaseModel {

    public static $resourceName = 'RestSetting';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'close_type',
        'start_date',
        'end_date',
        'week',
        'start_time',
        'end_time',
        'issue_successive',
        'status',
    ];
    public static $htmlSelectColumns = [
        'type' => 'aValidTypes',
        'lottery_id' => 'aLotteries',
        'close_type' => 'aRestType',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'issue_successive' => 'in:0,1',
        'start_date' => 'date|dateformat:Y-m-d',
        'end_date' => 'date|dateformat:Y-m-d',
        'start_time' => 'dateformat:H:i:s',
        'end_time' => 'dateformat:H:i:s',
        'week' => 'max:20',
        'status' => 'in:0,1',
    ];
    protected $fillable = [
        'id',
        'lottery_id',
        'close_type',
        'start_date',
        'end_date',
        'week',
        'start_time',
        'end_time',
        'issue_successive',
        'status',
    ];

    const TYPE_DRAW_TIME = 1;
    const TYPE_REPEATE = 2;

    public static $aRestType = [
        self::TYPE_DRAW_TIME => 'Draw Time',
        self::TYPE_REPEATE => 'Repeat'
    ];
    protected $table = 'rest_settings';

    /**
     * 根据彩种id,获取休市信息
     * @param int $iLotteryId  彩种id
     */
    public function getClosedMarketInfoByLotteryId($iLotteryId) {
        $oAllInfos = DB::table($this->table)->where('lottery_id', $iLotteryId)->where('status', 1)->first();
        if ($oAllInfos != null) {
            $aClosedMarket = array();
            switch ($oAllInfos->close_type) {
                case self::TYPE_DRAW_TIME:
                    return $this->generateArrayByObject($oAllInfos, array('lottery_id', 'close_type', 'start_date', 'end_date', 'issue_successive'));
                    break;
                case self::TYPE_REPEATE:
                    return $this->generateArrayByObject($oAllInfos, array('lottery_id', 'close_type', 'start_time', 'end_time', 'week', 'issue_successive'));
                    break;
            }
        }
        return array();
    }

    /**
     * 从对象中提取以指定字段组成的数组
     * @param object $oModelData  对象数据
     * @param array $aFields     字段数组
     * @return type 数组
     */
    private function generateArrayByObject($oModelData, $aFields = array()) {
        if (empty($aFields)) {
            return objectToArray($oModelData);
        } else if (is_array($aFields)) {
            $aModel = array();
            foreach ($aFields as $v) {
                $aModel[$v] = $oModelData->$v;
            }
            return $aModel;
        } else {
            return array();
        }
    }

    protected function beforeValidate() {
        foreach ($this->columnSettings as $sColumn => $aSettings) {
            switch ($sColumn) {
                case 'end_date':
                case 'start_date':
                case 'start_time':
                case 'end_time':
                    !empty($this->$sColumn) or $this->$sColumn = null;
                    break;
            }
        }
        if (is_array($this->week)) {
            $this->week = implode(',', $this->week);
        }
        return parent::beforeValidate();
    }

    public static function getRestType($bOrderByTitle = false) {
        $aNewRestType = [];
        foreach (self::$aRestType as $key => $val) {
            $aNewRestType[$key] = __("_restsetting." . $val);
        }
        return $aNewRestType;
    }

}
