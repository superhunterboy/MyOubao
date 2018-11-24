<?php

/**
 * Class EventTaskQueue - 任务事件处理队列
 *
 * @author Johnny <Johnny@anvo.com>
 */
class EventTaskQueue extends BaseTask
{
    /**
     * 处理任务
     *
     * @return int
     */
    public function doCommand()
    {           
        $data = $this->data;
        
        DB::connection()->beginTransaction();

        try
        {   
            $i = 1;

            $tasks = ActivityTask::findAllByEvent($data['event']);

            $this->log  .= "\n############Start Event[{$data['event']}] ############\n";

            foreach ($tasks as $task)
            {
                $this->log  .= $i++.". Start Activity [{$task['activity_id']}] - Task [{$task['id']}] ----> ";

                if (!$task->complete($data['user_id'], $data['data']))
                {
                    $this->log  .= json_encode($task->errors()->getMessages());
                }

                $this->log  .= " ----> finished\n";
            }

            $this->log  .= "############Event[{$data['event']}] ############\n";

            DB::connection()->commit();

            return self::TASK_SUCCESS;
            
        }
        catch(Exception $e)
        {
            DB::connection()->rollBack();
            $this->log  = "Exception abnormal: ".$e->getMessage();
            return self::TASK_RESTORE;
        }
    }

}