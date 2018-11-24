<?php
class UserPrizeSetController extends AdminBaseController
{
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'UserPrizeSet';

    protected $resourceView = 'default';
    protected $customViewPath = 'userPrizeSet';
    protected $customViews = [
        'agentDistributionList',
        'agentPrizeGroupList',
        'setPrizeGroupForAgent'
    ];

    private $topAgentBoundPrizeGroup = [1961, 1962];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $aLotteries = & Lottery::getTitleList();
//        pr($aLotteries);
//        exit;
        $oPrizeGroup = new PrizeGroup;
        $aPrizeGroups = $oPrizeGroup->getValueListArray(PrizeGroup::$titleColumn,['series_id' => ['=', 1]], [PrizeGroup::$titleColumn => 'asc'], true);
        $this->setVars(compact('aLotteries','aPrizeGroups'));

        // switch ($this->action) {
        //     case 'agentPrizeGroupList':
        //         $aUserTypes = UserPrizeSet::$aUserTypes;
        //         $this->setVars(compact('aUserTypes'));
        //         break;
        //     case 'setPrizeGroupForAgent':
        //         break;
        //     default:
        //         # code...
        //         break;
        // }
    }

    /**
     * [setPrizeGroupForAgent 设置用户奖金组(永久/临时)]
     * @param  [Integer] $id [用户id]
     */
    public function setPrizeGroupForAgent($id)
    {
        // pr(Request::method());exit;
        if (Request::method() == 'POST') {
            if ( ! $this->params['prize_group'] ) {
                return false;
            }
/*            if (! $this->params['valid_days']) {
                $bSucc = $this->updateUserPrizeSet($id, $sErrorStr);
            } else {
                $bSucc = $this->updateTempUserPrizeSet($id, $sErrorStr);
            }*/
            // pr($bSucc);exit;
            $oUser = User::find($id);
            $oldPrizeGroup = $oUser->prize_group;
            $newPirzeGroup = $this->params['prize_group'];

            if ($this->updateUserPrizeSet($oUser, $sErrorStr)) {

                $iSuss = true;
                if($newPirzeGroup != $oldPrizeGroup)
                {
                    $iSuss = $this->_addPrizeSet($oUser, $oldPrizeGroup, $newPirzeGroup, $this->params['description']);
                    if($iSuss){
                        $rate = UserCommissionSet::getRateByPrizeGroup($newPirzeGroup);
                        $aReturnMsg = UserCommissionSet::getUserCommissionSet($oUser->id, SeriesSet::ID_LOTTERY)->updateCommissionRate($rate);
                        $iSuss = $aReturnMsg['success'];
                        $sErrorStr = $aReturnMsg['msg'];
                    }
                }

                if($iSuss){
                    DB::connection()->commit();
                    // TODO 代理奖金组列表是查询的用户表, 所以这里要强制跳转回用户列表
                    $this->redictKey = 'curPage-User';
                    return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
                }
            }

            DB::connection()->rollback();
            $this->langVars['reason'] = $sErrorStr;
            return $this->goBack('error', __('_basic.create-fail', $this->langVars));

        } else {

            $oUser = User::find($id);
            $iUserType = $oUser->getUserType();
            $datas = UserPrizeSet::getUserLotteriesPrizeSets($id);
            $data = $datas->first();
            $aLimitPrizeGroups = PrizeSysConfig::getPrizeGroups($iUserType, true);

            if($iUserType == User::TYPE_TOP_AGENT){
                $aLimitPrizeGroups = array_merge($aLimitPrizeGroups, PrizeSysConfig::getHighPrizeGroups($iUserType, true));
            }

            $this->setVars(compact('id', 'data', 'aLimitPrizeGroups'));
            return $this->render();
        }
    }



    /**
     * [updateUserPrizeSet 更新永久用户奖金组]
     * @param  [Integer] $iUserId [用户id]
     * @return [String]      [错误信息]
     */
    private function updateUserPrizeSet(& $oUser, & $sErrorStr)
    {
        // $oExistUserPrizeSet = UserPrizeSet::find($id);
        // $iUserId            = $oExistUserPrizeSet->user_id;
        $sPrizeGroup      = $this->params['prize_group'];
        $description      = $this->params['description'];

        $oPrizeGroups     = PrizeGroup::getPrizeGroupByName($sPrizeGroup);
        $aLotteriesSeries = Lottery::getAllLotteryIdsGroupBySeries();
        $aPrizeGroups     = [];
        foreach ($oPrizeGroups as $key => $oPrizeGroup) {
            $aPrizeGroups[$oPrizeGroup->series_id] = $oPrizeGroup;
        }
        // $iClassicPrize      = $oPrizeGroup->classic_prize;
        // $iPrizeGroupId      = $oPrizeGroup->id;
        $aUserPrizeSets     = UserPrizeSet::getUserLotteriesPrizeSets($oUser->id, null, ['*']);

        //$oUser = User::find($iUserId);
        // pr($oPrizeGroups->toArray());exit;
        $sErrorStr = '';
        DB::connection()->beginTransaction();
        foreach ($aUserPrizeSets as $oUserPrizeSet) {
            $oPrizeGroup   = $aPrizeGroups[$aLotteriesSeries[$oUserPrizeSet->lottery_id]];
            $iClassicPrize = $oPrizeGroup->classic_prize;
            $iPrizeGroupId = $oPrizeGroup->id;
            $aParam = ['group_id' => $iPrizeGroupId, 'prize_group' => $sPrizeGroup, 'classic_prize' => $iClassicPrize, 'description' => $description];
            // pr($aParam);
            if (! $bSucc = $oUserPrizeSet->update($aParam)) {
                $sErrorStr = $oUserPrizeSet->getValidationErrorString();
                break;
            }
        }
        $bSucc = $oUser->update(['prize_group' => $sPrizeGroup]);
        // exit;
        return $bSucc;
    }

    /**
     *
     * @param int $iClassicPrize     奖金组信息
     * @param object $oUser               用户对象
     * @return boolean                      成功或失败
     */
    private function _addPrizeSet(& $oUser, $oldPrizeGroup, $newPirzeGroup, $description = null) {

        $isUp = $oldPrizeGroup > $newPirzeGroup ? 0 : 1;

        $oUserPrizeSetFloat = new UserPrizeSetFloat;
        $oUserPrizeSetFloat->user_id = $oUser->id;
        $oUserPrizeSetFloat->username = $oUser->username;
        $oUserPrizeSetFloat->old_prize_group = $oldPrizeGroup;
        $oUserPrizeSetFloat->new_prize_group = $newPirzeGroup;
        $oUserPrizeSetFloat->standard_turnover = 0;
        $oUserPrizeSetFloat->total_team_turnover = 0;
        $oUserPrizeSetFloat->day = 0;
        $oUserPrizeSetFloat->is_up = $isUp;
        $oUserPrizeSetFloat->description = $description;

        return $oUserPrizeSetFloat->save();
    }


    /**
     * [updateTempUserPrizeSet 更新临时用户奖金组]
     * @param  [Integer] $iUserId [用户id]
     * @return [String]      [错误信息]
     */
    private function updateTempUserPrizeSet($iUserId, & $sErrorStr)
    {
        $sPrizeGroup        = $this->params['prize_group'];
        $oPrizeGroups       = PrizeGroup::getPrizeGroupByName($sPrizeGroup);
        $aLotteriesSeries   = Lottery::getAllLotteryIdsGroupBySeries();
        $aPrizeGroups = [];
        foreach ($oPrizeGroups as $key => $oPrizeGroup) {
            $aPrizeGroups[$oPrizeGroup->series_id] = $oPrizeGroup;
        }
        $aUserPrizeSets     = UserPrizeSet::getUserLotteriesPrizeSets($iUserId, null, ['*']);
        $aUserPrizeSetTemps = UserPrizeSetTemp::getUserLotteriesPrizeSets($iUserId, null, ['*']);
        $data = [];
        foreach ($aUserPrizeSetTemps as $oUserPrizeSetTemp) {
            $key = $oUserPrizeSetTemp->user_id . '_' . $oUserPrizeSetTemp->lottery_id;
            $data[$key] = $oUserPrizeSetTemp;
        }
        // pr($aUserPrizeSetTemps->toArray());exit;
        $sErrorStr = '';
        DB::connection()->beginTransaction();
        foreach ($aUserPrizeSets as $oUserPrizeSet) {
            $oPrizeGroup   = $aPrizeGroups[$aLotteriesSeries[$oUserPrizeSet->lottery_id]];
            $iClassicPrize = $oPrizeGroup->classic_prize;
            $iPrizeGroupId = $oPrizeGroup->id;
            $aParams       = $oUserPrizeSet->getAttributes();
            $aParams['group_id']      = $iPrizeGroupId;
            $aParams['prize_group']   = $sPrizeGroup;
            $aParams['classic_prize'] = $iClassicPrize;
            $aParams['valid_days']    = $this->params['valid_days'];
            $aParams['expired_at']    = Carbon::today()->addDays($this->params['valid_days'])->toDateTimeString();
            // $aParams['user_parent_id'] or $aParams['user_parent_id'] = '';
            // $aParams['user_parent'] or $aParams['user_parent'] = '';
            $key = $oUserPrizeSet->user_id . '_' . $oUserPrizeSet->lottery_id;
            if ($data && array_key_exists($key, $data)) {
                $oUserPrizeSetTemp = $data[$key];
            } else {
                $oUserPrizeSetTemp = new UserPrizeSetTemp;
            }
            // pr($aParams);
            // pr($oUserPrizeSet->toArray());
            // exit;
            $oUserPrizeSetTemp->fill($aParams);
            // pr($oUserPrizeSetTemp->toArray());exit;
            if (! $bSucc = $oUserPrizeSetTemp->save()) {
                $sErrorStr = $oUserPrizeSetTemp->getValidationErrorString();
                break;
            }
        }
        return $bSucc;
    }

}