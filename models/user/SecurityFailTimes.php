<?php

class SecurityFailTimes extends BaseModel {
    protected $table = 'security_fail_times';
    protected $fillable = [
        'user_id',
        'times',
    ];
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
      'user_id',
        'times',
    ];
    
    public static $rules = [
        'user_id'                      => 'required',
        'times'                      => '',
    ];
    
    public static function isUserExistsByUserId($iUserId){
        if(empty($iUserId)){
            return false;
        }
        return self::where('user_id', $iUserId)->first();
    }
    
    public static function insertData($data){
        return self::insert($data);
    }
    public static function updateTimesByUserId($iUserId){
        return self::where('user_id',$iUserId)->increment('times', 1);
    }
    
    public static function getTimesByUserId($iUserId){
        return self::where('user_id', $iUserId)->first();
    }
    
    public static function updateFailTimesByUserId($iUserId){
        return self::where('user_id', $iUserId)->update(array('times'=> 0));
    }
    
}

