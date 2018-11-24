<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-11
 * Time: 上午11:24
 */

namespace JcModel;


class ManJcMatchMethod extends JcMatchMethod
{
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'match_id',
        'method_id',
        'is_single',
        'lottery_id',
        'is_enable'
    ];
    protected $fillable = [
        'id',
        'match_id',
        'method_id',
        'is_single',
        'lottery_id',
        'is_enable'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    //public $timestamps = false;

    public static $aMaps = [
        'crs' => JcMethod::STRING_IDENTIFIER_CORRECT_SCORE,
        'had' => JcMethod::STRING_IDENTIFIER_WIN,
        'hafu' => JcMethod::STRING_IDENTIFIER_HAFU,
        'hhad' => JcMethod::STRING_IDENTIFIER_HANDICAP_WIN,
        'ttg' => JcMethod::STRING_IDENTIFIER_TOTAL_GOALS,
    ];

    public static $aMethods = [];

    public static function saveSingle($oMatche, $iLotteryId){
        foreach (self::$aMaps as $k => $v) {
            if(!isset(self::$aMethods[$k])) {
                $oMethod = JcMethod::getMethodByIdentifier($iLotteryId,$v);
                self::$aMethods[$k] = $oMethod->id;
            }

            $aMethodInfo = [];
            if($oMatche->$k){
                $aMethodInfo = json_decode($oMatche->$k,true);
            }
            $bSingle = $bEnable = false;
            if(is_array($aMethodInfo) && $aMethodInfo) {
                $bSingle = isset($aMethodInfo['single']) && $aMethodInfo['single'];
                $bEnable = isset($aMethodInfo['p_status']) && $aMethodInfo['p_status'] === 'Selling';
            }
            $aSingleDatas[self::$aMethods[$k]] = [
                'match_id'=>$oMatche->match_id,
                'method_id'=>self::$aMethods[$k],
                'lottery_id'=>$iLotteryId,
                'is_single'=> $bSingle ? 1 : 0,
                'is_enable'=> $bEnable ? 1 : 0,
            ];
        }

        $iCount = self::getByMatchId($oMatche->match_id)->count();
        if ($iCount) {
            return true;
        }

        $iSucc = self::insert($aSingleDatas);
        return $iSucc;
    }

    public static function getByMatchId($iMatchId){
        return self::where('match_id','=',$iMatchId)->get();
    }

    public function getMethodAttribute(){
        $method_id = self::getAttribute('method_id');
        $oMethod = JcMethod::find($method_id);
        return $oMethod->name;
    }

}
