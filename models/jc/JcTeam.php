<?php

namespace JcModel;
/**
 * 参赛队伍模型
 */
class JcTeam extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcTeam';
    protected $table = 'jc_team';
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
    
    protected function getIconUrlAttribute($sIconUrl) {
        if (!isset($sIconUrl)){
            $sIconUrl = '/assets/images/sports/icons/'. $this->original_id . '.png';
            $sPath = dirname(app_path()) . '/userpublic' . $sIconUrl;
            if (!file_exists($sPath)){
                $sIconUrl = '/assets/images/sports/icons/unkown.png';
            }
        }
        return $sIconUrl;
    }

    public static function saveTeams($oMatche){
        $aHostData = [
            'name' => $oMatche->h_cn,
            'short_name' => $oMatche->h_cn_abbr,
            'original_id' => $oMatche->h_id
        ];
        $oHome = self::where('original_id','=',$oMatche->h_id)->first();
        if(!$oHome)
        {
            $oHome = self::create($aHostData);
        }
        $aAwayData = [
            'name' => $oMatche->a_cn,
            'short_name' => $oMatche->a_cn_abbr,
            'original_id' => $oMatche->a_id
        ];
        $oAway = self::where('original_id','=',$oMatche->a_id)->first();
        if(!$oAway)
        {
            $oAway = self::create($aAwayData);
        }

        return [$oHome->id,$oAway->id];
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
