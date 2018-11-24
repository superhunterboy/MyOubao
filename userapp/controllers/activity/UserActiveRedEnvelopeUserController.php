<?php

class UserActiveRedEnvelopeUserController extends Controller {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActiveRedEnvelopeUser';
    protected $customViewPath = 'active-red-envelopes-users';
    protected $customViews = [
        'index',
        'getRedEnvelope',
    ];

    public function getRedEnvelope() {
        //活动设置
        $beginDate = SysConfig::readValue('active_red_envelopes_start_time');
        $endDate = SysConfig::readValue('active_red_envelopes_end_time');
        $every_ative_mins = SysConfig::readValue('active_red_envelopes_mins');
        $active_red_envelopes_status = SysConfig::readValue('active_red_envelopes_status');
        if (!$active_red_envelopes_status) {
            return $this->outPut(0, '活动已经关闭');
        }
        //当前活动
        $oCurrentRedEnvelope = ActiveRedEnvelope::getCurrentRedEnvelope();
        $user_id = Session::get('user_id');
        if (!is_object($oCurrentRedEnvelope)) {
            return $this->outPut(0, '没有活动!');
        }
        if ($oCurrentRedEnvelope->balance - $oCurrentRedEnvelope->amount <= 0) {

            return $this->outPut(0, '红包已被抢光啦');
            //红包已经抢完
        }
//        echo $oCurrentRedEnvelope->id;
        //获取
        $currentRedEnvolopeUser = ActiveRedEnvelopeUser::getCurrentRedEnvelopeUser($user_id, $oCurrentRedEnvelope->id);
        if (!$currentRedEnvolopeUser) {

            return $this->outPut(0, '有效投注额不满足!');
        }
        if ($currentRedEnvolopeUser->status == 1) {
            return $this->outPut(0, '不要重复强红包哦!');
        }

//        pr($oCurrentRedEnvelope->toArray());
        $userRule = ActiveRedEnvelopeRule::getRules($currentRedEnvolopeUser);

        if (!$userRule) {
            //销售量或者投注次数没有达到强红包限额
            return $this->outPut(0, '有效投注额不满足!');
        }
        $balnce = $oCurrentRedEnvelope->balance - $oCurrentRedEnvelope->amount;
        $amount_array = $userRule->getAmountArray();
        //如果余额小于范围最大， 则取余额。
        /*if ($balnce <= 0 || empty($amount_array)) {
            //红包已经抢完
            return $this->outPut(0, '红包已被抢光啦!');
        }*/

        if ($balnce <= 0 || empty($amount_array)) {
            //红包已经抢完
            return $this->outPut(0, '红包已被抢光啦!');
        }
        foreach($amount_array as $key=>$value){
            if($amount_array[$key] > $balnce) unset($amount_array[$key]);
        }
        if(!$amount_array){
            return $this->outPut(1, 'success', ['amount'=>0]);
        }
        $amount_array = array_values($amount_array);


        $res = ActiveRedEnvelopeUser::getUserActiveRedEnvelopeAmount($amount_array, $oCurrentRedEnvelope, $currentRedEnvolopeUser);
        if (!$res) {
            return $this->outPut(0, '抢红包失败!');
        }
        return $this->outPut(1, 'success', $res->toArray());
    }

    /**
     * 输出
     * @param type $isSucess
     * @param type $msg
     * @param type $data
     * @return type
     */
    public function outPut($isSucess, $msg = '', $data = []) {
        $aDatas = [
            'isSuccess' => $isSucess,
            'msg' => $msg,
            'type' => $isSucess == 1 ? 'success' : 'error',
            'data' => $data
        ];
        return Response::json($aDatas);
    }

    public function getDatas() {
        //活动设置

        $status = SysConfig::readValue('active_red_envelopes_status');
//        $beginDate = SysConfig::readValue('active_red_envelopes_start_time');
//        $endDate = SysConfig::readValue('active_red_envelopes_end_time');
//        $every_ative_mins = SysConfig::readValue('active_red_envelopes_mins');

        $user_id = Session::get('user_id');
        $aDatas = [
            'status' => $status,
            'user_id' => $user_id,
            'isUserAvailable' => 0,
            'currentEndTime' => '',
            'currentStartTime' => '',
            'currentTime' => date('Y-m-d H:i:s'),
            'balance' => 0,
            'turnover' => 0,
        ];

        $oCurrent = ActiveRedEnvelope::getCurrentRedEnvelope();
        if (is_object($oCurrent)) {
            $aDatas['currentEndTime'] = $oCurrent->end_time;
            $aDatas['currentStartTime'] = $oCurrent->start_time;
            $aDatas['balance'] = $oCurrent->balance-$oCurrent->amount;
            if ($currentRedEnvolopeUser = ActiveRedEnvelopeUser::getCurrentRedEnvelopeUser($user_id, $oCurrent->id)) {
                $aDatas['turnover'] = $currentRedEnvolopeUser->turnover;
                if ($currentRedEnvolopeUser->status == 1) {
                    $aDatas['isUserAvailable'] = 1;
                } else {
                    if ($userRule = ActiveRedEnvelopeRule::getRules($currentRedEnvolopeUser)) {
                        $aDatas['isUserAvailable'] = 2;
                    }
                }
            }
        }
        return Response::json($aDatas);
//        echo $sDatas;exit;
//        $this->layout = View::make('events.anniversary.index')->with(['sDatas'=>$sDatas]);
    }

}
