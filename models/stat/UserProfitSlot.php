<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 16-3-14
 * Time: 上午11:43
 */

class UserProfitSlot extends BaseModel
{
    protected $table = 'user_profits_slot';
    public static $resourceName = 'UserProfitSlot';
    
    public static function getAllProfltSlotOrLs($userid, $field, $start, $end) {
        if (empty($userid) || empty($field) || empty($start) || empty($end)) {
            return false;
        }
        return UserProfitSlot::where('user_id', $userid)->where('date', '>=', $start)->where('date', '<=', $end)->sum($field);
    }

}