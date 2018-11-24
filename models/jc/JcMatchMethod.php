<?php

namespace JcModel;
/**
 * 赛事玩法关联模型
 */
class JcMatchMethod extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcMatchMethod';
    protected $table = 'jc_match_method';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'match_id' => 'required|integer',
        'method_id' => 'required|integer',
        'is_single' => 'sometimes|in:0,1',
        'is_enable' => 'sometimes|in:0,1',
    ];
    
    //public $timestamps = false;
    
    public static function getByMatchIds($aMatchIds){
        $aColumn = ['match_id','method_id','is_enable','is_single'];
//        $oWhere = self::whereIn('match_id',$aMatchIds);
        $oWhere = self::whereIn('match_id',$aMatchIds);
        $oQuery = $oWhere->get($aColumn);
        return $oQuery;
    }
}
