<?php

class PrizeSetFloatRuleController extends AdminBaseController {

    protected $resourceView = 'liftRules';
    protected $modelName = 'PrizeSetFloatRule';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
            case 'update':
                $bFloatEnabled = SysConfig::readValue('prize_group_float_enabled');
                $sFloatSeries = SysConfig::readValue('prize_group_float_series');
                $this->setVars('floatEnabled', $bFloatEnabled);
                $this->setVars('floatSeries', $sFloatSeries);
                break;
        }
    }

    public function index() {
        $aPrizeSetFloatRules = PrizeSetFloatRule::getRuleList();
//        pr($aPrizeSetFloatRules);exit;
        $this->setVars('aLiftRules', $aPrizeSetFloatRules);
        $this->setVars('aTopAgentPrizeGroups', PrizeGroup::getTopAgentPrizeGroups());
        return $this->render();
    }

    public function updateRule() {
        if (!empty($this->params)) {
            $bSucc = $this->_saveSysconfig($this->params);
            if (!$bSucc) {
                return $this->goBack('error', __('_prizesetfloatrule.sysconfig-error'));
            }
            if (key_exists('liftType', $this->params)) {
                $aData = [];
                foreach ($this->params['liftType'] as $key => $val) {
                    if (in_array($this->params['day'][$key] . '|' . $val, $aData)) {
                        return $this->goBack('error', '_prizesetfloatrule.' . __(($val ? 'up' : 'down') . '-range-dumplicated'));
                    }
                    $aData[] = $this->params['day'][$key] . '|' . $val;
                }
                foreach ($this->params['liftType'] as $key => $value) {
                    DB::connection()->beginTransaction();
                    $bSucc = true;
                    $aTopAgentPrizeGroups = PrizeGroup::getTopAgentPrizeGroups();
                    $iClassicPrize = $aTopAgentPrizeGroups[0];
                    $iLengthTopAgentPrizeGroups = count($aTopAgentPrizeGroups);
                    for ($i = 0; $i < $iLengthTopAgentPrizeGroups; $i++) {
                        $oPrizeSetFloatRule = PrizeSetFloatRule::getRuleObject($value, $this->params['day'][$key], $iClassicPrize);
                        if (is_object($oPrizeSetFloatRule)) {
                            $oPrizeSetFloatRule->turnover = @$this->params['turnover' . $iClassicPrize][$key] * PrizeSetFloatRule::NUMBER_WAN;
                            $bSucc = $oPrizeSetFloatRule->save(PrizeSetFloatRule::$rules);
                            if (!$bSucc) {
                                return $this->goBack('error', implode(",", $oPrizeSetFloatRule->validationErrors->all()));
                            }
                        } else {
                            PrizeSetFloatRule::updateRule($value, $this->params['day'][$key], $iClassicPrize, $this->params['turnover' . $iClassicPrize][$key] * PrizeSetFloatRule::NUMBER_WAN);
                        }
                        $iClassicPrize++;
                    }
                    if ($bSucc) {
                        DB::connection()->commit();
                    } else {
                        DB::connection()->rollback();
                    }
                }
            }
        }
        $this->action = 'index';
        $aPrizeSetFloatRules = PrizeSetFloatRule::getRuleList();
        $this->setVars('aLiftRules', $aPrizeSetFloatRules);
        return $this->goBackToIndex('success', __('_prizesetfloatrule.saved'));
    }

    /**
     * 保存系统配置参数信息
     * @param array $aParams    页面输入内容数组
     */
    private function _saveSysconfig($aParams) {
        $bSucc = true;
        if (key_exists('float_enabled', $aParams)) {
            $bSucc = SysConfig::setValue('prize_group_float_enabled', 1);
        } else {
            $bSucc = SysConfig::setValue('prize_group_float_enabled', 0);
        }
        $aFloatSeries = [];
        if (key_exists('lottery_series_ssc', $aParams)) {
            $aFloatSeries[] = 1;
        }
        if (key_exists('lottery_series_11y', $aParams)) {
            $aFloatSeries[] = 2;
        }
        if (count($aFloatSeries) > 0) {
            !$bSucc or $bSucc = SysConfig::setValue('prize_group_float_series', implode(',', $aFloatSeries));
        } else {
            !$bSucc or $bSucc = SysConfig::setValue('prize_group_float_series', 'null');
        }
        return $bSucc;
    }

    public function delete() {
        if (!key_exists('is_up', $this->params)) {
            return $this->goBack('error', __('_prizesetfloatrule.missing-isup'));
        }
        if (!key_exists('day', $this->params)) {
            return $this->goBack('error', __('_prizesetfloatrule.missing-day'));
        }
        $bSucc = PrizeSetFloatRule::deleteDataByDayUp($this->params['is_up'], $this->params['day']);
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_prizesetfloatrule.deleted'));
        } else {
            return $this->goBack('error', __('_prizesetfloatrule.deleted-fail'));
        }
    }

}
