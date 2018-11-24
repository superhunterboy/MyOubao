<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActivityMdSetting extends BaseModel {

    /**
     *
     * @var string
     */
    protected $table = 'activity_md_setrule';
//    public $orderColumns = ['status' => 'desc', 'id' => 'desc'];

    /**
     * 软删除
     * @var boolean
     */
//    protected $softDelete = false;
    protected $fillable = [
        'name',
        'vr_price',
        'day_times',
        'total_times',
        'rand_num',
        'fixed_totals',
        'gift_totals',
        'level',
        'limits',
        'type_hand',
        'price',
        'content',
        'type',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'ActivityMdSetting';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'vr_price',
        'day_times',
        'total_times',
        'rand_num',
        'fixed_totals',
        'gift_totals',
        'level',
        'limits',
        'type_hand',
        'price',
        'content',
        'type',
        'created_at',
        'updated_at',
    ];
    public static $titleColumn = 'id';
    public static $rules = [
        'name' => 'required',
        'vr_price' => 'required',
        'day_times' => 'required',
        'total_times' => 'required',
         'level' => 'required',
        'limits' => 'required',
        'rand_num' => 'required', //^\d+(,\d+)*$
        'gift_totals' => 'required',
         'content' => 'required',
         'type_hand' => 'required',
         'type' => 'required',
         'price' => 'required',
          'fixed_totals' => 'required',
        
    ];/**
     * 下拉列表框字段配置
     * @var array
     */
    
    const AUTORECHARGE = 1;
    const HUMANRECHARGE = 2;
    public static $htmlSelectColumns = [
        'type' => 'aTypeId',
    ];

       public static function getPerHandRule($id){
//           return ActivityMdSetting::whereRaw('find_in_set(?, type_hand)', [$id])->get();
           return ActivityMdSetting::where('type_hand', $id)->get();
       }

        public static function getAvaiableRule($profit, $hand_type){
            if(!isset($profit) || $hand_type <= 0){
                return [];
            }
            return ActivityMdSetting::where('vr_price', '<=', $profit)->whereRaw('find_in_set(?, limits)', [$hand_type])->orderBy('vr_price', 'desc')->get();
        }

        public static function updateGiftTotals($id){
            if(empty($id)){
                return false;
            }
            return ActivityMdSetting::where('id', $id)->where('gift_totals', '>', 0)->decrement('gift_totals');
        }
        
        public static function autoWay(){
            return [self::AUTORECHARGE=>"自动充值", self::HUMANRECHARGE=>"人工充值"];
        }
}
