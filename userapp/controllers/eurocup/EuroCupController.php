<?php

/**
 * 投注
 */
class EuroCupController extends UserBaseController {

    protected $errorFiles = [
        'system',
        'bet',
        'fund',
        'account',
        'lottery',
        'issue',
        'seriesway'
    ];
    protected $resourceView = 'eurocup.index';
    protected $customViewPath = 'eurocup';
    protected $modelName = 'UserUser';
    protected $customViews = [
        'index',
    ];
    protected $accountLocker = null;
    protected $dbThreadId = null;

    public function index(){
        //后台是否禁止投注
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        if (!is_object($oUser)) {
            echo '{"status":"0","msg":"'.__('_user.missing-user').'"}';exit;
        }

        if ($oUser->blocked == UserUser::BLOCK_BUY) {
            echo '{"status":"0","msg":"'.__('_user.bet-not-allowed').'"}';exit;
        }

        $bPost = Request::method() == 'POST';

        if ($bPost) {
            $aData = Input::all();

            $sFirst = $aData['champion'];
            $sSecond = $aData['runnerup'];
            $sThird = $aData['finafour'];
            $sFourth = $aData['finafour2'];

            if(!$sFirst || !$sSecond || !$sThird || !$sFourth) {
                echo '{"status":"2","msg":"四强必选"}';exit;
            }

            $sTime1 = '2016-04-21';
            $sTime2 = '2016-05-21';
            $sNow = date('Y-m-d');

            $iCount = 0;

            if($sNow >= $sTime1 && $sNow <= $sTime2){
                $fLotteryTurnover = UserUserProfit::where('user_id',$iUserId)
                    ->where('date','>=',$sTime1)
                    ->where('date','<=',$sTime2)->sum('turnover');
                $fSlotTurnover = UserUserSlotProfit::where('user_id',$iUserId)
                    ->where('date','>=',$sTime1)
                    ->where('date','<=',$sTime2)->sum('turnover');
                $fSportTurnover = UserUserSportProfit::where('user_id',$iUserId)
                    ->where('date','>=',$sTime1)
                    ->where('date','<=',$sTime2)->sum('turnover');

                $fTotalTurnover = $fLotteryTurnover + $fSlotTurnover + $fSportTurnover;

                if($fTotalTurnover >= 888) $iCount = 1;
            }elseif($sNow > $sTime2){
                $fSportTurnover = UserUserSportProfit::where('user_id',$iUserId)
                    ->where('date','>',$sTime2)->sum('turnover');
                if($fSportTurnover >= 88) $iCount = 1;
            }else{
                echo '{"status":"0","msg":"不在活动时间内"}';exit;
            }

            if(!$iCount) {
                echo '{"status":"0","msg":"投注金额不足"}';exit;
            }


            $iCount = DB::table('eurocups')->where('user_id',$iUserId)->count();
            if($iCount) {
                echo '{"status":"0","msg":"您已经提交竞猜组合!请勿重复提交"}';exit;
            }else{
                $bSucc = DB::table('eurocups')->insert(
                    array(
                        'user_id' => $iUserId,
                        'first' => $sFirst,
                        'second' => $sSecond,
                        'third' => $sThird,
                        'fourth' => $sFourth,
                        'username' => $oUser->username
                    )
                );
                if($bSucc) {
                    echo '{"status":"1","msg":"竞猜成功"}';exit;
                }else{
                    echo '{"status":"2","msg":"竞猜失败,请稍后再试"}';exit;
                }
            }
        } else {
            parent::index();
        }

    }


}
