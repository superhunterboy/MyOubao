<?php
namespace JcModel;
/**
 * 彩票系列模型
 */
class OriginalMatches extends JcMatchOriginal {
    
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'OriginalMatches';
    protected $table = 'jc_matchs';

//    public static $checkboxenable = true;

    public static $ignoreColumnsInView = [
        'l_id',
        'h_id',
        'a_id',
        'index_show',
        'show',
        'l_background_color',
        'weather',
        'weather_city',
        'temperature',
        'weather_pic',
        'match_info',
        'last_updated',
        'score_status',
        'created_at'
    ];
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'match_id',
        'id',
        'num',
        'b_date',
        'date',
        'time',
        'status',
        'hot',
        'l_cn',
        'h_cn',
        'a_cn',
//        'l_cn_abbr',
//        'h_cn_abbr',
//        'a_cn_abbr',
//        'score_status',
        'half_score',
        'score',
        'updated_at'
    ];
    protected $fillable = [
        'match_id',
        'id',
        'num',
        'date',
        'time',
        'b_date',
        'status',
        'hot',
        'l_id',
        'l_cn',
        'h_id',
        'h_cn',
        'a_id',
        'a_cn',
        'index_show',
        'show',
        'l_cn_abbr',
        'h_cn_abbr',
        'a_cn_abbr',
        'l_background_color',
        'weather_color',
        'weather',
        'weather_city',
        'temperature',
        'weather_pic',
        'match_info',
        'last_updated',
        'formatted_crs',
        'formatted_hhad',
        'formatted_had',
        'formatted_ttg',
        'formatted_hafu',
        'half_score',
        'score',
        'updated_at'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'b_date' => 'desc',
        'match_id' => 'asc'
    ];

//    public static $htmlSelectColumns = [
//        'status' => 'validStatuses',
//        'hot' => 'validHot'
//    ];

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
        'id' => '',
        'match_id' => 'required',
        'num' => 'required',
        'date' => 'required | date_format:Y-m-d',
        'time' => 'required | date_format:H:i:s',
        'b_date' => 'required | date_format:Y-m-d',
        'l_cn' => 'required',
        'h_cn' => 'required',
        'a_cn' => 'required',
        'l_cn_abbr' => 'required',
        'h_cn_abbr' => 'required',
        'a_cn_abbr' => 'required',
//        'crs' => '',
//        'hhad' => '',
//        'had' => '',
//        'ttg' => '',
//        'hafu' => ''
    ];

    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'hot' => 'formatted_hot'
    ];

    public static $viewColumnMaps = [
        'status' => 'formatted_status',
        'hot' => 'formatted_hot',
        'crs' => 'formatted_crs',
        'hhad' => 'formatted_hhad',
        'had' => 'formatted_had',
        'ttg' => 'formatted_ttg',
        'hafu' => 'formatted_hafu',
    ];
    
    //public $timestamps = false;
    const MATCHE_STATUS_NEW = 0;//待审核
    const MATCHE_STATUS_VERIFIED = 1;//已审核
    const MATCHE_STATUS_TEST = 2;



    const MATCHE_HOT = 1;
    const MATCHE_NOT_HOT = 0;

    public static $applyCanChangeStatus = [
        self::MATCHE_STATUS_NEW
    ];
    public static $manualCanChangeStatus = [
        self::MATCHE_STATUS_VERIFIED
    ];

    public static $validStatuses = [
        self::MATCHE_STATUS_NEW => 'New',
        self::MATCHE_STATUS_VERIFIED => 'Verified',
    ];

    public static $validHot = [
        self::MATCHE_HOT => 'Yes',
        self::MATCHE_NOT_HOT => 'No',
    ];

    protected function getFormattedStatusAttribute() {
        return __('_originalmatches.' . strtolower(\Str::slug(static::$validStatuses[$this->attributes['status']])));
    }


    protected function getFormattedHotAttribute() {
        return __('_originalmatches.' . strtolower(\Str::slug(static::$validHot[$this->attributes['hot']])));
    }

    protected function getFormattedCrsAttribute() {
        $aCrs = json_decode($this->attributes['crs'],true);
        $aCrs = is_array($aCrs) ? $aCrs : json_decode($aCrs,true);
        $crs_pattern = '/^([-0])([0-5])([-0])([adh0-5])$/';
        $str = '';
        if(empty($aCrs)) return false;
        $aData = [];
        foreach($aCrs as $key => $value){
            if(preg_match($crs_pattern,$key,$matches)){
                switch($matches[4]){
                    case 'a' :
                        $aData['key'][$key] = '负其他';
                        break;
                    case 'd' :
                        $aData['key'][$key] = '平其他';
                        break;
                    case 'h' :
                        $aData['key'][$key] = '胜其他';
                        break;
                    default :
                        $aData['key'][$key] = $matches[2].':'.$matches[4];
                        break;
                }
                $aData['value'][$key] = $value;
            }
        }
        return $aData;
    }


    protected function getFormattedTtgAttribute() {
        $aTtg = json_decode($this->attributes['ttg'],true);
        $ttg_pattern = '/^s([0-7])$/';
        $str = '';
        if(!is_array($aTtg)) return false;
        $aData = [];
        foreach($aTtg as $key => $value){
            if(preg_match($ttg_pattern,$key,$matches)){
                if($matches[1] < 7)
                    $aData['key'][$key] = $matches[1].'球';
                else
                    $aData['key'][$key] = $matches[1].'+球';

                $aData['value'][$key] = $value;
            }
        }

        return $aData;
    }

    protected function getFormattedHafuAttribute() {
        $aHafu = json_decode($this->attributes['hafu'],true);
        $hafu_pattern = '/^([adh])([adh])$/';
        $str = '';
        if(!is_array($aHafu)) return false;
        foreach($aHafu as $key => $value){
            if(preg_match($hafu_pattern,$key,$matches)){
                $aData['key'][$key] = self::strReplaceAssoc($key,['h' => '胜', 'd' => '平', 'a' => '负']);
                $aData['value'][$key] = $value;
            }
        }

        return $aData;
    }

    protected function getFormattedHhadAttribute() {
        return self::hadFormat('hhad');
    }

    protected function getFormattedHadAttribute() {
        return self::hadFormat('had');
    }

    private function hadFormat($method){
        $aHhad = json_decode($this->attributes[$method],true);
        $aData = [];
        if($method == 'hhad') $aData['fixedodds'] = $aHhad['fixedodds'];
        $had_hhad_pattern = '/^[adh]$/';
        if(!is_array($aHhad)) return false;
        foreach($aHhad as $key => $value){
            if(preg_match($had_hhad_pattern,$key,$matches)){
                switch($matches[0]){
                    case 'a' :
                        $aData['key'][$key] = '负';
                        break;
                    case 'd' :
                        $aData['key'][$key] = '平';
                        break;
                    case 'h' :
                        $aData['key'][$key] = '胜';
                        break;
                }
                $aData['value'][$key] = $value;
            }
        }
        return $aData;
    }

    private static function strReplaceAssoc( $subject, array $replace = ['h' => '3', 'd' => '1', 'a' => '0']) {
        return str_replace(array_keys($replace), array_values($replace), $subject);
    }

    public function getBetDate(){
        $Y = substr($this->match_id, 0, 4);
        $M = substr($this->match_id, 4, 2);
        $D = substr($this->match_id, 6, 2);
        return "{$Y}-{$M}-{$D}";
    }
}
