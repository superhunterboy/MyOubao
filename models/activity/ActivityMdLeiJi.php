<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActivityMdLeiJi extends BaseModel {

    /**
     * The database table used by the model.
     `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL DEFAULT '' COMMENT '用户id',
  `total_ls` decimal(16,4) NOT NULL DEFAULT '0.0000' COMMENT '用户活动总流水',
  `total_vr_price` decimal(16,4) NOT NULL DEFAULT '0.0000' COMMENT '用户活动总的虚拟定价',
  `total_times` int(11) NOT NULL DEFAULT '0' COMMENT '活动期间单人最高的中奖次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
     * @var string
     */
    protected $table = 'activity_md_leiji';
//    public $orderColumns = ['status' => 'desc', 'id' => 'desc'];

    /**
     * 软删除
     * @var boolean
     */
//    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'total_ls',
        'total_vr_price',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'ActivityMdAdd';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
          'user_id',
        'total_ls',
        'total_vr_price',
        'created_at',
        'updated_at',
    ];
    public static $titleColumn = 'id';
    public static $rules = [
        'user_id' => 'required',
        'total_ls' => 'required',
        'total_vr_price' => 'required',

    ];/**
     * 下拉列表框字段配置
     * @var array
     */
//    public static $htmlSelectColumns = [
//        'status' => 'aStatus',
//    ];

    public static function getAllUserLs($userid) {
        if (empty($userid)) {
            return false;
        }
        return ActivityMdLeiJi::where('user_id', $userid)->first();
    }
    
    public static function updateLs($oUserData, $oHand, $price){
        if(empty($oUserData) && empty($oHand) && empty($price)){
            return false;
        }
        $oUserData->total_ls = $oUserData->total_ls +  $oHand->money;
        $oUserData->total_vr_price =  $oUserData->total_vr_price + $price;
        return $oUserData->update();
    }
    
    public static function insertData($aUserLeiJi){
        if(empty($aUserLeiJi)){
            return false;
        }
        return ActivityMdLeiJi::insert($aUserLeiJi);
    }
    
     public static function getSumLsWithCache(){
        $iUserId = Session::get('user_id');
        
        $sCacheKey = self::getIntegraCacheKey($iUserId);
      
        if( Cache::has($sCacheKey)){
             $sumLs = Cache::get($sCacheKey);
        }else{
            $oActivity = ActivityMd::getActivity();
            $start_time = date("Y-m-d", strtotime($oActivity->start_time));
            $end_time = date("Y-m-d", strtotime($oActivity->end_time));
            $profitLs = UserProfit::getSumProfitOrLs($iUserId, 'turnover', $start_time, $end_time);  //流水
            $profitSlotLs = UserProfitSlot::getAllProfltSlotOrLs($iUserId, 'turnover', $start_time, $end_time);   //电子流水
            $oTotalLs = ActivityMdLeiJi::getAllUserLs($iUserId);   //总的流水
              if(empty($oTotalLs->total_ls)){
                    $sumLs = $profitLs + $profitSlotLs;        //总流水
              }else{
                    $sumLs = $profitLs + $profitSlotLs - $oTotalLs->total_ls;        //总流水
              }
              $sumLs = floor($sumLs);
              if($sumLs <= 0){
                  $sumLs = 0;
              }
            $date = date("Y-m-d H:i:s", time());
            if ($oActivity->start_time > $date || $date > $oActivity->end_time){
               $sumLs = -1;
            }
              Cache::put($sCacheKey, $sumLs, 300);
        }
        return $sumLs;
    }
    
    public static function getIntegraCacheKey($userid){
        return 'per_user_ls_' . $userid;
    }
    public static function deleteIntegraCache(){
        $iUserId = Session::get('user_id');
        $sCacheKey = self::getIntegraCacheKey($iUserId);
        return  Cache::forget($sCacheKey);
    }
}
