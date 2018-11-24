<?php

# 链接开户管理

class UserRegisterLinkController extends UserBaseController {

    protected $resourceView = 'centerUser.link';
    protected $modelName = 'UserRegisterLink';
    public $resourceName = '';

    protected static $createPagesize = 25;
    const QQ_NUM_MIN = 50000;
    const QQ_NUM_MAX = 300000000;

    public function beforeRender() {
        parent::beforeRender();
        // $aChannels = UserRegisterLink::$aChannels;
        // $oLottery     = new Lottery;
        // $aCondition   = null; //Session::get('is_tester') ? null : ['open' => ['=', 1]];
        // $aLotteries   = $oLottery->getValueListArray(Lottery::$titleColumn, $aCondition, [Lottery::$titleColumn => 'asc'], true);
        // $this->setVars(compact('aLotteries'));
        switch ($this->action) {
            case 'view':
                $aSeriesLotteries = Series::getLotteriesGroupBySeries(1);
                $this->setVars('aListColumnMaps', UserRegisterLink::$listColumnMaps);
                // $aPrizeGroupWaters       = PrizeGroup::getPrizeGroupWaterMap();
                $this->setVars(compact('aSeriesLotteries'));
            // pr($this->viewVars['data']->toArray());
            // exit;
            case 'index':
                $aUserTypes = User::$aUserTypes;
                $this->setVars(compact('aUserTypes'));
                break;
            case 'create': // 新增时, 需要提供奖金组范围, 当前奖金组信息等数据
                //-------------------各彩种可以设置不同奖金组时的方式-----------------------------------------
                // $iUserId = Session::get('user_id');
                // $aLotteriesPrizeSets = UserPrizeSet::generateLotteriesPrizeWithSeries($iUserId);
                // $oUser = User::find($iUserId);
                // $this->setVars('currentUserPrizeGroup', $oUser->prize_group);
                // // 获取玩家的奖金组范围
                // $iPlayerMaxPrizeGroup = Sysconfig::readValue('player_max_grize_group');
                // $aCurrentPrizeGroups = $aLotteriesPrizeSets[0]['children'][0];              // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                // $iSeriesId = $aCurrentPrizeGroups['series_id']; // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                // $iPlayerMinPrizeGroupRange = SysConfig::readValue('min_diff_between_player_agent');
                // if ($iPlayerMaxPrizeGroup < $aCurrentPrizeGroups['classic_prize'] - $iPlayerMinPrizeGroupRange) {
                //     $iCurrentPrize = $iPlayerMaxPrizeGroup;
                // } else {
                //     $iCurrentPrize = $aCurrentPrizeGroups['classic_prize'] - $iPlayerMinPrizeGroupRange;
                // }
                // $iPlayerMinPrizeGroup = Sysconfig::readValue('player_min_grize_group');
                // // 获取低于当前代理奖金组的玩家可能的6个奖金组
                // $oPossiblePrizeGroups = PrizeGroup::getPrizeGroupsBelowExistGroup($iCurrentPrize, $iSeriesId, 6, $iPlayerMinPrizeGroup);

                // // 如果是总代开户，获取代理的奖金组范围
                // $oPossibleAgentPrizeGroups = [];
                // if (Session::get('is_top_agent')) {
                //     $iAgentMaxPrizeGroup = Sysconfig::readValue('agent_max_grize_group');
                //     $aCurrentPrizeGroups = $aLotteriesPrizeSets[0]['children'][0];              // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                //     if ($iAgentMaxPrizeGroup < $aCurrentPrizeGroups['classic_prize']) {
                //         $iAgentCurrentPrize = $iAgentMaxPrizeGroup;
                //     } else {
                //         $iAgentCurrentPrize = $aCurrentPrizeGroups['classic_prize'];
                //     }
                //     $iAgentMinPrizeGroup = Sysconfig::readValue('agent_min_grize_group');
                //     $oPossibleAgentPrizeGroups = PrizeGroup::getPrizeGroupsBelowExistGroup($iAgentCurrentPrize, $iSeriesId, 6, $iAgentMinPrizeGroup);
                // }
                // $aDefaultMaxPrizeGroups = RegisterLink::$aDefaultMaxPrizeGroups;
                // $aDefaultPrizeGroups = RegisterLink::$aDefaultPrizeGroups;
                // // pr($aDefaultPrizeGroups);exit;
                // $this->setVars(compact('oPossiblePrizeGroups', 'oPossibleAgentPrizeGroups', 'aLotteriesPrizeSets', 'iAgentCurrentPrize', 'iCurrentPrize', 'aDefaultPrizeGroups', 'aDefaultMaxPrizeGroups', 'iAgentMinPrizeGroup', 'iPlayerMinPrizeGroup'));
                //-------------------各彩种可以设置不同奖金组时的方式-----------------------------------------

                $iUserId = Session::get('user_id');
                $oUser   = UserUser::find($iUserId);

                $aUserPrizeGroupRange   = $oUser->generatePrizeGroupSetData();
                $oUserCommissions = UserCommissionSet::getUserSeriesCommissionSets($iUserId);

                foreach($oUserCommissions as $key => $data){
                    //todo 暂时
                    if($data->series_set_id == SeriesSet::ID_FOOTBALL_MIX){
                        $oUserCommissions[$key]->commission_rate = min(8, $data->commission_rate);
                    }

                    $oUserCommissions[$key]->name = SeriesSet::find($data->series_set_id)->name;
                }

                $this->setVars(compact('oUserCommissions'));
                /*=============竞彩返点数据追加==============*/
//                $aExtraData = \JcModel\JcCommissionUser::getCommissionSetDataByUserId($oUser->id);
//                $aUserPrizeGroupRange = array_merge($aUserPrizeGroupRange, $aExtraData);
                /*=============竞彩返点数据追加==============*/
                
                $this->setVars($aUserPrizeGroupRange);
                break;
        }
    }

    public function index() {
        // $aUserLinkGroups = UserRegisterLink::getUserLinksWithChannelGroup();
        $iUserId = Session::get('user_id');
        if (!$iUserId)
            return $this->goBack('error', __('_basic.no-rights'));
        $this->params['user_id'] = $iUserId;
        $this->params['is_admin'] = 0;
        // TODO 是否只显示未删除的链接
        // $this->params['status']   = 0;

        return parent::index();
    }

    public function create($id = null) {
        if (!$bIsAgent = Session::get('is_agent')) {
            return $this->goBack('error', __('_basic.no-rights'));
        }
        if (Request::method() == 'POST') {
/*            $aInputData  = trimArray(Input::except(['_token', '_random']));
            if(!isset($aInputData['is_agent']) ||$aInputData['is_agent']!=1 || !isset($aInputData['prize_group']))
                return $this->goBack('error', __('_basic.no-rights'));*/

            $iAgentId = Session::get('user_id');
            $oUser = User::find($iAgentId);

//            $aCommissionRate = array_column(UserCommissionSet::getUserSeriesCommissionSets($iAgentId)->toArray(), 'commission_rate', 'series_set_id');
            $oSeriesSets = SeriesSet::all();
            $setPrizeGroup = $this->params['prize_group'];
            if($setPrizeGroup >=1956){
                return $this->goBack('error', '不允许开高额奖金组链接！');
            }
            foreach($oSeriesSets as $oSeriesSet)
            {
                $aCommissionRate[$oSeriesSet->id] = $oSeriesSet->id == SeriesSet::ID_LOTTERY ? UserCommissionSet::getRateByPrizeGroup($setPrizeGroup) : trim(Input::get('commission_rate_'.$oSeriesSet->id));
            }

            if(! UserCommissionSet::verifyCreate($oUser->id, $aCommissionRate)){
                return $this->goBack('error', '返点错误');
            }
            $this->params['commission_sets'] = json_encode($aCommissionRate);

            $setUserType = User::TYPE_AGENT;

            if($setPrizeGroup > $oUser->prize_group) {
                       return $this->goBack('error', __('_userprizeset.more-than-parent-prize-group'));
            }
            $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($setUserType, true)) ? $aPrizeGroups : [];

           // $jsonString  = (new UserRegisterLink)->generateUserPrizeSetJson( $this->params['is_agent'], $iPrizeGroupType, $iPrizeGroupId, $sLotteryPrizeJson, $sSeriesPrizeJson, $iAgentId);
           // $aPrizeGroup = json_decode($jsonString);
           $aPrizeGroup = UserPrizeSet::generateUserPrizeSetData($setUserType, $setPrizeGroup);

           if ($aPrizeGroup == false || !is_array($aPrizeGroup) || !in_array($setPrizeGroup, $aPrizeGroups))
           {
               return $this->goBack('error', __('_userprizeset.no-available-prize-group'));
           }

            if(UserRegisterLink::getAvailableRegisterNum($iAgentId) > 30){
                return $this->goBack('error', __('_registerlink.register-overlimit'));
            }
            $sPrizeGroupSetsJson = json_encode($aPrizeGroup);

            /*===================竞彩足球返点设置================*/
/*            $oJcCommissionSetting = \JcModel\JcCommissionUser::getByUserId($iAgentId);

            if($oJcCommissionSetting) {
                if($oJcCommissionSetting->single_rate < $aInputData['single_commission']/100){
                    return $this->goBack('error', __('_registerlink.more-than-parent-single-commission-setting'));
                }

                if($oJcCommissionSetting->multiple_rate < $aInputData['multiple_commission']/100){
                    return $this->goBack('error', __('_registerlink.more-than-parent-multiple-commission-setting'));
                }

                if( $aInputData['single_commission']/100 < 0 ||  $aInputData['multiple_commission']/100 < 0){
                    return $this->goBack('error', __('_registerlink.less-than-zero'.$aInputData['single_commission']));
                }

                $aJcCommissionSetting = \JcModel\JcCommissionUser::generateJcCommissionSettingData($aInputData['single_commission'],$aInputData['multiple_commission']);

                $sJcCommissionSettingJson = json_encode($aJcCommissionSetting);
                $this->params['jc_commission_sets'] = $sJcCommissionSettingJson;
            }*/
            /*===================竞彩足球返点设置================*/
            
            // pr($sPrizeGroupSetsJson);exit;
            // $iPrizeGroupType   = trim(Input::get('prize_group_type'));
            // $iPrizeGroupId     = trim(Input::get('prize_group_id'));
            // $sLotteryPrizeJson = trim(Input::get('lottery_prize_group_json'));
            // $sSeriesPrizeJson  = trim(Input::get('series_prize_group_json'));
            // $iUserId           = Session::get('user_id');

            // $oUserRegisterLink = new UserRegisterLink;
            // $sPrizeGroupSetsJson = $oUserRegisterLink->generateUserPrizeSetJson($this->params['is_agent'], $iPrizeGroupType, $iPrizeGroupId, $sLotteryPrizeJson, $sSeriesPrizeJson, $iUserId);

//            $iType = intval($this->params['is_agent']) ? Domain::TYPE_AGENT : Domain::TYPE_USER;

            if(empty($oUser->forefather_ids)){
                $userIdForAgentDomain = $oUser->id;
            }else{
                $forefatherIds = explode(',',$oUser->forefather_ids);
                $userIdForAgentDomain = current($forefatherIds);
            }
            $agentGroupId = AgentDomainUser::getDomainGroupIdByUserId($userIdForAgentDomain);
            if($agentGroupId){
                $sAvailableDomain = AgentDomain::getRandomDomainByGroupInPool($agentGroupId);
            }else{
                $sAvailableDomain = false;
            }
            //$sAvailableDomain or $sAvailableDomain = Domain::getRandomDomainInPool(Domain::TYPE_AGENT);
            // pr($sAvailableDomain);exit;
            //$sAvailableDomain = 'www.ncshizheng.com';
            $sAvailableDomain = $_SERVER['HTTP_HOST'];
            //pr($sAvailableDomain);exit;
            $this->params['user_id']          = $iAgentId;
            $this->params['username']         = Session::get('username');
            $this->params['is_tester']        = Session::get('is_tester');

            while (true) {
                $keyword = substr(md5($this->params['username'] . time() . random_str(5)), 0, 8);
                $oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($keyword);
                if(! is_object($oRegisterLink)) break;
            }
            $this->params['keyword'] = $keyword;

            //$this->params['keyword']          = substr(md5($this->params['username'] . time() . random_str(5)), 0, 8);
            $this->params['url']              = $sAvailableDomain . Config::get('var.default_signup_dir_name') . '' . $this->params['keyword']; // $_SERVER['SERVER_NAME']
            $this->params['prize_group_sets'] = $sPrizeGroupSetsJson;
            if (strpos($this->params['url'], 'http') !== 0) {
                $this->params['url'] = ((isset($_SERVER["https"]) && $_SERVER["https"]) ? 'https://' : 'http://') . $this->params['url'];
            }

            $bSucc = true;
            $aAgentQQs = array_filter($this->params['agent_qqs']);
            foreach($aAgentQQs as $key => $value) {
                if (! preg_match("/^\d*$/",$value) || $value < static::QQ_NUM_MIN ) { // || $value > static::QQ_NUM_MAX
                    $bSucc = false;
                    break;
                }
            }
            if (! $bSucc) {
                return $this->goBack('error', __('_registerlink.qq-number-error', ['min' => static::QQ_NUM_MIN, 'max' => static::QQ_NUM_MAX]));
            }
            $this->params['agent_qqs']        = implode(',', $aAgentQQs);
            $this->params['is_admin']         = 0;
            if (intval($this->params['valid_days'])) {
                // TIP 添加链接从添加时间的后一天开始计算, 到过期时间的23：59：59 即过期日期的后一天的00:00:00
                // 编辑时, 从编辑当天向后续期
                $this->params['expired_at'] = Carbon::today()->addDays(intval($this->params['valid_days']) + 1)->toDateTimeString();
            }
        }
        // pr($this->params['url']);
        Session::put($this->redictKey, route('user-links.create'));

        $iUserId = Session::get('user_id');
        $this->params['user_id'] = $iUserId;
        if (Request::method() != 'POST') {
            $this->params['status'] = implode(',', [RegisterLink::STATUS_IN_USE, RegisterLink::STATUS_EXPIRED, RegisterLink::STATUS_VALID_FOREVER]);
        }
        $this->params['is_admin'] = 0;


        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;

        $datas = $oQuery->paginate(static::$createPagesize);
        $aListColumnMaps = UserRegisterLink::$listColumnMaps;
        $this->setVars(compact('aListColumnMaps', 'datas'));
        return parent::create($id);
    }

    /**
     * [closeLink 删除代理创建的开户链接]
     * @param  [Integer] $id [链接id]
     */
    public function closeLink($id) {
        $oLink = UserRegisterLink::getActiveLink($id);
        // pr($oLink->toArray());exit;
        if (!$oLink) {
            return $this->goBack('error', __('_basic.no-rights'));
        }
        // 只能关闭自己的链接
        if ($oLink->user_id != Session::get('user_id')) {
            return $this->goBack('error', __('_basic.no-rights'));
        }
        // $oLink->status = 1;
        $bSucc = $oLink->update(['status' => 1]);
        if ($bSucc) {
            return $this->goBack('success', __('_basic.closed', $this->langVars));
        } else {
            return $this->goBack('error', __('_basic.close-fail', $this->langVars));
        }
    }

}
