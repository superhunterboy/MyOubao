<?php

/**
 * 单期盈亏
 *
 * @author frank
 */
class LotteryProfitController extends AdminBaseController {

    protected $errorFiles = [];
    protected $modelName  = 'ManLotteryProfit';

    function _setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('aSearchFields', 'bNeedCalendar'));
        $this->setVars('aSearchConfig', $this->searchConfig);
        $this->addWidget('w.search_download');
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'view':
            case 'index':
                $this->setVars('aLotteries', ManLottery::getTitleList());
                break;
        }
    }

    public function download() {
        $oQuery = $this->indexQuery();

        set_time_limit(0);

        $aLotteries = & ManLottery::getTitleList();
        $aRelations = [
            'lottery_id' => $aLotteries,
        ];

        $aData = $oQuery->get();

        $aData = $this->_makeDownloadData($aData, IssueProfit::$columnForList, IssueProfit::$listColumnMaps, $aRelations);

        return $this->downloadExcel(Issue::$columnForList, $aData, 'Stat Issue Profit Report');
    }

    private function _makeDownloadData($aData, $aFields, $aConvertFields, $aRelations) {
        $aResult = array();
        foreach ($aData as $oData) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oData->$key === '') {
                    $a[] = $oData->$key;
                    continue;
                }
                if (is_array($aConvertFields) && array_key_exists($key, $aConvertFields)) {
                    $a[] = $oData->{$aConvertFields[$key]};
                } else {
                    if (is_array($aRelations) && array_key_exists($key, $aRelations)){
                        $a[] = $aRelations[$key][$oData->$key];
                    }
                    else{
                        $a[] = $oData->$key;
                    }
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

    public function index(){
        !empty($this->params['date']) or $this->params['date'] = date('Y-m-d');
        return parent::index();
    }
}
