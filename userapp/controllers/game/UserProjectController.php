<?php

class UserProjectController extends UserBaseController {

    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];
    protected $resourceView = 'centerUser.bet';
    protected $customViewPath = 'centerGame';
    protected $modelName = 'UserProject';
    protected $projectType = ['lottery','casino','sport'];
    protected $seriesSetTypeId = ['lottery'=>1,'casino'=>2,'sport'=>3];

     public function view($id){
        $mode = Input::get('mode');
        if(!empty($mode) && $mode == 'casino'){
            $this->action='casinoView';
            $this->modelName='BlackJackProjectDetail';
            $this->initModel();
            $data = BlackJackProjectDetail::find($id);
            $oManProject = BlackJackProject::find($data->parent_project_id);

            $this->setVars(compact('data','oManProject'));
            return $this->render();
        }else{
            $sSeriesName = strtolower(Series::find(Lottery::find(Project::find($id)->lottery_id)->series_id)->name);
            $this->setVars(compact('sSeriesName'));
            return parent::view($id);
        }

     }
//

    protected function beforeRender() {
        parent::beforeRender();

        // $aStatusDesc = Project::$validStatuses;

        switch ($this->action) {
            case 'index':
            case 'miniWindow':
            case 'miniWindow4Xy28':
                $bHasSumRow = 1;
                // pr($this->params);exit;
                //$bPeriodTotal = ((isset($this->params['bought_at_from']) && $this->params['bought_at_from']) || (isset($this->params['bought_at_to']) && $this->params['bought_at_to']));
                // $oUserProfit = UserProfit::getUserProfitObject(date('Y-m-d'), Session::get('user_id'));
                // pr($oUserProfit->turnover);exit;
                // $aSum = [
                //     'amount_sum' => $oUserProfit->turnover_formatted, // formatNumber($oUserProfit->turnover + 0,4),
                //     'prize_sum'  => $oUserProfit->prize_formatted, // formatNumber($oUserProfit->prize + 0,4),
                // ];
                //$aSum      = $this->getSumData(['amount' , 'prize']);

                $this->params['status'] = '';
                $aSum = ['valid_amount_sum' => 0, 'amount_sum' => 0, 'prize_sum' => 0, 'commission_sum' => 0];
                if( $this->projectType == 'sport' && isset($this->condition)) {
                    $aProjects = \JcModel\JcUserProject::getList($this->condition);
                    //pr($this->condition);

                    if ($aProjects) {
                        $aProjectIds =array();
                        foreach ($aProjects as $aProject) {

                            $aSum['amount_sum'] += $aProject['amount'];

                            if ($aProject->status != \JcModel\JcProject::STATUS_DROPED && $aProject->status != \JcModel\JcProject::STATUS_DROPED_BY_SYSTEM) {
                                $aSum['prize_sum'] += $aProject['prize'];
                                $aSum['valid_amount_sum'] += $aProject['amount'];
                            }
                            $aProjectIds[] = $aProject->id;
                        }

                        if(!empty($aProjectIds)){
                            $aProjectIds = implode(',',$aProjectIds);
                           // $aProjectIds ='1296,1300';
                            $jcCondition = array(
                                'status'    => array(
                                    '=',
                                    \JcModel\JcProject::COMMISSION_STATUS_SENT,
                                ),
                                'project_id'=> array(
                                    'in',
                                    $aProjectIds
                                ),
                            );
                           // pr($jcCondition);
                            //exit;
                            $commission = \JcModel\JcCommission::doWhere($jcCondition)->get(array('amount'));
                            foreach($commission as $c){
                                $aSum['commission_sum'] += $c->amount;
                            }
                        }

                    }
                } else{

                    $aProjects = $this->indexQuery()->get($this->getColumns);
                    if ($aProjects) {
                        foreach ($aProjects as $aProject) {
                            $aSum['amount_sum'] += $aProject['amount'];

                            if ($aProject->status != Project::STATUS_DROPED && $aProject->status != Project::STATUS_DROPED_BY_SYSTEM) {
                                $aSum['prize_sum'] += $aProject['prize'];
                                $aSum['valid_amount_sum'] += $aProject['amount'];
                            }
                            if ($aProject->status_commission == Project::COMMISSION_STATUS_SENT) {

                                $aSum['commission_sum'] += $aProject->getCommissionAmount();
                            }
                        }
                    }
                }


                $aSum['amount_sum_format'] = number_format($aSum['amount_sum'], 4);
                $aSum['prize_sum_format'] = number_format($aSum['prize_sum'], 4);
                $aSum['valid_amount_sum_format'] = number_format($aSum['valid_amount_sum'], 4);
                $aSum['commission_sum_format'] = number_format($aSum['commission_sum'], 4);

                $aSelectorData = $this->generateSelectorData();
                // $aLotteriesWithSeries = Lottery::getAllLotteries();
                $this->setVars(compact('bHasSumRow', 'aSum', 'aSelectorData'));
                break;
            case 'casinoIndex':
                $bHasSumRow = 1;


                $this->params['status'] = '';
                $aSum = ['valid_amount_sum' => 0, 'amount_sum' => 0, 'prize_sum' => 0];
                $aProjects = $this->indexQuery()->get(['amount','prize','status']);
                if ($aProjects) {
                    foreach ($aProjects as $aProject) {

                        $aSum['amount_sum'] += $aProject['amount'];

                        if ($aProject->status != BlackJackProjectDetail::STATUS_DROPED ) {
                            $aSum['prize_sum'] += $aProject['prize'];
                            $aSum['valid_amount_sum'] += $aProject['amount'];
                        }

                    }
                }
                $aSum['amount_sum_format'] = number_format($aSum['amount_sum'], 4);
                $aSum['prize_sum_format'] = number_format($aSum['prize_sum'], 4);
                $aSum['valid_amount_sum_format'] = number_format($aSum['valid_amount_sum'], 4);

                $this->setVars(['current_tab'=>'single']);


                // $aLotteriesWithSeries = Lottery::getAllLotteries();
                $this->setVars(compact('bHasSumRow', 'aSum', 'aSelectorData'));
                break;
        }

        // $sModelName    = $this->modelName;
        $aCoefficients = Config::get('bet.coefficients');
        $aLotteries    = & Lottery::getTitleList();

        $this->setVars(compact('aLotteries' , 'aCoefficients'));

     //   $this->setVars(compact('aCoefficients'));
        $this->setVars('aValidStatus', UserProject::$validStatuses);
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

    /**
     * [index 投注列表]
     * @return [Response] [description]
     */
    public function index() {

        $this->getColumns=['amount', 'prize', 'commission', 'status_commission', 'status'];
        if(Input::get('mode') == 'single'){
            return $this->casinoIndex();
        }
        $this->params = trimArray(Input::except('page', 'sort_up', 'sort_down'));
        $this->params['bought_at_to'] = isset($this->params['bought_at_to']) ? $this->params['bought_at_to'] : '';
        $this->params['bought_at_from'] = isset($this->params['bought_at_from']) ? $this->params['bought_at_from'] :'';

        if (!isset($this->params['username'])||empty($this->params['username'])) {//没有username直接查询自己的记录
            $sUsername=Session::get('username');
            $this->params['user_id'] = Session::get('user_id');
        }else{
            $sUsername = $this->params['username'];
            $sUser = User::getUserByParams(['username'=>$this->params['username']],array());
            if($sUser) {
                unset($this->params['username']);
                $sUser = $sUser->toArray();
                $this->params['user_id'] = $sUser['id'];
                if($sUser['id']!=Session::get('user_id')){//username不是自己
                    if(empty($sUser['forefather_ids']) || !in_array(Session::get('user_id'),explode(',',$sUser['forefather_ids']))){//username是否为下级
                        $this->params['user_id'] = Session::get('user_id');
                        $sUsername = Session::get('username');
                    }
                }
            }
        }

        if (!empty($this->params['bet_status'])) {
            switch ($this->params['bet_status']) {
                case 1: //有效投注
                    $aBetStatus = Project::$validStatuses;
                    unset($aBetStatus[Project::STATUS_DROPED]);
                    unset($aBetStatus[Project::STATUS_DROPED_BY_SYSTEM]);
                    $aBetStatus = array_keys($aBetStatus);
                    break;
                case 2: //已撤销
                    $aBetStatus = [Project::STATUS_DROPED, Project::STATUS_DROPED_BY_SYSTEM];
                    break;
                case 3: //已中奖
                    $aBetStatus = [Project::STATUS_WON];
                    break;
                case 4: //未中奖
                    $aBetStatus = [Project::STATUS_LOST];
                    break;
            }
            $this->params['status'] = implode(',', $aBetStatus);
        }


        //处理状态
        $statusType = array(0,1,2,3,4);//status4种状态
        $statusStr = array();
        foreach($statusType as $val){
            isset($this->params['status_'.$val]) && ($statusStr[] = $this->params['status_'.$val]);
        }
        if(!empty($statusStr)){
            $statusStr  = implode(',',$statusStr);
            $this->params['status'] = $statusStr;
        }
        $projectType = !empty(Input::get('jc_type'))?Input::get('jc_type'):'lottery';

        if(!in_array($projectType,$this->projectType))
        {
             $projectType = 'lottery';
        }
        $this->projectType = $projectType;


        $has_lottery=false;//  娱乐场casino的lotteryid跟据此变量判断是否重新获取
        if(empty($this->params['lottery_id'])){
            $this->params['series_set_id'] = $this->seriesSetTypeId[$projectType];
            $has_lottery=true;
        }


        //电子娱乐场特殊处理
        if($this->projectType=='casino' && !$has_lottery){
            $this->params['series_id'] = $this->params['lottery_id'];
            unset($this->params['lottery_id']);

        }
        $t = Input::get('sort_up', Input::get('sort_down'));

        $this->setVars(['bought_at_to' => $this->params['bought_at_to'], 'bought_at_from' => $this->params['bought_at_from'],'susername'=>$sUsername,'username'=>$sUsername,'projectType'=>$projectType]);
        if( $this->projectType == 'sport'){
            if(isset($this->params['user_id']))
                $aConditions = ['user_id' => ['=', $this->params['user_id']]];
            else
                $aConditions = ['username'=>['=',$sUsername]];
            if(!$has_lottery)
                $aConditions['lottery_id'] = ['=', $this->params['lottery_id']];
            $this->getColumns=['amount', 'prize','status_commission', 'status'];
            if(!empty($this->params['bought_at_from']) && !empty($this->params['bought_at_to']))
                $aConditions['created_at']=['between',[$this->params['bought_at_from'],$this->params['bought_at_to']]];
            else
                if(!empty($this->params['bought_at_from']))
                    $aConditions['created_at'] = ['between',[$this->params['bought_at_from'],date('Y-m-d').' 23:59:59']];
                else
                    $aConditions['created_at'] = ['between',[date('Y-m-d').' 00:00:00',$this->params['bought_at_to']]];
            !empty($this->params['serial_number']) && $aConditions['serial_number']=['=',$this->params['serial_number']];
            !empty($this->params['way_group_id']) && $aConditions['method_group_id']=['in',[$this->params['way_group_id']]];
            !empty($this->params['type']) && $aConditions['type'] = array(0=>'=',1=>$this->params['type']);
            isset($this->params['status']) && $aConditions['status'] = array(0=>'in',1=>$this->params['status']);
           // var_dump(isset($this->params['status']));
            $this->condition = $aConditions;
            //pr($aConditions);
            $selectType = isset($this->params['type']) ? $this->params['type'] : 0;
            //$data = \JcModel\JcFindProject::getList($aConditions);
            $datas = \JcModel\JcUserProject::getList($aConditions);
            $this->setVars(['sportStatus'=>array(0=>'正常',1=>'已撤销',2=>'未中奖',3=>'已中奖',4=>'已中奖',5=>'已撤单',8=>'已取消',)]);
            $this->setVars(['sportType' =>array(1=>'自购',2=>'合买',3=>'合买跟单')]);
            $methodData = \JcModel\JcMethodGroup::all();
            $methods=array();
            foreach($methodData as $d) {
                $sData= $d->getAttributes();
                $methods[$d['id']] = $d['name'];
            }
            //pr($aConditions);
            $this->setVars(['methods'=>$methods,'type' =>$selectType]);
            $this->setVars(
                compact(
                    'datas'
                )
            );
            return $this->render();
        } else{

            return parent::index();
        }
    }
    public function casinoIndex(){
        $this->params = trimArray(Input::except('page', 'sort_up', 'sort_down'));
        $this->params['bought_at_to'] = isset($this->params['bought_at_to']) ? $this->params['bought_at_to'] : '';
        $this->params['bought_at_from'] = isset($this->params['bought_at_from']) ? $this->params['bought_at_from'] :'';
        $statusType = array(0,1,2,3,4);//status4种状态
        $statusStr = array();
        foreach($statusType as $val){
            isset($this->params['status_'.$val]) && ($statusStr[] = $this->params['status_'.$val]);
        }
        if(!empty($statusStr)){
            $statusStr  = implode(',',$statusStr);
            $this->params['status'] = $statusStr;
        }
        if (!isset($this->params['username'])||empty($this->params['username'])) {//没有username直接查询自己的记录
            $sUsername=Session::get('username');
            $this->params['user_id'] = Session::get('user_id');
        }else{
            $sUsername = $this->params['username'];
            $sUser = User::getUserByParams(['username'=>$this->params['username']],array());
            if($sUser) {
                unset($this->params['username']);
                $sUser = $sUser->toArray();
                $this->params['user_id'] = $sUser['id'];
                if($sUser['id']!=Session::get('user_id')){//username不是自己
                    if(empty($sUser['forefather_ids']) || !in_array(Session::get('user_id'),explode(',',$sUser['forefather_ids']))){//username是否为下级
                        $this->params['user_id'] = Session::get('user_id');
                        $sUsername = Session::get('username');
                    }
                }
            }
        }
        $this->setVars(['bought_at_to' => $this->params['bought_at_to'], 'bought_at_from' => $this->params['bought_at_from'],'susername'=>$sUsername,'username'=>$sUsername,'projectType'=>'casino']);
        $this->action='casinoIndex';
        $this->modelName='BlackJackProjectDetail';
        $this->initModel();

        return parent::index();
    }

    /**
     * [miniWindow 投注列表mini窗口，用于彩票投注页面]
     * @return [Response] [description]
     */
    public function miniWindow() {
        return $this->index();
    }

    /**
     * [miniWindow4Xy28 投注列表mini窗口，用于彩票投注页面]
     * @return [Response] [description]
     */
    public function miniWindow4Xy28() {
        return $this->index();
    }

    /**
     * [generateSearchParams 生成自定义查询参数]
     * @param  [Array]     & $aParams [查询参数数组的引用]
     */
    private function generateSearchParams(& $aParams) {
        if (isset($aParams['number_value']) && $aParams['number_value']) {
            $aParams[$aParams['number_type']] = $aParams['number_value'];
        }
        unset( $aParams['number_type'], $aParams['number_value']);
    }

    /**
     * 撤单
     * @param int $id
     * @return Redirect
     */
    function drop($id, $bRedirect = true) {
        $oProject = UserProject::find($id);
        $Redirect = Redirect::route('projects.view', ['id' => $oProject->id]);
        $oMessage = new Message($this->errorFiles);
        if (empty($oProject)) {
            if($bRedirect)
                return $Redirect->with('error', $oMessage->getResponseMsg(Project::ERRNO_PROJECT_MISSING));
            else
                return $this->halt(false, 'issue-error', Project::ERRNO_PROJECT_MISSING);
        }
        if (Session::get('user_id') != $oProject->user_id) {
            if($bRedirect)
                return $Redirect->with('error', $oMessage->getResponseMsg(Project::ERRNO_DROP_ERROR_NOT_YOURS));
            else
                return $this->halt(false, 'issue-error', Project::ERRNO_DROP_ERROR_NOT_YOURS);
        }
        $oAccount = Account::lock($oProject->account_id, $iLocker);
        if (empty($oAccount)) {
            if($bRedirect)
                return $Redirect->with('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
            else
                return $this->halt(false, 'issue-error', Account::ERRNO_LOCK_FAILED);
        }
        DB::connection()->beginTransaction();
        $this->writeLog('begin DB Transaction');
        if (($iReturn = $oProject->drop()) != Project::ERRNO_DROP_SUCCESS) {
            $this->writeLog($iReturn);
            DB::connection()->rollback();
            $this->writeLog('Rollback');
            Account::unLock($oAccount->id, $iLocker, false);
            if($bRedirect)
                return $Redirect->with('error', $oMessage->getResponseMsg($iReturn));
            else
                return $this->halt(false, 'issue-error', $iReturn);
            exit;
        }
        DB::connection()->commit();
        $this->writeLog('Commit');
        $oProject->addTurnoverStatTask(false); 
        Account::unLock($oAccount->id, $iLocker, false);
        if($bRedirect)
            return Redirect::route('projects.view', ['id' => $oProject->id])->with('success', __('_project.droped'));
        else
            return $this->halt(true, 'success', Project::ERRNO_DROP_SUCCESS);
    }

    /**
     * 撤消多个注单
     * @param int $id
     * @return Redirect
     */
    function dropMultiProjects() {
        $iSucc = true;
        if (Request::method() == 'POST') {

            $aDatas = Input::all();
            $oIssue = Issue::getOnSaleIssue($aDatas['lottery_id']);
            if($oIssue->issue != $aDatas['issue']){
                $iSucc = false;
            }
            if(empty($aDatas['project_ids'])){
                $iSucc = false;
            }
            $aProjects = UserProject::getProjectsByIds($aDatas['project_ids']);

            foreach ($aProjects as $oProject) {
                if (empty($oProject)) {
                    $iSucc = false;
                }
                if (Session::get('user_id') != $oProject->user_id) {
                    $iSucc = false;
                }
                DB::connection()->beginTransaction();
                $oAccount = Account::lock($oProject->account_id, $iLocker);
                if (empty($oAccount)) {
                    $iSucc = false;
                }
                $this->writeLog('begin DB Transaction');
                if (($iReturn = $oProject->drop()) != Project::ERRNO_DROP_SUCCESS) {
                    $this->writeLog($iReturn);
                    DB::connection()->rollback();
                    $this->writeLog('Rollback');
                    Account::unLock($oAccount->id, $iLocker, false);
                    $iSucc = false;
                }
                DB::connection()->commit();
                $this->writeLog('Commit');
                $oProject->setCommited();    // 建立销售量更新任务
                Account::unLock($oAccount->id, $iLocker, false);
            }
        }
        if($iSucc){
            $this->halt(true, 'success', Project::ERRNO_DROP_SUCCESS);
        }else{
            $this->halt(false, 'issue-error', Project::ERRNO_DROP_ERROR_STATUS_UPDATE_ERROR);
        }
    }



}
