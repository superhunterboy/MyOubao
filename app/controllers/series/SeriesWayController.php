<?php
/**
 * 系列方式控制器
 */
class SeriesWayController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'series.series-way';
    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [
        'index',
        'create',
        'edit',
        'view',
        'setNote'
    ];

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'SeriesWay';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oBasicMethod = new BasicMethod;
        $oWayMethod = new SeriesWayMethod;
        $oSeriesMethod = new SeriesMethod;
        $oBasicWay = new BasicWay;
        $aBasicMethods = & BasicMethod::getTitleList();
        $aConditions            = $aConditionsForBasicWay = [];

        switch ($this->action){
            case 'edit' :
                $series_id= $this->model->series_id;
            case 'create':
                isset($series_id)?'':$series_id=$this->viewVars['series_id'];
                $oSeries                    = Series::find($series_id);
                $aConditions[ 'series_id' ] = ['=',$oSeries->id];
                $aConditionsForBasicWay[ 'lottery_type' ] = [ '=',$oSeries->type];
            default:
                $oSeries                                = !empty($this->viewVars[ 'series_id' ]) ? Series::find($this->viewVars[ 'series_id' ]) : new Series;
                break;
        }
        $aSeries        = $oSeries->getTitleList();
        $aSeriesWayMethods    = $oWayMethod->getValueListArray('name',$aConditions,null,true);
        $aSeriesMethods = $oSeriesMethod->getValueListArray('name',$aConditions,null,true);
        $aBasicWays     = $oBasicWay->getValueListArray('name',$aConditionsForBasicWay,null,true);
        $this->setVars(compact('aSeries','aBasicMethods','aBasicWays','aSeriesMethods','aSeriesWayMethods'));
    }

    
    public function initData($iSeriesId){
        $mResult   = $this->model->makeSeriesWayData($iSeriesId);
        return ( $mResult === true ) ? $this->goBack('success', __('_seriesway.init-success')) : $this->goBack('error', $mResult);
    }
    
    
    public function testGetWnNumber($iSeriesId){
        $sNumber = '99488';
        echo 'Original Number: ', $sNumber, '<br>';
        $oBasicWays = BasicWay::all();
//        $aIgnoreWays = [2,3,6];
        $aIgnoreWays = [1,2,3,4,5,6,7,8,9];
        foreach($oBasicWays as $oBasicWay){
            if (in_array($oBasicWay->id, $aIgnoreWays)) continue;
            echo "<br>Way: $oBasicWay->name<br>";
            $oSeriesWays = SeriesWay::where('series_id' , '=', $iSeriesId)->where('basic_way_id', '=', $oBasicWay->id)->orderBy('id','asc')->get();
            foreach($oSeriesWays as $oSeriesWay){
                echo $oSeriesWay->id, ' : ', $oSeriesWay->name, ': ';
//                echo $oSeriesWay->name, ': ', $oSeriesWay->digital_count , '   ', $oSeriesWay->basic_methods , '<br>';
                $a = $oSeriesWay->getWinningNumber($sNumber);
                pr($a);
//                echo '<br>';
            }
        }
        exit;
    }

    /**
     * 设置投注说明和中奖说明
     * @param int $id
     */
    public function setNote($id){
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            $this->model->bet_note = $this->params['bet_note'];
            $this->model->bonus_note = $this->params['bonus_note'];
            $this->model->fillable(['bet_note','bonus_note']);
            if ($bSucc = $this->model->save()) {
                return $this->goBackToIndex('success', __('_seriesway.note-seted', $this->langVars));
            } else {
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_seriesway.note-set-failed', $this->langVars));
            }
        }
        else{
            $oSeries = Series::find($this->model->series_id);
            $this->setVars('data',$this->model);
            $this->setVars('series',$oSeries->name);
        }
        return $this->render();
    }

}