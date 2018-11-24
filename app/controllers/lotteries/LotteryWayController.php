<?php

class LotteryWayController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';

    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'lottery.lottery-way';

    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'index',
        'create',
        'edit',
        'view'
    ];

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'LotteryWay';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oSeries = new Series;
        $oBasicMethod = new BasicMethod;
        $oSeriesMethod = new SeriesMethod;
        $oBasicWay = new BasicWay;
        $oSeriesWay = new SeriesWay;
        $oLotteries = new Lottery;
        $aSeries = $oSeries->getValueListArray('name', null, null, true);
        $aBasicMethods = $oBasicMethod->getValueListArray('name', null, null, true);
        $aSeriesMethods = $oSeriesMethod->getValueListArray('name', null, null, true);
        $aBasicWays = $oBasicWay->getValueListArray('name', null, null, true);
        $aSeriesWays = $oSeriesWay->getValueListArray('name', null, null, true);
        $aLotteries = $oLotteries->getValueListArray('name', null, null, true);
        $this->setVars(compact('aSeries', 'aBasicMethods', 'aBasicWays', 'aSeriesMethods', 'aSeriesWays', 'aLotteries'));
    }

//    public function initData(){
//        $mResult = $this->model->makeSeriesWayData();
//        return ( $mResult === true ) ? $this->goBack('success', __('_seriesway.init-success')) : $this->goBack('error', $mResult);
//    }
}
