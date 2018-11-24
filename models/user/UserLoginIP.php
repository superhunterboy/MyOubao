<?php

/**
 * 用户登录记录
 *
 * @author white
 */
class UserLoginIP extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_login_ips';
    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'username',
        'is_tester',
        'parent_user',
        'parent_user_id',
        'top_agent_id',
        'top_agent',
        'forefather_ids',
        'forefathers',
        'nickname',
        'ip',
    ];
    public static $resourceName = 'UserLoginIP';
    public static $columnForList = [
        'username',
        'ip',
        'is_tester',
        'top_agent',
        'nickname',
    ];
    public $orderColumns = [
        'username' => 'asc',
    ];
    public static $listColumnMaps = [
        'is_tester' => 'formatted_is_tester',
    ];
    public static $viewColumnMaps = [
        'is_tester' => 'formatted_is_tester',
    ];

    public static function createLoginIPRecord($oUser) {
        $oUserLoginIP = self::getObjectByParams(['user_id' => $oUser->id, 'ip' => $oUser->login_ip]);
        if (is_object($oUserLoginIP)) {
            return true;
        }
        $oUserLoginIP = new static;
        $oUserLoginIP->fill(
                [
                    'user_id' => $oUser->id,
                    'username' => $oUser->username,
                    'is_tester' => $oUser->is_tester,
                    'nickname' => $oUser->nickname,
                    'parent_user' => $oUser->parent,
                    'parent_user_id' => $oUser->parent_id,
                    'forefather_ids' => $oUser->forefather_ids,
                    'forefathers' => $oUser->forefathers,
                    'top_agent_id' => $oUser->getTopAgentId(),
                    'top_agent' => $oUser->getTopAgentUserName(),
                    'ip' => $oUser->login_ip,
                ]
        );
        return $oUserLoginIP->save();
    }

    public static function getLoginUserCount($sBeginDate, $sEndDate = null) {
        $sEndDate or $sEndDate = "$sBeginDate 23:59:59";
        $sSql = "select count(distinct user_id) count from user_logins where created_at between '$sBeginDate' and '$sEndDate' and is_tester = 0";
        $aResults = DB::select($sSql);
        return $aResults[0]->count ? $aResults[0]->count : 0;
    }

    protected function getFormattedIsTesterAttribute() {
        if ($this->attributes['is_tester'] !== null) {
            return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
        } else {
            return '';
        }
    }

}
