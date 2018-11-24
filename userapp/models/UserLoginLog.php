<?php
class UserLoginLog extends BaseModel{

    protected $table = 'user_login_logs';
    protected $table_suffix = 10;
    protected $fillable = [
        'user_id',
        'username   ',
        'is_tester',
        'forefather_ids',
        'parent',
        'parent_id',
        'client_ip',
        'proxy_ip',
        'created_at',
    ];

    public static $columnForList = [
        'user_id',
        'username',
        'is_tester',
        'forefather_ids',
        'client_ip',
        'proxy_ip',
        'created_at',
    ];

    public static $rules = [
        'user_id'                       => 'Integer',
        'username'                      => 'required|regex:/^[a-zA-Z0-9]{6,16}$/',
        'is_tester'                     => 'in:0, 1',
        'forefather_ids'                => 'between:0,100',
        'created_at'                    => 'date',
        'client_ip'                     => 'between:0,15',
        'proxy_ip'                      => 'between:0,15',
    ];

    public static function userLog($oUser=null){
        if(!$oUser) {
            return false;
        }

        $UserLoginLog = new UserLoginLog();
        $t = $UserLoginLog->getTableName($oUser->id);

        $UserLoginLog->setTable($t);

        $UserLoginLog->user_id = $oUser->id;
        $UserLoginLog->username = $oUser->username;
        $UserLoginLog->is_tester = $oUser->is_tester;
        $UserLoginLog->forefather_ids = $oUser->forefather_ids;
        $UserLoginLog->parent_id=$oUser->parent_id;
        $UserLoginLog->parent = $oUser->parent;
        $UserLoginLog->client_ip = get_client_ip();
        $UserLoginLog->proxy_ip = get_proxy_ip();


        return $UserLoginLog->save();
    }

    public function getTableName($iUserId){

        return $this->table.'_'. ($iUserId % $this->table_suffix);
    }

}