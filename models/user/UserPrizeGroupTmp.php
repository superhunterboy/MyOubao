<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 15-6-15
 * Time: 下午5:04
 */

class UserPrizeGroupTmp extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_prize_group_tmp';

    protected $fillable = [
        'user_id',
        'tmp_prize_group',
        'forever_prize_group',
    ];

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    /**
     * 获取永久奖金组
     * @param $userId
     * @return bool|mixed
     */
    public static function getForeverPrize(&$oUser)
    {
        if($oPrizeTmp = self::find($oUser->id)){
            return $oPrizeTmp->forever_prize_group;
        }else{
            return $oUser->prize_group ? $oUser->prize_group : UserPrizeSet::getMaxGroup($oUser->id)->prize_group;
        }
    }

    /**
     * 是否存在临时奖金组
     * @param $oUser
     * @return bool
     */
    public static function existTmpPrize(&$oUser){
        if(self::find($oUser->id)) return true;
        return false;
    }

    /**
     * 设置临时奖金组(临时的保存，永久的删除)
     * @param $oUser
     * @param $setPrizeGroup
     * @param $isForever bool
     * @return bool|null
     * @throws Exception
     */
    public static function setTmpPrize(&$oUser, $setPrizeGroup, $isForever)
    {
        $aPrizeGroups = ($aPrizeGroups = PrizeSysConfig::getPrizeGroups($oUser->is_agent, true)) ? $aPrizeGroups : [];
        $aHighPrizeGroups = ($aHighPrizeGroups = PrizeSysConfig::getHighPrizeGroups($oUser->is_agent, true)) ? $aHighPrizeGroups : [];

        $maxPrizeGroup = max($aPrizeGroups);

        //不在基础奖金组和配额奖金组内
        if( !in_array($setPrizeGroup, $aPrizeGroups) && !in_array($setPrizeGroup, $aHighPrizeGroups)){
            return false;
        }

        $oAgent = self::find($oUser->id);
        //如果是永久 或者 临时奖金组小于基础奖金组的伐值 或者 临时下调为临时(但此时的临时点也是永久点)
        if($isForever || $setPrizeGroup <= $maxPrizeGroup || ($oAgent && $setPrizeGroup == $oAgent->forever_prize_group)){
            if ($oAgent) {
                return $oAgent->delete();
            }
            return true;
        }

        //只在基础奖金组内调整
        if (in_array($oUser->prize_group, $aPrizeGroups) && in_array($setPrizeGroup, $aPrizeGroups)) {
            return true;
        }

        //如果永久的1954调整为1955临时的，临时表中还是1952
        //以下是设置奖金组为临时配额奖金组
        if(! $oAgent){
            $oAgent = new UserPrizeGroupTmp;
            $oAgent->user_id = $oUser->id;
            $oAgent->forever_prize_group = in_array($oUser->prize_group, $aPrizeGroups) ? $maxPrizeGroup : $oUser->prize_group;
        }
        $oAgent->tmp_prize_group = $setPrizeGroup;
        return $oAgent->save();
/*

        if ($oAgent = self::find($oUser->id)) {
            if ($isForever || $setPrizeGroup <= $aPrizeGroups[1]) {
                return $oAgent->delete();
            } else {
                $oAgent->tmp_prize_group = $setPrizeGroup;
                return $oAgent->save();
            }
        } else {
            if ($isForever) {
                return true;
            } else {
                $oAgent = new UserPrizeGroupTmp;
                $oAgent->user_id = $oUser->id;
                $oAgent->tmp_prize_group = $setPrizeGroup;
                $oAgent->forever_prize_group = $oUser->prize_group < $aPrizeGroups[1] ? $aPrizeGroups[1] : self::getForeverPrize($oUser);

                return $oAgent->save();
            }
        }*/
    }

}