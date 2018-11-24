<?php

/**
 * 用户管理(代理用户才有)
 */
class UserUserController extends UserBaseController {

    protected $resourceView = 'centerUser.user';
    protected $modelName = 'UserUser';

    protected function beforeRender() {
        parent::beforeRender();

        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        switch ($this->action) {
            case 'index':

                // TODO 如果是二代，则需要递归计算他下面的用户
                //  $this->generateData();
                break;

            case 'personal':

            case 'bindEmail':
                $data = $oUser;
                $this->setVars(compact('data'));
                break;

            case 'passwordManagement':
                $sFundPassword = $oUser->fund_password;

                $bFundPasswordSetted = (int) ($sFundPassword != null);

                $this->setVars(compact('bFundPasswordSetted'));

            case 'accurateCreate':
                $aUserPrizeGroupRange   = $oUser->generatePrizeGroupSetData();
                $datas = UserCommissionSet::getUserSeriesCommissionSets($iUserId);

                foreach($datas as $key => $data){
                    //todo 暂时
                    if($data->series_set_id == SeriesSet::ID_FOOTBALL_MIX){
                        $datas[$key]->commission_rate = min(8, $data->commission_rate);
                    }
                    $datas[$key]->name = SeriesSet::find($data->series_set_id)->name;
                }

                $this->setVars(compact('datas'));
                $this->setVars($aUserPrizeGroupRange);

                break;
        }
    }

    /**
     * [getLoginUserMonetaryInfo ajax方式请求用户/代理可用余额，代理昨日销售额]
     * @return [Json] [用户/代理可用余额，代理昨日销售额]
     */
    public function getLoginUserMonetaryInfo() {
        $data = [];
        $iUserId = Session::get('user_id');
        if (Session::get('is_agent')) {
            $sDate = Carbon::yesterday()->toDateString();
            $oUserProfit = UserProfit::getUserProfitObject($sDate, $iUserId);
            $fTeamTurnOver = formatNumber($oUserProfit->team_turnover + 0, 1);
            $data['team_turnover'] = $fTeamTurnOver;
        }
        $fAvailable = formatNumber(Account::getAvaliable($iUserId), 1);
        $data['available'] = $fAvailable;
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
    public function index() {
        // TIP 已经在路由中过滤只有代理能访问
        // if (!Session::get('is_agent')) {
        //     return $this->goBack('error', __('_basic.agent-only', $this->langVars));
        // }
if (!isset($this->params['parent_id'])&&(!isset($this->params['username'])||empty($this->params['username']))) {
            $this->params['parent_id'] = Session::get('user_id');
            $this->setVars('old_params',  ['parent_id' => Session::get('user_id')]);
        } else if(!isset($this->params['parent_id'])){
            $this->setVars('old_params',  ['parent_id' => Session::get('user_id')]);
        }else{
            $this->setVars('old_params', ['parent_id' => $this->params['parent_id']]);
        }

        if (isset($this->params['reg_date_from']) && $this->params['reg_date_from'])
            $this->params['reg_date_from'].=" 00:00:00";
        if (isset($this->params['reg_date_to']) && $this->params['reg_date_to'])
            $this->params['reg_date_to'].=" 23:59:59";
        // return parent::index();
        $aConditions = parent::makeSearchConditions();
        if (!isset($this->params['page']))
            $this->params['page'] = 1;
        $sort = [];
        if (isset($this->params['sort_down']))
            $sort['desc'] = $this->params['sort_down'];
        if (isset($this->params['sort_up']))
            $sort['asc'] = $this->params['sort_up'];

        $re = UserUser::getSubUserStatTable($aConditions, $this->params['page'], $sort);

        $datas = $re['data'];
        foreach ($datas as $key => $val) {
            $oUser = UserUser::find($val->id);
            if (!$oUser)
                $datas[$key]->group_balance_sum = 0;
            else
                $datas[$key]->group_balance_sum = number_format($oUser->getGroupAccountSum(), 2);
        }
        $total = $re['counts'];
        $pages = ceil($total / 30);

        //是否有配额
        $bIsOverLimitPrizeGroup = Session::get('show_overlimit');
        $this->setVars(compact('datas', 'bIsOverLimitPrizeGroup'));
        $this->setVars('pages', $pages);
        $this->setVars('currentPage', $this->params['page']);
        $url = "?";
        foreach ($this->params as $key => $val) {
            if ($key != 'page')
                $url.=$key . "=" . $val . "&amp;";
        }
        $this->setVars('page_url', $url);
        $this->setVars('total', $total);


        return $this->render();
    }
     */


    public function index() {

            $this->params['parent_id'] = Input::get('parent_id', Session::get('user_id'));
            if(!empty($this->params['username'])){
                $userData = User::getUsersByUsernames(array($this->params['username']))->first();
                if($userData){
                    $userData = $userData->toArray();
                    $userFatherIds = $userData['forefather_ids'];
                    $userFatherIds = explode(',',$userFatherIds);
                    if(in_array(Session::get('user_id'),$userFatherIds)){
                        unset($this->params['parent_id']);
                    }
                }
            }
         //   $this->params['forefather_ids'] = Session::get('user_id');
       // pr($this->params);
        empty($this->params['sign_date_from']) or $this->params['sign_date_from'] .=" 00:00:00";
        empty($this->params['sign_date_to']) or $this->params['sign_date_to'] .=" 23:59:59";

        $oQuery = $this->indexQuery();
       $datas = $oQuery->paginate(static::$pagesize);
//        if((!empty($this->params['commission_rate']) || !empty($this->params['series_type'])) && $datas->count()){
//
//            $oUserIds = array();
//            foreach($datas as $data){
//                $uData = $data->getAttributes();
//                $oUserIds[] = $uData['id'];
//            }
//            $aCondition = array(
//                                'user_id'=> array('in',$oUserIds),
//                                );
//            !empty($this->params['commission_rate']) && $aCondition['commission_rate'] = array('like', '%'.$this->params['commission_rate'].'%');
//            !empty($this->params['series_type']) && $aCondition['type_id'] = array('=', $this->params['series_type']);
//            $uCommissionRateUser = UserCommissionSet::getUserCommissionSetList($aCondition,array('user_id'));
//            unset($oUserIds);
//            $cUserIds = array();
//            foreach($uCommissionRateUser as $uCUser){
//                $uData = $uCUser -> getAttributes();
//                $cUserIds[] = $uData['user_id'];
//            }
//            $cUserIds = array_unique($cUserIds);
//           // if(!empty($cUserIds))
//                foreach($datas as $key => $data){
//                    $uData = $data->getAttributes();
//                    if(!in_array($uData['id'],$cUserIds))
//                        unset($datas[$key]);
//                }
//
//        }
        $this->setVars(compact('datas'));
        return $this->render();
    }

    /**
     * get search conditions array
     *
     * @return array
     */
    protected function & makeSearchConditions() {
        $aConditions = [];
        $iCount = count($this->params);

        foreach ($this->paramSettings as $sColumn => $aParam) {
            if (!isset($this->params[$sColumn])) {
                if ($aParam['limit_when_null'] && $iCount <= 1) {
                    $aFieldInfo[1] = null;
                } else {
                    continue;
                }
            }

            $mValue = isset($this->params[$sColumn]) ? $this->params[$sColumn] : null;

            if (!mb_strlen($mValue) && !$aParam['limit_when_null'])
                continue;
            if (!isset($this->searchItems[$sColumn])) {
                $aConditions[$sColumn] = [ '=', $mValue];
                continue;
            }

            $aPattSearch = array('!\$model!', '!\$\$field!', '!\$field!');

            $aItemConfig = & $this->searchItems[$sColumn];

            $aPattReplace = array($aItemConfig['model'], $mValue, $aItemConfig['field']);
            $sMatchRule = preg_replace($aPattSearch, $aPattReplace, $aItemConfig['match_rule']);

            $aMatchRule = explode("\n", $sMatchRule);

            if (count($aMatchRule) > 1) {        // OR
                // todo : or
            } else {

                $aFieldInfo = array_map('trim', explode(' = ', $aMatchRule[0]));

                $aTmp = explode(' ', $aFieldInfo[0]);

                $iOperator = (count($aTmp) > 1) ? $aTmp[1] : '=';
                if (!mb_strlen($mValue) && $aParam['limit_when_null']) {
                    $aFieldInfo[1] = null;
                }
                list($tmp, $sField) = explode('.', $aTmp[0]);
                $sField{0} == '$' or $sColumn = $sField;
                if (isset($aConditions[$sColumn])) {
                    // TODO 原来的方式from/to的值和search_items表中的记录的顺序强关联, 考虑修改为自动从小到大排序的[from, to]数组
                    $arr = [$aConditions[$sColumn][1], $aFieldInfo[1]];
                    sort($arr);

                    $aConditions[$sColumn] = [ 'between', $arr];
                } else {
                    $aConditions[$sColumn] = [ $iOperator, $aFieldInfo[1]];
                }
            }

        }
        if((!empty($this->params['commission_rate']) || !empty($this->params['series_type']))){
            if($this->params['commission_rate']) $aCond['commission_rate'] = ['=', $this->params['commission_rate']];
            if($this->params['series_type']) $aCond['type_id'] = ['=', $this->params['series_type']];
            if($this->params['username']){
                $aCond['username'] = ['=', $this->params['username']];
            }else{
                $aCond['parent_id'] = $this->params['parent_id'];
            }
            $aCommissions = UserCommissionSet::getUserCommissionSetList($aCond,['user_id']);
            $aUsers = [];
            foreach ($aCommissions as $oCommission){
                $aUsers[] = $oCommission->user_id;
            }
            $aConditions['id'] = ['in', $aUsers];
        }
        return $aConditions;
    }

    /**
     * 查询代理下级用户信息
     * @return type
     */
    public function subUsers($pid) {
        if (!isset($pid)) {
            $this->goBack('error', '_user.missing-parent_id');
        }
        $aUsers = UserUser::getAllUsersBelongsToAgent(Session::get('user_id'));
        if (in_array($pid, $aUsers)) {
            $this->params['parent_id'] = $pid;
            $this->action = 'index';

            return $this->index();
        } else {
            $this->goBack('error', '_user.search-forbidden');
        }
    }

    /**
     * [generateData 生成用户数据]
     * @return [type] [description]
     */
//    private function generateData() {
//        // $iAccountFrom    = Input::get('account_from');
//        // $iAccountTo      = Input::get('account_to');
//        // TODO 有优化空间，目前是每次循环都查询团队余额，所属用户组，下级户数
//        $aUserIds = [];
//            
//        foreach ($this->viewVars['datas'] as $key => $oUser) {
//            $aUserIds[] = $oUser->id;
//            $aUserPrizeGroups[$oUser->id] = $oUser->prize_group;
//        }
//        if(empty($aUserIds)) return ;
//        $aOnlineUsers = UserOnline::getListByUserIds($aUserIds);
//        $iParentId = Session::get('user_id');
//        $iParentPrizeGroup = UserUser::find($iParentId)->prize_group;
//       // $aTeamCommissionContribution = Commission::getTeamCommissionContribution($aUserIds, $aUserPrizeGroups, $iParentId, $iParentPrizeGroup);
//        // pr($aTeamCommissionContribution);exit;
//        foreach ($this->viewVars['datas'] as $key => $oUser) {
//            // TIP 修改为过滤奖金组，玩家各彩种奖金组均一致
//            $iTeamTurnOverSum = UserProfit::getUserTotalTeamTurnover(date('Y-m').'-01', date('Y-m-d'), $oUser->id);
//            // if ($iAccountFrom && $iAccountSum < $iAccountFrom) {
//            //     array_forget($this->viewVars['datas'], $key);
//            //     continue;
//            // }
//            // if ($iAccountTo && $iAccountSum > $iAccountTo) {
//            //     array_forget($this->viewVars['datas'], $key);
//            //     continue;
//            // }
//            // $oUser->role_desc = $oUser->getUserRoleNames();
//            $oUser->children_num                 = $oUser->getAgentDirectChildrenNum();
//            $oUser->iTeamTurnOverSum            = number_format($iTeamTurnOverSum, 4);
//            $oUser->profit = number_format(UserProfit::getUserTotalProfit(date('Y-m').'-01', date('Y-m-d'), $oUser->id),4);
//            $oUser->online                       = isset($aOnlineUsers[$oUser->id]) ? $aOnlineUsers[$oUser->id] : 0; // 没有在user_onlines表中的用户算离线
//          //  $oUser->team_commission_contribution = isset($aTeamCommissionContribution[$oUser->id]) ? $aTeamCommissionContribution[$oUser->id] : 0;
//            // pr($oUser->toArray());exit;
//        }
//    }
    // public function userList()
    // {
    //     if (!Session::get('is_agent')) {
    //         return $this->goBack('error', __('_basic.agent-only', $this->langVars));
    //     }
    //     return $this->render();
    // }
    // private function generateUsers()
    // {
    //     $iAgentId = Session::get('user_id');
    //     $aUsers   = User::getUsersBelongsToAgent($iAgentId);
    //     foreach ($aUsers as $oUser) {
    //         $oUser->role_desc         = $oUser->getUserRoleNames();
    //         $oUser->children_num      = $oUser->getAgentDirectChildrenNum();
    //         $oUser->group_account_sum = $oUser->getGroupAccountSum();
    //     }
    //     // pr($aUsers->toArray());
    //     // exit;
    //     return $aUsers;
    // }


    public function personal() {
        if (Request::method() == 'PUT') {
            return $this->resetPersonalInfo();
        } else {
            //Session::get('is_agent')
            return $this->render();
        }
    }

    /**
     * [resetPersonalInfo 重置用户个人信息]
     * @return [Response] [description]
     */
    private function resetPersonalInfo() {
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        $sNickname = trim(Input::get('nickname'));
        // $oUser->nickname = $sNickname;
        $nicknameExist = $oUser->checkNickrnameExist($sNickname,$iUserId);
        if(!$nicknameExist) {
            $bSucc = $oUser->update(['nickname' => $sNickname]); // User::$rules['nickname']
            $sErrorMsg = &$oUser->getValidationErrorString();
            $sDesc = $bSucc ? '用户昵称更新成功！' : $sErrorMsg;
        }else{
            $bSucc=false;
            $sDesc='用户昵称已经存在';
        }
        return $this->renderReturn($bSucc, $sDesc);
    }

    /**
     * [passwordManagement 密码管理，包括登录密码和资金密码的重置]
     * @param  [Int] $iType [0: 登录密码, 1: 资金密码]
     * @return [Response]        [description]
     */
    public function passwordManagement($iType = null) {
        // pr(Request::method());exit;
        if (Request::method() == 'PUT') {
            $iId = Session::get('user_id');
            switch ($iType) {
                case 1:
                    return $this->changeFundPassword($iId);
                    break;
                case 0:
                default:
                    return $this->changePassword($iId);
                    break;
            }
        } else {
            $this->saveUrlToSession();
            return $this->render();
        }
    }

    /**
     * [safeChangeFundPassword 第一次设置资金密码]
     * @return [Response] [description]
     */
    public function safeChangeFundPassword() {
        if (Request::method() == 'PUT') {
            $iId = Session::get('user_id');
            return $this->changeFundPassword($iId, true);
        } else {
	        if ($this->model->find(Session::get('user_id'))->fund_password) {
	            return Redirect::to('users/password-management')
                                ->with('error', '你已经设置过资金密码了！');
	        }
            return $this->render();
        }
    }

    /**
     * [changePassword 改变用户密码]
     * @param  [Integer] $iId [用户id]
     * @return [Response]      [description]
     */
    private function changePassword($iId) {
        $sOldPassword = trim(Input::get('old_password'));
        $sNewPassword = trim(Input::get('password'));
        $sNewPasswordConfirmation = trim(Input::get('password_confirmation'));
        $this->model = $this->model->find($iId);
        $sOldPwd = md5(md5(md5($this->model->username . $sOldPassword)));
        if (!$this->model->checkPasswordFront($sOldPassword)) {
            return $this->goBack('error', __('_user.validate-password-fail'));
        }
        if ($this->model->checkFundPasswordFront($sNewPassword)) {
            return $this->goBack('error', __('_user.same-with-fund-password'));
        }
        $aFormData = [
            'password' => $sNewPassword,
            'password_confirmation' => $sNewPasswordConfirmation,
        ];
        $aReturnMsg = $this->model->resetPassword($aFormData);
        if (!$bSucc = $aReturnMsg['success']) {
            $this->langVars['reason'] = $aReturnMsg['msg'];
        }
        // pr($aReturnMsg);exit;
        $sDesc = $bSucc ? __('_user.password-updated') : __('_user.update-password-fail', $this->langVars);
        return $this->renderReturn($bSucc, $sDesc);
    }

    /**
     * [changeFundPassword 改变用户资金密码]
     * @param  [Integer] $iId      [用户id]
     * @param  [boolean] $bIsFirst [是否初次设置]
     * @return [Response]            [description]
     */
    private function changeFundPassword($iId, $bIsFirst = false) {
        $sOldFundPassword = trim(Input::get('old_fund_password'));
        $sNewFundPassword = trim(Input::get('fund_password'));
        $sNewFundPasswordConfirmation = trim(Input::get('fund_password_confirmation'));
        $this->model = $this->model->find($iId);
        if (!$bIsFirst) {
            if (!$this->model->checkFundPasswordFront($sOldFundPassword)) {
                return $this->goBack('error', __('_user.validate-fund-password-fail'));
            }
        }
        if (empty($sNewFundPassword) || empty($sNewFundPasswordConfirmation)) {
            return $this->goBack('error', '请正确输入资金密码');
        }
        if ($sNewFundPassword != $sNewFundPasswordConfirmation) {
            return $this->goBack('error', '两次输入的资金密码不一致');
        }
        if ($this->model->checkPasswordFront($sNewFundPassword)) {
            return $this->goBack('error', __('_user.same-with-password'));
        }
        $aFormData = [
            'fund_password' => $sNewFundPassword,
            'fund_password_confirmation' => $sNewFundPasswordConfirmation,
        ];
        $aReturnMsg = $this->model->resetFundPassword($aFormData);
        if (!$bSucc = $aReturnMsg['success']) {
            $this->langVars['reason'] = $aReturnMsg['msg'];
        }
        // pr($bSucc);
        // pr($this->model->getValidationErrorString());exit;
        // pr($this->langVars);exit;
        if ($bSucc) {
            if(!$bIsFirst){
                 $sUrl = $this->getUrlFromSession('users.password-management');
                 return Redirect::to($sUrl)->with('success', __('_user.fund-password-updated'));
            }else{
                $sUrl = route('security-questions.index');
                return Redirect::to($sUrl)->with('success', __('_user.fund-password-updated'));
            }

        } else {
            return $this->goBack('error', __('_user.update-fund-password-fail', $this->langVars));
        }
    }

    /**
     * [renderReturn 响应函数]
     * @param  [Boolean] $bSucc [是否成功]
     * @param  [String] $sDesc [响应描述]
     * @return [Response]        [响应]
     */
    public function renderReturn($bSucc, $sDesc) {
        // pr($this->model->validationErrors);exit;
        if ($bSucc) {
            return $this->goBack('success', $sDesc);
        } else {
            return $this->goBack('error', $sDesc);
        }
    }

    /**
     * [accurateCreate 精准开户
     *         注册流程:
     *            1. 判断随机码是否正确
     *            2. 判断是否代理用户(一代只能开玩家用户)
     *            3. 判断用户名是否已经存在
     *            4. 获取开户奖金组信息
     *            5. 生成用户信息
     *            6. 新建用户
     *            7. 新建用户的账户
     *            8. 更新用户的account_id字段
     *            9. 创建用户奖金组
     * ]
     * @return [Response] [description]
     * @author Roy
     */
    public function accurateCreate() {
        if (!Request::isMethod('POST')) {
            return $this->render();
        }

        // if(Session::has('aLotteriesPrizeSets'))
        // {
        //     $aLotteriesPrizeSets = Session::get('aLotteriesPrizeSets');
        // }
        // if(sizeof($aLotteriesPrizeSets) > 0)
        // {
        //     foreach ($aLotteriesPrizeSets as $per_lotto)
        //     {
        //         // $per_lotto : SSC , 11-5
        //         foreach($per_lotto['children'] as $per_children_lotto )
        //         {
        //             $sLotteryPrizeJson[ $per_children_lotto['id'] ] = Input::get('prize_group');
        //         }
        //         $sSeriesPrizeJson[ $per_lotto['type'] ] = Input::get('prize_group');
        //     }
        // }
        // if(Input::has('is_agent') && Input::get('is_agent') == 1)
        // {
        //     $sSeriesPrizeJson['all_lotteries'] = Input::get('prize_group');
        // }
        // $sSeriesPrizeJson  = json_encode($sSeriesPrizeJson);
        // $sLotteryPrizeJson = json_encode($sLotteryPrizeJson);
        // 先验证随机码
        $aRandom = explode('_', trim(Input::get('_random')));
        if ($aRandom[1] != Session::get($aRandom[0])) {
            return Redirect::back()->withInput()->with('error', '注册失败！');
        }

        // 只有代理才能开户
//        if (!Session::get('is_agent')) {
//            return $this->renderReturn(FALSE, __('_basic.no-rights', $this->langVars));
//        }

        // 验证用户名是否存在
        if ($this->validateUsernameExist($sErrorMsg)) {
            return $this->renderReturn(FALSE, $sErrorMsg);
        }
        $iAgentId = Session::get('user_id');
        $oParent = UserUser::find($iAgentId);

        $aInputData = trimArray(Input::except(['_token', '_random']));

        $isForever = Input::get('is_forever');
        $setPrizeGroup = $this->params['prize_group'];

        $oSeriesSets = SeriesSet::all();

        foreach($oSeriesSets as $oSeriesSet)
        {
            $aCommissionRate[$oSeriesSet->id] = $oSeriesSet->id == SeriesSet::ID_LOTTERY ? UserCommissionSet::getRateByPrizeGroup($setPrizeGroup) : trim(Input::get('commission_rate_'.$oSeriesSet->id));
        }
        //如果是基础，则为永久奖金组
        $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($oParent->is_agent, true)) ? $aPrizeGroups : [];
        if(in_array($setPrizeGroup, $aPrizeGroups)){
            $isForever = 1;
        }

/*        if ($aInputData['prize_group'] > $oAgent->prize_group) {
            return $this->goBack('error', __('_userprizeset.more-than-parent-prize-group'));
        }
        if (!$aInputData['is_agent']) {
            return $this->goBack('error', __('_userprizeset.user_type_error'));
        }

        $jsonString  = (new UserRegisterLink)->generateUserPrizeSetJson( $this->params['is_agent'], $iPrizeGroupType, $iPrizeGroupId, $sLotteryPrizeJson, $sSeriesPrizeJson, $iAgentId);
        $aPrizeGroup = json_decode($jsonString);

        $is_low_prize_agent = null;
        $lowPrizeUserList = config::get('useLowPrizeGroupWhiteList.user_list');
        $is_low_prize_agent = in_array($oAgent->username, $lowPrizeUserList);*/
        $userType = PrizeSysConfig::TYPE_AGENT;

        $aPrizeGroup = UserPrizeSet::generateUserPrizeSetData($userType, $setPrizeGroup, null);
        if ($aPrizeGroup == false || !is_array($aPrizeGroup)) {
            return $this->goBack('error', __('_userprizeset.no-available-prize-group'));
        }

        $aPrizeGroup = json_decode(json_encode($aPrizeGroup));

        $aExtraData = [
            'parent_id' => $iAgentId,
            'parent' => Session::get('username'),
            'is_from_link' => 0,
            'is_tester' => $oParent->is_tester,
            'register_at' => Carbon::now()->toDateTimeString(),
            'password_confirmation' => $aInputData['password'], // Just to satisfy the validation
        ];
        $aInputData = array_merge($aInputData, $aExtraData);

        $oUser = $this->model;
        $aReturnMsg = $oUser->generateUserInfo($setPrizeGroup, $aInputData);
        if (!$aReturnMsg['success']) {
            return Redirect::back()->withInput()->with('error', $aReturnMsg['msg']);
        }

        DB::connection()->beginTransaction();

        if($bSucc = $this->createProcess($oUser, $aPrizeGroup))
        {
            if(($bSucc = OverlimitPrizeGroup::setPrizeGroupNum($oParent, $oUser, $setPrizeGroup, $isForever)) && ($aReturnMsg = UserPrizeGroupTmp::setTmpPrize($oUser, $setPrizeGroup, $isForever)))
            {
                $aReturnMsg = UserCommissionSet::createCommissionRate($oUser, $aCommissionRate);
                $msg = $aReturnMsg['msg'];
                $bSucc = $aReturnMsg['success'];
            }else{
                $bSucc = false;
                $msg = '无效奖金组';
            }
        }else{
            $msg = $this->model->getValidationErrorString();
        }


        if($bSucc){
            DB::connection()->commit();
            file_put_contents('/tmp/event.log', 'accurate create');
            $sDesc = __('_basic.created', $this->langVars);
            return $this->renderReturn(true, $sDesc);
        }else{
            DB::connection()->rollback();
            return $this->renderReturn(FALSE, $msg);
        }

        /*==============竞彩返点设置================*/
/*        if ($bSucc){
            $aJcCommissionSetting = [
                'single_rate' => Input::get('single') / 100,
                'multiple_rate' => Input::get('multi') / 100,
            ];
            $bSucc = $this->saveJcCommissionSetting($this->model->id, $aJcCommissionSetting);
        }*/
        /*==============竞彩返点设置================*/
    }

    /**
     * [validateUsernameExist 验证用户名是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateUsernameExist(& $sErrorMsg) {
        $sUsername = trim(Input::get('username'));
        if (!$sUsername) {
            $sErrorMsg = '请填写用户名！';
            return true;
        } else if (UserUser::checkUsernameExist($sUsername)) {
            $sErrorMsg = '该用户名已被注册，请重新输入！';
            return true;
        }else if(UserUser::checkNickrnameExist($sUsername)){
            $sErrorMsg = '该用户名已被注册，请重新输入！';
        }
        return false;
    }

    /**
     * [validateEmailExist 验证邮箱是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateEmailExist(& $sErrorMsg) {
        $sEmail = trim(Input::get('email'));
        // $sPassword = trim(Input::get('password'));
        if (!$sEmail) {
            $sErrorMsg = '请填写邮箱！';
            return true;
        } else if (UserUser::checkEmailExist($sEmail)) {
            $sErrorMsg = '该邮箱已被注册，请重新输入！';
            return true;
        }
        return false;
    }

    /**
     * [createProcess 精准开户流程]
     * @return [Boolean] [开户是否成功]
     */

    /**
     * [createProcess 精准开户流程]
     * @param  [Object] $oUser       [用户对象]
     * @param  [Array] $aPrizeGroup  [奖金组数据]
     * @return [Boolean]             [开户成功/失败]
     */
    protected function createProcess(& $oUser, $aPrizeGroup) {
        // $bSucc = false;
        // $aRules = User::$rules;
        // $aRules['username'] = str_replace('{:id}', '', $aRules['username']);
        if ($bSucc = $oUser->save()) {
            $oAccount = $oUser->generateAccountInfo();
            if ($bSucc = $oAccount->save()) {
                // pr($bSucc);exit;
                // $aRules = User::$rules;
                // $aRules['username'] = str_replace('{:id}', $oUser->id, $aRules['username'] );
                $oUser->account_id = $oAccount->id;
                // $bSucc = $oUser->save($aRules);
                if ($bSucc = $oUser->save()) {
                    // pr($bSucc);exit;
                    $aReturnMsg = UserPrizeSet::createUserPrizeGroup($oUser, $aPrizeGroup);
                    $bSucc = $aReturnMsg['success'];
                }
            }
        }
        // pr($oUser->validationErrors->toArray());exit;
        return $bSucc;
    }

    /**
     * 绑定用户邮箱
     *
     * @return RedirectResponse|Response
     */
    public function bindEmail() {
        if (Request::method() == 'PUT') {
            return $this->bindEmailSave();
        }
        //申请绑定邮箱
        else {
            return $this->render();
        }
    }

    /**
     * 保存邮箱信息并给用户发送确认邮件
     *
     * @return RedirectResponse
     */
    private function bindEmailSave() {
        $user_id = Session::get('user_id');
        $email = trim(Input::get('email'));

        $user = UserUser::find($user_id);

        if (!$user->isActivated()) {
            //邮箱已被绑定
            if (User::checkEmailExist($email)) {
                return $this->goBack('error', '您的邮箱已被绑定，请重新输入邮箱！');
            }

            $user->email = $email;
            $user->save();

            //给用户发送一封激活邮件
//            $user->sendActivateMail();
//            Queue::push('EventTaskQueue', ['event'=>'bomao.info.lockEmail', 'user_id'=>$user_id, 'data' => []], 'activity');
            return $this->goBack('success', '链接已发送，24小时之内有效，请从邮箱激活！');
        }

        return $this->goBack('success', '您已绑定邮箱，无需重复绑定！');
    }

    /**
     * 激活邮箱
     *
     * @return RedirectResponse
     */
    public function activateEmail() {
        $user_id = trim(Input::get('u'));
        $code = trim(Input::get('c'));
        $suser_id = Session::get('user_id');

        //如果用户返回的信息有效，则成功
        $this->viewVars['msg'] = [
            0 => ['class' => 'alert-error', 'backUrl' => route('users.personal'), 'backMsg' => '重新绑定', 'msg' => '验证失败，邮件激活链接无效或已过期。'],
            1 => ['class' => 'alert-success', 'backUrl' => route('home'), 'backMsg' => '返回首页', 'msg' => '恭喜您，邮箱验证成功。'],
        ];

        $this->viewVars['state'] = 0;

        //登陆的用户需要一直才行
        if ($suser_id == $user_id && Cache::section('bindEmail')->get($user_id) == $code) {
            $this->viewVars['state'] = 1;
            //更新用户绑定时间，清空cache
            $user = UserUser::find($user_id);
            $user->activated_at = Carbon::now()->toDateTimeString();
            $user->save();

            Cache::section('bindEmail')->forget($user_id);
        }

        return $this->render();
    }

    /**
     * [getLatestUserAccountInfo 获取用户最新的账户信息]
     * @return [Json] [用户最新的账户信息]
     */
    public function getLatestUserAccountInfo() {
        $iUserId = Session::get('user_id');
        $iAvailable = Account::getAvaliable($iUserId);

        $aDepositInfo = UserTransaction::getUserOwnLatestRecords(1, [UserTransaction::TYPE_DEPOSIT_ONLINE, UserTransaction::TYPE_DEPOSIT_MANUAL]);
        $aWithdrawalInfo = UserTransaction::getUserOwnLatestRecords(1, [UserTransaction::TYPE_WITHDRAWAL_USER, UserTransaction::TYPE_WITHDRAWAL_ADMIN]);
        $aResponse = [
            'isSuccess' => 1,
            'type' => 'success',
            'data' => [
                [
                    'type' => 'account',
                    'data' => [
                        [
                            'type' => 'balance',
                            'data' => $iAvailable
                        ],
                    ]
                ]
            ]
        ];
        if (count($aWithdrawalInfo)) {
            $oWithdrawal = $aWithdrawalInfo[0];
            $aResponse['data'][0]['data'][] = [
                'type' => 'withdrawals',
                'data' => ['id' => $oWithdrawal->id, 'amount' => $oWithdrawal->transaction_amount]
            ];
        }
        if (count($aDepositInfo)) {
            $oDeposit = $aDepositInfo[0];
            $aResponse['data'][0]['data'][] = [
                'type' => 'recharge',
                'data' => ['id' => $oDeposit->id, 'amount' => $oDeposit->amount]
            ];
        }
        return Response::json($aResponse);
    }

    public function saveJcCommissionSetting($user_id, $aJcCommissionSetting){
        $oManJcCommissionUser = \JcModel\ManJcCommissionUser::where('user_id','=',$user_id)->first();
        if(!$oManJcCommissionUser){
            $oManJcCommissionUser = new \JcModel\JcCommissionUser($aJcCommissionSetting);
            $oManJcCommissionUser->user_id = $user_id;
        }else{
            $oManJcCommissionUser->fill($aJcCommissionSetting);
        }
        $bSucc = $oManJcCommissionUser->saveCommissionUser();
        return $bSucc;
    }


    public function diffCommissionRate($id){
        $rates = UserCommissionSet::find($id)->getDiffCommissionRate();
        $this->halt(true, 'info', null, $a, $a, $rates);
    }
}
