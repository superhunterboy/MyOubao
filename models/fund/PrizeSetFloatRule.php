<?php

/**
 * 总代升降奖金组规则类
 *
 * @author frank
 */
class PrizeSetFloatRule extends BaseModel {

    const FLOAT_TYPE_STAY = 0;
    const FLOAT_TYPE_UP = 1;
    const NUMBER_WAN = 10000;

    protected $table = 'prize_set_float_rules';
    public static $resourceName = 'PrizeSetFloatRule';
    public static $treeable = false;
    public static $sequencable = false;
    protected $softDelete = false;
    protected $fillable = [
        'is_up',
        'days',
        'classic_prize',
        'turnover',
        'prize_group_set',
    ];
    public static $prizeSet = [1956, 1957, 1958, 1959, 1960];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'is_up' => 'required|integer|in:0,1',
        'days' => 'required|integer|min:1',
        'classic_prize' => 'required|integer',
        'turnover' => 'required|integer|min:0',
//        'prize_group_set' => 'required|max:10240',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'days' => 'asc',
        'classic_prize' => 'asc',
    ];

    public static function & getRuleList() {
        $aFields = [
            'id',
            'is_up',
            'days',
            'turnover',
            'classic_prize',
        ];
        $oOriginRules = self::all($aFields);
        $aData = [];
        foreach ($oOriginRules as $oRule) {
            $aData[$oRule->is_up][$oRule->days][$oRule->classic_prize] = $oRule->turnover / self::NUMBER_WAN;
        }
        return $aData;
    }

    public static function getRuleObject($bUp, $iDays, $iClassicPrize) {
        $aConditons = [
            'is_up' => ['=', intval($bUp)],
            'days' => ['=', $iDays],
            'classic_prize' => ['=', $iClassicPrize],
        ];
        $oRule = self::doWhere($aConditons)->get()->first();
        if (!is_object($oRule)) {
            $data = [
                'is_up' => intval($bUp),
                'days' => $iDays,
                'classic_prize' => $iClassicPrize,
            ];
            $oRule = new PrizeSetFloatRule($data);
        }
        return $oRule;
    }

    public static function updateRule($bUp, $iDays, $iClassicPrize, $iTurnOver) {
        $oRule = self::getRuleObject($bUp, $iDays, $iClassicPrize);
        $oRule->turnover = $iTurnOver;
        return $oRule->save(self::$rules);
    }

    public static function deleteDataByDayUp($bUp, $iDay) {
        return self::where('is_up', '=', $bUp)->where('days', '=', $iDay)->delete();
    }

    /**
     * 根据天数返回最近的升降点奖金组条件数组
     * @param int $iDay
     * @return array
     */
    public static function getRulesByDayRange($sLastCalculateFloatDate) {
        $oOriginRules = self::getRuleList();
        $aData = [];
        if ($sLastCalculateFloatDate) {
            $bUp = $bDown = true;
            $iDay = daysbetweendates(date('Y-m-d H:i:s', time()), $sLastCalculateFloatDate);
            foreach ($oOriginRules as $bUpDown => $oRule) {
                foreach ($oRule as $day => $data) {
                    if ($bUpDown == self::FLOAT_TYPE_UP && $bUp) {
                        // 升点条件
                        if ($iDay <= $day) {
                            $aData['up']['days'] = $day;
                            $aData['up']['prizeset'] = $data;
                            $bUp = false;
                        }
                    } else if ($bUpDown == self::FLOAT_TYPE_STAY && $bDown) {
                        //降点条件
                        if ($iDay <= $day) {
                            $aData['down']['days'] = $day;
                            $aData['down']['prizeset'] = $data;
                            $bDown = false;
                        }
                    }
                }
            }
        } else {
            foreach ($oOriginRules as $bUpDown => $oRule) {
                foreach ($oRule as $day => $data) {
                    if ($bUpDown == self::FLOAT_TYPE_UP) {
                        // 升点条件
                        if (!isset($aData['up'])) {
                            $aData['up']['days'] = $day;
                            $aData['up']['prizeset'] = $data;
                        } else {
                            if ($aData['up']['days'] > $day) {
                                $aData['up']['days'] = $day;
                                $aData['up']['prizeset'] = $data;
                            }
                        }
                    } else if ($bUpDown == self::FLOAT_TYPE_STAY) {
                        //降点条件
                        if (!isset($aData['down'])) {
                            $aData['down']['days'] = $day;
                            $aData['down']['prizeset'] = $data;
                        } else {
                            if ($aData['down']['days'] > $day) {
                                $aData['down']['days'] = $day;
                                $aData['down']['prizeset'] = $data;
                            }
                        }
                    }
                }
            }
        }
        return $aData;
    }

}
