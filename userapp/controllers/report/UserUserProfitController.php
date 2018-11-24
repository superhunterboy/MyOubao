<?php

# 用户盈亏报表管理

class UserUserProfitController extends UserBaseController {

    protected $resourceView = 'centerUser.profit';
    protected $modelName = 'UserUserProfit';
    public $resourceName = 'user-profits';

    /**
     * 初始化模型实例及语言包等
     */
    protected function initModel() {
        $iType = Input::get('type') ? Input::get('type') : 0;
        switch($iType){
            case 0 : $this->modelName = 'UserUserProfit';break;
            case 1 : $this->modelName = 'UserUserSportProfit';break;
            case 2 : $this->modelName = 'UserUserSlotProfit';break;
        }
        parent::initModel();
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->setVars('resource', 'user-profits');
        $this->setVars('action', strtolower($this->action));
        $iType = Input::get('type') ? Input::get('type') : 0;
        $this->setVars('type', $iType);
        switch ($this->action) {
            case 'index':
            case 'withdraw-deposit':
                $aUserTypes = User::$aUserTypes;
                $this->setVars(compact('aUserTypes'));
                $this->setVars('reportName', 'profit');
                break;
            case 'commission':
                $this->setVars('reportName', 'commission');
                break;
        }
    }

    /**
     * [index 查询用户的盈亏报表]
     * @return [Response]          [description]
     */
    public function index() {
        $iType = Input::get('type') ? Input::get('type') : 0;
        switch($iType){
            case 0 : $sModel = 'UserUserProfit';break;
            case 1 : $sModel = 'UserUserSportProfit';break;
            case 2 : $sModel = 'UserUserSlotProfit';break;
        }

        $iUserId = Session::get('user_id');
        if (!$iUserId)
            return $this->goBack('error', __('_basic.no-rights'));


        $this->params['parent_user_id'] = $iUserId;

        $dateTo = $this->params['date_to'] = isset($this->params['date_to']) ? $this->params['date_to'] : date('Y-m-d');
        $dateFrom = $this->params['date_from'] = isset($this->params['date_from']) ? $this->params['date_from'] : date('Y-m-01');
        $sUsername = isset($this->params['username']) ? e($this->params['username']) : NULL;
        $aOrderSet=array();
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet = array($sOorderColumn,$sDirection);
        }

        $datas = $sModel::getProfitsSum($iUserId,$sUsername,$dateFrom,$dateTo,static::$pagesize,$aOrderSet);
        $total_group_buy_commission = 0;
        foreach($datas as $data){
            $total_group_buy_commission += $data->group_buy_commission;
        }

        //区间统计
        if (!$sUsername) {

            if(isset($this->params['is_agent']) && $this->params['is_agent'] == 1){//代理
                $oAgentSumPerDay = $sModel::queryTotal($this->indexQuery());
            }
            elseif(isset($this->params['is_agent']) && $this->params['is_agent'] == 2){//自己
                $oSelfProfit = $sModel:: queryByUserId($iUserId, $dateTo, $dateFrom);
            }
            else{
                $oAgentSumPerDay = $sModel::queryTotal($this->indexQuery());
                $oSelfProfit = $sModel:: queryByUserId($iUserId, $dateTo, $dateFrom);
                //团队统计=区间+自己
//                $oAgentSumPerDay->team_deposit = $oAgentSumPerDay->team_deposit + $oSelfProfit->deposit;
//                $oAgentSumPerDay->team_withdrawal = $oAgentSumPerDay->team_withdrawal + $oSelfProfit->withdrawal;
                $oAgentSumPerDay->team_turnover = $oAgentSumPerDay->team_turnover + $oSelfProfit->turnover;
                $oAgentSumPerDay->team_prize = $oAgentSumPerDay->team_prize + $oSelfProfit->prize;
                $oAgentSumPerDay->team_profit = $oAgentSumPerDay->team_profit + $oSelfProfit->profit;

                $oAgentSumPerDay->team_commission = $oAgentSumPerDay->team_commission + $oSelfProfit->commission;


                $oAgentSumPerDay->team_dividend = $oAgentSumPerDay->team_dividend + $oSelfProfit->dividend;
                $oAgentSumPerDay->team_bet_commission = $oAgentSumPerDay->team_bet_commission + $oSelfProfit->bet_commission;
                $oSelfProfit->group_buy_commission += $total_group_buy_commission;
                $oAgentSumPerDay->group_buy_commission = $oAgentSumPerDay->team_group_buy_commission + $oSelfProfit->group_buy_commission;
            }

            $this->setVars(compact('oSelfProfit', 'oAgentSumPerDay'));
        }
        //团队

        $this->setVars(compact('datas', 'dateTo', 'dateFrom'));
        $this->setVars('type',$iType);
        return $this->render();
    }

    public function commission() {
        return $this->index();
    }

    // public function create($id = null)
    // {
    //     if ( ! $bIsAgent = Session::get('is_agent')) {
    //         return $this->goBack('error', __('_basic.no-rights', $this->langVars));
    //     }
    //     return parent::create($id);
    // }




    public function bonus()
    {
        $iType = Input::get('type') ? Input::get('type') : 0;
        $sModel = '';
        switch($iType){
            case 0 : $sModel = 'UserUserProfit';break;
            case 1 : $sModel = 'UserUserSportProfit';break;
            case 2 : $sModel = 'UserUserSlotProfit';break;
        }
        $this->params['user_id'] = Session::get('user_id');

        //只有总代有分红
        if((!$oUser = User::find($this->params['user_id'])) || $oUser->parent_id){
            return false;
        }
        $iBonusStartDate = '2016-01-01'; //以前的忽略不计
        $iCurDate = date('Y-m-d');

        $aAreaDates = $datas = [];
        $iStartDate = $iBonusStartDate;

        //半个月统计一次
        while ($iStartDate <= $iCurDate) {
            $iEndDate = date('d', strtotime($iStartDate)) < 16 ? date('Y-m-15', strtotime($iStartDate)) : date('Y-m-t', strtotime($iStartDate));
            $aAreaDates[] = [$iStartDate, $iEndDate];
            $iStartDate = date('Y-m-d', strtotime("$iEndDate +1 day"));
        }
        $aAreaDates = array_reverse($aAreaDates);
        if(count($aAreaDates) > static::$pagesize){
            $aAreaDates = array_slice($aAreaDates, 0, static::$pagesize);
        }
        $datas = [];
        $aTeamProfitSet = [];
        $oBonusRule = new BonusRule;

        foreach ($aAreaDates as $aDates)
        {
            $this->params['date_from'] = $aDates[0];
            $this->params['date_to'] = $aDates[1];

            $oTeamProfit = $sModel::queryTotal($this->indexQuery());
            $oTeamProfit->team_bonus_month = date('Y年m月d日', strtotime($aDates[0])) . '-' . date('d日', strtotime($aDates[1]));

            $oTeamProfit->date = $aDates[0];
            $dDateYM = date('Ym', strtotime($aDates[0]));

//            if ($aDates[0] == '2016-04-01'){
//                $oTeamProfit->team_profit = 100;
//                $oTeamProfit->team_turnover = 10500000;
//                $oUser->prize_group = 1960;
//            }
//            if ($aDates[0] == '2016-02-16'){
//                $oTeamProfit->team_profit = -850;
//            }

            //团队分红（如果上半月代理赚钱，则下半月净盈亏包含上半月，反之）
//            $fTeamProfit = $oTeamProfit->team_profit + $firstHalfMonthProfit;
//            $fTeamProfit = $oTeamProfit->team_profit;

            $bIsFirstHalfMonth = date('d',strtotime($aDates[0])) <= 15 ? true : false;

            if ($bIsFirstHalfMonth){
                $aTeamProfitSet[$dDateYM][0] = $oTeamProfit->team_profit;
            }else{
                $aTeamProfitSet[$dDateYM][1] = $oTeamProfit->team_profit;
            }

            //if($firstHalfMonth){
//                $iBonusRate = $oBonusRule->getRate($oTeamProfit->team_turnover, $fTeamProfit, $oUser->prize_group, $iType);
//                $iBonusRate or $iBonusRate = $oBonusRule->getDeficitRate($oUser->prize_group, $fTeamProfit, $iType);
            //}else{
            //    $iBonusRate = $oBonusRule->getDeficitRate($oUser->prize_group, $fTeamProfit, $iType);
            //}
//            $oTeamProfit->team_bonus_rate = $iBonusRate;
//            $oTeamProfit->team_bonus = abs($fTeamProfit * $iBonusRate / 100);

            $datas[] = $oTeamProfit;
        }

        foreach($datas as $oTeamProfit){
            $iUnixDateTime = strtotime($oTeamProfit->date);
            $dDateDay = date('d', $iUnixDateTime);
            if ($dDateDay < 16){
                $fTeamProfit = $oTeamProfit->team_profit;

                //上半月不显示累计盈亏
                $oTeamProfit->team_accumulation_profit = '--';
            }else{
                $dDateYM = date('Ym', $iUnixDateTime);
                $aTeamProfitData = $aTeamProfitSet[$dDateYM];
                $fTeamAccumulationProfit = 0;
                if (isset($aTeamProfitData[0]) && $aTeamProfitData[0] > 0 && $aTeamProfitData[1] <= 0){
                    //若上半月盈利下半月亏损,需扣除上半月盈利额
                    $fTeamAccumulationProfit = $aTeamProfitData[0] + $aTeamProfitData[1];
                }else{
                    //上半月盈利下半月盈利 取下半月盈利额
                    //上半月亏损下半月盈利 取下半月盈利额
                    //上半月亏损下半月亏损 取下半月盈利额
                    $fTeamAccumulationProfit = $aTeamProfitData[1];
                }
                $fTeamProfit = $fTeamAccumulationProfit;
                $oTeamProfit->team_accumulation_profit = number_format($fTeamAccumulationProfit, 4);
            }
            $iBonusRate = $oBonusRule->getRate($oTeamProfit->team_turnover, $fTeamProfit, $oUser->prize_group, $iType);

            if(!$iBonusRate){
                $iBonusRate = $oBonusRule->getDeficitRate($oUser->prize_group, $fTeamProfit, $iType);
            }

            $oTeamProfit->team_bonus_rate = $iBonusRate;
            $oTeamProfit->team_bonus = abs($fTeamProfit * $iBonusRate / 100);

        }
        $this->setVars(compact('datas'));

        return $this->render();
    }
    /**
     * [withdraw_deposit 团队充提]
     * @return [Response]          [description]
     */
    public function withdraw_deposit() {
        $iUserId = Session::get('user_id');
        if (!$iUserId)
            return $this->goBack('error', __('_basic.no-rights'));

        if (Session::get('is_agent')) {
            $this->params['parent_user_id'] = $iUserId;
        } else {
            $this->params['user_id'] = $iUserId;
        }
        $dateTo = $this->params['date_to'] = isset($this->params['date_to']) ? $this->params['date_to'] : date('Y-m-d');
        $dateFrom = $this->params['date_from'] = isset($this->params['date_from']) ? $this->params['date_from'] : date('Y-m-01');
        $sUsername = isset($this->params['username']) ? e($this->params['username']) : NULL;


        //区间统计
        if (!$sUsername) {

            if(isset($this->params['is_agent']) && $this->params['is_agent'] == 1){//代理
                $oAgentSumPerDay = UserUserProfit::queryTotal($this->indexQuery());
            }
            elseif(isset($this->params['is_agent']) && $this->params['is_agent'] == 2){//自己
                $oSelfProfit = UserUserProfit:: queryByUserId($iUserId, $dateTo, $dateFrom);
            }
            else{
                $oAgentSumPerDay = UserUserProfit::queryTotal($this->indexQuery());
                $oSelfProfit = UserUserProfit:: queryByUserId($iUserId, $dateTo, $dateFrom);
                //团队统计=区间+自己
                $oAgentSumPerDay->team_deposit = $oAgentSumPerDay->team_deposit + $oSelfProfit->deposit;
                $oAgentSumPerDay->team_withdrawal = $oAgentSumPerDay->team_withdrawal + $oSelfProfit->withdrawal;
                $oAgentSumPerDay->team_numbers = $oAgentSumPerDay->team_numbers + $oSelfProfit->team_numbers;
//                $oAgentSumPerDay->team_turnover = $oAgentSumPerDay->team_turnover + $oSelfProfit->turnover;
//                $oAgentSumPerDay->team_prize = $oAgentSumPerDay->team_prize + $oSelfProfit->prize;
//                $oAgentSumPerDay->team_profit = $oAgentSumPerDay->team_profit + $oSelfProfit->profit;
//                $oAgentSumPerDay->team_commission = $oAgentSumPerDay->team_commission + $oSelfProfit->commission;
//                $oAgentSumPerDay->team_dividend = $oAgentSumPerDay->team_dividend + $oSelfProfit->dividend;
//                $oAgentSumPerDay->team_bet_commission = $oAgentSumPerDay->team_bet_commission + $oSelfProfit->bet_commission;
            }

            $this->setVars(compact('oSelfProfit', 'oAgentSumPerDay'));
        }
        //团队
        $datas = UserUserProfit::compileUserTotal($this->indexQuery(), static::$pagesize);
        $this->setVars(compact('datas', 'dateTo', 'dateFrom'));
        return $this->render();
    }
}
