<?php

use Illuminate\Support\Facades\Redis;

/**
 * 用户登录记录
 *
 */
class UserLogin extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_logins';
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
        'signed_time',
        'session_id',
        'http_user_agent',
    ];
    public static $resourceName = 'UserLogin';
    public static $columnForList = [
        'username',
        'is_tester',
        'top_agent',
        'nickname',
        'ip',
        'signed_time',
        'http_user_agent',
    ];
    public $orderColumns = [
        'signed_time' => 'desc',
        'username' => 'asc',
    ];
    public static $listColumnMaps = [
        'signed_time' => 'formatted_signed_time',
        'is_tester' => 'formatted_is_tester',
    ];
    public static $viewColumnMaps = [
        'signed_time' => 'formatted_signed_time',
        'is_tester' => 'formatted_is_tester',
    ];

    public static function createLoginRecord($oUser) {
        $oUserLogin = new static;
        $oUserLogin->fill(
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
                    'signed_time' => time(),
                    'session_id' => Session::getId(),
                    'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                ]
        );
        return $oUserLogin->save();
    }

    public static function getLoginUserCount($sBeginDate, $sEndDate = null) {
        $sEndDate or $sEndDate = "$sBeginDate 23:59:59";
        $sSql = "select count(distinct user_id) count from user_logins where created_at between '$sBeginDate' and '$sEndDate' and is_tester = 0";
        $aResults = DB::select($sSql);
        return $aResults[0]->count ? $aResults[0]->count : 0;
    }

    protected function getFormattedSignedTimeAttribute() {
        return date('Y-m-d H:i:s', $this->attributes['signed_time']);
    }

    protected function getFormattedIsTesterAttribute() {
        if ($this->attributes['is_tester'] !== null) {
            return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
        } else {
            return '';
        }
    }

    /**
     * 获取最近的登录记录
     * @param type $username
     */
    public static function getLatestLoginRecord($username) {
        return self::where('username', '=', $username)->orderBy('id', 'desc')->first();
    }

    /**
     * 单点登录，简易实现
     * @param string $username
     * @param string $oldSession
     * @param string $newSession
     */
    public static function sso($username, $oldSession, $newSession) {
//        Log::info('new session=' . $oldSession);
//        Log::info('old session=' . $newSession);
//                    pr($newKey);
        if ($oldSession == $newSession) {
            return;
        }
//        Log::info('delete session');
        $key = Config::get('cache.prefix') . ':' . $oldSession;
        $userSessionKey = User::compileUserSessionKey($username);
        $newKey = Config::get('cache.prefix') . ':' . $newSession;
        $redis = Redis::connection();
        if ($redis->exists($key)) {
//            Log::info('delete old key:' . $key);
            $redis->del($key);
        }
        if ($redis->exists($userSessionKey)) {
            $redis->del($userSessionKey);
        }
        $redis->set($userSessionKey, $newKey);
        $redis->select('1');
        if ($redis->exists($key)) {
//            Log::info('delete old key db1:' . $key);
            $redis->del($key);
        }
        if (!$redis->exists($newKey)) {
//            Log::info('set new key:' . $newKey);
            $redis->set($newKey, date('Y-m-d H:i:s'));
            $redis->expire($newKey, (Config::get('session.lifetime') * 60));
        }
        $redis->select('0');
    }

    /**
     * 单点登录退出，简易实现
     * @param string $username
     * @param string $oldSession
     * @param string $newSession
     */
    public static function ssoLogout($username, $session) {
//        Log::info('old session=' . $session);
//        Log::info('delete session');
        $key = Config::get('cache.prefix') . ':' . $session;
        $redis = Redis::connection();
        $redis->select('1');
        if ($redis->exists($key)) {
//            Log::info('key:' . $key);
            $redis->del($key);
        }
        $redis->select('0');
    }

    /**
     * 单点登录Session延迟，简易实现
     * @param string $username
     */
    public static function ssoSessionExtend($session) {
        $key = Config::get('cache.prefix') . ':' . $session;
        $redis = Redis::connection();
        $redis->select('1');
        if ($redis->exists($key)) {
            $redis->set($key, date('Y-m-d H:i:s'));
            $redis->expire($key, (Config::get('session.lifetime') * 60));
        }
        $redis->select('0');
    }

}
