<?php
# 追号
class UserTraceController extends UserBaseController {
    protected $resourceView = 'centerUser.trace';

    protected $modelName = 'UserTrace';

    protected function beforeRender(){
        parent::beforeRender();
        $aValidStatus = Project::$validStatuses;
        $aBoolDesc   = Config::get('var.boolean');
        $this->setVars(compact('aValidStatus', 'aBoolDesc'));
        switch($this->action){
            case 'index':
                $bHasSumRow = 1;
                $aSum = $this->getSumData(['amount', 'prize']);
                $aSelectorData = $this->generateSelectorData();
                $this->setVars(compact( 'bHasSumRow', 'aSum', 'aSelectorData' ));
                break;
            case 'view':
                break;
        }

        $aCoefficients = Config::get('bet.coefficients');
//        $aLotteries = & Lottery::getTitleList();
        $this->setVars(compact('aCoefficients'));
    }
    /**
     * [index 自定义追号记录列表查询, 代理用户需要可以查询其子用户的记录]
     * @return [Response] [description]
     */
    public function index(){
        $this->params = trimArray(Input::except('page', 'sort_up', 'sort_down'));
        $this->params['bought_at_to'] = isset($this->params['bought_at_to']) ? $this->params['bought_at_to'] : date('Y-m-d 23:59:59');
        $this->params['bought_at_from'] = isset($this->params['bought_at_from']) ? $this->params['bought_at_from'] : date('Y-m-d 00:00:00');

        if ($iCount = count($this->params)) $this->generateSearchParams($this->params);
        if (Session::get('is_agent')) {
            $oUser = User::find(Session::get('user_id'));
            $aUsers = $oUser->getUsersBelongsToAgent();
            $aUserIds = array_map(function ($item) {
                return $item['id'];
            }, $aUsers->toArray());
            $aUserIds[] = Session::get('user_id');
            $this->params['user_id'] = implode(',', $aUserIds);
        } else {
            $this->params['user_id'] = Session::get('user_id');
        }
        $this->setVars(['bought_at_to'=>$this->params['bought_at_to'], 'bought_at_from'=>$this->params['bought_at_from'],'projectType'=>'lottery']);
//         pr($this->params);exit;
        return parent::index();
    }
    /**
     * [view 查看追号记录的详情]
     * @param  [Integer] $id [追号记录id]
     * @return [Response]     [description]
     */
    public function view($id)
    {
        $aTraceDetailList = TraceDetail::getListByTraceId($id, 15);
        $this->setVars(compact('aTraceDetailList'));
        return parent::view($id);
    }

    /**
     * 终止追号任务
     * @param int $iTraceId
     * @return Redirect
     */
    public function stop($iTraceId)
    {
        $oTrace = Trace::find($iTraceId);
        if (empty($oTrace)){
            $this->goBack('error',__('_basic.no-data'));
        }
        if ($oTrace->user_id != Session::get('user_id')){
            return Redirect::route('traces.view',$iTraceId)->with('error',__('_trace.stop-failed'));
        }
        if ($oTrace->status != Trace::STATUS_RUNNING){
            return Redirect::route('traces.view',$iTraceId)->with('error',__('_trace.stop-failed-status'));
        }
        $oAccount = Account::lock($oTrace->account_id,$iLocker);
        if (empty($oAccount)){
            return Redirect::route('traces.view',$iTraceId)->with('error',__('_trace.stop-failed'));
        }
        $oUser = User::find($oTrace->user_id);
        $oTrace->setAccount($oAccount);
        $oTrace->setUser($oUser);
        $DB     = DB::connection();
        $DB->beginTransaction();
        if (($iReturn = $oTrace->terminate()) === true){
            $DB->commit();
            $sLangKey = '_trace.stoped';
            $sMsgType = 'success';
        }
        else{
            $DB->rollback();
            $sLangKey = '_trace.stop-failed';
            $sMsgType = 'error';
        }
        Account::unLock($oTrace->account_id,$iLocker,false);
        return Redirect::route('traces.view',$iTraceId)->with($sMsgType,__($sLangKey));
    }

    /**
     * 取消预约
     * @param int $iTraceId
     * @param array $aDetailId
     * @return Redirect
     */
    public function cancel($iTraceId,$aDetailId){
        is_array($aDetailId) or $aDetailId = [$aDetailId];
        $oTrace    = UserTrace::find($iTraceId);
        if ($oTrace->user_id != Session::get('user_id')){
            return Redirect::route('traces.view',$iTraceId)->with('error',__('_trace.stop-failed'));
        }
        $oAccount = Account::lock($oTrace->account_id,$iLocker);
        if (empty($oAccount)){
            return Redirect::route('traces.view',$iTraceId)->with('error',__('_trace.stop-failed'));
        }
        $oUser   = User::find($oTrace->user_id);
        $oTrace->setAccount($oAccount);
        $oTrace->setUser($oUser);
        $DB      = DB::connection();
        $DB->beginTransaction();
        if (($iReturn = $oTrace->cancelDetail($aDetailId)) == Trace::ERRNO_DETAIL_CANCELED){
            $DB->commit();
            $sLangKey = '_trace.detail-canceled';
            $sMsgType = 'success';
        }
        else{
            $DB->rollback();
            $sLangKey = '_trace.detail-not-canceled';
            $sMsgType = 'error';
        }
        Account::unLock($oTrace->account_id,$iLocker,false);
        return Redirect::route('traces.view',$iTraceId)->with($sMsgType,__($sLangKey));
//        return $this->goBack($sMsgType,__($sLangKey));
    }

    private function generateSearchParams(& $aParams)
    {
        if (isset($aParams[ 'number_type' ]) && isset($aParams[ 'number_value' ])){
            $aParams[ $aParams[ 'number_type' ] ] = $aParams[ 'number_value' ];
        }
        unset($aParams['way_group_id'], $aParams['number_type'], $aParams['number_value']);
    }

    private function generateSelectorData()
    {
        $aSelectColumn  = [
            ['name' => 'lottery_id',   'emptyDesc' => '所有游戏', 'desc' => '游戏名称：'],
            ['name' => 'way_group_id', 'emptyDesc' => '所有玩法群', 'desc' => '玩法群：'],
            ['name' => 'way_id',       'emptyDesc' => '所有玩法', 'desc' => '玩法：'],
        ];

        $aSelectorData = [
            'aSelectColumn'   => $aSelectColumn,
            'sFirstNameKey'   => 'name',
            'sSecondNameKey'  => 'title',
            'sThirdNameKey'   => 'title',
            'sDataFile'       => 'series-way-groups-way-group-ways',
            'sExtraDataFile'  => 'lottery-series',
            'sSelectedFirst'  => trim(Input::get('lottery_id')),
            'sSelectedSecond' => trim(Input::get('way_group_id')),
            'sSelectedThird'  => trim(Input::get('way_id')),
        ];
        return $aSelectorData;
    }

}
