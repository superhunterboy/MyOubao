<?php

class UserUser extends User {

    const ERRNO_LOGIN_EXPIRED = -1900;
    const ERRNO_LOGIN_FAILED = -1901;
    const ERRNO_USER_LOGIN_BLOCKED = -1902;
    const ERRNO_LOGIN_SUCCESS = -1903;
    const ERRNO_LOGINOUT_SUCCESS = -1904;
    const ERRNO_REGISTER_ERROR = -1905;
    const ERRNO_MISSING_USERNAME = -1906;
    const ERRNO_EXIST_USERNAME = -1907;
    const ERRNO_MISSING_EMAIL = -1908;
    const ERRNO_EXIST_EMAIL = -1909;
    const ERRNO_REGISTER_SUCCESS = -1910;
    const ERRNO_USER_INFO_GENERATED = -1911;
    const ERRNO_PASSWORD_GENERATED = -1912;
    const NO_PASSWORD = -1913;
    const ERRNO_USER_FUND_BLOCKED = -1914;
    const ERRNO_USER_BET_BLOCKED = -1915;
    const ERRNO_MISSING_FUND_PASSWORD = -1916;
    const ERRNO_WRONG_FUND_PASSWORD = -1917;
    const ERRNO_SAME_WITH_PASSWORD = -1918;
    const ERRNO_FUND_PASSWORD_UPDATED = -1919;
    const ERRNO_UPDATE_FUND_PASSWORD_FAILED = -1920;
    const ERRNO_SAME_WITH_FUND_PASSWORD = -1921;
    const ERRNO_WRONG_PASSWORD = -1922;
    const ERRNO_UPDATE_PASSWORD_FAILED = -1923;
    const ERRNO_PASSWORD_UPDATED = -1924;
    const ERRNO_FUND_PASSWORD_EXIST = -1925;

    protected static $cacheUseParentClass = true;
    protected $isAdmin = false;
    public $orderColumns = [
        'created_at' => 'desc'
    ];
    public static $customMessages = [
        'username.required' => '请填写用户名',
        'username.alpha_num' => '用户名只能由大小写字母和数字组成',
        'username.between' => '用户名长度有误，请输入 :min - :max 位字符',
        'username.unique' => '用户名已被注册',
        'username.custom_first_character' => '首字符必须是英文字母',
        'nickname.required' => '请填写昵称',
        'nickname.between' => '用户昵称长度有误，请输入 :min - :max 位字符',
        'password.custom_password' => '密码由字母和数字组成, 且需同时包含字母和数字, 不允许连续三位相同',
        'password.confirmed' => '密码两次输入不一致',
        'fund_password.custom_fund_password' => '资金密码由字母和数字组成, 且需同时包含字母和数字, 不允许连续三位相同',
        'fund_password.confirmed' => '资金密码两次输入不一致',
            // 'email.required'                  => '请填写邮箱地址',
    ];

    /**
     * 缓存的有效时间
     * @var int
     */
    protected static $cacheMinutes = 1440;

    /**
     * 生成用户唯一标识
     * @return string
     */
    protected function getUserFlagAttribute() {
        $iUserId = $this->id;
        // $sRange = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sRange = 'GqNbzewIF6kfx5mYaAnBEUvMuJyH8o9D7XcWt0hiQKOgRLdlSPpsC2jZ143rTV'; // 使用乱序字串
        if ($iUserId == 0) {
            return $sRange[0];
        }
        $iLength = strlen($sRange);
        $sStr = ''; // 最终生成的字串
        while ($iUserId > 0) {
            $sStr = $sRange[$iUserId % $iLength] . $sStr;
            $iUserId = floor($iUserId / $iLength);
        }
        return $sStr;
    }

    /**
     * [getRegistPrizeGroup 获取注册用户的奖金组信息]
     * @param  [String] $sPrizeGroup [链接开户特征码]
     * @param  &      $aPrizeGroup [奖金组数组的引用]
     * @param  &      $oPrizeGroup [奖金组对象的引用]
     * @return [type]              [description]
     */
    public static function getRegistPrizeGroup($sPrizeGroup = null, & $aPrizeGroup, & $oPrizeGroup) {
        // pr($sPrizeGroup);exit;
        // 如果不是链接开户的注册，提供默认奖金组供注册用
        if (!$sPrizeGroup) {
            $aLotteries = & Lottery::getTitleList();
            $oExpirenceAgent = User::getExpirenceAgent();
            if (!$oExpirenceAgent) {
                return false;
            }
            $iPrizeGroup = $oExpirenceAgent->prize_group;
            // $aPrizeGroup = [];
            foreach ($aLotteries as $key => $value) {
                $aPrizeGroup[] = arrayToObject(['lottery_id' => $key, 'prize_group' => $iPrizeGroup]);
            }
            // 模拟oPrizeGroup对象
            $oPrizeGroup = $oExpirenceAgent;
            $oPrizeGroup->is_admin = 0;
            $oPrizeGroup->is_agent = 1;
            $oPrizeGroup->user_id = $oExpirenceAgent->id;
        } else {
            $oPrizeGroup = UserRegisterLink::getRegisterLinkByPrizeKeyword($sPrizeGroup);
            // pr($oPrizeGroup->toArray());exit;
            // TODO 此处注册失败的具体条件后续可以改进
            if (!$oPrizeGroup) {
                return false;
            }
            // 总代开户链接只能使用一次
            if ($oPrizeGroup->is_admin && $oPrizeGroup->created_count) {
                return false;
                // return Redirect::back()->withInput()->with('error', '该链接已被使用。');
            }
            $aPrizeGroup = json_decode($oPrizeGroup->prize_group_sets, true);
            // pr($aPrizeGroup);exit;
            $aLotteries = & Lottery::getTitleList();

            $iPrizeGroup = $aPrizeGroup[0]['prize_group'];
            $aPrizeGroup = null;
            foreach ($aLotteries as $key => $value) {
                $aPrizeGroup[] = arrayToObject(['lottery_id' => $key, 'prize_group' => $iPrizeGroup]);
            }
        }
        return true;
    }

    public static function getSubUserStatTable($aconditions = [], $iPage = 1, $sort = []) {

        $sql = " select  users.parent_id,users.id,users.username,users.prize_group,users.is_agent,users.register_at,users.signin_at,
	IF(isnull(subuser_stat.sub_user_counts),0,subuser_stat.sub_user_counts) as sub_user_counts,
	IF(isnull(profit_stat.turnover),0,profit_stat.turnover) as turnover,
	IF(isnull(profit_stat.profit),0,profit_stat.profit) as profit,
	IF(isnull(online_user.online),0,1) as online,
        user_prize_group_tmp.forever_prize_group,
        user_prize_group_tmp.tmp_prize_group 
	from 
	users 
         
        left join 
	(
		select count(id) as sub_user_counts,parent_id as u_parent_id from users where not isnull(parent_id)   group by parent_id
	)as subuser_stat
		on users.id = subuser_stat.u_parent_id
        left join  
                user_prize_group_tmp 
                    on user_prize_group_tmp.user_id = users.id
	left join 
	(
		select sum(team_turnover+turnover) as turnover,
			sum(profit+team_profit+commission+team_commission+dividend+team_dividend) as profit,
			user_id 
		from 
			user_profits 
		where date between '" . date('Y-m') . "-01' and '" . date('Y-m-d') . "' 
		group by user_id
	) as profit_stat  
	on users.id = profit_stat.user_id
	left join 
	(
		select user_id as online from user_onlines where expires_time >= UNIX_TIMESTAMP(now()))as online_user
	on users.id = online_user.online where    isnull(deleted_at) and blocked in(if(is_tester=0,0,'0,1,2,3'))";
//       pr($aconditions);exit;
        foreach ($aconditions as $key => $val) {
            if ($key == 'username') {
                $sql.="and  find_in_set(" . Session::get('user_id') . ",forefather_ids)";
            }
            if ($val[0] == 'between') {
                $sql.="and (" . $key . " " . $val[0] . " '" . $val[1][0] . "' and '" . $val[1][1] . "' )";
            } else {
                $sql.=" and " . $key . ' ' . $val[0] . " '" . $val[1] . "'";
            }
        }
        //echo "select count(*) as c from (".$sql.") as tmp";exit;
        $o = DB::select("select count(*) as c from (" . $sql . ") as tmp");
        $counts = $o[0]->c;
        if (!empty($sort)) {
            $sql = "select * from (" . $sql . ") as tmp ";
            foreach ($sort as $key => $val)
                $sql.=" order by " . $val . " " . $key;
        }
        $alimit = [($iPage - 1) * 30, 30];
        $sql .= " limit " . implode(",", $alimit);
//        echo $sql;
        $r = DB::select($sql);
        return ['data' => $r, 'counts' => $counts];
    }

    /**
     * 获取注册次数
     * @param $clientIp
     * @param int $inHour
     * @return mixed
     */
    public static function getRegisterNum($clientIp, $inHour = 24) {

        $key = self::createCacheKey('register_at_' . $inHour . '_' . $clientIp);

        if (!$data = Cache::get($key)) {
            $data = self::where('register_ip', '=', $clientIp)->where('register_at', '>', Carbon::now()->addHours($inHour))->count();

            if (static::$cacheMinutes) {
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $data);
            }
        }
        return $data;
    }

    /**
     * 增加session注册数
     * @param $clientIp
     * @param int $inHour
     */
    public static function addSessionRegisterNum($clientIp, $inHour = 24) {

        $key = self::createCacheKey('register_at_' . $inHour . '_' . $clientIp);
        if ($data = Cache::get($key)) {
            $data = $data + 1;
        } else {
            $data = 1;
        }
        if (static::$cacheMinutes) {
            Cache::put($key, $data, static::$cacheMinutes);
        } else {
            Cache::forever($key, $data);
        }
    }

    /**
     * 团队：代理的注册人数
     * @param $fatherId
     * @return int
     */
    public static function getRegisterNums($parentId, $sBeginDate, $sEndDate) {
        return self::whereRaw(' find_in_set(?, forefather_ids)', [$parentId])
        			->where('register_at', '>=', $sBeginDate)
        			->where('register_at', '<=', $sEndDate)
        			->count();
    }
 
}
