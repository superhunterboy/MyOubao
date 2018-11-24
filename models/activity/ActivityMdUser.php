<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActivityMdUser extends BaseModel {

    /**
     * 
     * @var string
     */
    protected $table = 'activity_md_user';
//    public $orderColumns = ['status' => 'desc', 'id' => 'desc'];

    /**
     * 软删除
     * @var boolean
     */
//    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'user_name',
        'reward',
        'vr_price',
        'level',
        'hand_name',
        'client_ip',
         'price',
        'status',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'ActivityMdUser';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
           'user_id',
        'user_name',
        'reward',
        'vr_price',
         'level',
        'hand_name',
        'client_ip',
        'price',
        'status',
          'created_at',
        'updated_at',
    ];
    public static $listColumnMaps = [
        'status' => 'status_formatted',
    ];
    const SENDPRIZESUCCESS = 1;     //派奖
    const SENDPRIZEFALSE = 0;   //未派奖
    const NOREWARD = 2;   //未派奖
    public static $titleColumn = 'id';
    public static $rules = [
//        'user_id' => 'required',
//        'user_name' => 'required',
//        'total_vr_price' => 'required',
//        'total_times' => 'required',

    ];
       public static $validStatus = [
        self::SENDPRIZESUCCESS => 'sent prize',
        self::SENDPRIZEFALSE => 'win prize',
        self::NOREWARD => 'no reward'
    ];
    /**
     * 
     * 下拉列表框字段配置
     * @var array
     */
//    public static $htmlSelectColumns = [
//        'status' => 'aStatus',
//    ];
       
    protected function getStatusFormattedAttribute() {
        if (isset(self::$validStatus[$this->attributes['status']])){
            self::comaileLangPack();
            return self::translate(self::$validStatus[$this->attributes['status']]);
        }
        return '';
    }

    public static function getUserTotalTimes($userid, $start, $end){
        if(empty($userid) || empty($start) || empty($end)){
            return false;
        }
        return ActivityMdUser::where('user_id', $userid)->where('created_at', '>=', $start)->where('created_at', '<=', $end)->count();
    }
    
    public static function getDayTotalTimes($userid, $start, $end, $reward_id){
        if(empty($userid) || empty($start) || empty($end) || empty($reward_id)){
            return false;
        }
        return ActivityMdUser::where('user_id', $userid)->where('created_at', '>=', $start)->where('created_at', '<=', $end)->where('reward_id', $reward_id)->count();
    }

    public static function getUserRewardTimes($userid, $reward_id){
        if(empty($userid) || empty($reward_id)){
            return false;
        }
        return ActivityMdUser::where('user_id', $userid)->where('reward_id', $reward_id)->count();
    }
    
    public static function insertData($aUser){
        if(empty($aUser)){
            return false;
        }
        return  ActivityMdUser::insert($aUser); 
    }
    
    public static function updateSendPrize($id){
        return self::where('id', $id)->update(['status'=>self::SENDPRIZESUCCESS]);
    }
    public static function updateSendFalse($id){
        return self::where('id', $id)->update(['status'=>self::SENDPRIZEFALSE]);
    }
    
    public static function getUserData($id){
        return self::where('id', $id)->first();
    }
    
    public static function getUserPrizeDayTotalTimes($userid, $start, $end){
        if(empty($userid) || empty($start) || empty($end)){
            return false;
        }
         return ActivityMdUser::where('user_id', $userid)->where('created_at', '>=', $start)->where('created_at', '<=', $end)->where('reward_id', '<>', " ")->count();
    }
}
