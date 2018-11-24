<?php

/**
 * 用户管理
 */
class UserController extends AdminBaseController {

    // protected $resourceView   = 'admin.user';
    protected $customViewPath = 'admin.user';
    protected $customViews = [
        'index', 'create', 'edit', 'view', 'resetPassword', 'resetFundPassword', 'agentDistributionList', 'agentPrizeGroupList', 'agentGroupAccountInfo', 'deposit', 'withdraw'
    ];
    protected $errorFiles = ['system', 'fund', 'account'];

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'User';
    protected $bIsAgent = 1;

    protected function & _makeVadilateRules($oModel) {
        parent::_makeVadilateRules($oModel);
        $sClassName = get_class($oModel);
        $aRules = $sClassName::$rules;
        // $isEdit = (int)$oModel->id;
        $isEdit = $oModel->exists;
        // pr((int)$isEdit);exit;
        if ($isEdit) {
            array_forget($aRules, 'password');
            array_forget($aRules, 'password_confirmation');
            array_forget($aRules, 'fund_password');
            array_forget($aRules, 'fund_password_confirmation');
        }
        if ($oModel->id) {
            $aRules['username'] = 'required|alpha_num|between:6,16|unique:users,username,' . $oModel->id;
            $aRules['email'] = 'email|between:0, 50|unique:users,email,' . $oModel->id;
        }
        return $aRules;
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
//        $aParentIds = [];
//        $userIds = $sModelName::where('is_agent', '=', $this->bIsAgent)->get(['id', 'username', 'nickname']);
//        foreach ($userIds as $key => $value) {
//            $aParentIds[$value->id] = $value->username;
//        }
        $aBlockedTypes = $sModelName::$blockedTypes;
        $this->setVars(compact('aBlockedTypes'));
        // pr($this->viewVars['']);exit;
        switch ($this->action) {
            case 'index':
                $this->generateData();
//                $aUserRoles = Role::getAllRoleNameArray(Role::USER_ROLE);
                $this->setVars('aUserTypes', User::$aUserTypes);
                // user search form
                $this->setVars('aSearchFields', $this->params);
                $this->setVars('aWidgets', ['w.user_search']);
                break;
            case 'view':
            case 'edit':
            case 'create':
            case 'resetPassword':
                break;
            case 'agentPrizeGroupList':
                $aUserTypes = [User::TYPE_AGENT => User::$aUserTypes[1], User::TYPE_TOP_AGENT => User::$aUserTypes[2]];
                $this->setVars(compact('aUserTypes'));
                break;
            // case 'agentDistributionList':
            //     $aButtonParamMap = User::$aButtonParamMap;
            //     $this->setVars(compact('aButtonParamMap'));
            //     break;
        }
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        $datas = $oQuery->paginate($iPageSize);
        $this->setVars(compact('datas'));
        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1];
            } else {
                $$sMainParamName = null;
            }
            $this->setVars(compact($sMainParamName));
        }

        return $this->render();
    }

    public function indexQuery() {
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
        unset($aConditions['user_group']);
        // pr($aConditions);exit;
        $oQuery = $this->model->doWhere($aConditions);
        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(Input::get('_withTrashed', 0));
        // pr($bWithTrashed);exit;
        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    /**
     * 用户搜索中附件的搜索条件
     */
    public function makePlusSearchConditions() {
        $aPlusConditions = [];
        if (isset($this->params['user_group']) && $this->params['user_group'] !== '') {
            switch ($this->params['user_group']) {
                case User::TYPE_TOP_AGENT:
                    $aPlusConditions['parent_id'] = ['=', null];
                    $aPlusConditions['is_agent'] = ['=', User::TYPE_AGENT];
                    break;
                case User::TYPE_AGENT:
                    $aPlusConditions['parent_id'] = ['!=', null];
                    $aPlusConditions['is_agent'] = ['=', User::TYPE_AGENT];
                    break;
                case User::TYPE_USER:
                    $aPlusConditions['is_agent'] = ['=', User::TYPE_USER];
                    break;
            }
        }
        if (isset($this->params['amount']) && is_array($this->params['amount']) && !empty($this->params['amount'][0]) || !empty($this->params['amount'][1])) {
            $aAccountUserIds = Account::getUserIdsByAvailable($this->params['amount'][0], $this->params['amount'][1]);
            $aUserIds = isset($aUserIds) ? array_intersect($aAccountUserIds, $aUserIds) : $aAccountUserIds;
        }
        if (isset($aUserIds)) {
            $aPlusConditions['id'] = ['in', count($aUserIds) > 0 ? $aUserIds : 'null'];
        }
        return $aPlusConditions;
    }

    /**
     * [create 管理员直接开总代用户]
     * @param  [Integer] $id [description]
     * @return [type]     [description]
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
//            if (!isset($this->params['email']) || !$this->params['email']) {
//                return Redirect::back()
//                                ->withInput()
//                                ->with('error', __('validation.required', ['attribute' => __('_user.email')]));
//            } else
            if (isset($this->params['email']) && $this->params['email'] && User::checkEmailExist($this->params['email'])) {
                return Redirect::back()
                                ->withInput()
                                ->with('error', __('_user.email-binded'));
            }
            $aPrizeGroup = $this->generatePrizeGroup($this->params['prize_group']);
            $aExtraData = [
                'is_agent' => 1,
                'parent_id' => null,
                'register_at' => Carbon::now()->toDateTimeString(),
            ];
            $data = array_merge($this->params, $aExtraData);
            $sPrizeGroup = $this->params['prize_group'];
            $oUser = $this->model;
            $aReturnMsg = $oUser->generateUserInfo($sPrizeGroup, $data);
            // pr($bSucc);
            // pr($oUser->toArray());exit;
            if (!$aReturnMsg['success']) {
                return Redirect::back()
                                ->withInput()
                                // ->withErrors(['attempt' => __('_basic.signup-fail')]);
                                ->with('error', $aReturnMsg['msg']);
            }

            // 检查奖金组是否合法
            $iMinGroup = SysConfig::readValue('top_agent_min_grize_group');
            $iMaxGroup = SysConfig::readValue('top_agent_max_grize_group');
            if ($oUser->prize_group < $iMinGroup || $oUser->prize_group > $iMaxGroup) {
                $aInfo = [
                    'min' => $iMinGroup,
                    'max' => $iMaxGroup
                ];
                return $this->goBack('error', __('_user.top-agent-prize-group-error', $aInfo));
                // return Redirect::back()
                //                 ->withInput()
                //                 // ->withErrors(['attempt' => __('_basic.signup-fail')]);
                //                 ->with('error', '密码不正确！');
            }
            $aSeriesSet = array_combine($this->params['series_set_id'], $this->params['commission_rate']);

            $oUser->is_from_link = 0;
            DB::connection()->beginTransaction();

            if ($bSucc = $this->createProcess($oUser, $aPrizeGroup)) {

                //创建用户返点
                $aReturnMsg = UserCommissionSet::createCommissionRate($oUser, $aSeriesSet);
                if (!$aReturnMsg['success']) {
                    DB::connection()->rollback();
                    return Redirect::back()->withInput()->with('error', $aReturnMsg['msg']);
                }
                DB::connection()->commit();
                $oUser->is_tester or BaseTask::addTask('StatUpdateRegisterCountOfProfit', ['date' => $oUser->register_at, 'user_id' => $oUser->id], 'stat');
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            // TODO 目前固定死总代奖金组范围, 后续是否根据设置, 待完善(设置功能目前暂时没有)

            $aTopAgentPrizeGroups = PrizeSysConfig::getPrizeGroups(PrizeSysConfig::TYPE_TOP_AGENT, true);
            $this->setVars(compact('data', 'isEdit', 'aTopAgentPrizeGroups', 'iDefaultGroup'));
            $sModelName = $this->modelName;

            list($sFirstParamName, $tmp) = each($this->paramSettings);
            !isset($sFirstParamName) or $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];

            $oSeriesSets = SeriesSet::all();

            $this->setVars(compact('aInitAttributes', 'oSeriesSets'));


            return $this->render();
        }
    }

    /**
     * 开户流程
     * @param $oUser
     * @param $aPrizeGroup
     * @param $aSeriesSet
     * @return bool [开户流程是否成功]
     */
    protected function createProcess($oUser, $aPrizeGroup) {
        $bSucc = false;
        // $aRules = User::$rules;
        // $aRules['username'] = str_replace('{:id}', '', $aRules['username']);
        if ($bSucc = $oUser->save()) {
            $oAccount = $oUser->generateAccountInfo();
            if ($bSucc = $oAccount->save()) {
                // $aRules = User::$rules;
                // $aRules['username'] = str_replace('{:id}', $oUser->id, $aRules['username'] );
                $oUser->account_id = $oAccount->id;
                $bSucc = $oUser->save();
            }
            if ($bSucc) {
                $aReturnMsg = UserPrizeSet::createUserPrizeGroup($oUser, $aPrizeGroup);
                $bSucc = $aReturnMsg['success'];
            }
        }
        return $bSucc;
    }

    /**
     * [createUserPrizeGroup 创建用户奖金组]
     * @param  [Object] $oUser      [新建的用户对象]
     * @param  [Array] $aPrizeGroup [奖金组数组]
     * @return [Boolean]            [是否成功]
     */
    // private function createUserPrizeGroup($oUser, $aPrizeGroup) {
    //     $aLotteryPrizeGroups = $oUser->generateLotteryPrizeGroup($aPrizeGroup);
    //     $aUserPrizeGroups = $oUser->generateUserPrizeGroups($aLotteryPrizeGroups);
    //     foreach ($aUserPrizeGroups as $value) {
    //         $oUserPrizeSet = new UserPrizeSet;
    //         $oUserPrizeSet->fill($value);
    //         $bSucc = $oUserPrizeSet->save();
    //         if (!$bSucc)
    //             break;
    //     }
    //     return $bSucc;
    // }

    /**
     * [generatePrizeGroup 生成统一格式的奖金组数据]
     * @param  [String] $sPrizeGroup [奖金组]
     * @return [Array]              [奖金组数据]
     */
    private function generatePrizeGroup($sPrizeGroup) {
        $data = [];
        $oSeries = new Series;
        $aSeriesLinkTo = $oSeries->getValueListArray('link_to', [], [], true);
        foreach ($aSeriesLinkTo as $key => $value) {
            if (!$value) {
                $data[] = arrayToObject(['series_id' => $key, 'prize_group' => $sPrizeGroup]);
            }
        }
        return $data;
    }

    // /**
    //  * [createUserPrizeGroup 创建用户奖金组]
    //  * @param  [String] $sPrizeGroup [奖金组]
    //  * @param  [Object] $oUser       [用户对象]
    //  * @return [Boolean]             [创建成功/失败]
    //  */
    // private function createUserPrizeGroup($sPrizeGroup, $oUser) {
    //     $aSeriesLotteries = & Series::getLotteriesGroupBySeries();
    //     $aLotteryPrizeGroups = [];
    //     $aSeires = [];
    //     foreach ($aSeriesLotteries as $key => $aSeriesLottery) {
    //         $aSeires[$aSeriesLottery['id']] = $aSeriesLottery['children'];
    //     }
    //     // pr($aSeires);exit;
    //     foreach ($aSeires as $key => $aLotteries) {
    //         foreach ($aLotteries as $key2 => $aLottery) {
    //             $aLotteryPrizeGroups[] = [
    //                 'series_id' => $aLottery['series_id'],
    //                 'lottery_id' => $aLottery['id'],
    //                 'classic_prize' => $sPrizeGroup
    //             ];
    //         }
    //     }
    //     // pr($aLotteryPrizeGroups);exit;
    //     $aGroups = PrizeGroup::getPrizeGroupsWithOnlyKey([$sPrizeGroup]);
    //     // pr($aGroups);exit;
    //     foreach ($aLotteryPrizeGroups as $value) {
    //         $oUserPrizeSet = new UserPrizeSet;
    //         $key = $value['series_id'] . '_' . $value['classic_prize'];
    //         $data = [
    //             'user_id' => $oUser->id,
    //             'user_parent_id' => $oUser->parent_id,
    //             'user_parent' => $oUser->parent,
    //             'username' => $oUser->username,
    //             'is_agent' => $oUser->is_agent,
    //             'lottery_id' => $value['lottery_id'],
    //             'prize_group' => $aGroups[$key]['name'],
    //             'group_id' => $aGroups[$key]['id'],
    //             'classic_prize' => $value['classic_prize'],
    //         ];
    //         // pr($data);exit;
    //         $oUserPrizeSet->fill($data);
    //         // pr($oUserPrizeSet->toArray());exit;
    //         $bSucc = $oUserPrizeSet->save();
    //         if (!$bSucc)
    //             break;
    //     }
    //     return $bSucc;
    // }

    /**
     * [generateData 生成用户数据]
     * @return [type] [description]
     */
    private function generateData() {
        // $iAccountFrom = trim(Input::get('account_from'));
        // $iAccountTo = trim(Input::get('account_to'));
        // TODO 有优化空间，目前是每次循环都查询账户余额, (团队余额, 所属用户组, 下级户数)不做查询
        $aUsers = [];
        foreach ($this->viewVars['datas'] as $key => $oUser) {
            $aUsers[$oUser->id] = $oUser;
            // // $iAccountSum = $oUser->getGroupAccountSum();
            // $oAccount = Account::getAccountInfoByUserId($oUser->id);
            // if ($oAccount) {
            //     $iAvailable = $oAccount->available;
            //     if ($iAvailable && (( $iAccountFrom && $iAvailable < $iAccountFrom ) || ( $iAccountTo && $iAvailable > $iAccountTo ))) {
            //         array_forget($this->viewVars['datas'], $key);
            //         continue;
            //     }
            //     $oUser->account_available = $oAccount->available_formatted;
            // }
            // // $oUser->role_desc         = $oUser->getUserRoleNames();
            // // $oUser->children_num      = $oUser->getAgentDirectChildrenNum();
            // // $oUser->group_account_sum = number_format($iAccountSum, 4);
        }
        $aAccounts = [];
        $oAccounts = Account::getAccountInfoByUserId(array_keys($aUsers));
        // pr($oAccounts);exit;
        if ($oAccounts && count($oAccounts)) {
            foreach ($oAccounts as $key => $oAccount) {
                $aAccounts[$oAccount->user_id] = $oAccount;
            }
            foreach ($this->viewVars['datas'] as $key => $oUser) {
                if ($aAccounts[$oUser->id]) {
                    $oUser->account_available = $aAccounts[$oUser->id]->available_formatted;
                }
            }
        }
    }

    /**
     * [agentGroupAccountInfo 代理团队账户信息]
     * @param  [Integer] $iUserId [用户id]
     * @return [Response]          []
     */
    public function agentGroupAccountInfo($iUserId) {
        $data = User::find($iUserId);
        $data->group_account_sum = $data->getGroupAccountSum(false);
//         pr($data);exit;

        $this->setVars(compact('data'));
        return $this->render();
    }

    /**
     * [resetPassword 管理员帮用户重置资金密码]
     * @param  [Int] $id [用户id]
     * @return [Response]     []
     */
    public function resetFundPassword($id) {
        return $this->updatePasswrod($id, 2);
    }

    /**
     * [resetPassword 管理员帮用户重置密码]
     * @param  [Int] $id [用户id]
     * @return [Response]     []
     */
    public function resetPassword($id) {
        return $this->updatePasswrod($id, 1);
    }

    // /**
    //  * [changePassword 用户重置资金密码]
    //  * @param  [Int] $id [用户id]
    //  * @return [Response]     [description]
    //  */
    // public function changeFundPassword($id) {
    //     return $this->updatePasswrod($id);
    // }
    // /**
    //  * [changePassword 用户重置密码]
    //  * @param  [Int] $id [用户id]
    //  * @return [Response]     [description]
    //  */
    // public function changePassword($id) {
    //     return $this->updatePasswrod($id);
    // }

    /**
     * [updatePasswrod 重置密码]
     * @param  [Int] $id [用户id]
     * @param  [Int] $iType [类型值, null:用户重置行为, 2: 密码, 1:资金密码]
     * @return [Response]     [description]
     */
    private function updatePasswrod($id, $iType = null) {
        $this->model = $this->model->find($id);
        $isAdminReset = Session::get('admin_user_id');

        // pr($this->model->toArray());exit;
        if (!$this->model->exists) {
            $sMsg = __(sprintf('%s not exists', $this->resourceName));
            return $this->goBack('error', $sMsg);
        }
        // TIP 之前没有剥离前后台控制器时, 用户重置密码行为和管理员重置密码行为都放在该处, 剥离后这里不用再保留用户重置的逻辑, snowan 2014-10-02
        if (Request::method() == 'PUT') {
            $aReturnMsg = $this->generatePwdResetData($iType);
            // if (!$isAdminReset) {
            //     $bIsFund = $iType == 1;
            //     $formData = trimArray(Input::except('description'));
            //     $bSucc = $bIsFund ? $this->model->resetFundPassword($formData) : $this->model->resetPassword($formData);
            //     $sDesc = 'Reset ' . ($bIsFund ? 'fund ' : '') . 'password ' . ($bSucc ? 'success' : 'fialed');
            //     $sDesc = __($sDesc);
            // } else {
            // switch ($iType) {
            //     case 2:
            //         $data = $this->generatePasswordResetData();
            //         break;
            //     case 1:
            //         $data = $this->generateFundPasswordResetData();
            //         break;
            // }
            // pr($data);exit;
            if (!$aReturnMsg['success']) {
                return $this->goBack('error', $aReturnMsg['msg'], true);
            }
            DB::connection()->beginTransaction();
            $oAuditList = new AuditList($aReturnMsg['msg']);
            if ($bSucc = ( $oAuditList->save() && $this->createUserManageLog($id) )) {
                // $bSucc = $this->createUserManageLog($id);
                DB::connection()->commit();
            } else {
                DB::connection()->rollback();
            }
            $sDesc = $bSucc ? __('_user.commit-wait-audit') : __('_basic.create-fail');
            // }
            return $this->renderReturn($bSucc, $sDesc);
        } else {
            $data = $this->model;
            $this->setVars(compact('data'));
            return $this->render();
        }
    }

    /**
     * [generatePwdResetData 生成密码/资金密码的待审核的数据]
     * @param  [Integer] $iType [密码类型, 1: 密码, 2: 资金密码]
     * @return [Array]          [success => 成功/失败, msg => 待审核的数据/错误信息]
     */
    private function generatePwdResetData($iType = 1) {
        $aPwdNames = ['password', 'fund_password'];
        $sPwdName = $aPwdNames[$iType - 1];
        $sPwdConfirmName = $sPwdName . '_confirmation';
        // $formData        = array_map('trim', Input::all());

        $this->model->{$sPwdName} = $this->params[$sPwdName];
        $this->model->{$sPwdConfirmName} = $this->params[$sPwdConfirmName];
        // pr($this->params[$sPwdName]);
        // pr($iType);
        // pr($this->model->toArray());
        // exit;
        if ($iType == 1 && $this->model->checkFundPassword($this->params[$sPwdName])) {
            return ['success' => false, 'msg' => __('_user.same-with-fund-password')];
        } else if ($iType == 2 && $this->model->checkPassword($this->params[$sPwdName])) {
            return ['success' => false, 'msg' => __('_user.same-with-password')];
        }
        $aReturnMsg = $this->model->generatePasswordStr($iType);
        if (!$aReturnMsg['success']) {
            return $aReturnMsg;
        }
        // pr($aReturnMsg);exit;
        $sAuditData = $sPwdName . '=' . $aReturnMsg['msg'];
        $iAdminId = Session::get('admin_user_id');
        $sAdminUsername = Session::get('admin_username');
        $data = [
            'type_id' => $iType,
            'user_id' => $this->model->id,
            'username' => $this->model->username,
            'admin_id' => $iAdminId,
            'admin_name' => $sAdminUsername,
            'audit_data' => $sAuditData,
            'description' => $this->params['description'],
            'status' => 0,
        ];
        return ['success' => true, 'msg' => $data];
    }

    /**
     * [setBlockedStatus 设置用户冻结/解冻的状态值]
     * @param [Integer]  $id               [用户id]
     * @param [Integer]  $blocked          [冻结/解冻的状态值]
     * @param [Response]  [description]
     */
    private function setBlockedStatus($id, $blocked, $bIncludeChildren = false, $sComment = null) {
        $this->model = $this->model->find($id);
        $sModelName = get_class($this->model);
        if (!$this->model->exists) {
            $sMsg = __(sprintf('%s not exists', $this->resourceName));
            return $this->goBack('error', $sMsg);
        }
        $iBlocked = $this->model->blocked;
        $sOldStatusDesc = $sModelName::$blockedTypes[$iBlocked];
        if ($iBlocked == $blocked) {
            return $this->goBack('warning', __('User has been ' . $sOldStatusDesc), true);
        }
        $aSucc = [];
        $sBlockedDesc = __('_user.' . ($blocked == 0 ? 'unblock-user-title' : $sModelName::$blockedTypes[$blocked]));

        // $this->model->getConnection()->beginTransaction();
        DB::connection()->beginTransaction();

        $rules = ['blocked' => $sModelName::$rules['blocked']];
        $data = ['blocked' => $blocked];
        $this->model->fill($data);
        $bSucc = $this->model->save($rules);

        // $aSucc[] = (int)$bSucc;
        if ($bSucc) {
            if ($sComment) {
                $sComment = $sBlockedDesc . ': ' . $sComment;
            }
            $bSucc = $this->createUserManageLog($id, $sComment);
        }

        if ($bSucc && $bIncludeChildren) {
            $children = $this->model->children()->get(['id', 'parent_id']);
            // pr($children[0]->toArray()['id']);exit;
            foreach ($children as $key => $user) {
                // $aUserInfo = $user->toArray();
                $data = ['parent_id' => $user->parent_id, 'blocked' => $blocked];
                $user->fill($data);
                $bSucc = $user->save($rules);

                $iUserId = $user->id;
                // UserManageLog::createLog($iUserId, $iFunctionalityId, $sFunctionality);
                if (!$bSucc)
                    break;
                // $aSucc[] = (int)$bSucc;
            }
        }
        if ($bSucc) {
            if ($blocked == User::UNBLOCK && $iBlocked == User::BLOCK_LOGIN) {
                $bSucc = SecurityFailTimes::updateFailTimesByUserId($id);
            }
        }
        // pr($aSucc);exit;
        // if (!in_array(0, $aSucc)) {
        if ($bSucc) {

            DB::connection()->commit();
        } else {
            DB::connection()->rollback();
        }
        // $sBlockedDesc = $sModelName::$blockedTypes[$blocked];
        $sDesc = $sBlockedDesc . ' ' . __($bSucc ? '_basic.success' : '_basic.fialed');
        // $sDesc = __($sDesc);

        return $this->renderReturn($bSucc, $sDesc);
    }

    public function renderReturn($bSucc, $sDesc) {
        // pr((int)$bSucc . '------------' . $sDesc);exit;
        if ($bSucc) {
            return $this->goBackToIndex('success', $sDesc);
        } else {
            return $this->goBack('error', $sDesc, true);
        }
    }

    /**
     * [blockUser 冻结]
     * @param  [Int] $id [用户id]
     * @return [Boolean]     [冻结/解冻状态]
     */
    public function blockUser($id) {
        $data = array_map('trim', Input::all());
        $iBlocked = $data['blocked'];
        $bIncludeChildren = isset($data['is_include_children']) && !is_null($data['is_include_children']);
        $sComment = isset($data['comment']) ? $data['comment'] : null;
        return $this->setBlockedStatus($id, $iBlocked, $bIncludeChildren, $sComment);
    }

    /**
     * [unblockUser 解冻]
     * @param  [Int] $id [用户id]
     * @return [Boolean]     [冻结/解冻状态]
     */
    public function unblockUser($id) {
        // pr(Request::method());exit;
        $data = array_map('trim', Input::all());
        $bIncludeChildren = isset($data['is_include_children']) && !is_null($data['is_include_children']);
        $sComment = isset($data['comment']) ? $data['comment'] : null;
        // pr($bIncludeChildren);exit;
        return $this->setBlockedStatus($id, User::UNBLOCK, $bIncludeChildren, $sComment);
    }

    /**
     * [updateUserRoles 更新用户角色]
     * @param  [Integer] $user_id [用户id]
     * @param  [Integer] $role_id [角色id]
     */
    private function updateUserRoles($user_id, $role_id) {
        // pr($user_id . ',' . $role_id);exit;
        if (!$role_id || !$user_id) {
            $sDesc = __('The role or user you want to bind is not exits!');
            $this->goBack('error', $sDesc, true);
        }
        if (!$oUser = User::find($user_id)) {
            $sDesc = __('The role or user you want to bind is not exits!');
            $this->goBack('error', $sDesc, true);
        }
        if (!$oRole = Role::find($role_id)) {
            $sDesc = __('The role or user you want to bind is not exits!');
            $this->goBack('error', $sDesc, true);
        }
        // pr($oRole->toArray());exit;
        $sRolename = $oRole->name;
        $sUsername = $oUser->username;

        $bSucc = Role::updateUserRole($user_id, $role_id, $sRolename, $sUsername);
        $aReplace = ['rolename' => __('_role.' . $sRolename), 'username' => $sUsername];

        switch ((int) $bSucc) {
            case 1:
                $sDesc = __('_roleuser.binding-role-success', $aReplace);
                $sType = 'success';
                return $this->goBack($sType, $sDesc);
                break;
            case 2:
                $sDesc = __('_roleuser.binding-role-exist', $aReplace);
                $sType = 'warning';
                return $this->goBack($sType, $sDesc);
                break;
            case 0:
            default:
                $sDesc = __('_roleuser.binding-role-fail', $aReplace);
                $sType = 'error';
                return $this->goBack($sType, $sDesc, true);
                break;
        }

        // return [$sType, $sDesc, ($sType == 'error' ? true : false)];
    }

    /**
     * [addUserToUpPointsBlackList 升点黑名单]
     */
    public function addUserToUpPointsBlackList($id) {
        if (User::find($id)->parent_id)
            return $this->goBack('error', __('_basic.no-rights'));
        return $this->updateUserRoles($id, Role::DONT_UP_PRIZE);
        // pr($result);exit;
        // return $this->goBack($result[0], ($result[1]), $result[2]);
    }

    /**
     * [addUserToDownPointsBlackList 降点黑名单]
     */
    public function addUserToDownPointsBlackList($id) {
        if (User::find($id)->parent_id)
            return $this->goBack('error', __('_basic.no-rights'));
        return $this->updateUserRoles($id, Role::DONT_DOWN_PRIZE);
    }

    /**
     * [addUserToDividendBlackList 分红黑名单]
     */
    public function addUserToDividendBlackList($id) {
        if (!User::find($id)->is_agent)
            return $this->goBack('error', __('_basic.no-rights'));
        return $this->updateUserRoles($id, Role::BONUS_BLACK);
    }

    /**
     * [addUserToWithdrawalWhiteList 提现白名单]
     */
    public function addUserToWithdrawalWhiteList($id) {
        return $this->updateUserRoles($id, Role::WITHDRAW_WHITE);
    }

    /**
     * [addUserToWithdrawalBlackList 提现黑白单]
     * @param [type] $id [description]
     */
    public function addUserToWithdrawalBlackList($id) {
        return $this->updateUserRoles($id, Role::WITHDRAW_BLACK);
    }

    /**
     * [addUserToICBCRechargeWhiteList 工行白名单]
     */
    public function addUserToICBCRechargeWhiteList($id) {
        return $this->updateUserRoles($id, Role::ICBC_DEPOSIT_WHITE);
    }

    /**
     * [agentDistributionList 总代奖金组分布]
     * @return [Response] [description]
     */
    public function agentDistributionList() {
        $datas = User::getTopAgentPrizeGroupDistribution();
        $this->setVars(compact('datas'));
        return $this->render();
    }

    /**
     * [agentPrizeGroupList 代理奖金组列表]
     * @return [Response] [description]
     */
    public function agentPrizeGroupList() {
        $this->params['is_agent'] = 1;
        return $this->index();
    }

    /**
     * [makeSearchConditions 覆盖BaseController的同名方法, 适应根据parent_id判断是否总代/一代的搜索]
     * @return [Array] [搜索条件数组]
     */
    // protected function & makeSearchConditions() {
    //     // pr($this->params);exit;
    //     $aConditions = parent::makeSearchConditions();
    //     if ($this->action == 'agentPrizeGroupList') {
    //         $aOperators = ['=', '!='];
    //         $sOperator = '';
    //         // pr((int)($this->params['parent_id'] === ''));exit;
    //         if (isset($this->params['parent_id']) && $this->params['parent_id'] !== '') {
    //             $sOperator = $aOperators[intval(!!$this->params['parent_id'])];
    //             !$sOperator or $aConditions['parent_id'] = [$sOperator, null];
    //         }
    //     }
    //     // pr($aConditions);exit;
    //     return $aConditions;
    // }

    /**
     * 用户提现
     * @param int $id   用户id
     */
    public function withdraw($id) {
        $oUser = User::find($id);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        if (Request::method() == 'PUT') {
            $fAmount = $this->params['amount'];
            $sNote = $this->params['note'];
            $aValidateData = ['balance' => $fAmount, 'note' => $sNote];
            $aValidateRule = ['balance' => Account::$rules['balance'], 'note' => Transaction::$rules['note']];
            $validator = Validator::make($aValidateData, $aValidateRule);
            if (!$validator->passes()) {
                return $this->goBack('error', __('_basic.amount-error'));
            }
            $fAmount = formatNumber($fAmount, 2);
            $oAccount = Account::lock($oUser->account_id, $iLocker);
            if (empty($oAccount)) {
                $oMessage = new Message($this->errorFiles);
                return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
            }
//            if ($fAmount > $oAccount->getWithdrawableAmount()) {
            if ($fAmount > $oAccount->available) {
                Account::unLock($oUser->account_id, $iLocker, false);
                return $this->goBack('error', __('_user.withdraw-failed-overflow'));
            }
            DB::connection()->beginTransaction();
            $aExtraData = [
                'note' => $sNote,
                'administrator' => Session::get('username'),
                'admin_user_id' => Session::get('user_id'),
            ];
            $bSucc = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_WITHDRAW_BY_ADMIN, $fAmount, $aExtraData) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
            if ($bSucc) {
                DB::connection()->commit();
                Withdrawal::addProfitTask(date('Y-m-d'), $oUser->id, $fAmount);
            } else {
                DB::connection()->rollback();
            }
            Account::unLock($oUser->account_id, $iLocker, false);
            if ($bSucc) {
                return $this->goBackToIndex('success', __('_user.withdrawed'));
            } else {
                return $this->goBack('error', __('_user.withdraw-failed'));
            }
        } else {
            $oAccount = Account::getAccountInfoByUserId($id);
            if (!is_object($oAccount)) {
                return $this->goBack('error', __('_account.missing-account'));
            }
            $oAccount->withdrawable = $oAccount->getWithdrawableAmount();
            $this->setVars(compact('oUser', 'oAccount'));
            return $this->render();
        }
    }

    /**
     * 用户充值
     * @param int $id   用户id
     */
    public function deposit($id) {
        $oUser = User::find($id);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        if (Request::method() == 'PUT') {
            $fAmount = $this->params['amount'];
            $sNote = $this->params['note'];
            $iTransactionType = $this->params['transaction_type'];
            $aValidTransactionTypes = ManualDeposit::$aValidTransactionTypes;
//            $aValidateData = ['balance' => $fAmount, 'note' => $sNote, 'iTransactionType' => $iTransactionType];
//            $aValidateRule = ['balance' => Account::$rules['balance'], 'note' => Transaction::$rules['note'], 'iTransactionType' => 'in:'.  implode(',', $aValidTransactionTypes)];
//            $validator = Validator::make($aValidateData, $aValidateRule);

            if ($fAmount <= 0 || !in_array($iTransactionType, $aValidTransactionTypes)) {
                return $this->goBack('error', __('_basic.deposit-error'));
            }
            if ($iTransactionType == TransactionType::TYPE_DEPOSIT_BY_ADMIN) {
                $fAmount = formatNumber($fAmount, 2);
            } else {
                $fAmount = formatNumber($fAmount, 6);
            }
            $oAccount = Account::lock($oUser->account_id, $iLocker);

            if (empty($oAccount)) {
                $oMessage = new Message($this->errorFiles);
                return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
            }
            $aExtraData = [
                'note' => $sNote,
                'administrator' => Session::get('admin_username'),
                'admin_user_id' => Session::get('admin_user_id'),
            ];
            DB::connection()->beginTransaction();
            $bSucc = Transaction::addTransaction($oUser, $oAccount, $iTransactionType, $fAmount, $aExtraData) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
            if ($bSucc) {
                DB::connection()->commit();
                switch ($iTransactionType) {
                    case TransactionType::TYPE_DEPOSIT_BY_ADMIN :
                        Deposit::addProfitTask(date('Y-m-d'), $oUser->id, $fAmount);
                        break;
                    case TransactionType::TYPE_PROMOTIANAL_BONUS :
                    case TransactionType::TYPE_SEND_BONUS :
                    case TransactionType::TYPE_SETTLING_CLAIMS :
                        ActivityUserPrize::addPromoTask(date('Y-m-d'), $oUser->id, $fAmount);
                        break;
                }
            } else {
                DB::connection()->rollback();
            }
            Account::unLock($oUser->account_id, $iLocker, false);
            if ($bSucc) {
//                $iTransactionType != TransactionType::TYPE_DEPOSIT_BY_ADMIN or Queue::push('SignTaskQueue', ['task_id' => 6, 'user_id' => $id, 'activity_id' => 1, 'amount' => $fAmount], 'activity');
                return $this->goBackToIndex('success', __('_user.deposited'));
            } else {
                return $this->goBack('error', __('_user.deposit-failed'));
            }
        } else {
            $oAccount = Account::getAccountInfoByUserId($id);
            if (!is_object($oAccount)) {
                return $this->goBack('error', __('_account.missing-account'));
            }
            $oAccount->withdrawable = $oAccount->getWithdrawableAmount();
            $this->setVars(compact('oUser', 'oAccount'));
            return $this->render();
        }
    }

    public function download() {
        $oQuery = $this->indexQuery();
        set_time_limit(0);
        $aTitles = [
            'username' => __('_user.username'),
            'nickname' => __('_user.nickname'),
            'email' => __('_user.email'),
            'parent' => __('_user.parent'),
            'forefathers' => __('_user.forefathers'),
            'prize_group' => __('_user.prize_group'),
            'blocked' => __('_user.blocked'),
            'activated_at' => __('_user.activated_at'),
            'signin_at' => __('_user.signin_at'),
            // 'register_at'  => __('_user.register_at'),
            'created_at' => __('_user.created_at'),
            'is_agent' => __('_user.is_agent'),
            'is_tester' => __('_user.is_tester'),
            'register_ip' => __('_user.register_ip'),
            'login_ip' => __('_user.login_ip'),
        ];
        $aConvertFields = [
            'is_agent' => 'user_type_formatted',
            'is_tester' => 'friendly_is_tester',
            'blocked' => 'friendly_block_type',
            'created_at' => 'friendly_created_at',
        ];

        $aData = $oQuery->get(array_keys($aTitles));
        $aData = $this->_makeDownloadData($aData, array_keys($aTitles), $aConvertFields, null);
        return $this->downloadExcel($aTitles, $aData, 'User List');
    }

    private function _makeDownloadData($aData, $aFields, $aConvertFields, $aRelations) {

        $aResult = array();
        foreach ($aData as $oData) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oData->$key === '') {
                    $a[] = $oData->$key;
                    continue;
                }
                if (is_array($aConvertFields) && array_key_exists($key, $aConvertFields)) {
                    $a[] = $oData->{$aConvertFields[$key]};
                } else {
                    if (is_array($aRelations) && array_key_exists($key, $aRelations)) {
                        $a[] = $aRelations[$key][$oData->$key];
                    } else {
                        $a[] = $oData->$key;
                    }
                }
            }
            $aResult[] = $a;
        }
        // pr($aResult);exit;
        return $aResult;
    }

}
