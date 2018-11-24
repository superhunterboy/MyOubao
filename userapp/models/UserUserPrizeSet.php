<?php
class UserUserPrizeSet extends UserPrizeSet {
    protected $isAdmin = false;
    protected static $cacheUseParentClass = true;

    // public static $customMessages = [
    //     'username.required'               => '请填写用户名',
    //     'username.alpha_num'              => '用户名只能由大小写字母和数字组成',
    //     'username.between'                => '用户名长度必须介于 :min - :max 之间',
    //     'username.unique'                 => '用户名已被注册',
    //     'username.custom_first_character' => '首字符必须是英文字母',
    //     'nickname.required'               => '请填写昵称',
    //     'nickname.between'                => '昵称长度必须介于 :min - :max 之间',
    //     'password.custom_password'        => '密码由字母和数字组成, 且需同时包含字母和数字, 不允许连续三位相同',
    //     'password.confirmed'              => '密码两次输入不一致',
    //     'fund_password.custom_password'   => '资金密码由字母和数字组成, 且需同时包含字母和数字, 不允许连续三位相同',
    //     'fund_password.confirmed'         => '资金密码两次输入不一致',
    // ];
}