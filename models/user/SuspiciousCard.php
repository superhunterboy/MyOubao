<?php

# 链接开户管理

class SuspiciousCard extends BaseModel {

    protected $table = 'suspicious_cards';
    public static $resourceName = 'SuspiciousCard';
    protected $softDelete = false;
    const   DISABLESTATUS=1;
    const   ABLESTATUS=0;
    public static $disableStatus=0;
    protected $fillable = [
          'user_id',
        'username',
        'parent_name',
        'parent_id',
        'account_name',
        'account',
        'created_at',
        'updated_at',
        'status',
        'remark',
    ];
     /**
     * 下拉列表框字段配置
     * @var array
     */
//    public static $htmlSelectColumns = [
//        'status' => 'aStatuses',
//            // 'is_agent' => 'aIsAgents',
//    ];

//    public static $aStatuses = [
//        0=> '启用',
//        1 => '停用',
//    ];
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
//          'user_id',
        'username',
        'parent_name',
        'account_name',
        'account',
        'created_at',
        'updated_at',
        'status',
        'remark',
    ];

       public static $rules = [
        'username' => 'required|between:4,32',
        'account_name'       => 'required|between:1,20',
        'account'            => 'required|numeric',
        'remark' => 'max:100',
        'status' => 'in:0,1',
    ];
  
    
}
