<?php

class CasinoProjectDetailController extends ComplicatedSearchController {

    protected $searchBlade = 'w.casino_project_search';
      /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'casino_project_detail';
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
    protected $modelName = 'BlackJackProjectDetail';


    protected function beforeRender()
    {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aLotteries = & CasinoLottery::getTitleList();
        $aTables = & CasinoTable::getTitleList();
        $aWays = & CasinoWay::getTitleList();
        $aMethods = & CasinoMethod::getTitleList();

        $aStatusDesc = $sModelName::$validStatuses;
        $aStatusCommissions = $sModelName::$commissionStatuses;
        $aStatusPrizes = $sModelName::$prizeStatuses;
        $this->setVars(compact('aLotteries','aTables','aStatusDesc','aWays','aMethods','aStatusCommissions','aStatusPrizes'));
//        switch ($this->action) {
//            case 'index':
//        }
    }

}
