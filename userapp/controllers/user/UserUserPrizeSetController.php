<?php

class UserUserPrizeSetController extends UserBaseController {

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $resourceView = 'centerUser.userPrizeSet';
    protected $modelName = 'UserPrizeSet';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        // $oLottery     = new Lottery;
        // $aCondition   = null; // Session::get('is_tester') ? null : ['open' => ['=',1]];
        // $aLotteries   = $oLottery->getValueListArray(Lottery::$titleColumn, $aCondition, [Lottery::$titleColumn => 'asc'], true);
        $oPrizeGroup = new PrizeGroup;
        $aPrizeGroups = $oPrizeGroup->getValueListArray($oPrizeGroup->titleColumn, ['series_id' => ['=', 1]], [PrizeGroup::$titleColumn => 'asc'], true);
        // $iUserId      = Session::get('user_id');
        $this->setVars(compact('aPrizeGroups'));
        // pr($aLotteries);exit;
        switch ($this->action) {
            case 'gamePrizeSet':
            case 'userGamePrizeSet':
            case 'prizeSetDetail':
                // $iUserId       = Session::get('user_id');
                // $aUserPrizeSet = $this->generateUserPrizeSet($iUserId);
                $aPrizeLevel = ['一等奖', '二等奖', '三等奖', '四等奖', '五等奖'];
                // pr($aLotteriesPrizeSets[0]->lottery_id);exit;
                $this->setVars(compact('aPrizeLevel'));
                break;
        }
    }

    public function index($iLotteryId = null){
        $iUserId = Session::get('user_id');
        $oUser = User::find($iUserId);
        $iPrizeGroup = $oUser->prize_group;
        $fCommissionRate = UserCommissionSet::getRateByPrizeGroup($iPrizeGroup);
        $fCommissionRateFor11Y5 = UserCommissionSet::getRateByPrizeGroup($iPrizeGroup-20);
        $fCommissionRateForDpc = UserCommissionSet::getRateByPrizeGroup($iPrizeGroup-30);
        $oUserLotteryCommissionSets = UserCommissionSet::getUserCommissionSet($iUserId,SeriesSet::ID_LOTTERY);
        $oUserDiceCommissionSets = UserCommissionSet::getUserCommissionSet($iUserId,SeriesSet::ID_DICE);
        $oUserLhdCommissionSets = UserCommissionSet::getUserCommissionSet($iUserId,SeriesSet::ID_LHD);
        $oUserBjlCommissionSets = UserCommissionSet::getUserCommissionSet($iUserId,SeriesSet::ID_BJL);
        $oUserSingleCommissionSets = UserCommissionSet::getUserCommissionSet($iUserId,SeriesSet::ID_FOOTBALL_SINGLE);
        $oUserMixCommissionSets = UserCommissionSet::getUserCommissionSet($iUserId,SeriesSet::ID_FOOTBALL_MIX);

        $this->setVars(compact('oUserLotteryCommissionSets','oUserDiceCommissionSets','oUserLhdCommissionSets','oUserBjlCommissionSets','oUserSingleCommissionSets','oUserMixCommissionSets','iPrizeGroup','fCommissionRate','fCommissionRateFor11Y5','fCommissionRateForDpc'));
        return $this->render();
    }

    /**
     * [generateUserPrizeSet 生成用户信息 ]
     * @param  [Int] $iUserId [用户ID]
     * @return [Array]          [用户信息]
     */
    private function generateUserPrizeSet($iUserId) {
        $oUserAccount = Account::getAccountInfoByUserId($iUserId);
        $iBetMaxPrize = User::getPrizeLimit($iUserId);
        $iLowBetMaxPrize=User::getLowPrizeLimit($iUserId);
        $oUser = User::find($iUserId);
        $aUserPrizeSets = [
            'username' => $oUser->username, // Session::get('username'),
            'nickname' => $oUser->nickname, // Session::get('nickname'),
            'is_agent' => $oUser->is_agent,
            'is_agent_formatted' => $oUser->user_type_formatted, // Session::get('is_agent'),
            'available_formatted' => $oUserAccount->available_formatted,
            'bet_max_prize' => $iBetMaxPrize,
            'low_bet_max_prize' => $iLowBetMaxPrize
        ];
        return $aUserPrizeSets;
    }

    /**
     * [gamePrizeSet 查看彩票奖金组]
     * @param [Int] $iLotteryId [彩票ID]
     */
    public function gamePrizeSet($iLotteryId = null) {
        $iUserId = Session::get('user_id');
        $aUserPrizeSet = $this->generateUserPrizeSet($iUserId);
        // 获取该用户的所有的彩种的奖金组
        $oAllLotteriesPrizeSets = UserUserPrizeSet::getUserLotteriesPrizeSets($iUserId);
        // pr($oAllLotteriesPrizeSets[0]->id);exit;
        // 过滤停售彩票
        $iOpen = Session::get('is_tester') ? null : 1;
        $aLotteriesArray = Lottery::getAllLotteries($iOpen);
        $aLotteryIds = [];
        foreach ($aLotteriesArray as $value) {
            $aLotteryIds[] = $value['id'];
        }
        $oLotteriesPrizeSets = [];
        foreach ($oAllLotteriesPrizeSets as $key => $oLotteryPrizeSet) {
            if (in_array($oLotteryPrizeSet->lottery_id, $aLotteryIds)) {
                $oLotteriesPrizeSets[] = $oLotteryPrizeSet;
            }
        }
        // pr($oLotteriesPrizeSets);exit;

        if (!$iLotteryId) {
            $oUserPrizeSet = $oLotteriesPrizeSets[0];
            $iCurrentLotteryId = $oUserPrizeSet->lottery_id;
            $iCurrentPrizeGroupId = $oUserPrizeSet->group_id;
            $iCurrentPrizeGroup = $oUserPrizeSet->prize_group;
        } else {
            $oUserPrizeSet = UserUserPrizeSet::getUserLotteriesPrizeSets($iUserId, $iLotteryId);
            // $oUserPrizeSet        = $oLotteriesPrizeSets;
            $iCurrentLotteryId = $iLotteryId;
            $iCurrentPrizeGroup = $oUserPrizeSet->prize_group;
            $iCurrentPrizeGroupId = $oUserPrizeSet->group_id;
        }

        $aLotteriesPrizeSetsTable = $this->getLotteriesPrizeSetsTable($iUserId, $iCurrentLotteryId);
        // pr($aLotteriesPrizeSetsTable);exit;
        $this->getLotteriesPrizeSetsTableCount($aLotteriesPrizeSetsTable);
//        // TODO 返点率不是water字段，需要根据上下级奖金设置来计算，待定
//        $iWater = PrizeGroup::find($iCurrentPrizeGroupId)->water_formatted;
//
//        $oUser = User::find(Session::get('user_id'));
//        $iTopAgentMaxPrizeGroup = PrizeSysConfig::minPrizeGroup(PrizeSysConfig::TYPE_TOP_AGENT);
//        $iTopAgentMinPrizeGroup = PrizeSysConfig::maxPrizeGroup(PrizeSysConfig::TYPE_TOP_AGENT);

//        // 查看用户是否在升降点黑名单中
//        $bisUpRole = RoleUser::checkUserRoleRelation(Role::DONT_UP_PRIZE, Session::get('user_id')) || $oUser->prize_group == $iTopAgentMaxPrizeGroup;
//        $bisDownRole = RoleUser::checkUserRoleRelation(Role::DONT_DOWN_PRIZE, Session::get('user_id')) || $oUser->prize_group == $iTopAgentMinPrizeGroup;
//        $this->setVars('isUpRole', $bisUpRole);
//        $this->setVars('isDownRole', $bisDownRole);
        // 获取用户团队销售总额
//        $fTotalTurnover = UserProfit::getUserTotalTeamTurnover(null, null, Session::get('user_id'));

        // 获取用户需要展示的升降点信息
//        $sLastCalculateFloatDate = UserPrizeSetFloat::getLastCalculateFloatDate(Session::get('user_id'));

//        $iDayRange = daysbetweendates(date('Y-m-d H:i:s', time()), $sLastCalculateFloatDate);
        // 获取升降点条件中天数大于$iDayRange的最小的天数对应的记录
//        $aRuleData = PrizeSetFloatRule::getRulesByDayRange($iDayRange);
//        // 获取下次计算升降点的日期
//        if (key_exists('up', $aRuleData)) {
//            $iUpDay = $aRuleData['up']['days'] - $iDayRange;
//            $sUpDate = date('m月d日', strtotime($sLastCalculateFloatDate . " + " . ($iUpDay + 1) . " days"));
//        } else {
//            $iUpDay = $sUpDate = '';
//        }
//        if (key_exists('down', $aRuleData)) {
//            $iDownDay = $aRuleData['down']['days'] - $iDayRange;
//            $sDownDate = date('m月d日', strtotime($sLastCalculateFloatDate . " + " . ($iDownDay + 1) . " days"));
//        } else {
//            $iDownDay = $sDownDate = '';
//        }
//        // 获取用户指定日期范围的销售总额
//        $sCurrentDate = date('Y-m-d');
//        $aTopAgentFloatInfo = $this->_getTopAgentFloatInfo($sLastCalculateFloatDate, $sCurrentDate, $bisUpRole, $bisDownRole);
//        $this->setVars('aFloatRule', PrizeSetFloatRule::getRuleList());
//        $this->setVars(compact('aUserPrizeSet', 'aLotteriesPrizeSetsTable', 'oLotteriesPrizeSets', 'iCurrentLotteryId', 'iCurrentPrizeGroup', 'iWater', 'fTotalTurnover', 'iUpDay', 'sUpDate', 'iDownDay', 'sDownDate', 'aRuleData', 'sLastCalculateFloatDate', 'aTopAgentFloatInfo', 'sCurrentDate'));
//        $this->setVars('prizeset', PrizeGroup::getTopAgentPrizeGroups());
//        $this->setVars('topAgentMaxPrizeSet', SysConfig::readValue('top_agent_max_grize_group'));
//        // pr(($aCounts));exit;

        $oLottery = Lottery::find($iCurrentLotteryId);
        $this->setVars(compact('aUserPrizeSet', 'aLotteriesPrizeSetsTable', 'oLotteriesPrizeSets', 'iCurrentLotteryId', 'iCurrentPrizeGroup', 'oLottery'));
        $this->action = Session::get('is_agent') ? 'gamePrizeSet' : 'userGamePrizeSet';
        return $this->render();
    }

    /**
     * [setPrizeSet 设置彩票奖金组]
     * @param [Int] $iUserId    [用户ID]
     * @param [Int] $iLotteryId [彩票ID]
     */
    public function setPrizeSet($iUserId, $iLotteryId = null) {

        // TODO 暂时禁用, 该功能有问题
        // App::abort(403);
        $oAgent = User::find(Session::get('user_id'));
        $oUser = User::find($iUserId);
        if (!$oAgent) {
            // TIP 如果当前用户不存在，则直接退出登录
            return App::make('AuthorityController')->logout();
        }
        if (!$oUser) {
            return Redirect::route('users.index')->with('error', __('_user.user-not-exist'));
        }
        if (!$oAgent->checkUserBelongsToAgent($iUserId)) {
            return Redirect::route('users.index')->with('error', '操作不合法！');
        }
        // $sLotteryPrizeJson = trim(Input::get('lottery_prize_group_json'));
        // $sSeriesPrizeJson = trim(Input::get('series_prize_group_json'));
        // pr($sLotteryPrizeJson);
        // pr($sSeriesPrizeJson);
        // exit;

        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();
            $aReturnMsg = $this->setUserPrizeGroups($iUserId, $iLotteryId);
            if (isset($aReturnMsg['success']) && $aReturnMsg['success']){
                DB::connection()->commit();
		        $oUserLogin = UserOnline::getLatestLoginRecord($oUser->id);
		        if (is_object($oUserLogin)) {
		            UserOnline::sso($oUser, $oUserLogin->session_id, Session::getId());
		            UserOnline::offline($oUser->id);
		        }
		        return $this->goBack('success', $aReturnMsg['msg']);
            }else{
                DB::connection()->rollback();
                return $this->goBack('error', $aReturnMsg['msg']);
            }
        } else {
            $sCurrentUserPrizeGroup = $oUser->prize_group;
            $sCurrentAgentPrizeGroup = $oAgent->prize_group;
//            $iIsAgent = intval($oUser->is_agent);
//            $aLotteriesPrizeSets = UserUserPrizeSet::generateLotteriesPrizeWithSeries($iUserId);

            //基础奖金组
            $iPrizeGroups = PrizeSysConfig::getPrizeGroups($oUser->is_agent, true);
            $iMinPrizeGroup = $iPrizeGroups[0];
            $iMaxPrizeGroup = $iPrizeGroups[count($iPrizeGroups) - 1];

            $iPrizeGroups = array_fill_keys($iPrizeGroups, 1);
            //配额可用奖金组数
            $aPrizeAvailableNum = OverlimitPrizeGroup::getUserAvailableNum($oAgent);

            //永久奖金组
            $foreverPrize = UserPrizeGroupTmp::getForeverPrize($oUser);

            if($oUser->is_agent) $iPrizeGroups = $iPrizeGroups + $aPrizeAvailableNum;

            //点差列表
            $aDiffPrizes = [];
            foreach($iPrizeGroups as $iPrizeGroup=>$num){
                if($iPrizeGroup < $foreverPrize || ($num<=0 && ($iPrizeGroup!=$foreverPrize && $iPrizeGroup!=$oUser->prize_group))) continue;
                $aDiffPrizes[$iPrizeGroup] = $num;
            }

            //奖金组属性(永久或临时)
            if($foreverPrize == $oUser->prize_group) $isForever = 1;
            else $isForever = 0;

            $agent_min_high_grize_group=  SysConfig::readValue('agent_min_high_grize_group');
            $this->setVars('agent_min_high_grize_group',$agent_min_high_grize_group);
            $this->setVars('oUser',$oUser);
            $this->setVars('isOnline',UserOnline::isOnline($oUser->id) ? "在线": "离线");
            $this->setVars('hashOverLimits', $aPrizeAvailableNum);
            $this->setVars('aDiffPrizes', $aDiffPrizes);

//            // 获取玩家的奖金组范围
//            if ($iIsAgent == User::TYPE_USER) {
//                // 获取低于当前代理奖金组的玩家可能的6个奖金组
//            } else {
//                $iLevel = intval($oUser->user_level_formatted);
//                $iMinPrizeGroup = SysConfig::readValue('agent_min_grize_group');
//                switch ($iLevel) {
//                    case 1: // 一代
//                        $iMaxPrizeGroup = SysConfig::readValue('agent_max_grize_group');
//                        $isOverList     = OverlimitPrizeGroup::isOverlimit(Session::get('user_id'));
//                        $areaPrizeGroup = $this->getLimitPrizeGroup($sCurrentUserPrizeGroup, User::TYPE_AGENT);
//                        // pr($areaPrizeGroup);exit;
//                        if($areaPrizeGroup['bound']){
//                            if(($isOverList && $sCurrentUserPrizeGroup >= $iMaxPrizeGroup)){
//                                $iMinPrizeGroup = SysConfig::readValue('one_agent_bound_min_prize_group');
//                                $iMaxPrizeGroup = min($sCurrentAgentPrizeGroup, SysConfig::readValue('one_agent_bound_max_prize_group'));
//                            }
//                        }
//                        break;
//                    case 2: // 二代
//                        $iMaxPrizeGroup = SysConfig::readValue('agent_max_grize_group');
//                        break;
//                    case 3: // 三代
//                        $iMaxPrizeGroup = SysConfig::readValue('agent_3_max_prize_group'); // 三代最高奖金组
//                        break;
//                    default: // 四代及以下
//                        $iMaxPrizeGroup = SysConfig::readValue('agent_4_max_prize_group'); // 四代及以下最高奖金组
//                        break;
//                }
//            }

            //$aUserPrizeSet = $this->generateUserPrizeSet($iUserId);
//是否允许配置

            $oUserCommissions = UserCommissionSet::getUserSeriesCommissionSets($oAgent->id);
            $aSubCommissions = array_column(UserCommissionSet::getUserSeriesCommissionSets($iUserId)->toArray(), 'commission_rate', 'series_set_id');

            foreach($oUserCommissions as $key => $data){
                //todo 暂时
                if($data->series_set_id == SeriesSet::ID_FOOTBALL_MIX){
                    $oUserCommissions[$key]->commission_rate = min(8, $data->commission_rate);
                }
                $oUserCommissions[$key]->sub_commission_rate = $aSubCommissions[$data->series_set_id];
                $oUserCommissions[$key]->name = SeriesSet::find($data->series_set_id)->name;
            }
            $this->setVars(compact('oUserCommissions'));

//            $aLottery = UserProfit::getCurrentMonthData($oAgent->id, 1);
//            $aSport = UserProfit::getCurrentMonthData($oAgent->id, 2);
//            $aElectronic = UserProfit::getCurrentMonthData($oAgent->id, 3);

            $this->setVars(compact('aLottery', 'aSport', 'aElectronic'));


            $bIsOverLimitPrizeGroup = OverlimitPrizeGroup::checkIsOverLimitPrize(Session::get('user_id'));
            $this->setVars(compact('iUserId', 'sCurrentAgentPrizeGroup', 'sCurrentUserPrizeGroup', 'iMinPrizeGroup', 'iMaxPrizeGroup', 'isForever', 'foreverPrize','bIsOverLimitPrizeGroup'));
            return $this->render();
        }
    }

    /**
     * [getLotteriesPrizeSetsTable 获取彩票奖金组详细信息 ]
     * @param [Int] $iUserId    [用户ID]
     * @param [Int] $iLotteryId [彩票ID]
     * @return [Array]             [彩票奖金组详细列表数组]
     */
    private function getLotteriesPrizeSetsTable($iUserId, $iLotteryId) {
        $oLottery = Lottery::find($iLotteryId);
        $aLotteriesPrizeSetsTable = User::getWaySettings($iUserId, $oLottery);
        return $aLotteriesPrizeSetsTable;
    }

    /**
     * [getLotteriesPrizeSetsTableCount 获取彩票奖金组详细列表的各级子节点数量，供渲染table时使用]
     * @param  [Array] $aLotteriesPrizeSetsTable [彩票奖金组详细列表]
     * @return [Array]                           [包含各级子节点数量数组的彩票奖金组详细列表]
     */
    private function getLotteriesPrizeSetsTableCount(& $aLotteriesPrizeSetsTable) {
        $aLotteriesPrizeSetsTable = arrayToObject($aLotteriesPrizeSetsTable);
        foreach ($aLotteriesPrizeSetsTable as $aWayGroup) {
            $iCount = 0;
            foreach ($aWayGroup->children as $aWay) {
                $iICount = 0;
                foreach ($aWay->children as $aMethod) {
                    $iMethodId = $aMethod->id;
                    $item = explode(',', $aMethod->prize);
                    $item = array_unique($item);
                    $iPrizeCount = count($item);
                    // TIP 特殊处理, 定位但有5个奖级但是都是一样的值
                    if ($iPrizeCount == 1) {
                        $aMethod->prize = $item[0];
                    }
                    // $iPrizeCount = count(explode(',', $item));
                    $aMethod->count = $iPrizeCount;
                    $iCount += $iPrizeCount;
                    $iICount += $iPrizeCount;
                }
                // $aCounts['way_' . $aWay['id']] = $iICount;
                $aWay->count = $iICount;
            }
            // $aCounts['waygroup_' . $aWayGroup['id']] = $iCount;
            $aWayGroup->count = $iCount;
        }
        return $aLotteriesPrizeSetsTable;
    }

    /**
     * [setUserPrizeGroups 更新用户各彩种的奖金组]
     * @param [Integer] $iUserId    [用户id]
     * @param [Integer] $iLotteryId [description]
     */
    private function setUserPrizeGroups($iUserId, $iLotteryId = null) {
        $oExistUserPrizeGroups = UserUserPrizeSet::getUserLotteriesPrizeSets($iUserId, $iLotteryId);
        $aExistUserPrizeGroups = [];
        foreach ($oExistUserPrizeGroups as $key => $oExistUserPrizeGroup) {
            $aExistUserPrizeGroups[$oExistUserPrizeGroup->lottery_id] = $oExistUserPrizeGroup;
        }
        // $iPrizeGroupType = 2;
        // $sLotteryPrizeJson = trim(Input::get('lottery_prize_group_json'));
        // $sSeriesPrizeJson = trim(Input::get('series_prize_group_json'));


        // $oUserCreateUserLink = new UserRegisterLink;

        // $aCustomSeries = objectToArray(json_decode($sSeriesPrizeJson));
        // $aCustomLotteries = objectToArray(json_decode($sLotteryPrizeJson));

        $isForever = Input::get('is_forever');

        $oUser = User::find($iUserId);
        $oParent = User::find(Session::get('user_id'));
        $setPrizeGroup = e(trim(Input::get('prize_group')));

        //返点
        $oSeriesSets = SeriesSet::all();

        foreach($oSeriesSets as $oSeriesSet)
        {
            $rate = $oSeriesSet->id == SeriesSet::ID_LOTTERY ? UserCommissionSet::getRateByPrizeGroup($setPrizeGroup) : trim(Input::get('commission_rate_'.$oSeriesSet->id));
            $oUserCommissionSet = UserCommissionSet::getUserCommissionSet($iUserId, $oSeriesSet->id);
            
//            if($oSeriesSet->type_id == SeriesSet::TYPE_ELECTRONIC && $oUserCommissionSet->commission_rate > $rate) return $this->goBack('error', '电子娱乐返点不允许往下调');
            if($oSeriesSet->type_id != SeriesSet::TYPE_LOTTERY && $oUserCommissionSet->commission_rate > $rate){
                return ['success' => false, 'msg' => '返点不允许往下调'];
            }
            $aReturnMsg = $oUserCommissionSet->updateCommissionRate($rate);
            if(!$aReturnMsg['success']){
                return ['success' => false, 'msg' => $aReturnMsg['msg']];
            }
        }

        $iForeverPrize = UserPrizeGroupTmp::getForeverPrize($oUser);

        //当前奖金组等于设置奖金组 && ( 当前奖金组等于永久奖金组 || 当前奖金组是临时奖金组且设置奖金组属性也是临时 )
        if($oUser->prize_group == $setPrizeGroup && ( $oUser->prize_group == $iForeverPrize || (UserPrizeGroupTmp::existTmpPrize($oUser) && !$isForever)) ){
//            return Redirect::route('users.index');
            return ['success' => true, 'msg' => __('_basic.updated', $this->langVars)];
        }

        $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($oParent->is_agent, true)) ? $aPrizeGroups : [];
        if(in_array($setPrizeGroup, $aPrizeGroups) || $setPrizeGroup == $iForeverPrize){
            $isForever = 1;
        }
//                DB::connection()->rollback();
//        var_dump($iForeverPrize);die;

        //临时的设置成永久的且奖金组不变
        if($oUser->prize_group == $setPrizeGroup && UserPrizeGroupTmp::existTmpPrize($oUser) && $isForever)
        {
            if(OverlimitPrizeGroup::setPrizeGroupNum($oParent, $oUser, $setPrizeGroup, $isForever) && UserPrizeGroupTmp::setTmpPrize($oUser, $setPrizeGroup, $isForever)){
                Session::put($this->redictKey, route('users.index'));
                return ['success' => true, 'msg' => __('_basic.updated', $this->langVars)];
            }

        } else {
            //新设置的奖金组必须大于等于永久奖金组
            if($iForeverPrize > $setPrizeGroup){
                return ['success' => false, 'msg' => __('_userprizeset.less-than-exist-prize-group')];
            }
            if(UserPrizeGroupTmp::getForeverPrize($oParent) < $setPrizeGroup){
                return ['success' => false, 'msg' => __('_userprizeset.more-than-parent-prize-group')];
            }

            //是否有配额
            $bOverLimit = true;
            if(($aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups($oUser->is_agent, true)) && in_array($setPrizeGroup, $aHighPrizeGroups)){
                if($setPrizeGroup != $iForeverPrize){
                    $bOverLimit = OverlimitPrizeGroup::isOverlimit($oParent->id, $setPrizeGroup);
                }
            }

            //生成用户的彩种奖金组数组
            $aPrizeGroup = UserPrizeSet::generateUserPrizeSetData($oUser->is_agent, $setPrizeGroup, $oUser->prize_group);

            if(!$bOverLimit || $aPrizeGroup == false || !is_array($aPrizeGroup)){
                return ['success' => false, 'msg' => __('_userprizeset.no-available-prize-group')];
            }

            $aPrizeGroup = json_decode(json_encode($aPrizeGroup));


            if(($aReturnMsg = OverlimitPrizeGroup::setPrizeGroupNum($oParent, $oUser, $setPrizeGroup, $isForever)) && ($aReturnMsg = UserPrizeGroupTmp::setTmpPrize($oUser, $setPrizeGroup, $isForever)))
            {
                $oUser->prize_group = $setPrizeGroup;
                $oUser->save();
                $aReturnMsg = UserUserPrizeSet::createUserPrizeGroup($oUser, $aPrizeGroup, $aExistUserPrizeGroups);

                if ($bSucc = $aReturnMsg['success'] && $oUser->save()) {

                    Session::put($this->redictKey, route('users.index'));
                    return ['success' => true, 'msg' => __('_basic.updated', $this->langVars)];
                }
            }
        }

        //$this->langVars['reason'] = $aReturnMsg['msg'];
        $this->langVars['reason'] = '';
        return ['success' => false, 'msg' => __('_basic.update-fail', $this->langVars)];
    }

    /**
     * [setUserPrizeGroup 设置用户奖金组]
     * @param [Int] $iUserId    [用户ID]
     * @param [Int] $iLotteryId [彩票ID]
     */
    // private function setUserPrizeGroup($iUserId, $iLotteryId)
    // {
    //     $iPrize                       = trim(Input::get('user_Lottery_prize'));
    //     if (! $oLottery = Lottery::find($iLotteryId)) {
    //         return $this->goBack('error', __('_basic.no-available-lottery', $this->langVars));
    //     }
    //     $iLotteryType = $oLottery->type;
    //     if (!$oPrizeGroup = PrizeGroup::getPrizeGroupByClassicPrize($iPrize, $iLotteryType)) {
    //         return $this->goBack('error', __('_basic.no-available-prize-group', $this->langVars));
    //     }
    //     $oUserPrizeSet                = UserUserPrizeSet::getUserLotteriesPrizeSets($iUserId, $iLotteryId);
    //     $oUserPrizeSet->group_id      = $oPrizeGroup->id;
    //     $oUserPrizeSet->prize_group   = $oPrizeGroup->name;
    //     $oUserPrizeSet->classic_prize = $oPrizeGroup->classic_prize;
    //     // pr($oUserPrizeSet->toArray());exit;
    //     $bSucc = $oUserPrizeSet->save();
    //     if ($bSucc) {
    //         return $this->goBack('success', __('_basic.update-success', $this->langVars));
    //     } else {
    //         return $this->goBack('error', __('_basic.update-fail', $this->langVars));
    //     }
    // }

    /**
     * [prizeSetDetail 根据奖金值查询彩票奖金组信息]
     * @param [Integer] $iPrize [奖金组]
     * @param [Integer] $iLotteryId [彩种id]
     */
    public function prizeSetDetail($iPrize, $iLotteryId = null) {
        // $aLotteries   = Lottery::all();
        if (!$iLotteryId) {
            $oLottery = Lottery::first();
        } else {
            $oLottery = Lottery::find($iLotteryId);
        }
        // pr($aLotteries);exit;
        $iLotteryType = $oLottery->type;
        $iCurrentLotteryId = $oLottery->id;
        $iCurrentPrizeGroup = $iPrize;
//        $iGroupId = PrizeGroup::getPrizeGroupByClassicPrize($iPrize, $iLotteryType)->id;
        $iGroupId = PrizeGroup::where('series_id', '=', $oLottery->series_id)->where('classic_prize', '=', $iPrize)->first()->id;
        // pr($iGroupId);exit;
        // $aLotteriesPrizeSetsTable = WayGroup::getWaySettings($oLottery, $iGroupId);
        $aPrizes = & PrizeGroup::getPrizeDetails($iGroupId);
        // pr($iGroupId);
        // pr($aPrizes);exit;
        $aLotteriesPrizeSetsTable = WayGroup::getWayInfos($oLottery, $aPrizes, null);
        // pr($aLotteriesPrizeSetsTable);exit;
        $this->getLotteriesPrizeSetsTableCount($aLotteriesPrizeSetsTable);

        $oUser = User::find(Session::get('user_id'));

        $iWater = ($oUser->prize_group - $iPrize)/2000*100;

        $this->setVars(compact('aLotteriesPrizeSetsTable', 'iCurrentPrizeGroup', 'iCurrentLotteryId', 'iWater'));
        return $this->render();
    }

    /**
     * 获取总代我的奖金组页面升降点统计信息
     * @param type $sLastCalculateFloatDate     上次升降点统计信息
     * @param string $sCurrentDate                  当前日期
     * @param boolean $bisUpRole                是否允许升点
     * @param boolean $bisDownRole          是否允许降点
     * @return array
     */
    private function _getTopAgentFloatInfo($sLastCalculateFloatDate, $sCurrentDate, $bisUpRole, $bisDownRole) {
        $aFloatRule = PrizeSetFloatRule::getRuleList();
        $aResult = [];
        if (!is_null($sLastCalculateFloatDate)) {
            foreach ($aFloatRule as $isUp => $val) {
                foreach ($val as $iDay => $aTurnover) {
                    if ($isUp == PrizeSetFloatRule::FLOAT_TYPE_UP && $bisUpRole || $isUp == PrizeSetFloatRule::FLOAT_TYPE_STAY && $bisDownRole) {
                        $aResult[$isUp][$iDay]['beginDate'] = '----';
                        $aResult[$isUp][$iDay]['endDate'] = '----';
                        $aResult[$isUp][$iDay]['isUp'] = $isUp;
                        $aResult[$isUp][$iDay]['turnover'] = null;
                    } else {
                        $sBeginDate = date('Y-m-d', strtotime($sCurrentDate . " - " . ($iDay - 1) . " days"));
                        $sBeginDate = $sBeginDate > $sLastCalculateFloatDate ? $sBeginDate : $sLastCalculateFloatDate;
                        $aResult[$isUp][$iDay]['beginDate'] = $sBeginDate;
                        $aResult[$isUp][$iDay]['endDate'] = date('Y-m-d', strtotime($sBeginDate . ' + ' . ($iDay - 1) . ' days'));
                        $aResult[$isUp][$iDay]['isUp'] = $isUp;
                        $aResult[$isUp][$iDay]['turnover'] = UserProfit::getUserTotalTeamTurnover($sBeginDate, $sCurrentDate, Session::get('user_id'));
                    }
                }
            }
        } else {
            foreach ($aFloatRule as $isUp => $val) {
                foreach ($val as $iDay => $aTurnover) {
                    if ($isUp == PrizeSetFloatRule::FLOAT_TYPE_UP && $bisUpRole || $isUp == PrizeSetFloatRule::FLOAT_TYPE_STAY && $bisDownRole) {
                        $aResult[$isUp][$iDay]['beginDate'] = '----';
                        $aResult[$isUp][$iDay]['endDate'] = '----';
                        $aResult[$isUp][$iDay]['isUp'] = $isUp;
                        $aResult[$isUp][$iDay]['turnover'] = null;
                    } else {
                        $sEndDate = date('Y-m-d', strtotime($sCurrentDate . " + " . ($iDay - 1) . " days"));
                        $aResult[$isUp][$iDay]['beginDate'] = $sCurrentDate;
                        $aResult[$isUp][$iDay]['isUp'] = $isUp;
                        $aResult[$isUp][$iDay]['endDate'] = $sEndDate;
                        $aResult[$isUp][$iDay]['turnover'] = UserProfit::getUserTotalTeamTurnover($sCurrentDate, $sEndDate, Session::get('user_id'));
                    }
                }
            }
        }
        return $aResult;
    }

}
