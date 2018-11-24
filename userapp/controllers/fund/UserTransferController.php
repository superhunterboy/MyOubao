<?php

class UserTransferController extends UserBaseController {

    protected $resourceView = 'centerUser.transfer';
    protected $modelName = 'User';
    protected $accountLocker = null;

    public function index($iAccountUserId = null) {
        $iUserId = Session::get('user_id');
        /**if (Session::get('user_prize_group')!=1962) {
            return $this->goBack('error', __('_user.transfer-now-allowed'));
        }*/
        $oUser = UserUser::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            return $this->goBack('error', __('_user.transfer-now-allowed'));
        }

        if (!$iUserCardNum = UserUserBankCard::getUserBankCardsCount()) {
            $this->view = 'centerUser.userBankCard.noCard';
            return $this->render();
        }
         $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId($iUserId);
        if(empty($oRes)){
            return Redirect::route('security-questions.index');  
        }
        $sUsername = '';
        if ($iAccountUserId) {
            $sUsername = User::where('id', $iAccountUserId)->pluck('username');
        }
        $oUserBandCard = new UserUserBankCard;
        $aBankCards = $oUserBandCard->getUserCardsInfo($iUserId, ['id', 'account', 'account_name', 'bank']);
        /*
          $oBanks = Bank::getSupportCardBank();
          foreach ($oBanks as $key => $value) {
          $aBanks[$value->id] = $value->name;
          } */
        $oAccount = Account::getAccountInfoByUserId($iUserId);
        $this->setVars('oAccount', $oAccount);
        $this->setVars(compact('aBankCards', 'sUsername'));

        return parent::render();
    }

    /**
     * 转账给下级
     */
    public function transferToSub() {
        if (SecurityUserAnswer::checkReferer() && SecurityUserAnswer::checkSecurity()){
            $aCallbackData = SecurityUserAnswer::getCallbackSession();
            SecurityUserAnswer::destroyCallbackSession();
            $aInputData = $aCallbackData['data'];
            Input::merge($aInputData);
            $this->params = array_merge($this->params, Input::all());
            Redirect::getUrlGenerator()->getRequest()->headers->set('referer', $aCallbackData['referer']);
            $bFromSecurity = true;
        }
        
        $userId = Session::get('user_id');

        $fAmount = Input::get('amount');
        $iCardId = $this->params['card_id'];
        $subUsername = Input::get('username');
        $sFundPassword = $this->params['fund_password'];
        $cardNumber = str_replace(' ', '', $this->params['card_number']);

        $oUser = User::find($userId);
        $oSubUser = User::findUser($subUsername);

        if (empty($subUsername) || empty($oSubUser)) {
            return Redirect::back()->withInput()->with('error', '请填写有效的用户名。');
        }
        
        $forefatherIds = explode(',', $oSubUser->forefather_ids);
        
        //资金已冻结
        if ($oUser->blocked == User::BLOCK_FUND_OPERATE) {
            return Redirect::back()->withInput()->with('error', '您的资金已冻结。');
        }
        if ($oSubUser->blocked == User::BLOCK_FUND_OPERATE) {
            return Redirect::back()->withInput()->with('error', $oSubUser->username . '的资金已冻结。');
        }
        if (empty($fAmount) || !is_numeric($fAmount) || $fAmount <= 0) {
            return Redirect::back()->withInput()->with('error', '请填写有效的金额。');
        }
        if (empty($iCardId) || !is_numeric($cardNumber) || empty($sFundPassword)) {
            return Redirect::back()->withInput()->with('error', '银行账号信息不能为空。');
        }
        if ($oSubUser->id == $userId) {
            return Redirect::back()->withInput()->with('error', '不能给自己转账。');
        }

        if (empty($forefatherIds) || !in_array($userId, $forefatherIds)) {
            return Redirect::back()->withInput()->with('error', '此用户不属于你的下级。');
        }
        /*        if($oSubUser->parent_id != $userId || empty($oSubUser->parent_id)){
          return Redirect::back()->withInput()->with('error', '此用户不属于你的下级。');
          } */
        if (empty($fAmount) || !is_numeric($fAmount)) {
            return Redirect::back()->withInput()->with('error', '请填写有效的金额。');
        }
        if (Account::getAccountInfoByUserId($userId)->available < $fAmount) {
            return Redirect::back()->withInput()->with('error', '转账金额不能超过您的可用金额。');
        }
        if (!$oUser->checkFundPassword($sFundPassword)) {
            return Redirect::back()->withInput()->with('error', '资金密码错误。');
        }
        //出现安全验证的条件：1、单比转账金额大于1000。  2、上次转账时的ip和本次ip不一致  3、日累计转账金额大于2000
        if (!isset($bFromSecurity) || empty($bFromSecurity)){
            if ($fAmount > 1000) {
                return SecurityUserAnswer::Redirect();
            }
            $oTrans = Transaction::getClientLastIpByUserId($userId);
            if(!empty($oTrans) && $oTrans->ip != get_client_ip()){
                return SecurityUserAnswer::Redirect();
            }
            $iTotalAmount = Transaction::getDayTransAmountByUserId($userId);
            if(empty($iTotalAmount)){
                $iTotalAmount = 0;
            }
            if(($iTotalAmount + $fAmount) > 2000){
                return SecurityUserAnswer::Redirect();
            }
        }
        //出现安全验证的条件：1、单比转账金额大于100。  2、一自然日累计转账金额大于500

        /* if($fAmount > 100 || UserTransaction::getUserTransferOut($userId, Carbon::today()->toDateTimeString(), Carbon::tomorrow()->toDateTimeString()) > 500){

          } */

        //$oBankCard = UserUserBankCard::getUserBankCardAccount($cardNumber);
        $oBankCard = UserUserBankCard::find($iCardId);

        if (empty($oBankCard) || $oBankCard->account != $cardNumber || $oBankCard->user_id != $userId) {
            return Redirect::back()->withInput()->with('error', '银行账号信息有误。');
        }
        $oAccount = Account::lock($oUser->account_id, $iAccountLocker);
        $oSubAccount = Account::lock($oSubUser->account_id, $iSubAccountLocker);
        if (!$oAccount || !$oSubAccount) {
            $aOaccounts = Account::getAccountInfoByUserId([$oUser->id, $oSubUser->id]);
            $oAccount = $aOaccounts[0];
            $oSubAccount = $aOaccounts[1];
            //  return Redirect::back()->withInput()->with('error', '目前不能转帐，请稍后再试。');
        }
        DB::connection()->beginTransaction();
        $aExtraData = ['related_user_id' => $oSubUser->id, 'related_user_name' => $oSubUser->username, 'client_ip' => get_client_ip(), 'proxy_ip' => get_proxy_ip()];
        $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_TRANSFER_OUT, $fAmount, $aExtraData);
        
        $aExtraData = ['related_user_id' => $oUser->id, 'related_user_name' => $oUser->username, 'client_ip' => get_client_ip(), 'proxy_ip' => get_proxy_ip()];
        $iSubReturn = Transaction::addTransaction($oSubUser, $oSubAccount, TransactionType::TYPE_TRANSFER_IN, $fAmount, $aExtraData);


        if ($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL && $iSubReturn == Transaction::ERRNO_CREATE_SUCCESSFUL) {
            DB::connection()->commit();
            $oReturn = true;
        } else {
            DB::connection()->rollback();
            $oReturn = false;
        }

        Account::unLock($oUser->account_id, $iAccountLocker, false);
        Account::unLock($oSubUser->account_id, $iSubAccountLocker, false);
        if($oReturn)
        {
        	return Redirect::back()->withInput()->with('success', '转账成功。');
        }
        else
        {
        	return Redirect::back()->withInput()->with('error', '转账失败。');
        }
    }

}
