<?php

/*
 * 在途交易列表模型类
 * 作用：用来检测是否存在正在进行的投注操作，避免发生少计奖开奖的情况
 */

class BetThread extends BaseModel {

    protected $table        = 'bet_threads';
    private static $skipLotteries = [];

    /**
     * 检查处理在途交易列表，并返回是否为空
     * 1、获得当前在途交易线程列表------A；
     * 2、获得当前数据库服务器的process list------B；
     * 3、遍历A，检查是否存在于B，不存在则将其从数据表中删除，存在则返回false；
     * 4、检查A是否为空，是则返回true,否则返回false。
     * 
     * @param int $iLotteryId 彩种id
     * @param String $sIssue    奖期
     * @return boolean 按照上述逻辑返回TRUE和FALSE
     */
    public static function isEmpty($iLotteryId,$sIssue){
        $aBetThreads       = self::getThreadList($iLotteryId,$sIssue);
        $aMySQLThreads     = DbTool::getDbThreads();
//        $iBetThreadsLength = count($aBetThreads);
        $aDiff             = array_diff($aBetThreads,$aMySQLThreads);
        return empty($aDiff) or self::deleteThread($aDiff);
    }

    /**
     * 返回在途交易的mysql线程ID列表，返回值为一维数组。
     * @param int $iLotteryId 彩种id
     * @param String $sIssue    奖期
     * @return array 包含mysql线程ID的一维数组
     */
    public static function getThreadList($iLotteryId,$sIssue){
        $aThreadIds = [];
        if (self::validateBetThread($iLotteryId,$sIssue,-1)){
            $aAllThreads = self::where('lottery_id','=',$iLotteryId)->where('issue','=',$sIssue)->get(['thread_id']);
            foreach ($aAllThreads as $val) {
                $aThreadIds[] = $val->thread_id;
            }
        }
        return $aThreadIds;
    }

    /**
     * 向在途交易线程表中增加指定的线程。
     * @param int $iLotteryId 彩种id
     * @param String $sIssue    奖期
     * @param int $iThreadId mysql线程ID
     * @return boolean 添加成功返回TRUE，失败返回FALSE
     */
    public static function addThread($iLotteryId,$sIssue,& $iThreadId){
        // 过滤不需要加入在途交易列表的
        if (in_array($iLotteryId,static::$skipLotteries)){
            return TRUE;
        }
        !empty($iThreadId) or $iThreadId = DbTool::getDbThreadId();
        $iResult   = 0;
        if (self::validateBetThread($iLotteryId,$sIssue,$iThreadId)){
            $aThread = [
                'lottery_id' => $iLotteryId,
                'issue'      => $sIssue,
                'thread_id'  => $iThreadId
            ];
            $iResult = self::insert($aThread);
        }
        return (bool) $iResult;
    }

    /**
     * 从在途交易线程表中删除指定的线程。
     * @param array $aThreadIds   mysql线程ID数组
     * @return boolean 删除成功返回TRUE，失败返回FALSE
     */
    public static function deleteThread($aThreadIds){
        $aThreadIds = (array)$aThreadIds;
//        is_array($aThreadIds) or $aThreadIds = [$aThreadIds];
        return self::whereIn('thread_id',$aThreadIds)->delete() > 0;
    }

    /**
     * 验证数据的有效性
     * @param int $iLotteryId 彩种id
     * @param String $sIssue    奖期
     * @param int $iThreadId    mysql线程ID
     */
    private static function validateBetThread($iLotteryId,$sIssue,$iThreadId){
        return (!empty($iLotteryId) && !empty($sIssue) && !empty($iThreadId));
    }

}

