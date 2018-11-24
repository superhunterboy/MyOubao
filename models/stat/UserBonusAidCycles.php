<?php

/**
 * 分红统计表
 *
 * @author abel
 */
class UserBonusAidCycles extends BaseModel {

    protected $table = 'user_bonus_aid_cycles';
    public static $resourceName = 'UserBonusAidCycles';
    public  $primaryKey='user_id';
    public static $columnForList = [
        'user_id',
        'cycles',
        'username',
    ];
    public $orderColumns = [
        'created_at' => 'asc',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'cycles',
        ];
      public static $rules = [
        'username' => 'required|max:16',
        'user_id' => 'required|integer',
          'cycles'=>'required|integer'
          ];
    

}
