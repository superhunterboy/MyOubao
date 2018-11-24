<?php

class CommissionSettings extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commission_settings';

 
    static $Commission_type = [
            'deposit'=>1,
            'bet' =>2,
            'profit' =>3
    ];
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = false; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'id',
        'commission_type',
        'amount',
        'multiple',
        'return_money_1',
        'return_money_2',
        'return_money_3',
            
    ];
     /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'commission_type',
        'amount',
        'multiple',
        'return_money_1',
        'return_money_2',
        'return_money_3',
    ];
     
            
            
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
         
        'amount'=>'desc'
    ];
            

            
    public static $rules = [
        'commission_type' => 'required|integer|in:1,2,3',
        'amount' => 'required|integer',
        'multiple' => 'required|integer',
        'return_money_1' => 'required|integer',
        'return_money_2' => 'required|integer',
        'return_money_3' => 'required|integer',
            
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = []; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)
    public static function getAllSettingsByTypeId($commission_type = 1){
        $bReadDb = true;
        $bPutCache = false;
        $aCommissionSettings = [];
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = "CommissionSettings_".$commission_type;
            if ($aCommissionSettings = Cache::get($sCacheKey)) {
                $bReadDb = false;
                
            }
            else{
                $bPutCache = true;
            }
        }
        
        if($bReadDb){
            $oCommissionSettings =  self::where('commission_type','=',$commission_type)->orderBy('amount','desc')->get();
            foreach($oCommissionSettings as $setting){
                $aCommissionSettings[] = [
                    'amount'=>$setting->amount,
                    'multiple'=>$setting->multiple,
                    'return_money_1'=>$setting->return_money_1,
                    'return_money_2'=>$setting->return_money_2,
                    'return_money_3'=>$setting->return_money_3
                ];
            }
        }
        
        if($bPutCache){
            Cache::forever($sCacheKey, $aCommissionSettings);
        }
        return $aCommissionSettings;
    }
    
}
