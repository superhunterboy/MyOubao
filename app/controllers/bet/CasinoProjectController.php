<?php

class CasinoProjectController extends AdminBaseController {

   // protected $searchBlade = 'w.casino_project_search';
      /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'casino_project';
    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'view',
    ];

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'BlackJackProject';


    protected function beforeRender()
    {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aLotteries = & CasinoLottery::getTitleList();
        $aTables = & CasinoTable::getTitleList();
        $aStatusDesc = $sModelName::$validStatuses;
        $aTestDesc = $sModelName::$isTest;
        $this->setVars(compact('aLotteries','aTables','aStatusDesc','aTestDesc'));
//        switch ($this->action) {
//            case 'index':
//        }
    }

    protected function cancelGame($id){
        $manProject = BlackJackProject::find($id);
        if (empty($manProject)) {
            return $this->goBack('error', __('_basic.no-data'));
        }
        $oTable = BlackJackTable::find($manProject->table_id);
        $oLottery = CasinoLottery::find($manProject->lottery_id);
        $oUser = UserUser::find($manProject->user_id);
        $oBlackJackStage = new BlackJackStage($oLottery,$oTable,$oUser);
        $oBlackJackStage->delStage();
        if ($manProject->setFinished(BlackJackProject::STATUS_DROPED) > 0 ) {
            $sLangKey = '_blackjackproject.game_canceled';
            $sMsgType = 'success';
        } else {
            $sLangKey = '_blackjackproject.game_canceled_fails';
            $sMsgType = 'error';
        }

        return $this->goBack($sMsgType, __($sLangKey));
    }

}
