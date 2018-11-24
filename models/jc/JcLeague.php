<?php

namespace JcModel;
/**
 * 联赛模型
 */
class JcLeague extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcLeague';
    protected $table = 'jc_league';
    public static $titleColumn = 'name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'short_name',
        'original_id'
    ];
    protected $fillable = [
        'id',
        'name',
        'short_name',
        'original_id'
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

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [];
    
    //public $timestamps = false;

    public static function saveLeague($oMatche){
        $aLeagueData = [
            'name' => $oMatche->l_cn,
            'short_name' => $oMatche->l_cn_abbr,
            'original_id' => $oMatche->l_id
        ];
        $oLeague = self::where('original_id','=',$oMatche->l_id)->first();
        if(!$oLeague)
            return self::create($aLeagueData);
        else
            return $oLeague;
    }
    
    public static function getByIds($aIds = []){
        if (!is_array($aIds) || count($aIds) <= 0){
            return [];
        }
        $oQuery = self::whereIn('id', $aIds)->get();
        $aList = [];
        foreach($oQuery as $oRow){
            $aList[$oRow->id] = $oRow;
        }
        return $aList;
    }
}
