<?php

/**
 * 用户盈亏表
 *
 */
class TeamProfit extends BaseModel {

    protected $table = 'team_profits';
    public static $resourceName = 'TeamProfit';
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'deposit' => 2,
        'withdrawal' => 2,
        'turnover' => 4,
        'prize' => 4,
        'profit' => 6,
        'commission' => 6,
    ];
    public static $columnForList = [
        'date',
        'username',
        'is_tester',
        'user_type',
        'parent_user',
        'prize_group',
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'bonus',
        'commission',
        'profit',
    ];
    public static $totalColumns = [
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'bonus',
        'commission',
        'profit',
    ];
    public static $listColumnMaps = [
        'user_type' => 'user_type_formatted',
        'turnover' => 'turnover_formatted',
        'prize' => 'prize_formatted',
        'bonus' => 'bonus_formatted',
        'commission' => 'commission_formatted',
        'profit' => 'profit_formatted',
        'is_tester' => 'is_tester_formatted',
    ];
    protected $fillable = [
        'date',
        'user_id',
        'is_agent',
        'is_tester',
        'prize_group',
        'user_level',
        'username',
        'parent_user_id',
        'parent_user',
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'bonus',
        'commission',
        'profit',
    ];
    public static $rules = [
        'date' => 'required|date',
        'user_id' => 'required|integer',
        'is_agent ' => 'in:0,1',
        'prize_group' => 'integer',
        'user_level' => 'required|min:0|max:2',
        'username' => 'required|max:16',
        'parent_user_id' => 'integer',
        'parent_user' => 'max:16',
        'deposit' => 'numeric|min:0',
        'withdrawal' => 'numeric|min:0',
        'turnover' => 'numeric',
        'prize' => 'numeric',
        'bonus' => 'numeric',
        'profit' => 'numeric',
        'commission' => 'numeric',
    ];
    public static $noOrderByColumns = [
        'user_type',
    ];
    public $orderColumns = [
        'date' => 'desc',
        'turnover' => 'desc',
        'username' => 'asc',
    ];
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'username';
    public static $aUserTypes = ['-1' => 'Top Agent', '0' => 'Agent'];
    public static $classGradeFields = [
        'profit',
    ];

    // 按钮指向的链接，查询列名和实际参数来源的列名的映射
    // public static $aButtonParamMap = ['parent_user_id' => 'user_id'];

    /**
     * 返回TeamProfit对象
     *
     * @param string $sDate
     * @param string $iUserId
     * @return TeamProfit
     */
    public static function getTeamProfitObject($sDate, $iUserId) {
        $obj = self::where('user_id', '=', $iUserId)->where('date', '=', $sDate)->get()->first();
        $oUser = User::find($iUserId);
        if (!is_object($obj)) {
//            $oUser = User::find($iUserId);
//            pr($oUser->toArray());
//            pr($oUser->toArray());
            $data = [
                'user_id' => $oUser->id,
                'is_agent' => $oUser->is_agent,
                'is_tester' => $oUser->is_tester,
                'prize_group' => $oUser->prize_group,
                'user_level' => $oUser->user_level,
                'username' => $oUser->username,
                'parent_user_id' => $oUser->parent_id,
                'user_forefather_ids' => $oUser->forefather_ids,
                'parent_user' => $oUser->parent,
                'date' => $sDate
            ];
            $obj = new TeamProfit($data);
        } else {
            $obj->user_level = $oUser->user_level;
            $obj->prize_group = $oUser->prize_group;
        }
        return $obj;
    }

    /**
     * 代理盈亏总计
     * @param string $sBeginDate  开始日期
     * @param string $sEndDate    结束日期
     * @param int $iUserId         用户id
     * @return array
     */
    public static function getAgentSumInfo($sBeginDate, $sEndDate, $iUserId, $username = '') {
        $sSql = 'select sum(deposit) total_deposit, sum(withdrawal) total_withdrawal,sum(turnover) total_turnover,sum(commission) total_commission, sum(profit) total_profit,sum(prize) total_prize from team_profits where 1';
        $iUserId == null or $sSql .= ' and (parent_user_id = ?)';
        $iUserId == null or $aValue = [$iUserId];
//        !$username ? $sSql .=" or user_id=?)" : $sSql.=")";
//        $username or $aValue[] = $iUserId;
        !$sBeginDate or $sSql .=" and date>=?";
        !$sBeginDate or $aValue[] = $sBeginDate;
        !$sEndDate or $sSql .=" and date<=?";
        !$sEndDate or $aValue[] = $sEndDate;
        if ($username) {
            $sSql .= ' and username = ?';
            array_push($aValue, $username);
        }
        $results = DB::select($sSql, $aValue);
        return objectToArray($results[0]);
    }

    /**
     * 返回包含直接销售额，直接盈亏记录和团队销售额的数组
     *
     * @param string $sDate     只有年和月,格式：2014-01-01
     * @param string $iUserId   用户id
     * @return array
     */
    public static function getTeamProfitByDate($sBeginDate, $sEndDate, $iUserId) {
        $oQuery = self::where('user_id', '=', $iUserId);
        if (!is_null($sBeginDate)) {
            $oQuery->where('date', '>=', $sBeginDate);
        }
        if (!is_null($sEndDate)) {
            $oQuery->where('date', '<=', $sEndDate);
        }
        $aTeamProfits = $oQuery->get(['turnover', 'profit']);
        $data = [];
        $i = 0;
        foreach ($aTeamProfits as $oTeamProfit) {
            $data[$i]['turnover'] = $oTeamProfit->turnover;
            $data[$i]['profit'] = $oTeamProfit->profit;
            $i++;
        }
        return $data;
    }

    /**
     * 获取指定用户的团队销售总额
     * @param int $iUserId  用户id
     * @return float        团队销售总额
     */
    public static function getUserTotalTeamTurnover($sBeginDate, $sEndDate, $iUserId) {
        $aUserProfits = self::getTeamProfitByDate($sBeginDate, $sEndDate, $iUserId);
        $aTeamTurnovers = [];
        foreach ($aUserProfits as $data) {
            $aTeamTurnovers[] = $data['turnover'];
        }
        $fTotalTeamTurnover = array_sum($aTeamTurnovers);
        return $fTotalTeamTurnover;
    }

    /**
     * 获取指定用户的销售总额
     * @param int $iUserId  用户id
     * @return float        销售总额
     */
    public static function getUserTotalTurnover($sBeginDate, $sEndDate, $iUserId) {
        $aUserProfits = self::getTeamProfitByDate($sBeginDate, $sEndDate, $iUserId);
        $aTurnovers = [];
        foreach ($aUserProfits as $data) {
            $aTurnovers[] = $data['turnover'];
        }
        $fTotalTurnover = array_sum($aTurnovers);
        return $fTotalTurnover;
    }

    /**
     * 获取指定用户用户盈亏
     * @param int $iUserId  用户id
     * @return float        用户盈亏
     */
    public static function getUserTotalProfit($sBeginDate, $sEndDate, $iUserId) {
        $aUserProfits = self::getUserProfitByDate($sBeginDate, $sEndDate, $iUserId);
        $aProfits = [];
        foreach ($aUserProfits as $data) {
            $aProfits[] = $data['profit'];
        }
        $fTotalProfit = array_sum($aProfits);
        return $fTotalProfit;
    }

    /**
     * 累加充值额
     * @param float $fAmount
     * @return boolean
     */
    public function addDeposit($fAmount) {
        $this->deposit += $fAmount;
        return $this->save();
    }

    /**
     * 累加提现额
     * @param float $fAmount
     * @return boolean
     */
    public function addWithdrawal($fAmount) {
        $this->withdrawal += $fAmount;
        return $this->save();
    }

    /**
     * 累加个人销售额
     * @param float $fAmount
     * @return boolean
     */
    public function addTurnover($fAmount) {
        $this->turnover += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    /**
     * 累加奖金
     *
     * @param float $fAmount
     * @return boolean
     */
    public function addPrize($fAmount) {
        $this->prize += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    /**
     * 累加促销奖金
     *
     * @param float $fAmount
     * @return boolean
     */
    public function addBonus($fAmount) {
        $this->bonus += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    private function countProfit() {
        return $this->prize + $this->bonus + $this->commission - $this->turnover;
    }

    /**
     * 累加个人佣金
     * @param float $fAmount
     * @return boolean
     */
    public function addCommission($fAmount) {
        $this->commission += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    public static function & comipleTurnover($oUser, $fAmount) {
        $aForeFathers = explode(',', $oUser->forefather_ids);
        $aTurnovers = [];
        foreach ($aForeFathers as $iForeFatherId) {
            $aTurnovers[$iForeFatherId] = $fAmount;
        }
        $aTurnovers[$oUser->id] = $fAmount;
        return $aTurnovers;
    }

    public static function updateTurnOver($sDate, $oUser, $fAmount) {
        return self::updateProfitData('turnover', $sDate, $oUser, $fAmount);
    }

    public static function updatePrize($sDate, $oUser, $fAmount) {
        return self::updateProfitData('prize', $sDate, $oUser, $fAmount);
    }

    public static function updateBonus($sDate, $oUser, $fAmount) {
        return self::updateProfitData('bonus', $sDate, $oUser, $fAmount);
    }

    public static function updateCommission($sDate, $oUser, $fAmount) {
        return self::updateProfitData('commission', $sDate, $oUser, $fAmount);
    }

    public static function updateProfitData($sType, $sDate, $oUser, $fAmount) {
        $sFunction = 'add' . ucfirst($sType);
        $bSucc = true;
        $aForeFathers = $oUser->forefather_ids ? explode(',', $oUser->forefather_ids) : [];
        !$oUser->is_agent or $aForeFathers[] = $oUser->id;
        foreach ($aForeFathers as $iForeFatherId) {
            $oUserProfit = self::getTeamProfitObject($sDate, $iForeFatherId);
//                pr($oUserProfit->toArray());
            if (!$bSucc = $oUserProfit->$sFunction($fAmount)) {
//                                    pr($oUserProfit->validationErrors->toArray());
                break;
            }
//                pr($oUserProfit->toArray());
        }
//        pr($bSucc);
        return $bSucc;
    }

    // protected function getUserTypeFormattedAttribute() {
    //     // return static::$aUserTypes[($this->parent_user_id != null ? 'not_null' : 'null')];
    //     return __('_userprofit.' . strtolower(static::$aUserTypes[intval($this->parent_user_id != null) - 1]));
    // }

    protected function getUserTypeFormattedAttribute() {
        if ($this->parent_user_id)
            $sUserType = User::$aUserTypes[$this->is_agent];
        else
            $sUserType = User::$aUserTypes[User::TYPE_TOP_AGENT];
        return __('_user.' . $sUserType);
    }

    protected function getDepositFormattedAttribute() {
        return $this->getFormattedNumberForHtml('deposit');
    }

    protected function getWithdrawalFormattedAttribute() {
        return $this->getFormattedNumberForHtml('withdrawal');
    }

    protected function getTurnoverFormattedAttribute() {
        return $this->getFormattedNumberForHtml('turnover');
    }

    protected function getPrizeFormattedAttribute() {
        return $this->getFormattedNumberForHtml('prize');
    }

    protected function getBonusFormattedAttribute() {
        return $this->getFormattedNumberForHtml('bonus');
    }

    protected function getCommissionFormattedAttribute() {
        return $this->getFormattedNumberForHtml('commission');
    }

    protected function getProfitFormattedAttribute() {
        return $this->getFormattedNumberForHtml('profit');
    }

    public function clear() {
        $this->deposit = 0;
        $this->withdrawal = 0;
        $this->turnover = 0;
        $this->prize = 0;
        $this->bonus = 0;
        $this->commission = 0;
        $this->profit = 0;
    }

    protected function getIsTesterFormattedAttribute() {
        return is_null($this->attributes['is_tester']) ? '' : __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
    }

    /**
     * 获取所有总代指定日期的盈亏数据
     * @param string $sBeginDate    开始时间
     * @param string $sEndDate       结束时间
     */
    public static function getAllTopAgentProfitByDate($sDate) {
        $oQuery = self::where('date','=',$sDate)->where('user_level', '=', 0);
        $aResult = $oQuery->get();
        return $aResult;
    }
    
    /**
     * 获取总代指定时间的盈亏数据总和
     * @param string $sBeginDate    开始时间
     * @param string $sEndDate       结束时间
     */
    public static function getTopAgentProfitByDate($sBeginDate, $sEndDate) {
        $oQuery = DB::table('team_profits')->select(DB::raw('username,user_id,is_tester, sum(profit) total_profit, sum(turnover) total_turnover, sum(prize) total_prize, sum(commission) total_commission, sum(bonus) total_bonus'));
        $oQuery = $oQuery->where('date', '>=', $sBeginDate)->where('date', '<=', $sEndDate)->where('user_level', '=', 0);
        $oQuery = $oQuery->groupBy('username');
        $aResult = $oQuery->get();
        return $aResult;
    }

    public function getGroupProfit() {
        return self::select(DB::raw('username, sum(deposit) deposit, sum(withdrawal) withdrawal, sum(turnover) turnover, sum(prize) prize, sum(bonus) bonus, sum(commission) commission, sum(profit) profit'));
    }

}
