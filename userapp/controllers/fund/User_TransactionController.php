<?php

class User_TransactionController extends UserBaseController {

    protected $resourceView = 'centerUser.transaction';
    protected $modelName = 'UserTransaction';
    private static $aTransactionTypeMyDeposit = [TransactionType::TYPE_DEPOSIT, TransactionType::TYPE_DEPOSIT_BY_ADMIN];
    private static $aTransactionTypeMyWithdraw = [TransactionType::TYPE_WITHDRAW, TransactionType::TYPE_WITHDRAW_BY_ADMIN];
    private static $aTransactionTypeMyTransfer = [TransactionType::TYPE_TRANSFER_IN, TransactionType::TYPE_TRANSFER_OUT];
    private static $aTransactionTypeMyCommission = [TransactionType::TYPE_TURNOVER_COMMISSION, TransactionType::TYPE_PROFIT_COMMISSION];

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('related_user', false);
        $aCoefficients = Config::get('bet.coefficients');
        $aLotteries    = & Lottery::getTitleList();
        $aSeriesWays = & SeriesWay::getTitleList(); // TODO
        switch ($this->action) {
            case 'index':
                $this->setVars('reportName', 'transaction');
                break;
            case 'myDeposit':
                $this->resourceView = "centerUser.mydeposit";
                $this->action = 'index';
                $this->setVars('reportName', 'deposit');
                $this->setVars('depositTransactionType', self::$aTransactionTypeMyDeposit);
                break;
            case 'myWithdraw':
                $this->resourceView = "centerUser.mywithdraw";
                $this->action = 'index';
                $aTansanctiongType = TransactionType::getFieldsOfAllTransactionTypesArray();
                $this->setVars('aTansanctiongType', $aTansanctiongType);
                $this->setVars('reportName', 'withdraw');
                $this->setVars('depositTransactionType', self::$aTransactionTypeMyWithdraw);
                break;
            case 'myTransfer':
                $this->action = 'index';
                $this->setVars('reportName', 'transfer');
                $this->setVars('related_user', true);
                $this->setVars('depositTransactionType', self::$aTransactionTypeMyTransfer);
                break;
            case 'myCommission':
                $this->action = 'index';
                $this->resourceView = "centerUser.mycommission";
                $this->setVars('reportName', 'commission');
                $this->setVars('depositTransactionType', self::$aTransactionTypeMyCommission);
                break;
            case 'view':
                // $bHasSumRow = 1;
                // $aNeedSumColumns = ['amount', 'transaction_charge', 'transaction_amount'];
                // $aSum = $this->getColumnSum($aNeedSumColumns);
//                $aSum = $this->getSumData(['amount'], true);
                break;
        }
        $aTransactionTypes = TransactionType::getAllTransactionTypes();
        $aSelectorData = $this->generateSelectorData();

        // pr($aTransactionTypes);exit;
        $this->setVars(compact('aCoefficients', 'aSeriesWays', 'aTransactionTypes', 'aSelectorData','aLotteries'));
    }

    /**
     * [index 自定义资金列表查询, 代理用户需要可以查询其子用户的记录]
     * @return [Response] [description]
     */
    public function index($iUserId = null) {
        $this->params = trimArray(Input::except('page', 'sort_up', 'sort_down'));
        if ($iCount = count($this->params))
            $this->generateSearchParams($this->params);
        if ($iUserId) {
        	$oAgent = User::find(Session::get('user_id'));
        	if (!$oAgent->checkUserBelongsToAgent($iUserId)) {
            	return Redirect::route('users.index')->with('error', '操作不合法！');
        	}
            $this->params['user_id'] = $iUserId;
            $sJumpUsername = User::find($iUserId)->username;
            $this->setVars('sJumpUsername', $sJumpUsername);
        } else {
            $iLoginUserId = Session::get('user_id');
            // 如果是代理并且有username参数，则精准查找该代理下用户名为输入参数的子用户的账变列表
            // 否则，查询该代理的账变列表
            if (Session::get('is_agent') && isset($this->params['username']) && $this->params['username']) {
                // $oUser = User::find($iLoginUserId);
                // $aUsers   = $oUser->getUsersBelongsToAgent();
                // $aUserIds = array_map(function ($item){
                //     return $item['id'];
                // } , $aUsers->toArray());
                // $this->params['user_id'] = implode(',' , $aUserIds);
                //
                $oUser = User::getUserByParams(['username' => $this->params['username'], 'forefather_ids' => $iLoginUserId], ['forefather_ids']);
                // $queries = DB::getQueryLog();
                // $last_query = end($queries);
                // pr($last_query);exit;
                if ($oUser) {
                    $this->params['user_id'] = $oUser->id;
                } else {
                    $aReplace = ['username' => $this->params['username']];
                    return $this->goBack('error', __('_basic.not-your-user', $aReplace));
                }
            } else {
                $this->params['user_id'] = $iLoginUserId;
            }
            // $this->params['user_id'] = Session::get('user_id');
        }
//         pr($this->params);exit;
        $sDataPath = Config::get('widget.data_path');
        $sPath     = realpath($sDataPath) . '/';
        $lotteryMap =file_get_contents($sPath.'lotterymap.blade.php');
        $lotteryMap=json_decode($lotteryMap,true);




        if(isset($this->params['lottery_id'])){
            $realLotteryIds=array();
            $lottersBySer = Lottery::getAllLotteryIdsGroupBySeries();
            if($lottersBySer){
                foreach($lottersBySer as $key => $val){
                    if($val == $this->params['lottery_id']){
                        $realLotteryIds[] = $key;
                    }
                }
            }
            $realLotteryIds = array_intersect($realLotteryIds,$lotteryMap['casino']);//lottery_id 属于casino
            if(!empty($realLotteryIds)){
                $realLotteryIds = implode(',', $realLotteryIds);
                $this->params['lottery_id'] = $realLotteryIds;
            }else{
                $sportIds = array_intersect(array($this->params['lottery_id']),$lotteryMap['sport']);//lottery_id 属于sport
                if($sportIds){
                    $this->params['way_id']=Input::get('way_group_id');
                }

            }
        }
        $this->setVars(['sportIds'=>implode(',',$lotteryMap['sport'])]);
        return parent::index();
    }

    /**
     * [generateSearchParams 生成自定义查询参数]
     * @param  [Array]     & $aParams [查询参数数组的引用]
     */
    private function generateSearchParams(& $aParams) {
        if (isset($aParams['number_type']) && isset($aParams['number_value'])) {
            $aParams[$aParams['number_type']] = $aParams['number_value'];
        }
        unset($aParams['way_group_id'], $aParams['number_type'], $aParams['number_value']);
    }

    /**
     * [generateSelectorData 页面公用下拉框的生成参数]
     * @return [Array] [参数数组]
     */
    private function generateSelectorData() {
        $aSelectColumn = [
            ['name' => 'lottery_id', 'emptyDesc' => '所有游戏', 'desc' => '游戏名称：'],
            ['name' => 'way_group_id', 'emptyDesc' => '所有玩法群', 'desc' => '玩法群：'],
            ['name' => 'way_id', 'emptyDesc' => '所有玩法', 'desc' => '玩法：'],
        ];

        $aSelectorData = [
            'aSelectColumn' => $aSelectColumn,
            'sFirstNameKey' => 'name',
            'sSecondNameKey' => 'title',
            'sThirdNameKey' => 'title',
            'sDataFile' => 'series-way-groups-way-group-ways',
            'sExtraDataFile' => 'lottery-series',
            'sSelectedFirst' => trim(Input::get('lottery_id')),
            'sSelectedSecond' => trim(Input::get('way_group_id')),
            'sSelectedThird' => trim(Input::get('way_id')),
        ];
        return $aSelectorData;
    }

    public function myDeposit($iUserId = null) {
        if (empty($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyDeposit);
        }
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    public function myWithdraw($iUserId = null) {
        if (empty($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyWithdraw);
        }
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    public function myCommission($iUserId = null) {

        if (empty($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyCommission);
        }
        $this->params['user_id'] = Session::get('user_id');
        $created_at_from = '';
        $created_at_to = '';
        if (!isset($this->params['created_at_from']) && !isset($this->params['created_at_to'])) {
            $oCarbonNow = Carbon::now();
            $created_at_to = $oCarbonNow->toDateString();
            $created_at_from = $oCarbonNow->startOfMonth()->toDateString();
            $this->params['created_at_to'] = $created_at_to;
            $this->params['created_at_from'] = $created_at_from;
        }
        $this->setVars(compact('created_at_from', 'created_at_to'));
        return parent::index();
    }

    /**
     * 我的转账
     * @param null $iUserId
     * @return Response
     */
    public function myTransfer() {
        //  $this->resourceView = 'centerUser.transaction.mytransfer';
        $this->params['user_id'] = Session::get('user_id');
        if (empty($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyTransfer);
        }
        if (Session::get('is_agent') && !empty($this->params['username']) && $this->params['username'] != Session::get('username')) {
            $oUser = User::findUser($this->params['username']);
            $oSelf = User::find(Session::get('user_id'));
            if (!$oUser || ($oUser->parent_id != Session::get('user_id') && $oSelf->parent_id != $oUser->id ))
                return Redirect::route('user-transactions.mytransfer', Session::get('user_id'))->withInput()->with('error', '此用户不是你的直属上/下级。');
            if($oSelf->forefather_ids && in_array($oUser->id,explode(',',$oSelf->forefather_ids)))
                return Redirect::route('user-transactions.mytransfer', Session::get('user_id'))->withInput()->with('error', '您无权查看上级转账记录。');
            $this->params['user_id'] = $oUser->id;
//            $this->params['related_user_name'] = $this->params['username'];
//            unset($this->params['username']);
//            unset($this->params['user_id']);
        }
//        $this->model->switchTable();


        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;
        $datas = $oQuery->paginate(static::$pagesize);

        foreach ($datas as $data) {
            $oRelatedUser = TransactionsRelatedUser::find($data->id);
            if ($oRelatedUser) {
                if ($data->type_id == TransactionType::TYPE_TRANSFER_IN){
                    $data->from_user_name = $oRelatedUser->related_user_name;
                    $data->to_user_name = $data->username;
                }else{
                    $data->from_user_name = $data->username;
                    $data->to_user_name = $oRelatedUser->related_user_name;
                }
            }
        }

        $this->setVars(compact('datas'));

        return $this->render();
    }

}
