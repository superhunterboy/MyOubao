<?php

class GameMenuController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'GameMenu';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aLotteries = & Lottery::getTitleList();
        $aLotteries[0] = '';
        $aSeriesLotteries = & Series::getLotteriesGroupBySeries();
//        pr($aLotteries);exit;
        $aPGroup = GameMenu::getGroups();
        $this->setVars(compact('aLotteries', 'aPGroup'));
        switch ($this->action) {
            case 'index':
                break;
        }
    }

    public function create($id = null) {
        //自动填充系列id
        if (Request::method() == 'POST' && !isset($this->params['type']) && empty($this->params['type'])) {
            $lottery_id = $this->params['lottery_id'];
            if ($lottery = Lottery::where('id', $lottery_id)->first()) {
                $this->params['series_id'] = $lottery->series_id;
            } else {

                return $this->goBack('error', __('lottery error', $this->langVars));
            }
        }
        return parent::create($id);
    }

    public function edit($id) {
        //自动填充系列id
        if (Request::method() == 'PUT' && !isset($this->params['type']) && empty($this->params['type'])) {
            $lottery_id = $this->params['lottery_id'];
            if ($lottery = Lottery::where('id', $lottery_id)->first()) {
                $this->params['series_id'] = $lottery->series_id;
//                if (!isset($this->params['url']) || empty($this->params['url'])) {
//                    $this->params['url']=  route('bets.bet', $lottery_id);
//                }
            } else {
                return $this->goBack('error', __('lottery error', $this->langVars));
            }
        }

        return parent::edit($id);
    }

}
