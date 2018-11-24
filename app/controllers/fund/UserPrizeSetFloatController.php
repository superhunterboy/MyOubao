<?php

class UserPrizeSetFloatController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'UserPrizeSetFloat';
    protected $resourceView = 'default';

//    protected $customViewPath = 'userPrizeSet';
//    protected $customViews = [
//        'agentDistributionList',
//        'agentPrizeGroupList',
//        'setPrizeGroupForAgent'
//    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $aLotteries = & Lottery::getTitleList();
//        pr($aLotteries);
//        exit;
        $oPrizeGroup = new PrizeGroup;
        $aPrizeGroups = $oPrizeGroup->getValueListArray(PrizeGroup::$titleColumn, ['series_id' => ['=', 1]], [PrizeGroup::$titleColumn => 'asc'], true);
        $this->setVars(compact('aLotteries', 'aPrizeGroups'));

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

}
