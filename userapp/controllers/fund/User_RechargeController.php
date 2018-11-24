<?php

class User_RechargeController extends UserBaseController {

    protected $resourceView = 'centerUser.recharge';
    protected $modelName = 'UserDeposit';
    public $resourceName = '';

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
        return parent::index();
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
            return $this->goBack('error', __('_user.missing-user'));
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            return $this->goBack('error', __('_user.deposit-now-allowed'));
        }
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId($iUserId);
        if(empty($oRes)){
            return Redirect::route('security-questions.index');  
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
        $oAllBanks = Bank::getSupportCardBank();
        foreach ($oAllBanks as $k=>$oBank) {
               $oAllBanks[$k]->is_mbank = Mbank::checkMbank($oBank->id, Mbank::BANK_MODE_BANK_CARD);
        }    
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
        if (SysConfig::readValue('prohibited_withdraw')) {
            return $this->goBack('error', __('_user.prohibited-withdraw'));
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
        $sAllBanksJs = json_encode($aAllBanks); // 页面JS数据接口
//        $aMaintainBank = Mbank::checkMbank();
        /* 验证（以下验证不再需要 @20141104） */
        // 是否需要输入资金密码（用于资金密码框显示）
        // 是否至少绑定了一张银行卡
        // 系统是否有可用充值银行
        // 是否达到充值次数上限
        // return View::make($this->resourceView . '.netbank');
        $this->setVars(compact('oAllBanks', 'sAllBanksJs', 'bCheckUserBankCard', 'bSetFundPassword'));
        $this->setVars('is_tester',$oUser->is_tester);
        return $this->render();
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
        foreach ($oAllBanks as $k=>$oBank) {
               $oAllBanks[$k]->is_mbank = Mbank::checkMbank($oBank->id, Mbank::BANK_MODE_THIRD_PART);
        }    
        if (SysConfig::readValue('prohibited_withdraw')) {
            return $this->goBack('error', __('_user.prohibited-withdraw'));
        }
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
//         $aMaintainBank = Mbank::checkMbank();
        $this->setVars('is_tester',$oUser->is_tester);
        $this->setVars(compact('oAllBanks', 'sAllBanksJs', 'bSetFundPassword', 'fMinLoad', 'fMaxLoad'));
        return $this->render();
    }

    /**
     * 充值确认
     */
    public function confirm() {
//       $params = Input::all();
       $status = Mbank::isMaintainBank($this->params['bank'], $this->params['deposit_mode']);       //判断是否银行卡是否在维护中
       if ($status) {
           return $this->goBack('error', __('_deposit.deposit-error-00'));
       }
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
            return $this->goBack('error', __('_deposit.deposit-error-00'));
        }
        // 1 资金密码 
        if ($this->bCheckFundPassword && !$oUser->checkFundPassword($this->params['fund_password'])) {
            return $this->goBack('error', __('_deposit.wrong-fund-passwd'));
        }
        // 2 是否绑定银行卡
        // 3 当前银行是否可用
        $iDepositMode = $this->params['deposit_mode']; // 充值方式
        $oBank = Bank::find($this->params['bank']);
        $iPayMode = Bank::getPayMode($oBank->identifier, $iDepositMode);
        if (!$oBank || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
            return $this->goBack('error', __('_deposit.missing-bank'));
        }
        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD) { // 用户选择银行转账
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_BANK_CARD])) { // 当前银行是否支持银行卡转账
                return $this->goBack('error', __('_deposit.missing-bank'));
            }
            if ($this->params['amount'] < $oBank->min_load || $this->params['amount'] > $oBank->max_load) { // 金额超出范围
                return $this->goBack('error', __('_deposit.amount-out-range'));
            }
        }
        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART) { // 用户选择第三方充值
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_THIRD_PART])) { // 当前银行是否支持第三方充值
                return $this->goBack('error', __('_deposit.missing-bank'));
            }
            $fMinLoad = number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
            $fMaxLoad = number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
            if ($this->params['amount'] < $fMinLoad || $this->params['amount'] > $fMaxLoad) { // 金额超出范围
                return $this->goBack('error', __('_deposit.amount-out-range'));
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
            'pay_mode' => $iPayMode,
        ];
        $oUserDeposit = UserDeposit::createDeposit($aInitData); // 创建订单
        if (!$oUserDeposit) { // 生成订单失败
            return $this->goBack('error', __('_deposit.deposit-error-01'));
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

        $forefather_ids = $oUser->forefather_ids;
        $VipList =  Config::get('user_vip.users');
        $fatherVipList = Config::get('user_vip.parent_id');
        $isVip=false;

        if(!empty($forefather_ids) && !empty($fatherVipList)){
            $forefather_ids = explode(',',$forefather_ids);
            if(in_array($oUser->id,$fatherVipList)){
                $isVip = true;
            }else if(!empty(array_intersect($forefather_ids,$fatherVipList))){
                $isVip = true;;
            }
        }
        if( ($isVip || (!empty($VipList) && in_array($oUser->id,$VipList))) && ($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD)){
            $aResponse = $this->sendDeposit2MownecumForVip($aDepositApiData);
        }else{
            $aResponse = $this->sendDeposit2Mownecum($aDepositApiData);
        }

        try {
            if (empty($aResponse)) { // MC无响应
                throw new Exception(__('_deposit.deposit-error-02'));
            }
            if (!array_get($aResponse, 'status', 0)) { // MC主动返回错误
                throw new Exception(__('_deposit.deposit-error-03'));
            }
            $verifyResult = $this->verifyApiResponse($aResponse,$oUserDeposit);
            if (!$verifyResult) { // MC响应信息未通过接口验证
                throw new Exception(__('_deposit.deposit-error-04'));
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
                file_put_contents("/tmp/deposit_validError", $oUserDeposit->validationErrors,FILE_APPEND);
//               pr($oUserDeposit->validationErrors);
                throw new Exception(__('_deposit.deposit-error-05'));
            }
        
            //微信充值直接跳转到目标地址
            if($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART && $oApplyBank->mc_bank_id != 51){
                return Redirect::to($oUserDeposit->break_url);
            }
        } catch (Exception $e) {
            $oUserDeposit->setRefused(['error_msg' => array_get($aResponse, 'error_msg')]);
            return $this->goBack('error', $e->getMessage());
        }
        /* Step 4: 页面展示 */
        // return View::make($this->resourceView . '.confirm');
        $this->setVars(compact('oApplyBank', 'oCollectionBank', 'oUserDeposit', 'iDepositMode'));
        return $this->render();
    }

    /*     * *
     * sdpay 支付确认
     */

    public function confirmSdpay() {
        $oUser = UserUser::find(Session::get('user_id'));
        /* Step 1: 验证 */
        $aFormRules = [
            'bank' => 'required|numeric',
            'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'deposit_mode' => 'required|in:' . UserDeposit::DEPOSIT_MODE_SDPAY
        ];
        // 验证表单
        $validator = Validator::make($this->params, $aFormRules);
        if (!$validator->passes()) { // 表单未通过验证
            return $this->goBack('error', __('_deposit.deposit-error-00'));
        }
        //充值0 无意义
        if($this->params['amount']<=0){
            return $this->goBack('error', __('_deposit.amount_can_not_be_0'));
        }
        $sdpay_deposit_enable = SysConfig::readValue('sdpay_deposit_enable');
        if (!$sdpay_deposit_enable) {
            return $this->goBack('error', __('_deposit.missing-bank'));
        }
        //验证银行是否可用
        $oBank = Bank::find($this->params['bank']);
        $iDepositMode = $this->params['deposit_mode']; // 充值方式
        // 当前银行是否支持银行卡转账
        if (!$oBank->mode == UserDeposit::DEPOSIT_MODE_SDPAY) {
            return $this->goBack('error', __('_deposit.missing-bank'));
        }
        $day_deposit_amount_max = SysConfig::readValue('day_deposit_amount_max');
        $deposit_amount_min=SysConfig::readValue('deposit_amount_min');
        $single_deposit_amount_max = SysConfig::readValue('single_deposit_amount_max');
        $aAmounts=Deposit::getDepositAmountByDate(date("Y-m-d 00:00:00"),date("Y-m-d 23:59:59"),  Session::get('user_id'));
        $iTodayTotalAmount=array_sum(array_column($aAmounts,'real_amount'));
        // 金额超出范围
        if ($this->params['amount'] < $deposit_amount_min || $this->params['amount'] > $single_deposit_amount_max) {
            return $this->goBack('error', __('_deposit.amount-out-range'));
        }  
        
        //单日充值超限额
        if ($day_deposit_amount_max<$iTodayTotalAmount+$this->params['amount']) {
            return $this->goBack('error', __('_deposit.day-amount-out-range'));
        }

        /* Step 2: 创建新订单 */
        $CompanyOrderNum = UserDeposit::getDepositOrderNum(); // 生成订单号
//        echo $CompanyOrderNum;exit;
        $aInitData = [
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'is_tester' => $oUser->is_tester,
            'top_agent' => array_get(explode(',', $oUser->forefathers), 0),
            'bank_id' => $this->params['bank'],
            'amount' => $this->params['amount'],
            'company_order_num' => $CompanyOrderNum,
            'deposit_mode' => $iDepositMode,
            'status' => UserDeposit::DEPOSIT_STATUS_RECEIVED,
        ];
        $oUserDeposit = UserDeposit::createDeposit($aInitData); // 创建订单
        if (!$oUserDeposit) { // 生成订单失败
            return $this->goBack('error', __('_deposit.deposit-error-01'));
        }
        /* Step 3: 生成订单 */
        $aResponse = $this->sendDeposit2Sdpay($aInitData);
        //页面展示
        $this->setVars(compact('aResponse'));
        return $this->render();
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
        $this->writeLog(['url' => $sMcDepositUrl, 'date' => date('Y-m-d H:i:s'), 'request' => $aData, 'response' => $sResponse]);
        $aResponse = !empty($sResponse) ? json_decode($sResponse, true) : [];
        return $aResponse;
    }


    private function sendDeposit2MownecumForVip(array $aData){
        $sMcDepositUrl = SysConfig::readValue('mc_deposit_url_vip');
        $iCompanyId = SysConfig::readValue('mc_company_id_vip');
        $aData['company_id'] = $iCompanyId;
        $aData['key'] = UserDeposit::getApiKey($aData, UserDeposit::DEPOSIT_API_REQUEST);

        $oCurl = new MyCurl($sMcDepositUrl);
        $oCurl->setPost(http_build_query($aData));
        $oCurl->createCurl();
//        $oCurl->setTimeout(20);
        $oCurl->execute();
        $sResponse = $oCurl->__tostring();
        $this->writeLog(['url' => $sMcDepositUrl, 'date' => date('Y-m-d H:i:s'), 'request' => $aData, 'response' => $sResponse]);
        $aResponse = !empty($sResponse) ? json_decode($sResponse, true) : [];
        return $aResponse;
    }
    /**
     * 向Mownecum发送订单数据
     * @param array $aData 要发送的数据包
     * @return array
     */
    private function sendDeposit2Sdpay(array $aData) {
//todo
        $deposit_channel_description = SysConfig::readValue('deposit_channel_description');
        $str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<message>
  <cmd>" . Deposit::SDPAYREQUESTCMD . "</cmd>
  <merchantid>" . Deposit::SDPAYMERCHANTID . "</merchantid>
  <language>" . Deposit::SDPAYLANGUAGE . "</language>
  <userinfo>
    <order>" . $aData['company_order_num'] . "</order>
    <username>" . $aData['username'] . "</username>
    <money>" . $aData['amount'] . "</money>
    <unit>" . Deposit::SDPAYUNIT . "</unit>
    <time>".date("Y-m-d H:i:s")."</time>
    <remark>" . $deposit_channel_description . "</remark>
    <backurl>" . Deposit::SDPAYBACKURL . "</backurl>
  </userinfo>
</message>";

        $myencrypt = new SDPayEncrypt(Deposit::SDPAYKEY1, Deposit::SDPAYKEY2);
        $md5Str = md5($str . Deposit::SDPAYKEY3);
        $tempStr = $str . $md5Str;
        $aData['des'] = $myencrypt->encryptData($tempStr);

        $this->writeLog([ 'date' => date('Y-m-d H:i:s'), 'request' => $aData, 'xml' => $str],'/tmp/bomao_sdpay_deposit');
        return $aData;
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
    protected function writeLog($msg,$url='/tmp/bomao_deposit') {
        !is_array($msg) or $msg = var_export($msg, true);
        @file_put_contents($url, $msg . "\n", FILE_APPEND);
    }

    protected function beforeRender() {
        parent::beforeRender();
        $aPayMode = Bank::$aPayMode;
        Bank::translateArray($aPayMode);
        $aWeixinPayBank = $this->_getEnableWeixinPay();
        $this->setVars('aPayMode', $aPayMode);
        $this->setVars('bCheckFundPassword', $this->bCheckFundPassword);
        $this->setVars('aWeixinPayBank', $aWeixinPayBank);
        switch ($this->action) {
            case 'index':
                $this->setVars('reportName', 'depositApply');
                break;
        }
    }

    public function sdpay() {

        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
        $oBank = Bank::getSupportSdpayBank();
        //是否开启提款和充值
        if(!SysConfig::readValue('sdpay_deposit_enable')){
            return $this->goBack('error', __('_user.prohibited-withdraw'));
        }
        if (SysConfig::readValue('prohibited_withdraw')) {
            return $this->goBack('error', __('_user.prohibited-withdraw'));
        }


        //充值限额
        $fMinLoad = number_format(SysConfig::readValue('deposit_amount_min'));
        $fMaxLoad = number_format(SysConfig::readValue('single_deposit_amount_max'));
        $fDayMaxLoad = number_format(SysConfig::readValue('day_deposit_amount_max'));
        $iDepositFee = number_format(SysConfig::readValue('deposit_fee'));

        $this->setVars('is_tester',$oUser->is_tester);
        $this->setVars(compact('fMinLoad', 'bSetFundPassword', 'fMaxLoad', 'oBank','fDayMaxLoad','iDepositFee'));

        return $this->render();
    }
    public function caifutong() {

        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
        $oBank = Bank::getSupportCaiFuTongBank();
        //是否开启提款和充值
        if (SysConfig::readValue('prohibited_withdraw')) {
            return $this->goBack('error', __('_user.prohibited-withdraw'));
        }

        //充值限额
        $fMinLoad = number_format($oBank->min_load);
        $fMaxLoad = number_format($oBank->max_load);
        $fDayMaxLoad = number_format(SysConfig::readValue('caifutong_day_deposit_amount_max'));

        $this->setVars('is_tester',$oUser->is_tester);
        $this->setVars(compact('fMinLoad', 'bSetFundPassword', 'fMaxLoad', 'oBank','fDayMaxLoad'));

        return $this->render();
    }

    public function alipay(){
        if (!User::checkIsSafeUser(Session::get('user_id'))){
            return $this->goBack('error', '该充值功能未开启');
        }
        return $this->viewPage('alipay');
    }

    public function getAlipayQrCode(){
        if (!User::checkIsSafeUser(Session::get('user_id'))){
            return $this->goBack('error', '该充值功能未开启');
        }
        return $this->getQrCode('alipay');
    }

    public function weixin(){
        $aData = $this->_getEnableWeixinPay();
        if(empty($aData)){
            return $this->goBack('error', '该充值功能未开启');
        }
        return $this->viewPage('weixin');
    }

    public function getWeiXinQrCode(){
        return $this->getQrCode('weixin');
    }

    private function viewPage($page){
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码

        $fun = 'getSupport'.ucfirst($page);

        $oBank = Bank::$fun();
        if(!$oBank){
            return $this->goBack('error', '该充值功能未开启');
        }

        //是否开启提款和充值
        if (SysConfig::readValue('prohibited_withdraw')) {
            return $this->goBack('error', __('_user.prohibited-withdraw'));
        }

        //充值限额
        $fMinLoad = number_format($oBank->min_load);
        $fMaxLoad = number_format($oBank->max_load);
        $fDayMaxLoad = number_format(SysConfig::readValue($page.'_day_deposit_amount_max'));


        $this->setVars(compact('fMinLoad', 'bSetFundPassword', 'fMaxLoad', 'oBank','fDayMaxLoad'));
        $this->setVars('is_tester',$oUser->is_tester);
        $account = isset($_COOKIE[$page.'_account']) ? $_COOKIE[$page.'_account'] : '';
        $username = isset($_COOKIE[$page.'_username']) ? $_COOKIE[$page.'_username'] : '';
        $this->setVars('account',$account);
        $this->setVars('username',$username);

        return $this->render();
    }

    private function getQrCode($page){
        $oUser = UserUser::find(Session::get('user_id'));
        /* Step 1: 验证 */

        if($page == 'weixin') $iDepositModeRule = UserDeposit::DEPOSIT_MODE_THIRD_PART;
        elseif($page == 'alipay') $iDepositModeRule = UserDeposit::DEPOSIT_MODE_QRCODE;
        $aFormRules = [
            'bank' => 'required|numeric',
            'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'fund_password' => ($this->bCheckFundPassword ? 'required|' : '') . 'between:0, 60',
            'deposit_mode' => 'required|in:' . $iDepositModeRule,
            $page.'_account' => 'required'
        ];

        // 验证表单
        $validator = Validator::make($this->params, $aFormRules);

        if (!$validator->passes()) { // 表单未通过验证
            return $this->goBack('error', __('_deposit.deposit-error-00'));
        }


        $account = isset($this->params[$page.'_account']) ? $this->params[$page.'_account'] : '';
        $username = isset($this->params[$page.'_username']) ? $this->params[$page.'_username'] : '';

        setcookie($page.'_account',$account);
        setcookie($page.'_username',$username);
        $note = '';
        if($page == 'alipay' && preg_match('/^1[34578]\d{9}$/',$account)){
            $note = mb_substr($username,-1,1,"UTF-8").substr($account,0,3).substr($account,-4,4);
        }elseif($page == 'alipay' && preg_match('/^(\w+([-+.]\w+)*)(@\w+([-.]\w+)*\.\w+([-.]\w+)*)$/',$account,$match)){
            $note = mb_substr($username,-1,1,"UTF-8").substr($match[1],0,3).$match[3];
        }elseif($page == 'weixin'){
            $note = $account;
        }

        if ($note == '') { // 表单未通过验证
            return $this->goBack('error', '账号有误,请重试');
        }

        // 1 资金密码
        if ($this->bCheckFundPassword && !$oUser->checkFundPassword($this->params['fund_password'])) {
            return $this->goBack('error', __('_deposit.wrong-fund-passwd'));
        }
        // 2 是否绑定银行卡
        // 3 当前银行是否可用
        $iDepositMode = $this->params['deposit_mode']; // 充值方式

        $oBank = Bank::find($this->params['bank']);
        if (!$oBank || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
            return $this->goBack('error', __('_deposit.missing-bank'));
        }
       $iPayMode = Bank::getPayMode($oBank->identifier, $iDepositMode);
        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD) { // 用户选择银行转账
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_BANK_CARD])) { // 当前银行是否支持银行卡转账
                return $this->goBack('error', __('_deposit.missing-bank'));
            }
            if ($this->params['amount'] < $oBank->min_load || $this->params['amount'] > $oBank->max_load) { // 金额超出范围
                return $this->goBack('error', __('_deposit.amount-out-range'));
            }
        }

        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART) { // 用户选择第三方充值
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_THIRD_PART])) { // 当前银行是否支持第三方充值
                return $this->goBack('error', __('_deposit.missing-bank'));
            }
            $fMinLoad = number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
            $fMaxLoad = number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
            if ($this->params['amount'] < $fMinLoad || $this->params['amount'] > $fMaxLoad) { // 金额超出范围
                return $this->goBack('error', __('_deposit.amount-out-range'));
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
            'pay_mode' => $iPayMode
        ];

        $oUserDeposit = UserDeposit::createDeposit($aInitData); // 创建订单
        if (!$oUserDeposit) { // 生成订单失败
            return $this->goBack('error', __('_deposit.deposit-error-01'));
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
            'group_id' => 1, // 目前为空
            'web_url' => Request::server('HTTP_HOST'),
            'memo' => '1',
            'note' => $note,
            'note_model' => UserDeposit::DEPOSIT_NOTE_MODE_SELF, // 使用MOW附言
        ];

        $aResponse = $this->sendDeposit2Mownecum($aDepositApiData);
        try {
            if (empty($aResponse)) { // MC无响应
                throw new Exception(__('_deposit.deposit-error-02'));
            }
            if (!array_get($aResponse, 'status', 0)) { // MC主动返回错误
                throw new Exception(__('_deposit.deposit-error-03'));
            }
            if (!$this->verifyApiResponse($aResponse, $oUserDeposit)) { // MC响应信息未通过接口验证
                throw new Exception(__('_deposit.deposit-error-04'));
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
                file_put_contents("/tmp/deposit_validError", $oUserDeposit->validationErrors,FILE_APPEND);
//               pr($oUserDeposit->validationErrors);
                throw new Exception(__('_deposit.deposit-error-05'));
            }
        } catch (Exception $e) {
            $oUserDeposit->setRefused(['error_msg' => array_get($aResponse, 'error_msg')]);
            return $this->goBack('error', $e->getMessage());
        }
        /* Step 4: 页面展示 */
        // return View::make($this->resourceView . '.confirm');
        $this->setVars(compact('oApplyBank', 'oCollectionBank', 'oUserDeposit', 'iDepositMode', 'aResponse'));
        $this->setVars('is_tester',$oUser->is_tester);
        return $this->render();
    }

    public function confirmWeiXin(){
        $status = Mbank::isMaintainBank($this->params['bank'], $this->params['deposit_mode']);       //判断是否银行卡是否在维护中
        if ($status) {
            return $this->goBack('error', __('_deposit.deposit-error-00'));
        }
        header("Content-type: text/html; charset=utf-8");
        $oUser = UserUser::find(Session::get('user_id'));

        $iDepositModeRule = UserDeposit::DEPOSIT_MODE_THIRD_PART;

        $aFormRules = [
            'bank' => 'required|numeric',
            'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'fund_password' => ($this->bCheckFundPassword ? 'required|' : '') . 'between:0, 60',
            'deposit_mode' => 'required|in:' . $iDepositModeRule,
        ];

        // 验证表单
        $validator = Validator::make($this->params, $aFormRules);
        if (!$validator->passes()) { // 表单未通过验证
            return $this->goBack('error', __('_deposit.deposit-error-00'));
        }

        // 1 资金密码
        if ($this->bCheckFundPassword && !$oUser->checkFundPassword($this->params['fund_password'])) {
            return $this->goBack('error', __('_deposit.wrong-fund-passwd'));
        }

        // 2 是否绑定银行卡
        // 3 当前银行是否可用
        $iDepositMode = $this->params['deposit_mode']; // 充值方式
        $oBank = Bank::find($this->params['bank']);
        $iPayMode = Bank::getPayMode($oBank->identifier, $iDepositMode);
        if (!$oBank || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
            return $this->goBack('error', __('_deposit.missing-bank'));
        }

        if ($this->params['amount'] < $oBank->min_load || $this->params['amount'] > $oBank->max_load) { // 金额超出范围
            return $this->goBack('error', __('_deposit.amount-out-range'));
        }


        // 4 是否达到充值次数上限
//        $sRequestMethod = Request::method();
        /* Step 2: 创建新订单 */
        $CompanyOrderNum = UserDeposit::getDepositOrderNum(); // 生成订单号
        $aData = $this->_getEnableWeixinPay();
        $iDepositWay = array_rand($aData);
        if( empty($iDepositWay)){
             return $this->goBack('error', __('_deposit.deposit-error-01'));
        }
        if($iDepositWay == 'tonghuika'){
              $CompanyOrderNum = 't' . $CompanyOrderNum;
        }elseif($iDepositWay == 'youfu'){
              $CompanyOrderNum = 'y' . $CompanyOrderNum;
        }
        $aInitData = [
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'is_tester' => $oUser->is_tester,
            'top_agent' => array_get(explode(',', $oUser->forefathers), 0),
            'bank_id' => $this->params['bank'],
            'amount' => $this->params['amount'],
            'company_order_num' => $CompanyOrderNum,
            'deposit_mode' => $iDepositMode,
            'pay_mode' => $iPayMode,
        ];

        $oUserDeposit = UserDeposit::createDeposit($aInitData); // 创建订单

        if (!$oUserDeposit) { // 生成订单失败
            return $this->goBack('error', __('_deposit.deposit-error-01'));
        }

        /* Step 3: 向通汇发送新订单请求 */
        $oApplyBank = Bank::find($this->params['bank']);
        
    
//        $iDepositWay = 'youfu';
        switch($iDepositWay){
            case 'tonghuika':
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
                $aResponse = $this->makeParamsForTh($aDepositApiData);
                break;
            case 'youfu':
                    $aResponse = $this->_makeYouFuParams($this->params['amount'], $CompanyOrderNum);
                break;
        }
        $this->setVars(compact('aResponse'));
        $this->setVars(compact('iDepositWay'));
        return $this->render();
    }

    public function getClientIp()
    {
        $sReqCustomerId = null;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($sReqCustomerId != null)
            $ip = $sReqCustomerId;
        return $ip;
    }

    public function makeParamsForTh($aDepositApiData){
        $bankCode = 'WEIXIN';
        $orderNo = $aDepositApiData['company_order_num'];
        $orderAmount = $aDepositApiData['amount'];
        $productName = 'deposit';
        $productNum = 1;
        $referer = Sysconfig::readValue('REQ_REFERER');
//        $referer = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $sBackNotifyUrl = $referer.Sysconfig::readValue('BACK_NOTIFY_URL');
        $sPageNotifyUrl = $referer.Sysconfig::readValue('PAGE_NOTIFY_URL');
        $sDateTimeFormat = Sysconfig::readValue('DATE_TIME_FORMAT');
        $sCharset = Sysconfig::readValue('CHARSET');
        $iPayType = Sysconfig::readValue('PAY_TYPE');
        $iMerNo = Sysconfig::readValue('MER_NO');
        $customerIp = $this->getClientIp();
        $customerPhone = '12345678901';
        $receiveAddr = '12345678901';
        $returnParams = '0|EF9012AB21';
        $currentDate = (new DateTime())->format($sDateTimeFormat);

        $kvs = new KeyValues();
        $kvs->add(AppConstants::$INPUT_CHARSET, $sCharset);
        $kvs->add(AppConstants::$NOTIFY_URL, $sBackNotifyUrl);
        $kvs->add(AppConstants::$RETURN_URL, $sPageNotifyUrl);
        $kvs->add(AppConstants::$PAY_TYPE, $iPayType);
        $kvs->add(AppConstants::$BANK_CODE, $bankCode);
        $kvs->add(AppConstants::$MERCHANT_CODE, $iMerNo);
        $kvs->add(AppConstants::$ORDER_NO, $orderNo);
        $kvs->add(AppConstants::$ORDER_AMOUNT, $orderAmount);
        $kvs->add(AppConstants::$ORDER_TIME, $currentDate);
        $kvs->add(AppConstants::$PRODUCT_NAME, $productName);
        $kvs->add(AppConstants::$PRODUCT_NUM, $productNum);
        $kvs->add(AppConstants::$REQ_REFERER, $referer);
        $kvs->add(AppConstants::$CUSTOMER_IP, $customerIp);
        $kvs->add(AppConstants::$CUSTOMER_PHONE, $customerPhone);
        $kvs->add(AppConstants::$RECEIVE_ADDRESS, $receiveAddr);
        $kvs->add(AppConstants::$RETURN_PARAMS, $returnParams);

        $sign = $kvs->sign();

        $gatewayUrl = Sysconfig::readValue('GATEWAY_URL');
        URLUtils::appendParam($gatewayUrl, AppConstants::$INPUT_CHARSET, $sCharset, false);
        URLUtils::appendParam($gatewayUrl, AppConstants::$NOTIFY_URL, $sBackNotifyUrl, true, $sCharset);
        URLUtils::appendParam($gatewayUrl, AppConstants::$RETURN_URL, $sPageNotifyUrl, true, $sCharset);
        URLUtils::appendParam($gatewayUrl, AppConstants::$PAY_TYPE, $iPayType);
        URLUtils::appendParam($gatewayUrl, AppConstants::$BANK_CODE, $bankCode);
        URLUtils::appendParam($gatewayUrl, AppConstants::$MERCHANT_CODE, $iMerNo);
        URLUtils::appendParam($gatewayUrl, AppConstants::$ORDER_NO, $orderNo);
        URLUtils::appendParam($gatewayUrl, AppConstants::$ORDER_AMOUNT, $orderAmount);
        URLUtils::appendParam($gatewayUrl, AppConstants::$ORDER_TIME, $currentDate);
        URLUtils::appendParam($gatewayUrl, AppConstants::$PRODUCT_NAME, $productName, true, $sCharset);
        URLUtils::appendParam($gatewayUrl, AppConstants::$PRODUCT_NUM, $productNum);
        URLUtils::appendParam($gatewayUrl, AppConstants::$REQ_REFERER, $referer, true, $sCharset);
        URLUtils::appendParam($gatewayUrl, AppConstants::$CUSTOMER_IP, $customerIp);
        URLUtils::appendParam($gatewayUrl, AppConstants::$CUSTOMER_PHONE, $customerPhone);
        URLUtils::appendParam($gatewayUrl, AppConstants::$RECEIVE_ADDRESS, $receiveAddr, true, $sCharset);
        URLUtils::appendParam($gatewayUrl, AppConstants::$RETURN_PARAMS, $returnParams, true, $sCharset);
        URLUtils::appendParam($gatewayUrl, AppConstants::$SIGN, $sign);

        $post_data = array (
            AppConstants::$INPUT_CHARSET => $sCharset,
            AppConstants::$NOTIFY_URL => $sBackNotifyUrl,
            AppConstants::$RETURN_URL => $sPageNotifyUrl,
            AppConstants::$PAY_TYPE => $iPayType,
            AppConstants::$BANK_CODE => $bankCode,
            AppConstants::$MERCHANT_CODE => $iMerNo,
            AppConstants::$ORDER_NO => $orderNo,
            AppConstants::$ORDER_AMOUNT => $orderAmount,
            AppConstants::$ORDER_TIME => $currentDate,
            AppConstants::$PRODUCT_NAME => $productName,
            AppConstants::$PRODUCT_NUM => $productNum,
            AppConstants::$REQ_REFERER => $referer,
            AppConstants::$CUSTOMER_IP => $customerIp,
            AppConstants::$CUSTOMER_PHONE => $customerPhone,
            AppConstants::$RECEIVE_ADDRESS => $receiveAddr,
            AppConstants::$RETURN_PARAMS => $returnParams,
            AppConstants::$SIGN => $sign
        );

        return $post_data;

    }

    public function weixinPayReturn(){
        $merchantCode = Input::get(AppConstants::$MERCHANT_CODE);
        $notifyType = Input::get(AppConstants::$NOTIFY_TYPE);
        $orderNo = Input::get(AppConstants::$ORDER_NO);
        $orderAmount = Input::get(AppConstants::$ORDER_AMOUNT);
        $orderTime = Input::get(AppConstants::$ORDER_TIME);
        $returnParams = Input::get(AppConstants::$RETURN_PARAMS);
        $tradeNo = Input::get(AppConstants::$TRADE_NO);
        $tradeTime = Input::get(AppConstants::$TRADE_TIME);
        $tradeStatus = Input::get(AppConstants::$TRADE_STATUS);
        $sign = Input::get(AppConstants::$SIGN);

        $kvs = new KeyValues();
        $kvs->add(AppConstants::$MERCHANT_CODE, $merchantCode);
        $kvs->add(AppConstants::$NOTIFY_TYPE, $notifyType);
        $kvs->add(AppConstants::$ORDER_NO, $orderNo);
        $kvs->add(AppConstants::$ORDER_AMOUNT, $orderAmount);
        $kvs->add(AppConstants::$ORDER_TIME, $orderTime);
        $kvs->add(AppConstants::$RETURN_PARAMS, $returnParams);
        $kvs->add(AppConstants::$TRADE_NO, $tradeNo);
        $kvs->add(AppConstants::$TRADE_TIME, $tradeTime);
        $kvs->add(AppConstants::$TRADE_STATUS, $tradeStatus);
        $_sign = $kvs->sign();

        if ($_sign == $sign)
        {
            if ($tradeStatus == "success")
            {
                $tradeResult = "支付成功！";

                $oDeposit = Deposit::where('company_order_num', $orderNo)->first();

                if($merchantCode != Sysconfig::readValue('MER_NO') || $oDeposit->amount != $orderAmount){
                    $tradeResult = '不合法数据';
                    return Redirect::route('user-recharges.index')->with('error', $tradeResult);
                }

                if(!$oDeposit){
                    $tradeResult = '订单不存在';
                    return Redirect::route('user-recharges.index')->with('error', $tradeResult);
                }

                if($oDeposit->status == Deposit::DEPOSIT_STATUS_NEW){
                    $oUser = User::find($oDeposit->user_id);
                    $oAccount = Account::getAccountInfoByUserId($oDeposit->user_id);
                    Account::lock($oUser->account_id, $iLocker);
                    DB::connection()->beginTransaction();
                    try{
                        $bSuccessful = $oDeposit->setReceived([
                            'real_amount' => $oDeposit->amount,
                            'fee' => 0,
                            'pay_time' => $tradeTime,
                        ]);
                        // 更新订单状态
                        $bSuccessful = $oDeposit->setSuccess([
                            'real_amount' => $oDeposit->amount,
                            'fee' => 0,
                            'pay_time' => $tradeTime,
                        ]);


                        if(!$bSuccessful) { // 是否更新状态成功
                            throw new Exception('UPDATE STATUS ERROR');
                        }
                        // 增加游戏币
                        $dDepositDate = date('Y-m-d');
                        $aExtraData = [
                            'note' => $oDeposit->deposit_mode == Deposit::DEPOSIT_MODE_THIRD_PART
                                ? Deposit::$aDepositMode[Deposit::DEPOSIT_MODE_THIRD_PART] : '微信',
                        ];
                        $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_DEPOSIT, $oDeposit->amount, $aExtraData);

                        // 添加状态是否成功
                        if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL){
                            throw new Exception('ADD COIN ERROR');
                        }
                    } catch (Exception $e) {
                        DB::connection()->rollback();
                        Account::unlock($oUser->account_id, $iLocker);
                        $oDeposit->setAddFail();
                    }
                    DB::connection()->commit();
                    Account::unlock($oUser->account_id, $iLocker);
                }else{
                    $tradeResult = '订单已处理';
                }
            }
            else
            {
                $tradeResult = "支付失败";
            }
        }
        else
        {
            $tradeResult = "不合法数据";
        }
        return Redirect::route('user-recharges.index')->with('error', $tradeResult);
    }

    private function _makeYouFuParams($amount, $order_no){
            $data['MER_NO']          = SysConfig::readValue('YOUFU_MER_NO');//charset
            $data['VERSION']          = SysConfig::readValue('YOUFU_VERSION');//VERSION
            $data['INPUT_CHARSET']   = SysConfig::readValue('YOUFU_CHARSET');//charset
            $data['RETURN_URL']      = SysConfig::readValue('YOUFU_BACK_NOTIFY_URL');//回调地址
            $data['NOTIFY_URL']      = SysConfig::readValue('YOUFU_PAGE_NOTIFY_URL');;//通知地址
            $data['BANK_CODE']       = 'WEIXIN';
            $data['ORDER_NO']        = $order_no;
            $data['ORDER_AMOUNT']    = number_format($amount, 2, '.', '');
            $data['PRODUCT_NAME']    = 'deposit';
            $data['PRODUCT_NUM']     = 1;
            $data['REFERER']         = SysConfig::readValue('YOUFU_REFERER');
            $data['CUSTOMER_IP']     = $this->getClientIp();
            $data['CUSTOMER_PHONE']  = '';
            $data['RECEIVE_ADDRESS'] = '';
            $data['RETURN_PARAMS']   = '';
            ksort($data);//参数排序
            $url = '';
            foreach($data as $key => $val){
                $url .= $key.'='.$val.'&';
            }
            $hmacstr = $url . 'KEY=' . SysConfig::readValue('YOUFU_MER_KEY');//结尾带上KEY
            $data['SIGN']= md5($hmacstr);//MD5加密生成签名
            return $data;
    }
    
    private function _getEnableWeixinPay(){
            $aData = [];
            if(SysConfig::readValue('is_enable_tonghuika')){
              $aData['tonghuika'] = 1;
            }
            if(SysConfig::readValue('is_enable_youfu')){
              $aData['youfu'] = 1;
            }
            return $aData;
    }
}
