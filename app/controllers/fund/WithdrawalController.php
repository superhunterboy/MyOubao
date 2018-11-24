<?php

class WithdrawalController extends ComplicatedSearchController {

    protected $errorFiles = ['account', 'withdrawal', 'system'];

    /**
     * 资源视图目录
     * @var string
     */
    protected $customViewPath = 'admin.withdrawal';
    protected $customViews = [
        'index', 'view', 'setToSuccess', 'remittanceVerify', 'verify',
    ];
    protected $o_mc_order = null;
    protected $searchBlade = 'w.withdrawal_search';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'Withdrawal';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aUsers = User::getTitleList();
        $aAccounts = [];
        $aHiddenColumns = $sModelName::$aHiddenColumns;
        $aReadonlyInputs = $sModelName::$aReadonlyInputs;
        switch ($this->action) {
            case 'index':
                $aUserIds = [Role::WITHDRAW_BLACK => 'Black List', Role::WITHDRAW_WHITE => "Normal User"];
                $this->setVars("aUserIds", $aUserIds);
                $sMode = '';
                if (array_get($this->params, 'status') == Withdrawal::$unVerifiedStatus) {
                    $sMode = __('_withdrawal.' . 'unverified');
                } else if (array_get($this->params, 'status') == Withdrawal::$verifiedStatus) {
                    $sMode = __('_withdrawal.' . 'verified');
                } else if (array_get($this->params, 'status') == Withdrawal::$remitStatus) {
                    $sMode = __('_withdrawal.' . 'remit-verify-title');
                }
                $this->viewVars['sPageTitle'] .= ' - ' . $sMode;
                $this->viewVars['resourceName'] .= '(' . $sMode . ')';
                break;
            case 'view':
                $validStatuses = Withdrawal::getTranslateValidStatus();
                $this->setVars(compact('validStatuses'));
                $this->viewVars['aColumnSettings']['account']['type'] = 'text';
                break;
        }
// pr($this->viewVars['validStatuses']);exit;
// $oBank = new Bank;
// $this->setVars('aBanks', $oBank->getValueListArray('name', ['status' => ['=', Bank::BANK_STATUS_AVAILABLE]], [], true));
        $aBanks = [];
        $oBanks = Bank::getSupportCardBank();
        foreach ($oBanks as $key => $value) {
            $aBanks[$value->id] = $value->name;
        }
// pr($aBanks);exit;
        $this->setVars(compact('aBanks', 'aUsers', 'aAccounts', 'aHiddenColumns', 'aReadonlyInputs'));
    }

    /**
     * 客服受理审核
     */
    public function acceptVerify($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBackToIndex('error', __('_withdrawal.missing-data'));
        }
        if (!in_array($oWithdrawal->status, [Withdrawal::WITHDRAWAL_STATUS_NEW, Withdrawal::WITHDRAWAL_STATUS_RECEIVED])) {
            return $this->goBackToIndex('error', __('_withdrawal.status-error'));
        }
        if ($oWithdrawal->setVerifyAccected(Session::get('admin_user_id'))) {
            Withdrawal::updateNewFlag();
            $this->view = $this->customViewPath . '.verify';
            $oBankCard = BankCard::getObjectByParams(['account' => $oWithdrawal->account]);
            $oRoleUser = RoleUser::getObjectByParams(['user_id' => $oWithdrawal->user_id, 'role_id' => Role::BAD_RECORD]);
            $this->setVars(compact('oWithdrawal', 'oBankCard', 'oRoleUser'));
            return $this->render();
//            return $this->goBackToIndex('success', __('_withdrawal.accepted'));
        } else {
            return $this->goBackToIndex('error', __('_withdrawal.accept-failed'));
        }
    }

    /**
     * 审核通过
     * @param  [Integer] $id [提现记录id]
     */
    public function verify($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (Request::method() == 'POST') {
            if (!is_object($oWithdrawal)) {
                return $this->goBackToIndex('error', __('_withdrawal.missing-data'));
            }
            if ($oWithdrawal->verify_accepter_id != Session::get('admin_user_id')) {
                return $this->goBackToIndex('error', __('_withdrawal.failed-wrong-administrator'));
            }
            if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_VERIFY_ACCEPTED) {
                return $this->goBackToIndex('error', __('_withdrawal.status-error'));
            }
            $aAuditorInfo = [
                'auditor_id' => Session::get('admin_user_id'),
                'auditor' => Session::get('admin_username'),
                'verified_time' => Carbon::now()->toDateTimeString(),
                'status' => Withdrawal::WITHDRAWAL_STATUS_VERIFIED,
            ];
            if ($oWithdrawal->setToVerified($aAuditorInfo)) {
                $oWithdrawal->setNewFlagForFinance();
                return $this->goBackToIndex('success', __('_withdrawal.verified'));
            } else {
                return $this->goBackToIndex('error', __('_withdrawal.verify-failed'));
            }
        } else {
            $oBankCard = BankCard::getObjectByParams(['account' => $oWithdrawal->account]);
            $oRoleUser = RoleUser::getObjectByParams(['user_id' => $oWithdrawal->user_id, 'role_id' => Role::BAD_RECORD]);
            $this->setVars(compact('oWithdrawal', 'oBankCard', 'oRoleUser'));
            return $this->render();
        }
    }

    /**
     * 受理提现
     * @param  [Integer] $id [提现记录id]
     */
    public function acceptWithdrawal($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBack('error', __('_withdrawal.missing-data'));
        }
        if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_VERIFIED) {
            return $this->goBack('error', __('_withdrawal.status-error'));
        }
        if ($oWithdrawal->setWithdrawalAccected(Session::get('admin_user_id'))) {
            $this->view = $this->customViewPath . '.setToSuccess';
            $oBankCard = BankCard::getObjectByParams(['account' => $oWithdrawal->account]);
            $oRoleUser = RoleUser::getObjectByParams(['user_id' => $oWithdrawal->user_id, 'role_id' => Role::BAD_RECORD]);
            $this->setVars(compact('oWithdrawal', 'oBankCard', 'oRoleUser'));
            Withdrawal::updateNewFlagForFinance();
            return $this->render();
        } else {
            return $this->goBack('error', __('_withdrawal.withdrawal_accepted-failed'));
        }
    }

    /**
     * 提交汇款凭证
     * @return type
     */
    public function submitDocument($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBack('error', __('_withdrawal.missing-data'));
        }
        if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED) {
            return $this->goBack('error', __('_withdrawal.status-error'));
        }
        if (Request::method() == 'POST') {
//            $sUrl = $this->saveImg();
//            if ($sUrl == false) {
//                return $this->goBack('error', __('_withdrawal.save-img-error'));
//            }
            $aServiceData = [
//                'transaction_pic_url' => $sUrl,
                'status' => Withdrawal::WITHDRAWAL_STATUS_VERIFY_DEDUCT,
            ];
            if (!$oWithdrawal->setVerifyDeduct($aServiceData)) {
                return $this->goBack('error', __('_withdrawal.set-verify-deduct-failed'));
            } else {
                return $this->goBackToIndex('success', __('_deposit.set-verify-deduct-success'));
            }
        } else {
            $this->setVars(compact('oWithdrawal'));
            return $this->render();
        }
    }

    /**
     * 审核扣款通过
     * @param  [Integer] $id [提现记录id]
     */
    public function verifyDeduct($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBack('error', __('_withdrawal.missing-data'));
        }
        if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_VERIFY_DEDUCT) {
            return $this->goBack('error', __('_withdrawal.status-error'));
        }
        if (Request::method() == 'POST') {
            if ($oWithdrawal->setToDeduct()) {
                return $this->goBack('success', __('_withdrawal.verified'));
            } else {
                return $this->goBack('error', __('_withdrawal.verify-failed'));
            }
        } else {
            $this->setVars(compact('oWithdrawal'));
            return $this->render();
        }
    }

    public function remitRecords() {
        if (!isset($this->params['status']) || !$this->params['status']) {
            $this->params['status'] = Withdrawal::$remitStatus;
        }
        $validStatuses = Withdrawal::getTranslateValidStatus(3);
// pr($validStatuses);exit;
        $this->setVars(compact('validStatuses'));
        $this->action = 'index';
        return $this->index();
    }

    public function verifiedRecords() {
        if (!isset($this->params['status']) || !$this->params['status']) {
            $this->params['status'] = Withdrawal::$verifiedStatus;
        }
        $validStatuses = Withdrawal::getTranslateValidStatus(1);
        $this->setVars(compact('validStatuses'));
        $this->action = 'index';
        return $this->index();
    }

    public function unVefiriedRecords() {
// pr($this->params);exit;
        if (!isset($this->params['status']) || $this->params['status'] === '') {
            $this->params['status'] = Withdrawal::$unVerifiedStatus;
        }
        $validStatuses = Withdrawal::getTranslateValidStatus(2);
// pr($validStatuses);exit;
        $this->setVars(compact('validStatuses'));
        $this->action = 'index';
        return $this->index();
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
// verifiedRecords, unVefiriedRecords 两个方法也走index方法的处理流程, 所以判断有没有设置validStatuses变量

        if (!isset($this->viewVars['validStatuses'])) {
            $validStatuses = Withdrawal::getTranslateValidStatus();
            $this->setVars(compact('validStatuses'));
        }
// pr($this->viewVars['validStatuses']);exit;

        return $this->render();
    }

    public function indexQuery() {
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
        $oQuery = $this->model->doWhere($aConditions);
// TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(Input::get('_withTrashed', 0));
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
     * 提现搜索中附加的搜索条件
     */
    public function makePlusSearchConditions() {
        $aPlusConditions = [];
        if (isset($this->params['top_agent']) && !empty($this->params['top_agent'])) {
            $aUserIds = User::getAllUsersBelongsToAgentByUsername($this->params['top_agent']);
        }
        if (isset($this->params['role_id']) && !empty($this->params['role_id'])) {
            $aUserIdsFromRole = RoleUser::getUserIdsFromRoleId($this->params['role_id']);
            $aUserIds = isset($aUserIds) ? array_intersect($aUserIdsFromRole, $aUserIds) : $aUserIdsFromRole;
        }
        if (isset($aUserIds)) {
            $aPlusConditions['user_id'] = ['in', count($aUserIds) > 0 ? $aUserIds : 'null'];
        }
        return $aPlusConditions;
    }

    /**
     * [generateAccounts]
     * @param  [Array] $aBankAccounts [银行 => [账号, 账号]格式的数组]
     * @return [Array]             [账号数组]
     */
    private function generateAccounts($aBankAccounts) {
        $bank_id = $this->model->bank_id or $bank_id = 1;
        $arrAccounts = $aBankAccounts[$bank_id];
        $aAccounts = [];
        foreach ($arrAccounts as $account) {
            $aAccounts[$account['account']] = $account;
        }
        return $aAccounts;
    }

    /**
     * 扣款
     * @param id 提现记录id
     */
    public function setToSuccess($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBackToIndex('error', __('_withdrawal.missing-data'));
        }
        if ($oWithdrawal->withdrawal_accepter_id != Session::get('admin_user_id')) {
            return $this->goBackToIndex('error', __('_withdrawal.failed-wrong-administrator'));
        }
        if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED) {
            return $this->goBackToIndex('error', __('_withdrawal.status-error'));
        }
        if (Request::method() == 'POST') {
//            $sUrl = $this->saveImg();
//            if ($sUrl == false) {
//                return $this->goBackToIndex('error', __('_withdrawal.save-img-error'));
//            }
            $aServiceData = [
//                'transaction_pic_url' => $sUrl,
                'status' => Withdrawal::WITHDRAWAL_STATUS_SUCCESS,
            ];
            if (!$oWithdrawal->setToDeduct($aServiceData)) {
                return $this->goBackToIndex('error', __('_withdrawal.set-verify-deduct-failed'));
            }
// 扣款
            if (Withdrawal::addWithdrawalTask($oWithdrawal->id)) {
                $oWithdrawal->addMessageTask($oWithdrawal->id);
                return Redirect::to('withdrawals/verified?is_tester=0')->with('success', __('_withdrawal.add-task-success'));
            }
        } else {
            $oBankCard = BankCard::getObjectByParams(['account' => $oWithdrawal->account]);
            $oRoleUser = RoleUser::getObjectByParams(['user_id' => $oWithdrawal->user_id, 'role_id' => Role::BAD_RECORD]);
            $this->setVars(compact('oWithdrawal', 'oBankCard', 'oRoleUser'));
            return $this->render();
        }
    }

    public function manualSetToFailure($i_withdraw_id) {
        if (!$this->statusCheck("manual", $i_withdraw_id)) {
            return $this->goBack('error', __('_withdrawal.status-cannot-be-changed'));
        }
        $oMessage = new Message($this->errorFiles);
        $oWithdrawal = Withdrawal::find($i_withdraw_id);
        $oUser = User::find($oWithdrawal->user_id);
        $oAccount = Account::lock($oUser->account_id, $iLocker);
        if (empty($oAccount)) {
            return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }
        if (!$oWithdrawal->setUser($oUser) || !$oWithdrawal->setAccount($oAccount)) {
            return $this->goBack('error', 'data-error');
        }
        DB::connection()->beginTransaction();
        $bSucc = $oWithdrawal->setToFailture() && $oWithdrawal->ReFund();
        $bSucc ? DB::connection()->commit() : DB:: connection()->rollback();
        Account::unLock($oUser->account_id, $iLocker, false);

        return $bSucc ? $this->goBack("success", __('_withdrawal.manual-set-success')) : $this->goBack("error", __('_withdrawal.manual-set-failture'));
    }

    public function createWithdrawalTask($id) {
        $oWithdrawal = Withdrawal::find($id);
        if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_SUCCESS) {
            return $this->goBack(('error'), __('_withdrawal.status-error'));
        }
        if (Withdrawal::addWithdrawalTask($oWithdrawal->id)) {
            return $this->goBackToIndex('success', __('_withdrawal.add-task-success'));
        }
    }

    /**
     * [renderReturn 根据成功/失败返回响应]
     * @param  [Boolean] $bSucc [成功/失败]
     * @param  [Int]     $sDesc [状态类型描述]
     * @return [Response]       [框架的响应]
     */
    private function renderReturn($bSucc, $sDesc) {
// pr(strtolower($sDesc));exit;
        if ($bSucc) {
            return $this->goBack('success', __('_withdrawal.change-status-success', ['desc' => __('_withdrawal.' . strtolower($sDesc))])); // Change status to "' . $sDesc . '" success.
        } else {
            return $this->goBack('error', __('_withdrawal.change-status-fialed', ['desc' => __('_withdrawal.' . strtolower($sDesc))]), true); // Change status to "' . $sDesc . '" failed.
        }
    }

    /**
     * [waitingForConfirmation 客服待定]
     * @param  [Integer] $id [提现记录id]
     */
    public function waitingForConfirmation($id) {
        $sModelName = $this->modelName;
        $this->model = $sModelName::find($id);
        if (!is_object($this->model)) {
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $sRemark = e(trim(Input::get('remark')));
        $aAuditorInfo = [
            'remark' => $sRemark,
        ];
        $iStatus = $this->model->status;
// pr($this->model->toArray());exit;
        $bSucc = $this->model->setToWaitingForConfirmation($iStatus, $aAuditorInfo);
        $sDesc = $sModelName::$validStatuses[$sModelName::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM];

        return $this->renderReturn($bSucc, $sDesc);
    }

    /**
     * 总体状态检查
     * params $action
     */
    public function statusCheck($action, $id) {
        $sModelName = $this->modelName;
        $this->model = $sModelName::find($id);

        switch ($action) {
            case "verify":
                if (!in_array($this->model->status, Withdrawal::$applyCanChangeStatus)) {
                    return false;
                }
                break;
            case "manual":
                if (!in_array($this->model->status, Withdrawal::$manualCanChangeStatus)) {
                    return false;
                }
                break;
            case "default":
                break;
        }
        return true;
    }

    /**
     * [refuse 审核拒绝]
     * @param  [Integer] $id [提现记录id]
     */
    public function refuse2($id) {
        $oMessage = new Message($this->errorFiles);
        $sMsg = e(trim(Input::get('error_msg')));
        $sModelName = $this->modelName;
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $iStatus = $oWithdrawal->status;

//开始退款
        $oUser = User::find($oWithdrawal->user_id);
        $account_id = $oUser->account_id;

        $oAccount = Account::lock($account_id, $iLocker);
        if (empty($oAccount)) {
            return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }
        DB::connection()->beginTransaction();
        $aAuditorInfo = [
            'auditor_id' => Session::get('admin_user_id'),
            'auditor' => Session::get('admin_username'),
            'verified_time' => Carbon::now()->toDateTimeString(),
            'error_msg' => $sMsg,
        ];
        $account_info = [
            'oAccount' => $oAccount,
            'oUser' => $oUser,
            'amount' => $oWithdrawal->amount,
        ];

        $bSucc = $oWithdrawal->setToRejection($iStatus, $aAuditorInfo, $account_info);
        $bSucc ? DB::connection()->commit() : DB::connection()->rollback();

        Account::unLock($account_id, $iLocker, false);

        return $this->renderReturn($bSucc, $sModelName::$validStatuses[$sModelName::WITHDRAWAL_STATUS_REFUSE]);
    }

    public function download() {
        $oQuery = $this->indexQuery();
        set_time_limit(0);

        $aConvertFields = [
            'status' => 'formatted_status',
            'is_large' => 'boolean',
        ];

        $aUser = User::getTitleList();
        $aColumn = array_merge(Withdrawal::$columnForList, ['withdrawal_accepted_at','remittance_submited_at']);
        $aData = $oQuery->get($aColumn);
        $aData = $this->model->makeData($aData, $aColumn, $aConvertFields, null, $aUser);
        return $this->downloadExcel($aColumn, $aData, 'Withdrawal Report');
    }

    private function saveImg() {
        $aInputs = Input::all();
        $sDirPath = SysConfig::readValue('deposit_transaction_pic_path') . '/';
        $sFileObj = 'transaction_pic';
        $bSucc = true;

        $rules = array(
            $sFileObj => 'required|mimes:jpeg,gif,png|max:1024',
        );
// 自定义验证消息
        $messages = array(
            $sFileObj . '.required' => '请选择需要上传的图片。',
            $sFileObj . '.mimes' => '请上传 :values 格式的图片。',
            $sFileObj . '.max' => '图片的大小请控制在 1M 以内。',
        );
        $validator = Validator::make(['transaction_pic' => $aInputs['transaction_pic']], $rules, $messages);
        if ($validator->passes()) {
            $url = $this->updateFile($aInputs['transaction_pic'], $sDirPath, $rules, $messages);
        } else {
            $url = false;
        }
        return $url;
    }

    /*
     * 图片上传方法
     */

    private function updateFile($oFile, $sDirPath, $rules, $messages) {
        file_exists($sDirPath) or mkdir($sDirPath, 0777, 1);
        $sNewFileName = '';
        //检验一下上传的文件是否有效.
        if (is_object($oFile) && $oFile->isValid()) {
            $ext = $oFile->guessClientExtension();
            $sOriginalName = $oFile->getClientOriginalName(); // 客户端文件名，包括客户端拓展名
            $sNewFileName = md5($sOriginalName . time()) . '.' . $ext; // 哈希处理过的文件名，包括真实拓展名
            $portrait = Image::make($oFile->getRealPath());
            $oldImage = Input::get('oldimg');
            File::delete(
                    public_path($oldImage)
            );
            $portrait->save($sDirPath . $sNewFileName);
        }
        return $sNewFileName;
    }

    public function loadImg($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            exit;
        }

        $n = new imgdata;
        $sFileName = SysConfig::readValue('deposit_transaction_pic_path') . '/' . $oWithdrawal->transaction_pic_url;
//        die($sFileName);
        if (!is_readable($sFileName)) {
            exit;
        }
        $n->getdir($sFileName);
        $n->img2data();
        $n->data2img();
        exit;
    }

    public function refuse($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBackToIndex('error', __('_withdrawal.missing-data'));
        }
        if ($oWithdrawal->status == Withdrawal::WITHDRAWAL_STATUS_VERIFY_ACCEPTED && $oWithdrawal->verify_accepter_id != Session::get('admin_user_id')) {
            return $this->goBackToIndex('error', __('_withdrawal.failed-wrong-administrator'));
        }
        if ($oWithdrawal->status == Withdrawal::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED && $oWithdrawal->withdrawal_accepter_id != Session::get('admin_user_id')) {
            return $this->goBackToIndex('error', __('_withdrawal.failed-wrong-administrator'));
        }
        if (!in_array($oWithdrawal->status, [Withdrawal::WITHDRAWAL_STATUS_VERIFY_ACCEPTED, Withdrawal::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED])) {
            return $this->goBackToIndex('error', __('_withdrawal.status-error'));
        }

//开始退款
        $oUser = User::find($oWithdrawal->user_id);
        $account_id = $oUser->account_id;

        $oAccount = Account::lock($account_id, $iLocker);
        if (empty($oAccount)) {
            $oMessage = new Message($this->errorFiles);
            return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }
        DB::connection()->beginTransaction();
        $bSucc = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $oWithdrawal->amount) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
        if ($bSucc) {
            DB::connection()->commit();
        } else {
            DB::connection()->rollback();
        }
        Account::unLock($oUser->account_id, $iLocker, false);
        if ($bSucc) {
            $aServiceData = [
                'auditor_id' => Session::get('admin_user_id'),
                'auditor' => Session::get('admin_username'),
                'verified_time' => Carbon::now()->toDateTimeString(),
                'error_msg' => e(trim(Input::get('error_msg'))),
                'status' => Withdrawal::WITHDRAWAL_STATUS_REFUSE,
            ];
            if ($oWithdrawal->setReject($aServiceData)) {
                return $this->goBackToIndex('success', __('_withdrawal.set-to-reject-success'));
            } else {
                return $this->goBackToIndex('error', __('_withdrawal.set-to-reject-failed'));
            }
        } else {
            return $this->goBackToIndex('error', __('_withdrawal.withdrawal-failed'));
        }
    }

    /**
     * 汇款审核
     * @param type $id
     */
    public function remittanceVerify($id) {
        $oWithdrawal = Withdrawal::find($id);
        if (!is_object($oWithdrawal)) {
            return $this->goBackToIndex('error', __('_withdrawal.missing-data'));
        }
        if ($oWithdrawal->status != Withdrawal::WITHDRAWAL_STATUS_DEDUCT_SUCCESS) {
            return $this->goBackToIndex('error', __('_withdrawal.status-error'));
        }
        if (Request::method() == 'POST') {
            $aExtraInfo = [
                'remittance_auditor_id' => Session::get('admin_user_id'),
                'remittance_auditor' => Session::get('admin_username'),
                'remittance_auditor_at' => Carbon::now()->toDateTimeString(),
                'status' => Withdrawal::WITHDRAWAL_STATUS_REMITT_VERIFIED,
            ];
            if ($oWithdrawal->setToRemitVerified($aExtraInfo)) {
                return $this->goBackToIndex('success', __('_withdrawal.remit-verified'));
            } else {
                return $this->goBackToIndex('error', __('_withdrawal.remit-verified-failed'));
            }
        } else {
            $this->setVars(compact('oWithdrawal'));
            return $this->render();
        }
    }

}
