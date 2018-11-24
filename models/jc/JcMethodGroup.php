<?php

namespace JcModel;
/**
 * 玩法模型
 */
class JcMethodGroup extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcMethodGroup';
    protected $table = 'jc_method_groups';
    
    public static $titleColumn = 'name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'lottery_id',
        'name',
        'identifier',
        'methods',
        'sequence',
    ];
    protected $fillable = [
        'id',
        'lottery_id',
        'name',
        'identifier',
        'methods',
        'sequence',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [
        'lottery_id' => 'validLotteries'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id' => 'required|integer',
        'name' => 'required',
        'identifier' => 'required',
        'methods' => 'required',
    ];
    
    public static $sequencable = true;
    
    public static function getMenuByIdentifier($iLotteryId = 0, $sIdentifier = ''){
        $aList = self::getMenuByLotteryId($iLotteryId);
        foreach($aList as $val){
            if ($val->identifier == $sIdentifier){
                return $val;
            }
        }
    }
    public static function getBasicByIdentifier($iLotteryId = 0, $sIdentifier = ''){
        $aList = self::getBasicByLotteryId($iLotteryId);
        foreach($aList as $val){
            if ($val->identifier == $sIdentifier){
                return $val;
            }
        }
    }
    
    public static function getAllByLotteryId($iLotteryId = 0){
        $oQuery = self::where('lottery_id', $iLotteryId)->orderby('sequence', 'desc')->get();
        $aList = [];
        foreach($oQuery as $oRow){
            $aList[$oRow->id] = $oRow;
        }
        return $aList;
    }
    
    public static function getMenuByLotteryId($iLotteryId = 0){
        $aList = self::getAllByLotteryId($iLotteryId);
        foreach($aList as $key => $val){
            if (!$val->is_menu){
                unset($aList[$key]);
            }
        }
        return $aList;
    }
    
    public static function getBasicByLotteryId($iLotteryId = 0){
        $aList = self::getAllByLotteryId($iLotteryId);
        foreach($aList as $key => $val){
            if (!$val->is_basic){
                unset($aList[$key]);
            }
        }
        return $aList;
    }

    public static function getAllBasic(){
        $aList = self::all();
        foreach($aList as $key => $val){
            if (!$val->is_basic){
                unset($aList[$key]);
            }
        }
        return $aList;
    }
    
    public static function getBasicTitleList(){
        $aList = self::getAllBasic();
        $aData = [];
        foreach ($aList as $v){
            $aData[$v->id] = $v->{self::$titleColumn};
        }
        return $aData;
    }
}
