<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActiveRedEnvelopeUser extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'active_red_envelope_users';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'username',
        'turnover',
        'user_id',
        'red_envelope_id',
        'bets_times',
        'status',
        'amount',
        'get_amount_at',
    ];
    public static $resourceName = 'ActiveRedEnvelopeUser';

    public $orderColumns = ['id'=>'desc'];
    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'turnover',
        'user_id',
        'red_envelope_id',
        'bets_times',
        'status',
        'amount',
        'get_amount_at',
        'updated_at',
        'created_at',
    ];
    public static $titleColumn = 'username';
    public static $ignoreColumnsInEdit = ['user_id', 'username', 'red_envelope_id'];
    public static $rules = [
        'bets_times' => 'required|integer',
        'user_id' => 'required|integer',
        'username' => 'required|between:1,16',
        'turnover' => 'required|numeric',
        'amount' => 'required|numeric',
        'red_envelope_id' => 'required:integer',
        'status' => 'required|in:0,1,2',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];
    public static $aStatus=[
        '1'=>'已领取',
        '0'=>'未领取',
    ];
    static public function updateUserActiveProfitData($currentActive, $oUser, $turnover) {
        $bSucc = true;
        $oUserActiveProfit = self::getUserActiveProfitObject($currentActive->id, $oUser);
        if(isset($oUserActiveProfit->status)&&$oUserActiveProfit->status==1){
            return false;
        }
        return $oUserActiveProfit->addSelfUserActiveTurnover($turnover);
//         pr($oUserActiveProfit->validationErrors->toArray());
    }

    /**
     * 累加个人销售额
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addSelfUserActiveTurnover($fAmount) {
        $this->turnover += $fAmount;
        $this->bets_times+=1;
        $this->amount = 0;
        $this->status = 0;
        return $this->save();
    }

    /**
     * 返回UserProfit对象
     *
     * @param string $sDate
     * @param string $iUserId
     * @return UserProfit
     */
    public static function getUserActiveProfitObject($red_envelope_id, $oUser) {

        if (!$obj = self::where('user_id', '=', $oUser->id)->where('red_envelope_id', '=', $red_envelope_id)->first()) {
            $data = [
                'user_id' => $oUser->id,
                'username' => $oUser->username,
                'red_envelope_id' => $red_envelope_id,
            ];
            $obj = new ActiveRedEnvelopeUser($data);
        }
        return $obj;
    }

    public static function ActiveUpdateRedEnvelope($datas) {
//pr($datas);exit;
        extract($datas);
        if (!$user_id || !$username || !$turnover || !$lottery_id || !$way_id) {
            return "ERROR: Invalid Data, Exiting";
//            $this->log = "ERROR: Invalid Data, Exiting";
            return false;
        }
        //lotteryid是否为彩票
        $sDataPath = Config::get('widget.data_path');
        $sPath     = realpath($sDataPath) . '/';
        $lotteryMap =file_get_contents($sPath.'lotterymap.blade.php');
        $lotteryMap=json_decode($lotteryMap,true);
        if(isset($lotteryMap['lottery']) && !in_array($lottery_id,$lotteryMap['lottery']))
        {
            return false;
        }
        //检查游戏玩法是不是在活动游戏里
        /* $ways = ActiveRedEnvelopeWay::isValidateWay($lottery_id, $way_id);
         if (!is_object($ways)) {
             return "way not in active";
             return true;
         }*/
        //获取红包期id
//        $ActiveRedEnvelope=new ActiveRedEnvelope();
        $currentActive = ActiveRedEnvelope::getCurrentRedEnvelope();
        if (!is_object($currentActive)) {
            return "no active";
//            echo 4;
//            $log = "ERROR: Invalid activeRedEnvelop, Exiting";
            return false;
        }
        //统计入库
        $oUser = User::find($user_id);
        // 更新用户盈亏数据
        if (!$bSucc = ActiveRedEnvelopeUser::updateUserActiveProfitData($currentActive, $oUser, $turnover)) {
            return "failed";
            return false;
        }
        return true;
    }

    /**
     * 获取当前活动的投注额
     */
    public static function getCurrentRedEnvelopeUser($user_id, $red_envelope_id) {
        return self::where('user_id', $user_id)->where('red_envelope_id', $red_envelope_id)->first();
    }


//红包派发  $userActiveStatusData, $oCurrentRedEnvelope,$currentRedEnvolopeUser
    static public function getUserActiveRedEnvelopeAmount($amount_array, $oCurrentRedEnvelope,$currentRedEnvolopeUser) {
        //算出中奖金额
        $res_red_envelope = false;
        $res_red_envelope_users = false;
        $res = false;
        $amount = self::getRandAmount($amount_array,$oCurrentRedEnvelope);
        $oUser = User::find($currentRedEnvolopeUser->user_id);
        $oAccount = Account::getAccountInfoByUserId($currentRedEnvolopeUser->user_id);
        Account::lock($oUser->account_id, $iLocker);
        DB::connection()->beginTransaction();
        //更新红包期数表
        $oCurrentRedEnvelope->amount+=$amount;
        $res_red_envelope = $oCurrentRedEnvelope->save();
        //更新红包用户表
        $currentRedEnvolopeUser->amount = $amount;
        $currentRedEnvolopeUser->status = 1;
        $currentRedEnvolopeUser->get_amount_at =date('Y-m-d H:i:s');

        $res_red_envelope_users = $currentRedEnvolopeUser->save();
        //帐变$amount

        $active_red_envelopes_name= SysConfig::readValue('active_red_envelopes_name');
        $aExtraData=['note'=>$active_red_envelopes_name];
        $res = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_PROMOTIANAL_BONUS, $amount,$aExtraData);
        if ($res && $res_red_envelope && $res_red_envelope_users) {
            DB::connection()->commit();
            Account::unlock($oUser->account_id, $iLocker);
            return $currentRedEnvolopeUser;
        } else {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            return false;
        }
    }
    public static function getRandAmount($amount_array,$oCurrentRedEnvelope){
        $index=  rand(0, count($amount_array)-1);
        $balnce = $oCurrentRedEnvelope->balance - $oCurrentRedEnvelope->amount;
        if($amount_array[$index]>$balnce){
            return $balnce;
        }
        return $amount_array[$index];
    }

}
