<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 15-6-11
 * Time: 下午5:15
 */

class PrizeSysConfig extends SysConfig{

    const ERRNO_BET_PRIZE_GROUP_WRONG      = -941;

    const TYPE_TOP_AGENT     = 2;
    const TYPE_AGENT         = 1;
    const TYPE_USER          = 0;
    const TYPE_LOW_PRIZE_AGENT          = 3;

    const TOP_AGENT_DIFF_PRIZE_GROUP = 'top_agent_diff_grize_group';
    const TOP_AGENT_MIN_PRIZE_GROUP = 'top_agent_min_grize_group';
    const TOP_AGENT_MAX_PRIZE_GROUP = 'top_agent_max_grize_group';

    const TOP_AGENT_DIFF_HIGH_PRIZE_GROUP = 'top_agent_diff_high_grize_group';
    const TOP_AGENT_MIN_HIGH_PRIZE_GROUP = 'top_agent_min_high_grize_group';
    const TOP_AGENT_MAX_HIGH_PRIZE_GROUP = 'top_agent_max_high_grize_group';

    const AGENT_DIFF_PRIZE_GROUP = 'agent_diff_grize_group';
    const AGENT_MIN_PRIZE_GROUP = 'agent_min_grize_group';
    const AGENT_MAX_PRIZE_GROUP = 'agent_max_grize_group';

    const AGENT_DIFF_HIGH_PRIZE_GROUP = 'agent_diff_high_grize_group';
    const AGENT_MIN_HIGH_PRIZE_GROUP = 'agent_min_high_grize_group';
    const AGENT_MAX_HIGH_PRIZE_GROUP = 'agent_max_high_grize_group';

    const PLAYER_DIFF_PRIZE_GROUP = 'player_diff_grize_group';
    const PLAYER_MIN_PRIZE_GROUP = 'player_min_grize_group';
    const PLAYER_MAX_PRIZE_GROUP = 'player_max_grize_group';

    /**
     * 最低奖金组
     * @param $userType
     * @param $high
     *          false 获取最近基础奖金组
     *          true  获取最低配额奖金组
     * @return bool
     */
    public static function minPrizeGroup($userType, $high = false)
    {
        if(false === $userType = self::validUserType($userType) ) return false;

        if(! $high)
        {
            switch($userType)
            {
                case self::TYPE_TOP_AGENT :
                    $iMinGroup = SysConfig::readValue(self::TOP_AGENT_MIN_PRIZE_GROUP); break;

                case self::TYPE_AGENT :
                    $iMinGroup = SysConfig::readValue(self::AGENT_MIN_PRIZE_GROUP); break;

                case self::TYPE_USER :
                    $iMinGroup = SysConfig::readValue(self::PLAYER_MIN_PRIZE_GROUP); break;
            }
        }else{
            switch($userType)
            {
                case self::TYPE_TOP_AGENT :
                    $iMinGroup = SysConfig::readValue(self::TOP_AGENT_MIN_HIGH_PRIZE_GROUP);
                    break;

                case self::TYPE_AGENT :
                    $iMinGroup = SysConfig::readValue(self::AGENT_MIN_HIGH_PRIZE_GROUP);
                    break;
            }
        }

        if($iMinGroup) return $iMinGroup;
        else false;
    }

    /**
     * 最高奖金组
     * @param $userType
     * @param $high
     *          false 获取最高基础奖金组
     *          true  获取最高配额奖金组
     * @return bool
     */
    public static function maxPrizeGroup($userType, $high = false)
    {
        if(false === $userType = self::validUserType($userType) ) return false;

        if (!$high)
        {
            switch($userType)
            {
                case self::TYPE_TOP_AGENT :
                    $iMaxGroup = SysConfig::readValue(self::TOP_AGENT_MAX_PRIZE_GROUP); break;

                case self::TYPE_AGENT :
                    $iMaxGroup = SysConfig::readValue(self::AGENT_MAX_PRIZE_GROUP); break;

                case self::TYPE_USER :
                    $iMaxGroup = SysConfig::readValue(self::PLAYER_MAX_PRIZE_GROUP); break;
            }
        }else{
            switch($userType)
            {
                case self::TYPE_TOP_AGENT :
                    $iMaxGroup = SysConfig::readValue(self::TOP_AGENT_MAX_HIGH_PRIZE_GROUP); break;

                case self::TYPE_AGENT :
                    $iMaxGroup = SysConfig::readValue(self::AGENT_MAX_HIGH_PRIZE_GROUP); break;
            }
        }

        if($iMaxGroup) return $iMaxGroup;
        else false;
    }

    /**
     * @param $userType
     * @param bool $high
     *          false 获取基础奖金组点差
     *          true  获取配额奖金组点差
     * @return bool
     */
    public static function diffPrizeGroup($userType, $high = false)
    {
        if(false === $userType = self::validUserType($userType) ) return false;

        if (!$high)
        {
            switch($userType)
            {
                case self::TYPE_TOP_AGENT :
                    $iDiff = SysConfig::readValue(self::TOP_AGENT_DIFF_PRIZE_GROUP); break;

                case self::TYPE_AGENT :
                    $iDiff = SysConfig::readValue(self::AGENT_DIFF_PRIZE_GROUP); break;

                case self::TYPE_USER :
                    $iDiff = SysConfig::readValue(self::PLAYER_DIFF_PRIZE_GROUP); break;
            }
        }else{
            switch($userType)
            {
                case self::TYPE_TOP_AGENT :
                    $iDiff = SysConfig::readValue(self::TOP_AGENT_DIFF_HIGH_PRIZE_GROUP); break;

                case self::TYPE_AGENT :
                    $iDiff = SysConfig::readValue(self::AGENT_DIFF_HIGH_PRIZE_GROUP); break;
            }
        }

        if($iDiff) return $iDiff;
        else false;
    }


    /**
     * 获取基础奖金组列表
     * @param $userType
     * @param bool $diff
     *                  false: 获取最低和最高奖金组
     *                  true:  根据点差生成基础奖金组列表
     * @return array|bool
     */

    public static function getPrizeGroups($userType, $diff = false)
    {
        if(false === $userType = self::validUserType($userType) ) return false;

        $iDiff = $iMinGroup = $iMaxGroup = 0;
        switch($userType)
        {
            case self::TYPE_TOP_AGENT :
                $iDiff = SysConfig::readValue(self::TOP_AGENT_DIFF_PRIZE_GROUP);
                $iMinGroup = SysConfig::readValue(self::TOP_AGENT_MIN_PRIZE_GROUP);
                $iMaxGroup = SysConfig::readValue(self::TOP_AGENT_MAX_PRIZE_GROUP);
                break;

            case self::TYPE_AGENT :
                $iDiff = SysConfig::readValue(self::AGENT_DIFF_PRIZE_GROUP);
                
            $lowPrizeUserList = config::get('useLowPrizeGroupWhiteList.user_list');
           if( in_array(Session::get('username'), $lowPrizeUserList)){
                $iMinGroup = config::get('useLowPrizeGroupWhiteList.prize_group');
        }else{
                $iMinGroup = SysConfig::readValue(self::AGENT_MIN_PRIZE_GROUP);
        }
                $iMaxGroup = SysConfig::readValue(self::AGENT_MAX_PRIZE_GROUP);
                break;

            case self::TYPE_USER :
                $iDiff = SysConfig::readValue(self::PLAYER_DIFF_PRIZE_GROUP);
                $iMinGroup = SysConfig::readValue(self::PLAYER_MIN_PRIZE_GROUP);
                $iMaxGroup = SysConfig::readValue(self::PLAYER_MAX_PRIZE_GROUP);
                break;
            case self::TYPE_LOW_PRIZE_AGENT:
                
                $iDiff = SysConfig::readValue(self::AGENT_DIFF_PRIZE_GROUP);
                $iMinGroup = config::get('useLowPrizeGroupWhiteList.prize_group');;
                $iMaxGroup = SysConfig::readValue(self::AGENT_MAX_PRIZE_GROUP);
                
                break;
        }

        if(!$iDiff || !$iMinGroup || !$iMaxGroup || $iMinGroup >= $iMaxGroup) return false;


        if($diff) return range($iMinGroup,$iMaxGroup,$iDiff);
        else return [$iMinGroup, $iMaxGroup];
    }

    /**
     * 获取配额奖金组列表
     * @param $userType
     * @param bool $diff
     *                  false: 获取最低和最高奖金组
     *                  true:  根据点差生成基础奖金组列表
     * @return array|bool
     */
    public static function getHighPrizeGroups($userType, $diff = false)
    {
        if(false === $userType = self::validUserType($userType) ) return false;

        $iDiff = $iMinGroup = $iMaxGroup = 0;

        switch($userType)
        {
            case self::TYPE_TOP_AGENT :
                $iDiff = SysConfig::readValue(self::TOP_AGENT_DIFF_HIGH_PRIZE_GROUP);
                $iMinGroup = SysConfig::readValue(self::TOP_AGENT_MIN_HIGH_PRIZE_GROUP);
                $iMaxGroup = SysConfig::readValue(self::TOP_AGENT_MAX_HIGH_PRIZE_GROUP);
                break;

            case self::TYPE_AGENT :
                $iDiff = SysConfig::readValue(self::AGENT_DIFF_HIGH_PRIZE_GROUP);
                $iMinGroup = SysConfig::readValue(self::AGENT_MIN_HIGH_PRIZE_GROUP);
                $iMaxGroup = SysConfig::readValue(self::AGENT_MAX_HIGH_PRIZE_GROUP);
                break;

            case self::TYPE_USER :
                return false;
        }

        if(!$iDiff || !$iMinGroup || !$iMaxGroup || $iMinGroup >= $iMaxGroup) return false;

        if($diff) return range($iMinGroup,$iMaxGroup,$iDiff);
        else return [$iMinGroup, $iMaxGroup];
    }


    /**
     * 用户类型是否有效
     * @param $userType
     * @return bool
     */
    public static function validUserType($userType)
    {
        $userType = intval($userType);

        if ($userType === self::TYPE_TOP_AGENT || $userType === self::TYPE_AGENT || $userType === self::TYPE_USER||$userType===self::TYPE_LOW_PRIZE_AGENT) {
            return $userType;
        }else{
            return false;
        }
    }
}