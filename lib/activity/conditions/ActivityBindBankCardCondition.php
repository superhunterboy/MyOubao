<?php
/**
 * Class ActivityBindBankCardCondition - 绑定银行卡
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityBindBankCardCondition extends BaseActivityCondition
{
    /**
     * 条件是否满足
     *
     * @return bool
     */
    public function complete($userCondition)
    {
        return UserBankCard::where('user_id', '=', $userCondition->user_id)->exists();
    }
}