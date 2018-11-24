<?php

class ActivityReportBaseController extends AdminBaseController {

    /**
     * 根据搜索配置生成搜索表单数据
     */
    function _setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('aSearchFields', 'bNeedCalendar'));
        $this->setVars('aSearchConfig', $this->searchConfig);
        $this->addWidget('w.search_download');
    }

    public function download() {
        $oQuery = $this->indexQuery();

        set_time_limit(0);
        $sModelName = $this->modelName;
        $aData = $oQuery->get($sModelName::$columnForList);
        $aConvertFields = [
            'created_at' => 'date',
            'register_at' => 'date',
        ];
        $aData = $this->_makeData($aData, $sModelName::$columnForList, $aConvertFields);
        return $this->downloadExcel($sModelName::$columnForList, $aData, 'Report');
    }

    function _makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
//            pr($oDeposit->getAttributes());continue;
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = '';
                            }
                            break;
                    }
                } else {
                    $a[] = $oDeposit->$key;
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

}
