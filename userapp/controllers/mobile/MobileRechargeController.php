<?php

class MobileRechargeController extends MobileBaseController {

    protected $modelName = 'UserDeposit';

    /**
     * 是否需要检查用户绑卡情况（如有需要可改为配置方式）
     * @var boolean
     */
    protected $bCheckUserBankCard = false;

    /**
     * 是否需要验证用户资金密码（如有需要可改为配置方式）
     * @var boolean
     */
    protected $bCheckFundPassword = false;

    /**
     * 充值响应验证规则
     * @var array
     */
    private $depositApiRules = [
        'bank_card_num' => 'RequiredIf:mode,1',
        'bank_acc_name' => 'RequiredIf:deposit_mode,1',
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'email' => 'RequiredIf:mode,2',
        'company_order_num' => 'required|between:1,64',
        'datetime' => 'required|date',
        'key' => 'required|between:32,32',
        'note' => 'RequiredIf:deposit_mode,1',
        'mownecum_order_num' => 'required|between:1,64',
        'status' => 'required|in:0,1',
        'error_msg' => 'RequiredIf:status,0',
        'mode' => ['required', 'in:0,1,2'],
        'issuing_bank_address' => '',
        'break_url' => ['RequiredIf:deposit_mode,2', 'RequiredIf:deposit_mode,3', 'between:1,1000', 'url'],
        'deposit_mode' => 'required|in:1,2,3',
        'collection_bank_id' => ['RequiredIf:deposit_mode,1', 'integer'],
    ];

    /**
     * 充值查询列表
     * @see BaseController::index()
     * @return Response
     */
    public function index() {
        $iLoginUserId = Session::get('user_id');
        if (Session::get('is_agent') && isset($this->params['username']) && $this->params['username']) {
            $oUser = User::getUserByParams(['username' => $this->params['username'], 'forefather_ids' => $iLoginUserId], ['forefather_ids']);
            if ($oUser) {
                $this->params['user_id'] = $oUser->id;
            } else {
                $aReplace = ['username' => $this->params['username']];
                return $this->goBack('error', __('_basic.not-your-user', $aReplace));
            }
        } else {
            $this->params['user_id'] = $iLoginUserId;
        }
        $data = parent::index();
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    // public function create()
    // {
    //     return View::make('l.index');
    // }
    // public function destroy($id)
    // {
    //     return View::make('l.index');
    // }
    // public function edit()
    // {
    //     return View::make('l.index');
    // }
    // public function view()
    // {
    //     return View::make($this->resourceView . '.records-game-detail');
    // }

    /**
     * 银行转账
     * @return type
     */
    public function netbank() {
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if (!is_object($oUser)) {
            $this->halt(false, 'error', UserUser::ERRNO_MISSING_USERNAME);
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            $this->halt(false, 'error', UserUser::ERRNO_USER_FUND_BLOCKED);
        }
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            $this->halt(false, 'error', UserUser::ERRNO_MISSING_FUND_PASSWORD);
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
        $oAllBanks = Bank::getSupportCardBank();
        $aUserBankCards = [];
        $bCheckUserBankCard = $this->bCheckUserBankCard; // 是否需要检查用户绑卡情况（如有需要可改为配置方式）
        if ($bCheckUserBankCard) {
            $oUserBankCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'bank_id', 'account_name', 'account']);
            foreach ($oUserBankCards as $bankcard) {
                $aUserBankCards[$bankcard->bank_id][$bankcard->id] = [
                    'id' => $bankcard->id,
                    'name' => $bankcard->account_name,
                    'number' => $bankcard->account_hidden,
                    'isdefault' => false,
                ];
            }
        }
        $aAllBanks = []; // 页面JS数据接口
        foreach ($oAllBanks as $bank) {
            $bank->is_band_card = !$bCheckUserBankCard || !empty($aUserBankCards[$bank->id]); // 显示用户是否有绑卡，当不检查绑卡时默认值为true
            $aAllBanks[$bank->id] = [
                'id' => $bank->id,
                'name' => $bank->name,
                'min' => $bank->min_load,
                'max' => $bank->max_load,
                'text' => $bank->notice,
                'userAccountList' => !empty($aUserBankCards[$bank->id]) ? $aUserBankCards[$bank->id] : [],
            ];
        }
        $data['banks'] = $aAllBanks;
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
     * 快捷充值
     * @return type
     */
    public function quick() {
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
        $oAllBanks = Bank::getSupportThirdPartBank();

        $fMinLoad = number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
        $fMaxLoad = number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
        $aAllBanks = []; // 页面JS数据接口
        foreach ($oAllBanks as $bank) {
            $aAllBanks[$bank->id] = [
                'id' => $bank->id,
                'name' => $bank->name,
                'min' => !empty($fMinLoad) ? $fMinLoad : $bank->min_load,
                'max' => !empty($fMaxLoad) ? $fMaxLoad : $bank->max_load,
            ];
        }
        $sAllBanksJs = json_encode($aAllBanks); // 页面JS数据接口

        $this->setVars(compact('oAllBanks', 'sAllBanksJs', 'bSetFundPassword', 'fMinLoad', 'fMaxLoad'));
        return $this->render();
    }

    /**
     * 充值确认
     */
    public function confirm() {
        $oUser = UserUser::find(Session::get('user_id'));
        /* Step 1: 验证 */
        $aFormRules = [
            'bank' => 'required|numeric',
            'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'fund_password' => ($this->bCheckFundPassword ? 'required|' : '') . 'between:0, 60',
            'deposit_mode' => 'required|in:' . UserDeposit::DEPOSIT_MODE_BANK_CARD . ',' . UserDeposit::DEPOSIT_MODE_THIRD_PART
        ];
        // 验证表单
        $validator = Validator::make($this->params, $aFormRules);
        if (!$validator->passes()) { // 表单未通过验证
            $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_ERROR_00);
        }
        // 1 资金密码
        if ($this->bCheckFundPassword && !$oUser->checkFundPassword($this->params['fund_password'])) {
            $this->halt(false, 'error', UserUser::ERRNO_WRONG_FUND_PASSWORD);
        }
        // 2 是否绑定银行卡
        // 3 当前银行是否可用
        $iDepositMode = $this->params['deposit_mode']; // 充值方式
        $oBank = Bank::find($this->params['bank']);
        if (!$oBank || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
            $this->halt(false, 'error', Bank::ERRNO_MISSING_DATA);
        }
        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD) { // 用户选择银行转账
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_BANK_CARD])) { // 当前银行是否支持银行卡转账
                $this->halt(false, 'error', Bank::ERRNO_MISSING_DATA);
            }
            if ($this->params['amount'] < $oBank->min_load || $this->params['amount'] > $oBank->max_load) { // 金额超出范围
                $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_AMOUNT_OUT_RANGE);
            }
        }
        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART) { // 用户选择第三方充值
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_THIRD_PART])) { // 当前银行是否支持第三方充值
                $this->halt(false, 'error', Bank::ERRNO_MISSING_DATA);
            }
            $fMinLoad = number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
            $fMaxLoad = number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
            if ($this->params['amount'] < $fMinLoad || $this->params['amount'] > $fMaxLoad) { // 金额超出范围
                $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_AMOUNT_OUT_RANGE);
            }
        }
        // 4 是否达到充值次数上限
//        $sRequestMethod = Request::method();
        /* Step 2: 创建新订单 */
        $CompanyOrderNum = UserDeposit::getDepositOrderNum(); // 生成订单号
        $aInitData = [
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'is_tester' => $oUser->is_tester,
            'top_agent' => array_get(explode(',', $oUser->forefathers), 0),
            'bank_id' => $this->params['bank'],
            'amount' => $this->params['amount'],
            'company_order_num' => $CompanyOrderNum,
            'deposit_mode' => $iDepositMode,
        ];
        $oUserDeposit = UserDeposit::createDeposit($aInitData); // 创建订单
        if (!$oUserDeposit) { // 生成订单失败
            $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_ERROR_01);
        }
        /* Step 3: 向Mownecum发送新订单请求 */
        $oApplyBank = Bank::find($this->params['bank']);
        $aDepositApiData = [
            'amount' => number_format($this->params['amount'], 2, '.', ''),
            'bank_id' => $oApplyBank->mc_bank_id,
            'company_order_num' => $CompanyOrderNum,
            'company_user' => $oUser->user_flag,
            'estimated_payment_bank' => $oApplyBank->mc_bank_id, // SAME AS bank_id,
            'deposit_mode' => $iDepositMode,
            'group_id' => 0, // 目前为空
            'web_url' => Request::server('HTTP_HOST'),
            'memo' => '',
            'note' => '',
            'note_model' => UserDeposit::DEPOSIT_NOTE_MODE_MOW, // 使用MOW附言
        ];
        $aResponse = $this->sendDeposit2Mownecum($aDepositApiData);
        try {
            if (empty($aResponse)) { // MC无响应
                $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_ERROR_02);
            }
            if (!array_get($aResponse, 'status', 0)) { // MC主动返回错误
                $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_ERROR_03);
            }
            if (!$this->verifyApiResponse($aResponse, $oUserDeposit)) { // MC响应信息未通过接口验证
                $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_ERROR_04);
            }
            $aSaveData = [
                'amount' => number_format(array_get($aResponse, 'amount'), 2, '.', ''),
                'note' => addslashes(array_get($aResponse, 'note')),
                'mownecum_order_num' => addslashes(array_get($aResponse, 'mownecum_order_num')),
                'accept_card_num' => addslashes(array_get($aResponse, 'bank_card_num')),
                'accept_email' => addslashes(array_get($aResponse, 'email')),
                'accept_acc_name' => addslashes(array_get($aResponse, 'bank_acc_name')),
                'accept_bank_address' => array_get($aResponse, 'issuing_bank_address', ''),
                'mode' => array_get($aResponse, 'mode'),
                'break_url' => addslashes(array_get($aResponse, 'break_url', '')),
                'status' => UserDeposit::DEPOSIT_STATUS_RECEIVED,
            ];
            if ($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD) { // 银行转账充值时进行特别处理
                $oCollectionBank = Bank::findBankByMcBankId(array_get($aResponse, 'collection_bank_id'));
                $aSaveData['collection_bank_id'] = $oCollectionBank->id;
            }
            if ($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART) { // 第三方充值时进行特别处理
                $iStrPos = strpos(array_get($aResponse, 'break_url', ''), 'token=');
                if ($iStrPos !== false) {
                    $iStrPos += 6; // 加上'token='的长度6
                    // 只取MC的token进行MD5以防止在高并发下多域名使用同一token的问题
                    $aSaveData['mc_token'] = md5(substr(trim($aResponse['break_url']), $iStrPos));
                }
            }
            $oUserDeposit->fill($aSaveData);
            if (!$oUserDeposit->save()) { // 系统错误，更新订单失败
//               pr($oUserDeposit->validationErrors);
                $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_ERROR_05);
            }
        } catch (Exception $e) {
            $oUserDeposit->setRefused(['error_msg' => array_get($aResponse, 'error_msg')]);
            $this->halt(false, 'error', UserDeposit::ERRNO_DEPOSIT_UNKNOWN_ERROR);
        }
        /* Step 4: 页面展示 */
        // return View::make($this->resourceView . '.confirm');
        $data['apply_bank_id'] = $oApplyBank->id;
        $data['amount'] = $oUserDeposit->amount;
        $data['accept_bank_name'] = $oCollectionBank->name;
        $data['accept_account_name'] = $oUserDeposit->accept_acc_name;
        $data['mode'] = $oUserDeposit->mode;
        $data['accept_card_number'] = $oUserDeposit->accept_card_num;
        $data['accept_email'] = $oUserDeposit->accept_email;
        $data['note'] = $oUserDeposit->note;
        $data['mownecum_order_num'] = $oUserDeposit->mownecum_order_num;
        $data['deposit_notice'] = $oCollectionBank->deposit_notice;
        $this->halt(true, 'success', null, $a, $a, $data);
    }

    /**
     * 向Mownecum发送订单数据
     * @param array $aData 要发送的数据包
     * @return array
     */
    private function sendDeposit2Mownecum(array $aData) {
//        $oSysConfig = new SysConfig;
        $sMcDepositUrl = SysConfig::readValue('mc_deposit_url');
        $iCompanyId = SysConfig::readValue('mc_company_id');

        $aData['company_id'] = $iCompanyId;
        $aData['key'] = UserDeposit::getApiKey($aData, UserDeposit::DEPOSIT_API_REQUEST);
        $oCurl = new MyCurl($sMcDepositUrl);
        $oCurl->setPost(http_build_query($aData));
        $oCurl->createCurl();
//        $oCurl->setTimeout(20);
        $oCurl->execute();
        $sResponse = $oCurl->__tostring();
        // if (!$sResponse) {
        //     $this->writeLog(['curl_error' => (curl_error($oCurl->getCurlInstance()))]);
        // }
        $this->writeLog(['url' => $sMcDepositUrl, 'date' => date('Y-m-d H:i:s'), 'request' => $aData, 'response' => $sResponse]);
        $aResponse = !empty($sResponse) ? json_decode($sResponse, true) : [];
        return $aResponse;
    }

    /**
     * 验证订单响应接口
     * @param array $aResponse 得到的响应信息
     * @param Deposit $oDeposit 充值实例
     * @return boolean
     */
    private function verifyApiResponse(array $aResponse, Deposit $oDeposit) {
        $validator = Validator::make($aResponse, $this->depositApiRules);
        if (!$validator->passes()) {
//            pr($validator->getMessageBag()->toArray());pr($aResponse);exit;
            return false;
        }
        if ($aResponse['company_order_num'] != $oDeposit->company_order_num) { // company_order_num error
//            echo 'company_order_num error';
            return false;
        }
        if ($aResponse['deposit_mode'] == 1 && !in_array($aResponse['mode'], [1, 2]) || in_array($aResponse['deposit_mode'], [2, 3]) && !$aResponse['mode'] == 0) {
//            echo 'mode error';
            return false;
        }
        if (UserDeposit::getApiKey($aResponse, UserDeposit::DEPOSIT_API_RESPONSE) != $aResponse['key']) { // key error
//            echo 'key error';
            return false;
        }
        return true;
    }

    /**
     * 写充值日志
     * @param string|array $msg
     */
    protected function writeLog($msg) {
        !is_array($msg) or $msg = var_export($msg, true);
        @file_put_contents('/tmp/deposit', $msg . "\n", FILE_APPEND);
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('bCheckFundPassword', $this->bCheckFundPassword);
        switch ($this->action) {
            case 'index':
                $this->setVars('reportName', 'depositApply');
                break;
        }
    }

}
