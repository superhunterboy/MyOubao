<?php

class WayGroupController extends AdminBaseController {
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'series.way-group';
    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'create',
        'index',
        'edit',
        'view'
    ];

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'WayGroup';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oSeries     = !empty($this->viewVars[ 'series_id' ]) ? Series::find($this->viewVars[ 'series_id' ]) : new Series;
        $aConditions = ['parent_id' => ['=',null]];
//        pr($this->viewVars);
//        pr()

        $aSeries = & Series::getTitleList();
        empty($oSeries->id) or $aConditions[ 'series_id' ] = [ '=',$oSeries->id];
        $aMainGroups                = $this->model->getValueListArray('title',$aConditions,null,true);
        switch ($this->action){
            case 'create':
                if ($iParentId = !empty($this->viewVars[ 'parent_id' ]) ? $this->viewVars[ 'parent_id' ] : null){
//                    pr($iParentId);
                    $oParent     = WayGroup::find($iParentId);
                    $aConditions[ 'series_id' ] = ['=',$oParent->series_id];
                    $aMainGroups = $this->model->getValueListArray('title',$aConditions,null,true);
                }
                break;
            case 'edit':
                $aConditions[ 'series_id' ] = ['=',$this->model->series_id];
                $aMainGroups                = $this->model->getValueListArray('title',$aConditions,null,true);
        }
        $this->setVars(compact('aMainGroups','aSeries'));
    }

}