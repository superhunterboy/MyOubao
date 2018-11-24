<?php
/**
 * 链接开户管理
 */
class RegisterLinkController extends AdminBaseController {
    protected $customViewPath = 'link';
    protected $customViews = [
        'index',
        'create',
        'edit',
        'view',
    ];
    protected $modelName = 'RegisterLink';
    public $resourceName = '';

    public function beforeRender()
    {
        parent::beforeRender();
        // $aChannels = UserRegisterLink::$aChannels;
        $oLottery     = new Lottery;
        $aLotteries   = $oLottery->getValueListArray(Lottery::$titleColumn, ['open' => ['=',1]], [Lottery::$titleColumn => 'asc'], true);
        $aStatuses    = RegisterLink::$aStatuses;
        $isEdit = false;
        $sCurrentUserPrize = 0;
        $iCurrentPrizeId   = 0;
        switch($this->action){
            case 'view':
                $aSeriesLotteries = & Series::getLotteriesGroupBySeries();
                // $aPrizeGroupWaters       = PrizeGroup::getPrizeGroupWaterMap();
                $this->setVars('aListColumnMaps',UserRegisterLink::$listColumnMaps);
                $this->setVars(compact( 'aSeriesLotteries' ));
            case 'index':
                $aUserTypes = User::$aUserTypes;
                $aLangVars = ['title' => $this->langVars['resource']];
                // pr($aLangVars);exit;
                $this->setVars(compact( 'aUserTypes', 'aLangVars' ));
                break;
            case 'edit':
                $aCurrentPrizeGroups = json_decode($this->viewVars['data']->prize_group_sets);
                $sCurrentUserPrize   = $aCurrentPrizeGroups[0]->prize_group;
                $oCurrentUserPrize   = PrizeGroup::getPrizeGroupByClassicPrize($aCurrentPrizeGroups[0]->prize_group, 1);
                $iCurrentPrizeId     = $oCurrentUserPrize->id;
                // pr($iCurrentPrizeId);exit;
                $isEdit = true;
            case 'create':
                // pr($sCurrentUserPrize);
                $aDefaultMaxPrizeGroups = ['classic_prize' => SysConfig::readValue('top_agent_max_grize_group')]; // RegisterLink::$aDefaultMaxPrizeGroups[2];
                $aDefaultPrizeGroups    = ['classic_prize' => SysConfig::readValue('top_agent_min_grize_group')]; // RegisterLink::$aDefaultPrizeGroups[2];
                $aSeriesLotteries       = & Series::getLotteriesGroupBySeries();
                // $sDefaultUrl            = Config::get('var.default_url'); // TODO 默认绑定的平台，暂时读取var.php中的配置值，以后需要有一个管理模块
                // $oDomain = new Domain;
                // $aDomains               = $oDomain->getDataByParams(['conditions' => [['status', '=', '1'], ['type', '=', '0']], 'columns' => ['id', 'domain']]);
                $aDomains = Domain::getDomainsByType(0, ['id', 'domain']);
                $iSeriesId = 1; // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                // 获取低于当前代理的奖金组的可能的6个奖金组
                $oPossiblePrizeGroups   = PrizeGroup::getPrizeGroupsBelowExistGroup($aDefaultMaxPrizeGroups['classic_prize'], $iSeriesId, 6, $aDefaultPrizeGroups['classic_prize']);
                $this->setVars(compact('sCurrentUserPrize', 'iCurrentPrizeId', 'aDomains', 'oPossiblePrizeGroups', 'aSeriesLotteries', 'aDefaultPrizeGroups', 'aDefaultMaxPrizeGroups', 'aCurrentPrizeGroups'));
                // pr($oPossiblePrizeGroups->toArray());exit;
                break;
        }
        // $isEdit = $this->action == 'edit';
        // pr($this->viewVars['data']);exit;
        $this->setVars(compact('aLotteries', 'aStatuses', 'isEdit'));

    }

    /**
     * [generateInputs 拼装公共属性]
     */
    private function generateInputs() {
        $this->params['user_id']          = Session::get('admin_user_id');
        $this->params['username']         = Session::get('admin_username');
        $this->params['is_agent']         = 1;
        $this->params['is_admin']         = 1;
        if ($this->params['valid_days']) {
            // if ($this->model->created_at) {
            //     $aDate = explode('-', explode(' ', $this->model->created_at)[0]);
            //     $this->params['expired_at']   = Carbon::createFromDate($aDate[0], $aDate[1], $aDate[2])->addDays($this->params['valid_days'])->toDateTimeString();
            // } else {
                // TIP 添加链接从添加时间的后一天开始计算, 到过期时间的23：59：59 即过期日期的后一天的00:00:00
                // 编辑时, 从编辑当天向后续期
                $this->params['expired_at']   = Carbon::today()->addDays(intval($this->params['valid_days']) + 1)->toDateTimeString();
            // }
        }
    }
    /**
     * [generatePrizeGroupJson 生成json格式的奖金组字符串]
     * @return [String] [奖金组字符串]
     */
    private function generatePrizeGroupJson() {
        // $bIsAgent = Session::get('IsAdmin');
        $iPrizeGroupType     = trim(Input::get('prize_group_type'));
        $iPrizeGroupId       = trim(Input::get('prize_group_id'));
        $sLotteryPrizeJson   = trim(Input::get('lottery_prize_group_json'));
        $sSeriesPrizeJson    = trim(Input::get('series_prize_group_json'));
        $oRegisterLink       = new RegisterLink;
        $sPrizeGroupSetsJson = $oRegisterLink->generateUserPrizeSetJson(2, $iPrizeGroupType, $iPrizeGroupId, $sLotteryPrizeJson, $sSeriesPrizeJson);
        return $sPrizeGroupSetsJson;
    }

    public function create($id = null)
    {
        if (Request::method() == 'POST') {
            // pr(Input::all());exit;
            // if (! $bIsAgent = Session::get('IsAdmin')) {
            //     return $this->goBack('error', __('_basic.no-rights'));
            // }
            $sPrizeGroupSetsJson = $this->generatePrizeGroupJson();
            if (! $sPrizeGroupSetsJson) {
                return $this->goBack('error', __('_userprizeset.no-available-prize-group'));
            }
            if (! $this->params['url']) {
                return $this->goBack('error', __('validation.required', ['attribute' => __('_registerlink.url')]));
            }
            $this->generateInputs();
            $this->params['prize_group_sets'] = $sPrizeGroupSetsJson;
            $this->params['keyword']          = md5($this->params['username'] . time() . random_str(5));
            if (strpos($this->params['url'], 'http') !== 0) {
                $this->params['url'] = ((isset($_SERVER["https"]) && $_SERVER["https"]) ? 'https://' : 'http://') . $this->params['url'];
            }
            $this->params['url']             .= Config::get('var.default_signup_dir_name') . '?prize=' . $this->params['keyword'];

            // pr($this->params);
        }

        return parent::create($id);

    }

    public function edit($id)
    {
        if (Request::method() == 'PUT') {
            // pr(Input::all());exit;
            if (! $bIsAgent = Session::get('IsAdmin')) {
                return $this->goBack('error', __('_basic.no-rights'));
            }
            $this->model = $this->model->find($id);
            if (!is_object($this->model)) {
                return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
            }
            $sPrizeGroupSetsJson = $this->generatePrizeGroupJson();
            if (! $sPrizeGroupSetsJson) {
                return $this->goBack('error', __('_userprizeset.no-available-prize-group'));
            }
            $this->generateInputs();
            $this->params['prize_group_sets'] = $sPrizeGroupSetsJson;
            // TIP 编辑时特征码保持不变, 域名如果没有值, 则url也保持不变
            $this->params['keyword']          = $this->model->keyword; // md5($this->params['username'] . time() . random_str(5));
            if ($this->params['url']) {
                $this->params['url'] .= Config::get('var.default_signup_dir_name') . '?prize=' . $this->params['keyword'];
            } else {
                $this->params['url']  = $this->model->url;
            }
            // pr($this->params);
        }

        return parent::edit($id);

    }
    /**
     * [closeLink 关闭开户链接]
     * @param  [Integer] $id [链接id]
     */
    public function closeLink($id)
    {
        // 只有管理员能关闭
        if (! Session::get('IsAdmin')) {
            return $this->goBack('error', __('_basic.no-rights', $this->langVars));
        }
        $oLink = RegisterLink::getActiveLink($id);
        // pr($oLink->toArray());exit;
        if (! $oLink) {
            return $this->goBack('error', __('_basic.no-rights', $this->langVars));
        }
        // $oLink->status = 1;
        if ($bSucc = $oLink->update(['status' => 1])) {
            return $this->goBack('success', __('_basic.closed', $this->langVars));
        } else {
            return $this->goBack('error', __('_basic.close-fail', $this->langVars));
        }
    }

}
