<?php

/**
 * 初始化抽奖时间分布数据队列
 *
 */
class CreateRulePrizeTimeData extends BaseTask
{
    /**
     * 处理任务
     *
     * @return int
     */
    public function doCommand()
    {
        /* @var $oActivityRule ActivityRule */
        $oActivityRule = ActivityRule::find(array_get($this->data, 'id'));
        if($oActivityRule) {
            if(!$oActivityRule->generate_rules || $oActivityRule->total_count > 0) {
                return self::TASK_SUCCESS;
            }
            DB::connection()->beginTransaction(); // 开启事务
            if(!ActivityRulePrizeTime::init($oActivityRule)) {
                DB::connection()->rollback(); // 事务回滚
                return self::TASK_RESTORE;
            }
            if(!$oActivityRule->updateCount()) {
                DB::connection()->rollback(); // 事务回滚
                return self::TASK_RESTORE;
            }
            DB::connection()->commit(); // 事务提交
            return self::TASK_SUCCESS;
        }
        return self::TASK_SUCCESS;
    }

}
