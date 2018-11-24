<?php

class SeriesMethodController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    /**
     * self view path
     * @var string
     */
//    protected $customViewPath = 'lottery.way-method';
    /**
     * views use custom view path
     * @var array
     */
//    protected $customViews = [
//        'index',
//        'create',
//        'edit',
//        'view'
//    ];

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'SeriesMethod';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $series_id=isset($this->viewVars[ 'series_id' ])?$this->viewVars[ 'series_id' ]:$this->model->series_id;
        $oSeries       = !empty($series_id) ? Series::find($series_id) : new Series;
        $oBasicMethod = new BasicMethod;
        $aSeries = & Series::getTitleList();
        $aConditions   = $oSeries->id ? [ 'lottery_type' => [ '=',$oSeries->type]] : [];
        $aBasicMethods = $oBasicMethod->getValueListArray('name',$aConditions,null,true);
        $this->setVars(compact('aSeries','aBasicMethods'));
    }

}