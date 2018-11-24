<?php

class RechargeWhiteName extends BaseModel {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recharge_white_name';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'RechargeWhiteName';

    public static $columnForList = [
        'name',
        'admin_name',
        'admin_id',
        'created_at',
        'updated_at',
    ];


    protected $fillable = [
          'name',
        'admin_name',
        'admin_id',
        'created_at',
        'updated_at',
    ];
    public static $rules = [
        'name'       => 'required',
//        'admin_name'       => 'required',
//        'admin_id'      => 'required',
    ];
    public $orderColumns = [
        'id' => 'desc'
    ];

   public static function getWhiteNameByName($iName){
       if(empty($iName)){
           return false;
       }
       return self::where('name', $iName)->first();
   }
  
   public static function IsAdminUserExists($iName){
       if(empty($iName)){
           return false;
       }
       return AdminUser::where('username', $iName)->first();
   }
}


