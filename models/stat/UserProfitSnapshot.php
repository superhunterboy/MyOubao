<?php

/**
 * 用户盈亏表
 *
 * @author frank
 */
class UserProfitSnapshot extends BaseModel {

    protected $table = 'user_profits';
    public static $resourceName = 'UserProfitSnapshot';
    public static $amountAccuracy    = 6;

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

        'team_deposit',
        'team_withdrawal',
        'team_turnover',
        'team_prize',
        'team_profit',
        'team_commission',
        'team_dividend',
        'team_bet_commission',

        'direct_deposit',
        'direct_withdrawal',
        'direct_turnover',
        'direct_prize',
        'direct_profit',
        'direct_commission',
        'direct_dividend',
        'direct_bet_commission',

        'turnover',
        'prize',
        'profit',
        'commission',
        'deposit',
        'withdrawal',
        'dividend',
        'bet_commission'
    ];
    public static $rules = [
        'date' => 'required',
        'user_id' => 'required|integer',
        'is_agent' => 'in:0,1',
        'is_tester'         => 'in:0,1',
        'prize_group' => 'required',
        'user_level' => 'required|min:0|max:2',
        'username' => 'required|max:16',
        'parent_user_id' => 'integer',
        'parent_user' => 'max:16',

        'team_deposit' => 'numeric|min:0',
        'team_withdrawal' => 'numeric|min:0',
        'team_turnover' => 'numeric|min:0',
        'team_prize' => 'numeric|min:0',
        'team_profit' => 'numeric',
        'team_commission' => 'numeric|min:0',
        'team_dividend' => 'numeric|min:0',
        'team_bet_commission' => 'numeric|min:0',

        'direct_deposit' => 'numeric|min:0',
        'direct_withdrawal' => 'numeric|min:0',
        'direct_turnover' => 'numeric|min:0',
        'direct_prize' => 'numeric|min:0',
        'direct_profit' => 'numeric',
        'direct_commission' => 'numeric|min:0',
        'direct_dividend' => 'numeric|min:0',
        'direct_bet_commission' => 'numeric|min:0',

        'deposit' => 'numeric|min:0',
        'withdrawal' => 'numeric|min:0',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'profit' => 'numeric',
        'commission' => 'numeric|min:0',
        'dividend' => 'numeric|min:0',
        'bet_commission' => 'numeric|min:0',
    ];


    /**
     * 返回UserProfit对象
     *
     * @param string $sDate
     * @param string $iUserId
     * @return UserProfit
     */
    public static function getUserProfitObject($sDate, $iUserId) {
        $obj = self::where('user_id', '=', $iUserId)->where('date', '=', $sDate)->get()->first();
        if(!$oUser = User::find($iUserId)){
            return false;
        }
        if (!is_object($obj)) {
//            $oUser = User::find($iUserId);
//            pr($oUser->toArray());
//            pr($oUser->toArray());
            $data = [
                'user_id' => $oUser->id,
                'is_agent' => $oUser->is_agent,
                'is_tester'      => $oUser->is_tester,
                'prize_group' => $oUser->prize_group,
                'user_level' => $oUser->user_level,
                'username' => $oUser->username,
                'parent_user_id' => $oUser->parent_id,
                'parent_user' => $oUser->parent,
                'date' => $sDate
            ];
            $obj = new UserProfitSnapshot($data);
        } else {
            $obj->user_level = $oUser->user_level;
            $obj->prize_group = $oUser->prize_group;

            $obj->team_deposit = 0;
            $obj->team_withdrawal = 0;
            $obj->team_turnover = 0;
            $obj->team_prize = 0;
            $obj->team_profit = 0;
            $obj->team_commission = 0;
            $obj->team_dividend = 0;
            $obj->team_bet_commission = 0;

            $obj->direct_deposit = 0;
            $obj->direct_withdrawal = 0;
            $obj->direct_turnover = 0;
            $obj->direct_prize = 0;
            $obj->direct_profit = 0;
            $obj->direct_commission = 0;
            $obj->direct_dividend = 0;
            $obj->direct_bet_commission = 0;

            $obj->deposit = 0;
            $obj->withdrawal = 0;
            $obj->turnover = 0;
            $obj->prize = 0;
            $obj->profit = 0;
            $obj->commission = 0;
            $obj->dividend = 0;
            $obj->bet_commission = 0;
        }
        return $obj;
    }

    public function updateProfit($sType, $bDirect, $fAmount) {

        $this->addTeam($sType, $fAmount);

        if($bDirect){
            $this->addDirect($sType, $fAmount);
        }
    }

    public function updateSelfProfit($sType, $fAmount)
    {
        $this->addSelf($sType, $fAmount);
    }

    public function addSelf($sType, $fAmount){
        $this->{$sType} += $fAmount;
    }

    public function addDirect($sType, $fAmount){
        $this->{'direct_' . $sType} += $fAmount;
    }

    public function addTeam($sType, $fAmount){
        $this->{'team_' . $sType} += $fAmount;
    }

    public function saveProfit(){
        $this->profit = $this->prize + $this->commission + $this->dividend - $this->turnover + $this->bet_commission;
        $this->direct_profit = $this->direct_prize + $this->direct_commission + $this->direct_dividend - $this->direct_turnover + $this->direct_bet_commission;
        $this->team_profit = $this->team_prize + $this->team_commission + $this->team_dividend - $this->team_turnover + $this->team_bet_commission;
        $this->save();
    }

}
