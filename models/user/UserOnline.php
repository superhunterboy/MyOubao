<?php

use Illuminate\Support\Facades\Redis;

class UserOnline extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected $table = 'user_onlines';
    protected $fillable = [
        'user_id',
        'session_id',
        'expires_time',
    ];
    public $timestamps = true;

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserOnline';

    /**
     * 用户下线
     * @param $userId
     * @return mixed
     */
    public static function offline($userId) {
        $oUser = self::firstOrCreate(['user_id' => $userId]);
//        $oUser->session_id = Session::getId();
        $oUser->expires_time = time() - 1;
        return $oUser->save();
    }

    /**
     * 用户上线
     * @param $userId
     * @return bool
     */
    public static function online($userId) {
        $oUser = self::firstOrCreate(['user_id' => $userId]);
        $oUser->session_id = Session::getId();
        $oUser->expires_time = time() + Config::get('session.lifetime') * 60;
        return $oUser->save();
    }

    /**
     * 用户是否在线
     * @param $userId
     * @return bool
     */
    public static function isOnline($userId) {
        if ($oUser = self::where('user_id', '=', $userId)->first()) {
            if ($oUser->expires_time > time()) {
                return true;
            }
        }
        return false;
    }

    public static function getListByUserIds($aUserIds = null) {
        if (!$aUserIds || !count($aUserIds))
            return [];
        $oUserOnlines = UserOnline::whereIn('user_id', $aUserIds)->get();
        $data = [];
        foreach ($oUserOnlines as $key => $oUserOnline) {
            $data[$oUserOnline->user_id] = intval(time() < $oUserOnline->expires_time); // status 1: online, 0: offline
        }
        return $data;
    }

    /**
     * 团队的在线数
     * @param $fatherId
     * @return int
     */
    public static function getTeamOnlineNum($fatherId) {
        $onlineNum = 0;

        if ($aUserIds = User::getAllUsersBelongsToAgent($fatherId)) {
            $onlineNum = self::where('expires_time', '>', time())->whereIn('user_id', $aUserIds)->count();
        }

        return $onlineNum;
    }

    /**
     * 团队：代理的在线数
     * @param $fatherId
     * @return int
     */
    public static function getTeamAgentOnlineNum($fatherId) {
        $onlineNum = 0;

        if ($aUserIds = User::getTeamAgentUserIds($fatherId)) {
            $onlineNum = self::where('expires_time', '>', time())->whereIn('user_id', $aUserIds)->count();
        }

        return $onlineNum;
    }

    /**
     * 获取最近的登录记录
     * @param type $username
     */
    public static function getLatestLoginRecord($userId) {
        return self::where('user_id', '=', $userId)->orderBy('id', 'desc')->first();
    }

    /**
     * 单点登录，简易实现
     * @param string $username
     * @param string $oldSession
     * @param string $newSession
     */
    public static function sso($oUser, $oldSession, $newSession) {
//        Log::info('new session=' . $oldSession);
//        Log::info('old session=' . $newSession);
//                    pr($newKey);
        if ($oldSession == $newSession) {
            return;
        }
//        Log::info('delete session');
        $key = Config::get('cache.prefix') . ':' . $oldSession;
//        Log::info('old session key=' . $key);
        $userSessionKey = User::compileUserSessionKey($oUser->username);
        $newKey = Config::get('cache.prefix') . ':' . $newSession;
//        Log::info('new session key=' . $newKey);
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
    public static function ssoLogout($session) {
        $key = Config::get('cache.prefix') . ':' . $session;
        $redis = Redis::connection();
        $redis->select('1');
        if ($redis->exists($key)) {
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

    /**
     * 团队：玩家的在线数
     * @param $fatherId
     * @return int
     */
    public static function getTeamPlayerOnlineNum($fatherId) {
        $onlineNum = 0;

        if ($aUserIds = User::getTeamPlayerUserIds($fatherId)) {
            $onlineNum = self::where('expires_time', '>', time())->whereIn('user_id', $aUserIds)->count();
        }

        return $onlineNum;
    }

    /**
     * 直属玩家数
     * @param $fatherId
     * @return int
     */
    public static function getDirectPlayerOnlineNum($fatherId) {
        //todo
    }

    /**
     * 直属代理数
     * @param $fatherId
     * @return int
     */
    public static function getDirectAgentOnlineNum($fatherId) {
        //todo
    }

}
