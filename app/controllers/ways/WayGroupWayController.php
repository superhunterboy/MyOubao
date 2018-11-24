<?php

class WayGroupWayController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'WayGroupWay';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oSeries = new Series;
        $oSeriesWay = new SeriesWay;
//        $oGroup     = new WayGroup;
        $oGroup     = !empty($this->viewVars[ 'group_id' ]) ? WayGroup::find($this->viewVars[ 'group_id' ]) : new WayGroup;
        $aCondtitions = [];
        switch($this->action){
            case 'edit':
                $aCondtitions[ 'series_id' ] = [ '=',$this->model->series_id];
                $oGroup->getTree($aWayGroups,null,$aCondtitions);
            case 'create':
                empty($oGroup->id) or $aCondtitions[ 'series_id' ] = [ '=',$oGroup->series_id];
                $oGroup->getTree($aWayGroups,null,$aCondtitions);
                break;
            case 'index':
            case 'view':
            default:
                $aWayGroups = $oGroup->getValueListArray(WayGroup::$titleColumn,$aCondtitions,null,true);
        }
        $aSeries = & Series::getTitleList();
        $aSeriesWays = $oSeriesWay->getValueListArray('name',$aCondtitions,null,true);

        $this->setVars(compact( 'aSeriesWays', 'aWayGroups' , 'aSeries'));
    }

}