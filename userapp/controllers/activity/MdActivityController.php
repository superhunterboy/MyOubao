<?php
class MdActivityController extends UserBaseController
{

    protected  $modelName = 'ActivityMdUser';
    protected $accountLocker = null;
    public static $aRandArr = [1,2,3,4,5,6,7,8,9,10];  
    protected $resourceView = 'centerUser.activityMd';
    protected static $iRewardNum = 5;       //中奖数字
    
    public function beforeRender() {
        parent::beforeRender();
    }
    
    public function index(){
        $userid =  Session::get('user_id');
        if(empty($userid)){
            return Redirect::route('home');
        }
        
        ActivityMdLeiJi::deleteIntegraCache();
            
            
        $oActivity = ActivityMd::getActivity();
        $date = date("Y-m-d H:i:s", time());
        if ($oActivity->start_time > $date || $date > $oActivity->end_time){
            return Redirect::route('home');
        }
        $start_time = date("Y-m-d", strtotime($oActivity->start_time));
        $end_time = date("Y-m-d", strtotime($oActivity->end_time));
        $aAllHand = ActivityHand::getAllHand();
        foreach ($aAllHand as $k=>$oData){
            $aAllHand[$k]->rule = ActivityMdSetting::getPerHandRule($oData->id);
        }
      $profitLs = UserProfit::getSumProfitOrLs($userid, 'turnover', $start_time, $end_time);  //流水
      $profitSlotLs = UserProfitSlot::getAllProfltSlotOrLs($userid, 'turnover', $start_time, $end_time);   //电子流水
      $oTotalLs = ActivityMdLeiJi::getAllUserLs($userid);   //总的流水
        if(empty($oTotalLs->total_ls)){
              $sumLs = $profitLs + $profitSlotLs;        //总流水
        }else{
              $sumLs = $profitLs + $profitSlotLs - $oTotalLs->total_ls;        //总流水
        }
        $sumLs = floor($sumLs);
        if($sumLs <= 0){
            $sumLs = 0;
        }
        $len = strlen($sumLs);
        $aMyIntegra = [];
        for($i = 0;$i < $len;$i++){
            $aMyIntegra[] = substr($sumLs, $i, 1);
        }
        $aAviable = ActivityHand::getUserAvaiAble($sumLs);  //取出该用户可以点击的按钮
        $iMaxId =  ActivityMdVdata::getMaxId();
        $aRandNum = range(1, $iMaxId);
        $aRandId = array_rand($aRandNum, 16);
        $aVUserData = ActivityMdVdata::getAllUser($aRandId);
        $aRandUser = [];
        foreach($aVUserData as $k=>$oVdata){
            $firstTwo = substr($oVdata->user_id, 0,2);
            $lastTwo = substr($oVdata->user_id, -2);
            $aRandUser[] = $firstTwo . "***" . $lastTwo;
        }

        $this->setVars(compact('aAllHand', 'sumLs', 'aAviable', 'aMyIntegra', 'aRandUser', 'datas'));
         return $this->render();

//            return $id;
    }


    public function isReward(){
        $oActivity = ActivityMd::getActivity();
        $date = date("Y-m-d H:i:s", time());
        $start_time = date("Y-m-d", strtotime($oActivity->start_time));
        $end_time = date("Y-m-d", strtotime($oActivity->end_time));
        ActivityMdLeiJi::deleteIntegraCache();
        if ($oActivity->start_time <= $date && $date <= $oActivity->end_time){
            $userid =  Session::get('user_id');
            if(empty($userid)){
                return json_encode(['error' => 101, 'Msg' => '请先登录']);
            }
            $params =  Input::all();
            $profitLs = UserProfit::getSumProfitOrLs($userid, 'turnover', $start_time, $end_time);  //流水
            $profitSlotLs = UserProfitSlot::getAllProfltSlotOrLs($userid, 'turnover', $start_time, $end_time);   //电子流水
            $oTotalLs = ActivityMdLeiJi::getAllUserLs($userid);   //总的流水
            if(empty($profitLs) && empty($profitSlotLs)){
                return json_encode(['error' => 102, 'Msg' => '积分不足']);
            }
            if(empty($oTotalLs->total_ls)){
                  $sumLs = $profitLs + $profitSlotLs;        //总流水
            }else{
                  $sumLs = $profitLs + $profitSlotLs - $oTotalLs->total_ls;        //总流水
            }
            if($sumLs < $params['id']){
                 return json_encode(['error' => 103, 'Msg' => '积分不足']);
            }
            $start = Date("Y-m-d 00:00:00", time());        //今天凌晨开始
            $end = Date("Y-m-d H:i:s", time());     //当前时间
            $totalTimes = ActivityMdUser::getUserTotalTimes($userid, $start, $end);   //用户每天抽奖总次数
            if($totalTimes >= 50){
                 return json_encode(['error' => 104, 'Msg' => '今日抽奖次数以达到上限']);
            }
           
            $integral = $params['id'];
            $oHand = ActivityHand::getHandByMoney($integral);      //取出手柄类型
            if(empty($oHand)){
                return json_encode(['error' => 105, 'Msg' => '请输入正确的积分']);
            }
            $dProtfitStartTime = date("Y-09-26 00:00:00", strtotime($oActivity->start_time)); //输赢从1号开始计算
            $profit =  UserProfit::getSumProfitOrLs($userid, 'profit', $dProtfitStartTime, $end_time);
            $profitSlot =  UserProfitSlot::getAllProfltSlotOrLs($userid, 'profit', $dProtfitStartTime, $end_time); 
            $oHistoryProfit = ActivityMdLeiJi::getAllUserLs($userid);      //取出用户抽奖历史消耗的亏损额
            if(!is_numeric($profit) || !is_numeric($profitSlot)){
                return json_encode(['error' => 106, 'Msg' => '网络异常']);
            }
            if(isset($oHistoryProfit) && $oHistoryProfit){
                $sumProfit = 0 - ( $profit + $profitSlot ) - $oHistoryProfit->total_vr_price;
            }else{
                $sumProfit = 0 - ( $profit + $profitSlot );
            }
            if($sumProfit <= 0){
                 $sumProfit = 0;
            }
            $flag = false;
            $aReward = ActivityMdSetting::getAvaiableRule($sumProfit,$oHand->id);
            if(count($aReward) < 1){
                return json_encode(['error' => 107, 'Msg' => '网络异常']);
            }
            foreach ($aReward as $k=>$oData) {
                if ("-99" != $oData->gift_totals && $oData->gift_totals <= 0) {
                    continue;
                }
                $iDayCount = ActivityMdUser::getDayTotalTimes($userid, $start, $end, $oData->id);
                if( $iDayCount >= $oData->day_times  ){     //每天用户对该商品的中奖次数
                    continue;
                }
                $iTotalCount = ActivityMdUser::getUserRewardTimes($userid, $oData->id);
                if($iTotalCount >= $oData->total_times){        //用户总的对该商品的中奖次数
                    continue;
                }
//                $iDayTotalTimes = ActivityMdUser::getUserPrizeDayTotalTimes($userid, $start, $end);
//                if($iDayTotalTimes >= 20){      //用户每天中奖总的的次数
//                    continue;
//                }
                if($oData->rand_num == 0){      //概率为0直接进行下一次循环
                   continue;
                }
                 if($oData->rand_num == 10){      //概率为1直接跳出循环
                   $flag = true;
                   break;
                }
                if (isset($oData->rand_num) && $oData->rand_num){
                    $aRewardArr = array_rand(self::$aRandArr, $oData->rand_num);
                    $aRewardArr = (array)$aRewardArr;
                    foreach ($aRewardArr as $m=>$key){
                        if (self::$aRandArr[$key] == self::$iRewardNum) {
                            $flag = true;
                            break 2;
                        }
                    }
                }
            }
            $oUser = User::find($userid);
          
            $oAccount = Account::lock($oUser->account_id, $this->accountLocker);
            if (empty($oAccount)) {
                return json_encode(['error' => 108, 'Msg' => '网络异常']);
            }
            DB::connection()->beginTransaction(); //开启事物
            $iStatus = $this->updateData($flag, $oData, $oActivity, $oUser, $oAccount, $oHand);
            if(!$iStatus){
                   DB::connection()->rollBack();
                return json_encode(['error' => 109, 'Msg' => '网络异常']);
            }
            DB::connection()->commit();
            $sumLs = $sumLs - $oHand->money;
            $sumLs = floor($sumLs);
            if($sumLs <= 0){
                $sumLs = 0;
            }
        
            if($flag){
                return json_encode(['data' => ["name"=>$oData->name, 'level' => $oData->level, 'price' => $oData->price, 'is_sent'=>($oData->type ==1 ? true : false), 'content'=>$oData->content, 'is_win' => 1, 'integral'=>$sumLs]]);
            }else{
               return json_encode(['data' => [ 'integral'=>$sumLs, 'level' => 0]]);
            }
        }else{
           return json_encode(['error' => 110, 'Msg' => '活动已经结束']);
        }
    }

    public function updateData($flag, $oData, $oActivity, $oUser, $oAccount, $oHand){
        $userid =  Session::get('user_id');
        $reward = "";
        $reward_id = null;
        $level = 0;
        $price = 0;
        $status = ActivityMdUser::NOREWARD;
        if($flag){  //是否中奖
            $reward = $oData->name;
            $level = $oData->level;
            $price = $oData->vr_price;
            $reward_id = $oData->id;
        }
        if($flag && $oData->type == 1){
            $status = ActivityMdUser::SENDPRIZESUCCESS;
        }elseif($flag && $oData->type == 2){
            $status = ActivityMdUser::SENDPRIZEFALSE;
        }
        
        if($oUser->is_tester == 1){
            $ip = '127.0.0.1';
        }else{
            $ip = get_client_ip();
        }
        
        $aUser = [
            'user_id' => $userid,
            'user_name' => Session::get('username'),
            'reward' => $reward,
            'vr_price' => $price,
            'hand_name' => $oHand->name,
            'level' => $level,
            'status' => $status,
            'price' => $oData->price,
            'reward_id' => $reward_id,
            'hand_id' => $oHand->id,
            'client_ip' => $ip,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];

        $iId = ActivityMdUser::insertData($aUser);      //插入用户的抽奖数据
        if($iId <= 0){
        //                           $rollBack = true;
        //                         DB::connection()->rollBack();
        //                        Account::unlock($oUser->account_id, $iLocker);
        //                         return ['status' => 0, 'msg' => '网络异常','type' => false];
            return false;
        }
        $oUserData = ActivityMdLeiJi::getAllUserLs($userid);       //更新用户的总的流水和亏损额
        if(isset($oUserData) && $oUserData){
            $id = ActivityMdLeiJi::updateLs($oUserData, $oHand, $price);
            if($id <= 0){
        //                              $rollBack = true;
        //                             DB::connection()->rollBack();
        //                            Account::unlock($oUser->account_id, $iLocker);
        //                            return ['status' => 0, 'msg' => '网络异常','type' => false];
                return false;
            }
        }else{
            $aUserLeiJi = [
                'user_id' => $userid,
                'total_ls' => $oHand->money,
                'total_vr_price' => $price,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            ];

            $iLid = ActivityMdLeiJi::insertData($aUserLeiJi);
            if($iLid <= 0){
        //                             $rollBack = true;
        //                             DB::connection()->rollBack();
        //                            Account::unlock($oUser->account_id, $iLocker);
        //                             return ['status' => 0, 'msg' => '网络异常','type' => false];
                return false;
            }
        }
        //更新礼品数
        if($flag){
            $id = intval($oData->id);
            if($oData->gift_totals != "-99"){
                $iAffect = ActivityMdSetting::updateGiftTotals($id);
                if($iAffect <= 0){
        //                                 $rollBack = true;
        //                                 DB::connection()->rollBack();
        //                                Account::unlock($oUser->account_id, $iLocker);
        //                                return ['status' => 0, 'msg' => '网络异常','type' => false];
                    return false;
                 }
            }
            if($oData->type == 1){
                $aExtraData=['note'=>$oActivity->title];
                $res = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_ACTIVITY_PROMOTION, $oData->price,$aExtraData);
                if($res != -100){
        //                                 $rollBack = true;
        //                                DB::connection()->rollBack();
        //                                Account::unlock($oUser->account_id, $iLocker);
        //                                return ['status' => 0, 'msg' => '网络异常','type' => false];
                    return false;
                }

            }
        }
        return true;
    }
            
    public function __destruct() {
        if ($this->accountLocker){
            Account::unlock(Session::get('account_id'), $this->accountLocker);
        }

        parent::__destruct();
    }
    
    public function historyReward(){
        $userid =  Session::get('user_id');
        $aConditions = ['user_id'=>['=',$userid]];
        $oQuery = $this->model->doWhere($aConditions);
        $aOderSet['id'] = 'desc';
        $oQuery = $this->model->doOrderBy($oQuery, $aOderSet);
        $datas = $oQuery->paginate(static::$pagesize);
        $this->setVars(compact('datas'));
        return $this->render();
    }
    
   
}

