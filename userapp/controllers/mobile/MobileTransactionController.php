<?php

class MobileTransactionController extends MobileBaseController {

    protected $modelName = 'UserTransaction';

    protected function beforeRender() {
        parent::beforeRender();

        $aCoefficients = Config::get('bet.coefficients');
//        $aLotteries    = & Lottery::getTitleList();
        $aSeriesWays = & SeriesWay::getTitleList(); // TODO
        switch ($this->action) {
            case 'index':
                $this->setVars('reportName', 'transaction');
                break;
            case 'myDeposit':
                $this->action = 'index';
                $this->setVars('reportName', 'deposit');
                $this->setVars('depositTransactionType', self::$aTransactionTypeMyDeposit);
                break;
            case 'myWithdraw':
                $this->action = 'index';
                $this->setVars('reportName', 'withdraw');
                $this->setVars('depositTransactionType', self::$aTransactionTypeMyWithdraw);
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
        $this->setVars(compact('aCoefficients', 'aSeriesWays', 'aTransactionTypes', 'aSelectorData'));
    }

    /**
     * [index 自定义资金列表查询, 代理用户需要可以查询其子用户的记录]
     * @return [Response] [description]
     */
    public function index($iUserId = null) {
        if ($iCount = count($this->params))
            $this->generateSearchParams($this->params);
        if ($iUserId) {
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
        $data = parent::mobileIndex(Transaction::$mobileColumns);
        $data['transaction_type'] = TransactionType::getAllTransactionTypes()->toArray();
        $this->halt(true, 'info', null, $a, $a, $data);
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

}
