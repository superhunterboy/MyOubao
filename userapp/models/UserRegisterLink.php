<?php
# 链接开户管理
class UserRegisterLink extends RegisterLink {
    protected $table = 'register_links';

    public static $resourceName = 'UserRegisterLink';

    protected $isAdmin = false;

    public static $customMessages = [
        'username.required'     => '缺少开户人信息',
        'username.between'      => '开户人用户名长度必须介于 :min - :max 个字符之间',
        'valid_days.integer'    => '有效天数只能是整数',
        'keyword.required'      => '开户链接特征码缺失',
        'note.max'              => '备注信息长度有误，不能超过 :max 个字符',
        'channel.max'           => '推广渠道信息长度有误，不能超过 :max 个字符',
        'created_count.integer' => '注册人数只能是整数',
        'url.max'               => '推广链接长度有误，不能超过 :max 个字符',
    ];

    protected function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->url or $this->url = route('signup', ['prize' => $this->keyword]);
        }
       
        // pr($this->toArray());exit;
        return true;
    }

    public static function getRegisterLinkByPrizeKeyword($sKeyword)
    {
        // pr($sKeyword);exit;
        return self::where('keyword', '=', $sKeyword)->where('status', '=', 0)
                ->whereRaw(' (expired_at > ? or expired_at is null)', [Carbon::now()->toDateTimeString()])->first();
                // ->where('expired_at', '>', Carbon::now()->toDateTimeString())->orWhereNull('expired_at')->first();
    }


    /**
     * 获取已注册的可用链接数
     * @param $userId
     * @return mixed
     */
    public static function getAvailableRegisterNum($userId)
    {
        return self::where('user_id', '=', $userId)->where('status', '=', 0)
            ->whereRaw(' (expired_at > ? or expired_at is null)', [Carbon::now()->toDateTimeString()])
            ->count();
    }
}