<?php

class ComplicatedSearchController extends AdminBaseController {

    protected $searchBlade = 'w.search';

    /**
     * get search conditions array
     *
     * @return array
     */
    protected function & makeSearchConditions() {
        $aConditions = [];
        // pr($this->functionality->id);
        // pr($this->searchConfig);
        // pr($this->paramSettings);
        // pr($this->searchItems);
        // pr($this->params);
        // exit;

        $iCount = count($this->params);
        foreach ($this->paramSettings as $sColumn => $aParam) {
            if (!isset($this->params[$sColumn])) {
                if ($aParam['limit_when_null'] && $iCount <= 1) {
                    $aFieldInfo[1] = null;
                } else {
                    continue;
                }
            }

            $mValue = isset($this->params[$sColumn]) ? $this->params[$sColumn] : null;
            if (!is_array($mValue) && !mb_strlen($mValue) && !$aParam['limit_when_null'])
                continue;
            if (is_array($mValue) && empty($mValue[0]) && empty($mValue[1])) {
                continue;
            }
            // 处理between and 的情况
            if (!isset($this->searchItems[$sColumn])) {
                if (is_array($mValue) && !empty($mValue[0]) && !empty($mValue[1])) {
                    $aConditions[$sColumn] = [ 'between', $mValue];
                } else if (is_array($mValue) && !empty($mValue[0])) {
                    $aConditions[$sColumn] = [ '>=', $mValue[0]];
                } else if (is_array($mValue) && !empty($mValue[1])) {
                    $aConditions[$sColumn] = [ '<=', $mValue[1]];
                } else {
                    $aConditions[$sColumn] = [ '=', $mValue];
                }
                continue;
            }
            $aPattSearch = array('!\$model!', '!\$\$field!', '!\$field!');
            $aItemConfig = & $this->searchItems[$sColumn];
            $mValue = is_array($mValue) ? implode(',', $mValue) : $mValue;
            $aPattReplace = array($aItemConfig['model'], $mValue, $aItemConfig['field']);
            $sMatchRule = preg_replace($aPattSearch, $aPattReplace, $aItemConfig['match_rule']);
            $aMatchRule = explode("\n", $sMatchRule);
            if (count($aMatchRule) > 1) {        // OR
                // todo : or
            } else {
                $aFieldInfo = array_map('trim', explode(' = ', $aMatchRule[0]));
                $aTmp = explode(' ', $aFieldInfo[0]);
                $iOperator = (count($aTmp) > 1) ? $aTmp[1] : '=';
                if (!mb_strlen($mValue) && $aParam['limit_when_null']) {
                    $aFieldInfo[1] = null;
                }
                list($tmp, $sField) = explode('.', $aTmp[0]);
                $sField{0} == '$' or $sColumn = $sField;
                if (isset($aConditions[$sColumn])) {
                    // TODO 原来的方式from/to的值和search_items表中的记录的顺序强关联, 考虑修改为自动从小到大排序的[from, to]数组
                    $arr = [$aConditions[$sColumn][1], $aFieldInfo[1]];
                    sort($arr);
                    $aConditions[$sColumn] = [ 'between', $arr];
                } else {
                    if ($aFieldInfo[1] == "NULL") {
                        $aConditions[$sColumn] = [ $iOperator, null];
                    } else {
                        $aConditions[$sColumn] = [ $iOperator, $aFieldInfo[1]];
                    }
                }
            }
        }
        return $aConditions;
    }

    /**
     * 根据搜索配置生成搜索表单数据
     */
    function _setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('bNeedCalendar'));
//        !$bNeedCalendar or $this->setvars('aDateObjects',[]);
        $this->setVars('aSearchConfig', $this->searchConfig);
        $this->setVars('aSearchFields', $this->params);
        $this->addWidget($this->searchBlade);
    }

    function _makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
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
                        case 'formatted_status':
                            $a[] = $oDeposit->formatted_status;
                            break;
                        case 'bank':
                            $a[] = $aBanks[$oDeposit->$key];
                            break;
                        case 'user':
                            $a[] = $aUser[$oDeposit->$key];
                            break;
                        case 'deposit_mode':
                            $a[] = $oDeposit->formatted_deposit_mode;
                            break;
                        case 'deposit_add_game_money_time':
                            if ($key == 'updated_at') {
                                if ($oDeposit->status == Deposit::DEPOSIT_STATUS_SUCCESS && is_object($oDeposit->updated_at)) {
                                    $a[] = $oDeposit->updated_at->toDateTimeString();
                                } else {
                                    $a[] = '';
                                }
                            }
                            break;
                        case 'format_number2':
                            $a[] = number_format($oDeposit->$key, 2);
                            break;
                        case 'transaction_amount_formatted':
                            $a[] = ($oDeposit->is_income ? '+' : '-') . $oDeposit->amount;
                            break;
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = $oDeposit->$key;
                            }
                            break;
                        case 'boolean':
                            $a[] = $oDeposit->$key ? __('Yes') : __('No');
                            break;
                        case 'lottery':
                            $aLotteries = Lottery::getTitleList();
                            if (array_key_exists($oDeposit->$key, $aLotteries)) {
                                $a[] = $aLotteries[$oDeposit->$key];
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'way':
                            $aWays = SeriesWay::getTitleList();
                            if (array_key_exists($oDeposit->$key, $aWays)) {
                                $a[] = $aWays[$oDeposit->$key];
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'coefficient':
                            $aCoefficients = Config::get('bet.coefficients');
                            $a[] = $aCoefficients[$oDeposit->$key];
                            break;
                        case 'friendly_description':
                            $aCoefficients = Config::get('bet.coefficients');
                            $a[] = $oDeposit->friendly_description;
                            break;
                        case 'friendly_agent_name':
                            if ($aIds = array_get($oDeposit, 'user_forefather_ids')) {
                                $aIds = explode(',', $aIds);
                                $user = User::find($aIds[(count($aIds) - 1)]);
                                if (is_object($user)) {
                                    $a[] = $user->username;
                                } else {
                                    $a[] = '';
                                }
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
