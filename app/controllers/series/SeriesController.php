<?php
/**
 * 彩票系列管理
 */
class SeriesController extends AdminBaseController
{
    protected $customViewPath = 'series.create-lottery';
    protected $modelName = 'Series';

    protected function beforeRender(){
        parent::beforeRender();
        $this->setVars('aSeries',Series::getTitleList());
    }

    public function createLotteryFromSeries($iSeriesId){
        $oSeries = Series::find($iSeriesId);
        if (empty($oSeries)){
            return $this->goBack('error', '_basic.no-data');
        }
        if (!isset($this->params['step']) || (!$iStep = intval($this->params['step']))){
            $iStep = 1;
        }
        else{
            $iStep++;
        }
        switch($iStep){
            case 1:
                $aSeries = & Series::getTitleList();
//                exit;
                $this->view = $this->view = $this->customViewPath . '.step-' . $iStep;
                $this->setVars(compact('iSeriesId', 'aSeries'));
                break;
            case 2:
                if (Request::method() != 'POST') {
                    $this->goBack('error', '_basic.invalid-visit');
                }
//                    pr($this->params);
                $oSeries = Series::find($this->params['series_id']);
                if (empty($oSeries) || !$this->_checkNewLotteryData()){
                    return $this->goBack('error', '_basic.no-data');
                }
                $this->setVars('iSeriesId',$this->params['series_id']);
                $this->setVars('sSeries',$oSeries->name);
                $this->setVars('sName',$this->params['name']);
                $this->setVars('iDigitalCount',$this->params['digital_count']);
                $this->setVars('sIdentifier',$this->params['identifier']);
                $this->setVars('sIssueFormat',$this->params['issue_format']);
                $this->setVars('iFrequency',$this->params['frequency']);
                $this->setVars('step',$iStep);
                $this->view = $this->view = $this->customViewPath . '.step-' . $iStep;
                break;
            case 3:
                if (Request::method() != 'POST') {
                    $this->goBack('error', '_basic.invalid-visit');
                }
//                pr($this->params);
                $oSeries = Series::find($this->params['series_id']);
                if (empty($oSeries) || !$this->_checkNewLotteryData()){
                    return $this->goBack('error', '_basic.no-data');
                }
                
                $iMaxDigital = $this->params['digital_count'];
                $aValidOffest = $aValidDigital = [];
                $iMaxOffset = $iMaxDigital - 1;
//                for($i = 0; $i < $iMaxDigital;$i++){
//                    $aValidOffest[] = $i;
//                }
//                for($i = 1; $i <= $iMaxDigital;$i++){
//                    $aValidDigital[] = $i;
//                }

                $aLotteryData = [
                    'series_id' => $oSeries->id,
                    'name' => $this->params['name'],
                    'identifier' => $this->params['identifier'],
                    'issue_format' => $this->params['issue_format'],
                    'high_frequency' => $this->params['frequency'],
                    'type' => $oSeries->type,
                    'lotto_type' => $oSeries->lotto_type,
                    'sort_winning_number' => $oSeries->sort_winning_number,
                    'valid_nums' => $oSeries->valid_nums,
                    'buy_length' => $oSeries->buy_length,
                    'wn_length' => $oSeries->wn_length,
                    'open' => 0,
                    'days' => 127,
                ];
                $oLottery = new Lottery($aLotteryData);
//                if ($oSeries->type == 1){
//                    $oLottery->sort_winning_number = 0;
//                }
                
                $aConditions = [
                    'series_id' => ['=', $this->params['series_id']],
                    'digital_count' => ['<=', $iMaxDigital],
//                    'offset' => ['in', $aValidOffest]
                ];
//                pr($aConditions);
//                exit;
                $oSeriesWay = new SeriesWay;
                $oSeriesWays = $oSeriesWay->doWhere($aConditions)->get();
//                pr($oSeriesWays->count());
//                pr($oSeriesWays->toArray());
                if (!$bSucc = $oLottery->save()){
                    pr($oLottery->validationErrors->toArray());
                    exit;
                }
                $aLotteryWayIds = $aLotteryWays = [];
                foreach($oSeriesWays as $oSeriesWay){
                    $aOffset = explode(',', $oSeriesWay->offset);
                    $iMinOffset = min($aOffset);
                    if ($oSeriesWay->digital_count + $iMinOffset > $iMaxDigital){
                        continue;
                    }
                    $aLotteryWayIds[] = $oSeriesWay->id;
                    $aLotteryWays[] = [
                        'series_id' => $oLottery->series_id,
                        'lottery_id' => $oLottery->id,
                        'series_way_id' => $oSeriesWay->id,
                        'name' => $oSeriesWay->name,
                        'short_name' => $oSeriesWay->short_name,
                    ];
//                    $oLotteryWay = new LotteryWay($aLotteryWays);
//                    $aLotteryWays[] = $oSeriesWay->getAttributes();
                }
                $oLottery->series_ways = implode(',',$aLotteryWayIds);
                $oLottery->sequence = $oLottery->id * 10;
                if (!$oLottery->save()){
                    pr($oLottery->validationErrors->toArray());
                    exit;
                }
                $bSucc = true;
                foreach($aLotteryWays as $aLotteryWay){
                    $oLotteryWay = new LotteryWay($aLotteryWay);
                    if (!$bSucc = $oLotteryWay->save()){
                        pr($oLotteryWay->validationErrors->toArray());
                        exit;
                    }
                }
                $aLotteries = explode(',',$oSeries->lotteries);
                $aLotteries[] = $oLottery->id;
//                $aLotteries = array_unique($aLotteries);
                $oSeries->lotteries = implode(',',array_unique($aLotteries));
                $oSeries->save();
//                pr($aLotteryWays);
                return Redirect::route('series.index')->with('success', '_series.lottery-created');
        }
        return $this->render();
    }

    private function _checkNewLotteryData(){
        if (!isset($this->params['name']) || empty($this->params['name'])){
            return false;
        }
        if (!isset($this->params['identifier']) || empty($this->params['identifier'])){
            return false;
        }
        if (!isset($this->params['digital_count']) || empty($this->params['digital_count'])){
            return false;
        }
        if (!isset($this->params['issue_format']) || empty($this->params['issue_format'])){
            return false;
        }
        return true;
    }
}