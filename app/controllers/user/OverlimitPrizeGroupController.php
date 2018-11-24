<?php

class OverlimitPrizeGroupController extends AdminBaseController
{

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'overlimit_prize_group';
    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'create',
        'edit',
    ];
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'OverlimitPrizeGroup';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender()
    {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch ($this->action) {
            case 'create':
            case 'edit':
            case 'index':
                $aUsers = User::getTitleList();
                $aLimitGroups = OverlimitPrizeGroup::getHighPrizeGroups();
                $this->setVars(compact('aUsers', 'aLimitGroups'));
                break;
        }
    }

    public function create($top_agent_id = null)
    {
        if ($top_agent_id) {
            $oUser = User::find($top_agent_id);
            if (!is_object($oUser)) {
                return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
            }
            $aAvilibalePrizeGroups = array_diff(OverlimitPrizeGroup::getHighPrizeGroups(), OverlimitPrizeGroup::getPrizeGroups($top_agent_id));

            //不能高于总代的奖金组
            foreach($aAvilibalePrizeGroups as $iPrizeGroup){
                if($aAvilibalePrizeGroups[$iPrizeGroup] > $oUser->prize_group) unset($aAvilibalePrizeGroups[$iPrizeGroup]);
            }

            $this->setVars(compact('oUser', 'aAvilibalePrizeGroups'));
        }
        if (Request::method() == 'POST') {
            $top_agent_id = input::get('top_agent_id');
            $classic_prize_group = input::get('classic_prize_group');

            if ($classic_prize_group && in_array($classic_prize_group, OverlimitPrizeGroup::getPrizeGroups($top_agent_id))) {
                return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
            }
            // pr($classic_prize_group);exit;
            $aPrizeGroupsTopAgent = OverlimitPrizeGroup::getDatasByPrizeGroupAndTopAgentId($top_agent_id, $classic_prize_group);
            if ($aPrizeGroupsTopAgent) {
                return $this->goBackToIndex('error', 'error');
            }
        }
        return parent::create($top_agent_id);
    }

    public function edit($id = null)
    {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }

        if (Request::method() == 'PUT') {
            if ($this->params['limit_num'] < $this->model->used_num) {
                return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
            }
/*            $top_agent_id = input::get('top_agent_id');
            $classic_prize_group = input::get('classic_prize_group');
            $aLimitGroups = OverlimitPrizeGroup::getHighPrizeGroups();
            if (!isset($aLimitGroups[$classic_prize_group])) {
                return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
            }
            $aPrizeGroupsTopAgent = $this->model->getDatasByPrizeGroupAndTopAgentId($top_agent_id, $classic_prize_group);
            if ($aPrizeGroupsTopAgent && $aPrizeGroupsTopAgent->id != $id) {
                return $this->goBackToIndex('error', __('_overlimitprizegroup.has_set_prize_group'));
            }*/
        } else {
            // 这里的id传入的是用户id
            //$aAvilibalePrizeGroups = OverlimitPrizeGroup::getHighPrizeGroups();
            $this->setVars(compact('oUser'));
        }
        return parent::edit($id);
    }

//    private function getHighPrizeGroups()
//    {
//        $aLimitGroups = [];
//        $aPrizeSysConfig = PrizeSysConfig::getHighPrizeGroups(PrizeSysConfig::TYPE_AGENT, true);
//
//        foreach ((array)$aPrizeSysConfig as $iPrizeGroup) {
//            $aLimitGroups[$iPrizeGroup] = $iPrizeGroup;
//        }
//
//        return $aLimitGroups;
//    }


}
