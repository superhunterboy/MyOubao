<?php

/**
 * 日度盈亏表
 *
 */
class Profit extends BaseModel {

    protected $table = 'profits';
    public static $resourceName = 'Profit';
    protected $fillable = [
        'date',
        'deposit',
        'withdrawal',
        'turnover',
        'prj_count',
        'tester_prj_count',
        'net_prj_count',
        'turnover',
        'prize',
        'dividend',
        'profit',
        'commission',
        'deposit_fee',
        'daily_salary',
        'net_daily_salary',
        'tester_daily_salary',
        'bonus',
        'tester_deposit',
        'tester_withdrawal',
        'tester_turnover',
        'tester_prize',
        'tester_dividend',
        'tester_profit',
        'tester_commission',
        'tester_deposit_fee',
        'tester_bonus',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_dividend',
        'net_commission',
        'net_deposit_fee',
        'net_bonus',
        'net_profit',
        'profit_margin',
        'registered_count',
        'registered_top_agent_count',
        'signed_count',
        'bought_count',
        'signed_users',
        'bought_users',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];
    public static $rules = [
        'date' => 'required|date',
        'registered_count' => 'integer',
        'registered_top_agent_count' => 'integer',
        'signed_count' => 'integer',
        'bought_count' => 'integer',
        'deposit' => 'numeric|min:0',
        'withdrawal' => 'numeric|min:0',
        'prj_count' => 'integer',
        'turnover' => 'numeric',
        'prize' => 'numeric|min:0',
        'dividend' => 'numeric|min:0',
        'commission' => 'numeric|min:0',
        'bonus' => 'numeric',
        'daily_salary' => 'numeric',
        'net_daily_salary' => 'numeric',
        'tester_daily_salary' => 'numeric',
        'profit' => 'numeric',
        'tester_deposit' => 'numeric|min:0',
        'tester_withdrawal' => 'numeric|min:0',
        'tester_prj_count' => 'integer',
        'tester_turnover' => 'numeric',
        'tester_prize' => 'numeric|min:0',
        'tester_dividend' => 'numeric|min:0',
        'tester_commission' => 'numeric|min:0',
        'tester_bonus' => 'numeric',
        'tester_profit' => 'numeric',
        'net_deposit' => 'numeric|min:0',
        'net_withdrawal' => 'numeric|min:0',
        'net_prj_count' => 'integer',
        'net_turnover' => 'numeric',
        'net_prize' => 'numeric',
        'net_dividend' => 'numeric',
        'net_commission' => 'numeric',
        'net_bonus' => 'numeric',
        'net_profit' => 'numeric',
        'profit_margin' => 'numeric',
    ];
    public $orderColumns = [
        'date' => 'desc',
    ];
    public static $mainParamColumn = 'date';
    public static $titleColumn = 'date';

    protected function beforeValidate() {
        $this->registered_count or $this->registered_count = null;
        $this->registered_top_agent_count or $this->registered_top_agent_count = null;
        $this->bought_count or $this->bought_count = null;
        $this->signed_count or $this->signed_count = $this->bought_count;
        return parent::beforeValidate();
    }

    /**
     * 返回对象
     * @param string $sDate
     * @return UserProfit
     */
    public static function getProfitObject($sDate) {
        $obj = self::where('date', '=', $sDate)->get()->first();

        if (!is_object($obj)) {
            $data = [
                'date' => $sDate,
            ];
            $obj = new Profit($data);
        }
        return $obj;
    }

    /**
     * 累加充值额
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addDeposit($fAmount, $oUser) {
        $this->deposit += $fAmount;
        !$oUser->is_tester or $this->tester_deposit += $fAmount;
        $this->net_deposit = $this->deposit - $this->tester_deposit;
        return $this->save();
    }

    /**
     * 累加充值手续费
     * @param float $fAmount
     * @return boolean
     */
    public function addDeposit_fee($fAmount, $oUser) {
        $this->deposit_fee += $fAmount;
        !$oUser->is_tester or $this->tester_deposit_fee += $fAmount;
        $this->net_deposit_fee = $this->deposit_fee - $this->tester_deposit_fee;
        return $this->save();
    }

    /**
     * 累加提款额
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addWithdrawal($fAmount, $oUser) {
        $this->withdrawal += $fAmount;
        !$oUser->is_tester or $this->tester_withdrawal += $fAmount;
        $this->net_withdrawal = $this->withdrawal - $this->tester_withdrawal;
        return $this->save();
    }

    /**
     * 累加销售额
     * @param float $fAmount
     * @param boolean $oUser->is_tester
     * @return boolean
     */
    public function addTurnover($fAmount, $oUser) {
        $this->turnover += $fAmount;
        $fAmount > 0 ? $this->prj_count++ : $this->prj_count--;
        if ($oUser->is_tester) {
            $this->tester_turnover += $fAmount;
            $fAmount > 0 ? $this->tester_prj_count++ : $this->tester_prj_count--;
        }
        $this->net_prj_count = $this->prj_count - $this->tester_prj_count;
        $this->calculateProfit($oUser->is_tester);
        $oUser->is_tester or $this->updateBoughtCount($oUser);
        if ($bSucc = $this->save()) {
            file_put_contents('/tmp/profit', var_export($this->validationErrors->toArray(), 1));
            $this->setDateTurnoverCache();
        } else {
            file_put_contents('/tmp/profit', var_export($this->validationErrors->toArray(), 1));
        }
        return $bSucc;
    }

    private function calculateProfit($bTester = false) {
        $this->profit = $this->turnover - $this->prize - $this->commission - $this->bonus - $this->deposit_fee - $this->daily_salary - $this->dividend;
        !$bTester or $this->tester_profit = $this->tester_turnover - $this->tester_prize - $this->tester_commission - $this->tester_bonus - $this->tester_daily_salary - $this->tester_deposit_fee  - $this->tester_dividend;
        $this->net_turnover = $this->turnover - $this->tester_turnover;
        $this->net_prize = $this->prize - $this->tester_prize;
        $this->net_commission = $this->commission - $this->tester_commission;
        $this->net_bonus = $this->bonus - $this->tester_bonus;
        $this->net_daily_salary = $this->daily_salary - $this->tester_daily_salary;
        $this->net_profit = $this->net_turnover - $this->net_prize - $this->net_commission - $this->net_bonus - $this->net_deposit_fee - $this->net_dividend;
//        pr($this->toArray());
        $this->profit_margin = $this->net_turnover ? $this->net_profit / $this->net_turnover : 0;
    }

    /**
     * 累加奖金
     *
     * @param float $fAmount
     * @param User $oUser
     * @return boolean
     */
    public function addPrize($fAmount, $oUser) {
        $this->prize += $fAmount;
        !$oUser->is_tester or $this->tester_prize += $fAmount;
        $this->calculateProfit($oUser->is_tester);
        return $this->save();
    }

    /**
     * 累加团队佣金
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addCommission($fAmount, $oUser) {
        $this->commission += $fAmount;
        !$oUser->is_tester or $this->tester_commission += $fAmount;
        $this->calculateProfit($oUser->is_tester);
        return $this->save();
    }

    /**
     * 添加促销派奖
     * @param float $fAmount
     * @param object $oUser
     */
    public function addBonus($fAmount, $oUser) {
        $this->bonus +=$fAmount;
        !$oUser->is_tester or $this->tester_bonus += $fAmount;
        $this->calculateProfit($oUser->is_tester);
        return $this->save();
    }

    /**
     * 累加日工资
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addDaily_salary($fAmount, $oUser) {
        $this->daily_salary += $fAmount;
        !$oUser->is_tester or $this->tester_daily_salary += $fAmount;
        $this->calculateProfit($oUser->is_tester);
        return $this->save();
    }

    public static function updateProfitData($sType, $sDate, $fAmount, $oUser) {
        $sFunction = 'add' . ucfirst($sType);
        $bSucc = true;
        $oProfit = self::getProfitObject($sDate);
//        pr($oProfit->getAttributes());
//        exit;
        return $oProfit->$sFunction($fAmount, $oUser);
    }

    public static function updateSignedCount($sDate, $iUserId) {
        $oProfit = self::getProfitObject($sDate);
        if ($oProfit->signed_users) {
            $aSignedUsers = explode(',', $oProfit->signed_users);
            $bIncrement = !in_array($iUserId, $aSignedUsers);
        } else {
            $aSignedUsers = [];
            $bIncrement = true;
        }
//        pr($aSignedUsers);
        if ($bIncrement) {
            $aSignedUsers[] = $iUserId;
            $oProfit->signed_users = implode(',', $aSignedUsers);
            $oProfit->signed_count++;
            return $oProfit->save();
        } else {
            return true;
        }
    }

    public function updateBoughtCount($oUser) {
        if ($oUser->is_tester) {
            return true;
        }
        if ($this->bought_users) {
            $aBoughtUsers = explode(',', $this->bought_users);
            $bIncrement = !in_array($oUser->id, $aBoughtUsers);
        } else {
            $aBoughtUsers = [];
            $bIncrement = true;
        }
//        pr($aBoughtUsers);
        if ($bIncrement) {
            $aBoughtUsers[] = $oUser->id;
            $this->bought_users = implode(',', $aBoughtUsers);
            $this->bought_count++;
        }
        return true;
        ;
    }

    public static function updateRegisterCount($sDate, $bIsTopAgent) {
        $oProfit = self::getProfitObject($sDate);
        $sField = $bIsTopAgent ? 'registered_top_agent_count' : 'registered_count';
        $oProfit->$sField++;
        return $oProfit->save();
    }

    public function setDateTurnoverCache() {
        $key = 'date-turnover-' . $this->date;
        Cache::setDefaultDriver(static::$cacheDrivers[1]);
//        if (Cache::has($key)){
//            Cache::set($key, $this->net_turnover);
//        }
//        else{
        Cache::put($key, $this->net_turnover, 60 * 24);
//        }
    }

    public static function getTurnoverFromCache($sDate) {
        $key = 'date-turnover-' . $sDate;
        Cache::setDefaultDriver(static::$cacheDrivers[1]);
        return ($fTurnover = Cache::get($key)) ? $fTurnover : 0;
    }

}
