<?php

class CommissionConfig extends BaseModel {

    public static $resourceName      = 'CommissionConfig';
    protected $table                 = 'commission_configs';

    //protected $primaryKey = 'way_id';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'way_id',
        'level',
        'commission_rate',
        'rule',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'way_id' => 'aSeriesWays',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'way_id'    => 'required|integer',
        'level'    => 'required|integer',
        'commission_rate'     => 'required|numeric',
        'rule'            => 'max:50',
    ];

    protected $fillable = [
        'way_id',
        'level',
        'commission_rate',
        'rule',
    ];

    /**
     * @param $way_id
     * @param $betNumber
     * @param $betAmount
     * @return bool|float|int
     */
    public static function getCommissionAmount($way_id, $betNumber, $betAmount){

        $splitChar = Config::get('bet.split_char') or $splitChar = '|';
        $aBetNumber = array_flip(explode($splitChar, $betNumber));

        $oLevels = self::where('way_id', '=', $way_id)->get()->toArray();

        if(empty($oLevels) || !is_array($oLevels)) return false;

        $commission = 0;

        foreach($oLevels as $oLevel)
        {
            if(empty($oLevel['rule'])){
                $commission = $betAmount*$oLevel['commission_rate'] / 100;
                break;
            }
            foreach(explode(',',$oLevel['rule']) as $sRule){
                if(isset($aBetNumber[$sRule])) $commission += $betAmount/count($aBetNumber) * $oLevel['commission_rate'] / 100;
            }
        }

        return $commission;
    }

}
