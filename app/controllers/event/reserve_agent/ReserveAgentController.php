<?php

# 预约成为总代后台管理

class ReserveAgentController extends AdminBaseController {

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'ReserveAgent';
//    protected $view='events.reserve-agent';
    protected $customViewPath = 'events.reserve-agent';

    protected function beforeRender() {
        parent::beforeRender();

        switch ($this->action) {
            case 'index':
                break;
            case 'view':
                $this->resourceView = 'events.reserve-agent';
                break;
        }
        $this->setVars('aSale', ReserveAgent::$aSale);
    }

    //下载
    public function download() {

        $aConditions = &$this->makeSearchConditions();
        $oQuery = $this->model->doWhere($aConditions);
// 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);

        set_time_limit(0);
        $aTitles = [
            'qq' => __('_reserveagent.qq'),
            'created_at' => __('_reserveagent.created_at'),
            'platform' => __('_reserveagent.platform'),
            'sale' => __('_reserveagent.sale'),
            'sale_screenshot_path'=>__('_reserveagent.sale_screenshot_path'),
        ];

        $aConvertFields = [
            'sale' => 'formatted_sale',
            'created_at' => 'date',
        ];

        $aData = $oQuery->get(array_keys($aTitles));
        $aData = $this->_makeData($aData, array_keys($aTitles), $aConvertFields, null, null);
        $oDownExcel = new DownExcel;
        $oDownExcel->setTitle($aTitles);
        $oDownExcel->setData($aData);
        $sSheetTitle = $sFileName = 'Reserve Agent Report';
        $oDownExcel->setActiveSheetIndex(0);
        $oDownExcel->setSheetTitle($sSheetTitle);
        $oDownExcel->setEncoding('gb2312');

        $oDownExcel->Download($sFileName);
        return $this->render();
    }

    protected function _makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'formatted_sale':
                            $a[] = ReserveAgent::$aSale[$oDeposit->$key];
                            break;
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
