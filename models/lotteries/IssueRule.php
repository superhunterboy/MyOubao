<?php

/**
 * 奖期规则模型
 */
class IssueRule extends BaseModel {

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'Issue Rule';
    protected $table = 'issue_rules';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'begin_time',
        'end_time',
        'cycle',
        'first_time',
        'stop_adjust_time',
        'encode_time',
        'issue_count',
        'enabled',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'begin_time' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'lottery_id';
    public static $ignoreColumnsInEdit = [
        'issue_count',
    ];
    protected $fillable = [
        'id',
        'lottery_id',
        'begin_time',
        'end_time',
        'cycle',
        'first_time',
        'stop_adjust_time',
        'encode_time',
        'issue_count',
        'enabled',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id' => 'required|integer',
        'begin_time' => 'required|dateformat:H:i:s',
        'end_time' => 'required|dateformat:H:i:s',
        'cycle' => 'integer',
        'first_time' => 'required|dateformat:H:i:s',
        'stop_adjust_time' => 'numeric',
        'encode_time' => 'integer',
        'issue_count' => 'integer',
        'enabled' => 'in:0,1',
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'begin_time';

    /**
     * 奖级设置表名称
     *
     * @var string
     */
    private $tableBonusSet;

    /**
     * 奖金明细表名称
     *
     * @var string
     */
    private $tableBonusDetails;

    public function setOpenClose() {
        $this->enabled == 1 - $this->enabled;
        return $this->save();
    }

    protected function beforeValidate() {
        $iEndTime = strtotime($this->end_time);
        $this->end_time > $this->first_time or $iEndTime += 3600 * 24;
        $iTotalTime = $iEndTime - strtotime($this->first_time);
//        pr($iTotalTime);
//                exit;
        $this->issue_count = floor($iTotalTime / $this->cycle) + 1;
//        pr($this->issue_count);
        return parent::beforeValidate();
    }

    protected function afterSave($oSavedModel) {
        $oRules = self::where('lottery_id', '=', $this->lottery_id)->where('enabled', '=', 1)->get(['id', 'issue_count']);
        $iIssueCountOfDay = 0;
        foreach ($oRules as $oRule) {
            $iIssueCountOfDay += $oRule->issue_count;
        }
        $oLottery = Lottery::find($this->lottery_id);
        $bTraceEqual = $oLottery->daily_issue_count == $oLottery->trace_issue_count;
        $oLottery->daily_issue_count = $iIssueCountOfDay;
        !$bTraceEqual or $oLottery->trace_issue_count = $iIssueCountOfDay;
        $oLottery->save();
//        $oLottery->
        return parent::afterSave($oSavedModel);
    }

    /**
     * 根据彩种id获取对应的奖期规则信息
     * @param int $iLotteryId   彩种id
     * @return array  奖期规则对象数组
     */
    public function findIssueRulesByLotteryId($iLotteryId) {
        $aIssueRules = DB::table($this->table)->where('lottery_id', $iLotteryId)->get();
        return $aIssueRules;
    }

    public function getIssueRules() {
        $aIssueRules = DB::table($this->table)->get(['id', 'begin_time']);
        $aIssueRuleList = array();
        foreach ($aIssueRules as $val) {
            $aIssueRuleList[$val->id] = $val->begin_time;
        }
        return $aIssueRuleList;
    }

    /**
     * 根据彩种id获取对应的奖期规则信息
     * @param int $iLotteryId   彩种id
     * @return array  奖期规则对象数组
     */
    public static function getIssueRulesOfLottery($iLotteryId) {
        return self::where('lottery_id', '=', $iLotteryId)->where('enabled', '=', 1)->get();
    }

}
