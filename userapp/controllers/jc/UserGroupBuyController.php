<?php

namespace JcController;

use JcModel\JcGroupBuy;

class UserGroupBuyController extends \UserBaseController {
    
    protected $errorFiles = [
        'jc',
        'system',
        'fund',
        'account',
    ];
//    
//    protected $resourceView = 'jc.bet';
//    protected $customViewPath = 'jc';
    protected $modelName = '\JcModel\JcGroupBuy';
    protected $accountLocker = null;
    protected $dbThreadId = null;
    
    protected $oLottery = null;
    
    protected function beforeRender() {
        parent::beforeRender();
        
        if (isset($this->viewVars['oJcLottery'])){
            $oJcLottery = $this->viewVars['oJcLottery'];
            $this->viewVars['aMenuList'] = \JcModel\JcMethodGroup::getMenuByLotteryId($oJcLottery->id);
        }
        if (!isset($this->viewVars['sTabKey'])){
            $this->viewVars['sTabKey'] = 'groupbuy';
        }
    }

    public function groupbuy($sLotteryKey, $sMethodGroupKey = null){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $iLotteryId = $oJcLottery->id;
        $aMethodGroup = \JcModel\JcMethodGroup::getBasicByLotteryId($iLotteryId);
        if (isset($sMethodGroupKey)){
            $oFilterMethodGroup = \JcModel\JcMethodGroup::getBasicByIdentifier($iLotteryId, $sMethodGroupKey);
            if (empty($oFilterMethodGroup)){
                \App::abort(404);
            }
        }
        $aInputData = \Input::only('status', 'nickname', 'searchDate');
        if (empty($aInputData['searchDate'])){
            $aInputData['searchDate'] = 30;
        }
        $aConditions = [];
        foreach($aInputData as $sKey => $sVal){
            if (isset($sVal) && $sVal !== ''){
                if ($sKey == 'searchDate'){
                    switch ($sVal){
                        case 30:
                        case 7:
                        case 3:
                            $iStartDate = strtotime("-{$sVal} days");
                            $iEndDate = time();
                            break;
                        default :
                            $iStartDate = $iEndDate = strtotime($sVal);
                            break;
                    }
                    $sStartDate = date('Y-m-d 00:00:00', $iStartDate);
                    $sEndDate = date('Y-m-d 23:59:59', $iEndDate);
                    $aConditions['created_at'] = ['between', [$sStartDate, $sEndDate]];
                }else if ($sKey == 'nickname'){
                    $aUsers = \User::getByFuzzyNickName($sVal, ['id']);
                    $aUserIds = [];
                    foreach($aUsers as $oUser){
                        $aUserIds[] = $oUser->id;
                    }
                    $aConditions['user_id'] = count($aUserIds) > 0 ? ['in', $aUserIds] : null;
                }else{
                    $aConditions[$sKey] = $sVal;
                }
            }
        }
        
        if (isset($oFilterMethodGroup)){
            $aConditions['method_group_id'] = $oFilterMethodGroup->id;
        }
        $datas = \JcModel\JcUserGroupBuy::getListByLotteryIdAndStatus($iLotteryId, $aConditions);

        $aUserIds = [];
        foreach($datas as $oUserGroupBuy){
            $aUserIds[$oUserGroupBuy->user_id] = $oUserGroupBuy->user_id;
        }
        $aUserGrowth = [];
        if (count($aUserIds) > 0){
                $aUserGrowthList = \JcModel\JcUserGrowth::getTotalGrowthByLotteryIdAndUserIds($iLotteryId, $aUserIds);
            foreach($aUserGrowthList as $oUserGrowth){
                $aUserGrowth[$oUserGrowth->user_id] = $oUserGrowth;
            }
        }
        
        $aStatus = [
            \JcModel\JcUserGroupBuy::STATUS_NORMAL => 'normal',
            \JcModel\JcUserGroupBuy::STATUS_AVAILABLE => 'avaliable',
            \JcModel\JcUserGroupBuy::STATUS_DROPED => 'droped',
        ];
        \JcModel\JcUserGroupBuy::translateArray($aStatus);
        
        $aDates = [
            30 => '近30天',
            7 => '近7天',
            3 => '近3天',
        ];
        for($i=0;$i<30;$i++){
            $sDate = date('Y-m-d', strtotime("-{$i} days"));
            $aDates[$sDate] = $sDate;
        }
        
        $this->setVars(
                compact(
                        'datas', 'oJcLottery', 'sLotteryKey', 'sMethodGroupKey', 'aMethodGroup','aConditions','aStatus','aUserGrowth','aDates'
                )
        );
        $this->view = 'jc.groupbuy.list';
        $this->render();
    }
    
    public function yutou($sLotteryKey){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $iLotteryId = $oJcLottery->id;
        if (\Request::method() == 'POST'){
            $iUserId = \Session::get('user_id');
            $oUser = \User::find($iUserId);
            $oAccount = \Account::lock($oUser->account_id, $this->accountLocker);
            if (empty($oAccount)) {
                $this->halt(false, 'jc-error-bet-failed', \Account::ERRNO_LOCK_FAILED);
            }
            
            \DB::beginTransaction();
            $aData = [
                'lottery_id' => $iLotteryId,
                'user_id' => $oUser->id,
                'username' => $oUser->username,
                'fee_rate' => \Input::get('fee_rate'),
                'amount' => \Input::get('amount'),
                'buy_amount' => \Input::get('buy_amount'),
                'guarantee_amount' => \Input::get('guarantee_amount'),
                'show_type' => \Input::get('show_type'),
                //todo 后面做预投时再完善此部分功能
                //合买对象
            ];
            $oGroupBuy = new \JcModel\JcUserGroupBuy($aData);
            $oGroupBuy->setUser($oUser);
            $oGroupBuy->setAccount($oAccount);
            if ($oGroupBuy->addGroupBuy() == \JcModel\JcUserGroupBuy::ERRNO_SUCCESSED){
                \DB::commit();
                $this->halt(true, 'info',  null, $aSuccessedBets, $aFailedBets, $aResponseData);
            }
            \DB::rollback();
            $this->halt(false, 'jc-error-follow', \JcModel\JcUserGroupBuy::ERRNO_SAVE_FAILED);
        }
        $this->setVars(
                compact(
                        'oJcLottery'
                )
        );
        
        $this->view = 'jc.groupbuy.add';
        $this->render();
    }
    
    public function follow($iGroupId){
        $oGroupBuy = \JcModel\JcUserGroupBuy::find($iGroupId);
        if (empty($oGroupBuy)){
            \App::abort(404);
        }
        if ($oGroupBuy->bet_id > 0){
            $oBet = \JcModel\JcUserBet::find($oGroupBuy->bet_id);
            if (empty($oBet)){
                \App::abort(404);
            }
        }

        $iUserId = \Session::get('user_id');

        $oUser = \User::find($iUserId);

        //判断是否有权限访问合买
        if($oGroupBuy->allow_type == \JcModel\JcUserGroupBuy::ALLOW_TYPE_CHILD) {
            if(!($oGroupBuy->user_id == $iUserId || $oGroupBuy->user_id == $oUser->parent_id)){
                return $this->goBack('error','你没有权限访问该合买');
            }
        }

        if (\Request::method() == 'POST'){
            $fAmount = \Input::get('amount');

            if ($oGroupBuy->amount < $oGroupBuy->buy_amount + $fAmount){
                $this->halt(false, 'jc-error-follow', \JcModel\JcUserGroupBuy::ERRNO_OVER_TOTAL_AMOUNT);
            }
            if ($oGroupBuy->status != \JcModel\JcGroupBuy::STATUS_NORMAL){
                $this->halt(false, 'jc-error-follow', \JcModel\JcUserGroupBuy::ERRNO_SAVE_FAILED);
            }
            if (isset($oBet) && $oBet->isEnd){
                $this->halt(false, 'jc-error-follow', \JcModel\JcUserGroupBuy::ERRNO_SAVE_FAILED);
            }
            $iUserId = \Session::get('user_id');
            $oUser = \User::find($iUserId);
            $oAccount = \Account::lock($oUser->account_id, $this->accountLocker);
            if (empty($oAccount)) {
                $this->halt(false, 'jc-error-follow', \Account::ERRNO_LOCK_FAILED);
            }
            \DB::beginTransaction();
            $bSucc = $oGroupBuy->incrementBuyAmount($fAmount);
            if ($bSucc){
                $aData = array_merge($oGroupBuy->getAttributes(), [
                    'user_id' => $iUserId,
                    'account_id' => $oUser->account_id,
                    'username' => $oUser->username,
                    'bet_id' => $oGroupBuy->bet_id,
                    'group_id' => $oGroupBuy->id,
                    'amount' => $fAmount,
                    'type' => \JcModel\JcUserProject::TYPE_GROUP_BUY_FOLLOW,
                    'buy_type' => \JcModel\JcUserProject::BUY_TYPE_FOLLOW,
                ]);
                unset($aData['created_at']);
                unset($aData['updated_at']);
                $oProject = new \JcModel\JcProject($aData);
                $oProject->setUser($oUser);
                $oProject->setAccount($oAccount);
                $bSucc = $oProject->addProject() == \JcModel\JcProject::ERRNO_SUCCESSFUL;
                if ($bSucc && $oGroupBuy->buy_amount >= $oGroupBuy->amount){
                    $bSucc = $oGroupBuy->setToAvailable();
                }
            }
            if (!$bSucc){
                $iRes = \JcModel\JcUserGroupBuy::ERRNO_SAVE_FAILED;
            }
            $bSucc ? \DB::commit() : \DB::rollback();
            
            if ($bSucc){
                $this->halt(true, 'info',  null, $aSuccessedBets, $aFailedBets, $aResponseData);
            }else{
                $this->halt(false, 'jc-error-follow', $iRes);
            }
            
            return ;
        }
        $datas = [];
        $oJcLottery = \JcModel\JcLotteries::find($oGroupBuy->lottery_id);
        $iLotteryId = $oJcLottery->id;
        $iUserId = \Session::get('user_id');
        if (isset($oBet)){
            $datas = $oBet->getBetMatchData();
        }
        
        $oMethodGroup = \JcModel\JcMethodGroup::find($oGroupBuy->method_group_id);
        
        $oAccount = \Account::find(\Session::get('account_id'));
        
        $aProjects = \JcModel\JcUserProject::getFollowListByGroupId($iGroupId);
        
        $aUserExtra = [
            'lottery_id' => $iLotteryId,
            'user_id' => $oGroupBuy->user_id,
        ];
        $oUserExtra = new \JcModel\JcUserExtra($aUserExtra);
        
        $aIdentifiers = explode(',', $oBet->game_extra);
        $aWayList = \JcModel\JcWay::getWayByLotteryIdAndIdentifiers($iLotteryId, $aIdentifiers);
        
        $bDisplayBet = $oGroupBuy->checkDisplayBet();
        
        $iMaxGuaranteeCount = 3;
        
        $this->setVars(
            compact(
                'datas','oJcLottery','oUserExtra','oAccount','oGroupBuy','oBet','aProjects','bDisplayBet','aWayList','oMethodGroup','iMaxGuaranteeCount'
            )
         );
        
        $this->view = 'jc.groupbuy.follow';
        $this->render();
    }
    
    public function follow_list($iGroupId){
        $datas = [];
        $oGroupBuy = \JcModel\JcUserGroupBuy::find($iGroupId);
        if (empty($oGroupBuy)){
            \App::abort(404);
        }
        $iUserId = \Input::get('user_id');
        $aConditions = [];
        if (isset($iUserId)){
            if ($iUserId != \Session::get('user_id')){
                \App::abort(404);
            }else{
                $aConditions['user_id'] = $iUserId;
            }
        }
        $aProjects = \JcModel\JcUserProject::getFollowListByGroupId($oGroupBuy->id, $aConditions);
        
        $this->setVars(
            compact(
                'datas','oGroupBuy','aProjects'
            )
         );
        
        $this->view = 'jc.groupbuy.follow_list';
        $this->render();
    }
    
    public function drop($iGroupId){
        $oGroupBuy = \JcModel\JcUserGroupBuy::find($iGroupId);
        if (empty($oGroupBuy)){
            $this->halt(false, 'jc-error-drop', \JcModel\JcUserGroupBuy::ERRNO_GROUP_BUY_IS_NOT_EXISTS);
        }
        $fRate = \SysConfig::readValue('jc_group_buy_limit_drop');
        if ($oGroupBuy->buy_amount >= $oGroupBuy->amount * $fRate){
            $this->halt(false, 'jc-error-drop', \JcModel\JcUserGroupBuy::ERRNO_DROP_FAIL_OVER_LIMIT);
        }
        if (!$oGroupBuy->checkDrop()){
            $this->halt(false, 'jc-error-drop', \JcModel\JcUserGroupBuy::ERRNO_DROP_FAILED);
        }
        \DB::beginTransaction();
        $bSucc = $oGroupBuy->doDrop();
        if ($bSucc){
            \DB::commit();
            $this->halt(true, 'info',  null, $aSuccessedBets, $aFailedBets, $aResponseData);
        }else{
            \DB::rollback();
            $this->halt(false, 'jc-error-drop', \JcModel\JcUserGroupBuy::ERRNO_DROP_FAILED);
        }
    }
    
    public function drop_detail($iProjectId){
        $oProject = \JcModel\JcUserProject::find($iProjectId);
        if (empty($oProject)){
            $this->halt(false, 'jc-error-drop-detail', \JcModel\JcUserProject::ERRNO_PROJECT_IS_NOT_EXISTS);
        }
        $oGroupBuy = \JcModel\JcUserGroupBuy::find($oProject->group_id);
        if (empty($oGroupBuy)){
            $this->halt(false, 'jc-error-drop-detail', \JcModel\JcUserGroupBuy::ERRNO_GROUP_BUY_IS_NOT_EXISTS);
        }
        $oProject->userGroupBuy = $oGroupBuy;
        if (!$oProject->checkDrop()){
            $this->halt(false, 'jc-error-drop-detail', \JcModel\JcUserGroupBuy::ERRNO_DROP_FAILED);
        }
        \DB::beginTransaction();
        $oUser = \User::find($oProject->user_id);
        $oAccount = \Account::lock($oUser->account_id, $this->accountLocker);
        if (empty($oAccount)){
            $this->halt(false, 'jc-error-drop-detail', \Account::ERRNO_LOCK_FAILED);
        }
        $oProject->setUser($oUser);
        $oProject->setAccount($oAccount);
        $bSucc = $oProject->doDrop();
        if ($bSucc){
            \DB::commit();
            $this->halt(true, 'info',  null, $aSuccessedBets, $aFailedBets, $aResponseData);
        }else{
            \DB::rollback();
            $this->halt(false, 'jc-error-drop-detail', \JcModel\JcUserGroupBuy::ERRNO_DROP_FAILED);
        }
    }
    
    public function zj($sLotteryKey, $iUserId, $sMethodGroupKey = null){
        $oJcLottery = \JcModel\JcLotteries::getByLotteryKey($sLotteryKey);
        if (empty($oJcLottery)){
            \App::abort(404);
        }
        $iLotteryId = $oJcLottery->id;
        $oUser = \User::find($iUserId);
        if (empty($oUser)){
            \App::abort(404);
        }
        if (isset($sMethodGroupKey)){
            $oFilterMethodGroup = \JcModel\JcMethodGroup::getBasicByIdentifier($iLotteryId, $sMethodGroupKey);
            if (empty($oFilterMethodGroup)){
                \App::abort(404);
            }
        }
        $aMethodGroup = \JcModel\JcMethodGroup::getBasicByLotteryId($iLotteryId);
        $aConditions = [];
        if (isset($oFilterMethodGroup)){
            $aConditions['method_group_id'] = $oFilterMethodGroup->id;
        }
        $datas = \JcModel\JcUserBet::getZjListByLotteryIdAndUserId($iLotteryId, $iUserId, $aConditions);

        $aUserExtraData = ['lottery_id' => $iLotteryId, 'user_id' => $iUserId];
        if (isset($oFilterMethodGroup)){
            $aUserExtraData['method_group_id'] = $oFilterMethodGroup->id;
        }
        $oUserExtra = new \JcModel\JcUserExtra($aUserExtraData);
        
        $this->setVars(
            compact(
                'datas', 'oJcLottery', 'sLotteryKey', 'sMethodGroupKey', 'aMethodGroup', 'oUser','oUserExtra'
            )
         );
        
        $this->view = 'jc.groupbuy.zj';
        $this->render();
    }
    
    public function append($iGroupId){
        $oGroupBuy = \JcModel\JcUserGroupBuy::find($iGroupId);
        if (empty($oGroupBuy)){
            \App::abort(404);
        }
        $fGuaranteeAmount = intval(\Input::get('guarantee_amount'));
        
        if ($oGroupBuy->status != \JcModel\JcGroupBuy::STATUS_NORMAL){
            $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcUserGroupBuy::ERRNO_SAVE_FAILED);
        }
        $iMaxGuaranteeCount = 3;
        if ($oGroupBuy->guarantee_count >= $iMaxGuaranteeCount){
            $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcUserGroupBuy::ERRNO_GUARANTEE_OVER_COUNT);
        }
        if ($fGuaranteeAmount < 1){
            $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcUserGroupBuy::ERRNO_GUARANTEE_AMOUNT);
        }
        if ($oGroupBuy->guarantee_amount + $fGuaranteeAmount > $oGroupBuy->amount){
            $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcUserGroupBuy::ERRNO_GUARANTEE_AMOUNT);
        }
        if ($oGroupBuy->guarantee_amount == 0){
            $fLimitRate = \SysConfig::readValue('jc_group_buy_min_guarantee_rate');
            $fLimitAmount = $oGroupBuy->amount * $fLimitRate;
            if ($fGuaranteeAmount < $fLimitAmount){
                $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcUserGroupBuy::ERRNO_GUARANTEE_AMOUNT);
            }
        }
        $oUser = \User::find($oGroupBuy->user_id);
        $oAccount = \Account::lock($oUser->account_id, $this->accountLocker);
        if (empty($oAccount)){
            $this->halt(false, 'jc-error-append-guarantee', \Account::ERRNO_LOCK_FAILED);
        }
        if ($oAccount->available < $fGuaranteeAmount){
            $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcBet::ERRNO_BET_ERROR_LOW_BALANCE);
        }
        $oProject = \JcModel\JcUserProject::find($oGroupBuy->project_id);
        $oProject->setUser($oUser);
        $oProject->setAccount($oAccount);
        
        \DB::beginTransaction();
        $bSucc = $oGroupBuy->appendGuarantee($fGuaranteeAmount);
        if ($bSucc){
            $bSucc = $oProject->freezeForGuarantee(['amount' => $fGuaranteeAmount]);
        }
        if (!$bSucc){
            \DB::rollback();
            $this->halt(false, 'jc-error-append-guarantee', \JcModel\JcUserGroupBuy::ERRNO_SAVE_FAILED);
        }else{
            \DB::commit();
            $this->halt(true, 'info',  null, $aSuccessedBets, $aFailedBets, $aResponseData);
        }
    }
    
    public function __destruct() {
        if ($this->accountLocker){
            \Account::unlock(\Session::get('account_id'), $this->accountLocker);
        }
        
        parent::__destruct();
    }
}
