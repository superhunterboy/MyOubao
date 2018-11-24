<?php

class TestController extends Controller
{

    public function index()
    {
        /**TestController
         * 接口相关
         *
         * 1\是否已经完成指定任务
         * 2\是否已经完成指定条件
         * 3\报名
         *
         */

        /**
         *
         * 新手任务 (通过)
         **/
//        Queue::push('SignTaskQueue', ['task_id' => 7, 'user_id' => 35, 'activity_id' => 2], 'activity');
//        exit;
         $data = ['user_id'=>35,'event'=>'bomao.activity.projectSendPrize', 'data'=>[]];
//         $bSucc = event::fire('bomao.auth.register', $data);
//          Queue::push('EventTaskQueue', $data, 'activity');
        $tasks = ActivityTask::findAllByEvent('bomao.activity.deposit');
        foreach ($tasks as $task)
        {
            var_dump($task->complete($data['user_id'], $data['data']));
        }
        /**
         *
         * 首次提现送 (通过)
         *

        $data = ['user_id'=>43, 'event'=>'bomao.activity.deposit', 'data'=>[]];
        event::fire('bomao.activity.withDrawal', $data);*/

/*
        $tasks = ActivityTask::findAllByEvent($data['event']);

        foreach ($tasks as $task)
        {
            var_dump($task->complete($data['user_id'], $data['data']));
        }*/

        /**
         * 首充一次返 (通过,互斥条件生效)
         *
        $data = ['user_id'=>43, 'event'=>'bomao.activity.deposit', 'data'=>[]];
        event::fire('bomao.activity.deposit', $data);
         */

        /**
         * 首充四次返
         *

        $data = ['user_id'=>43, 'data'=>[]];
        event::fire('bomao.activity.deposit', $data);
         */

        /**
         * 每99送抽奖机会
         *

        $data = ['user_id'=>17, 'event'=>'bomao.activity.profit', 'data'=>[]];
        event::fire('bomao.activity.profit', $data);
         */
       /* $tasks = ActivityTask::findAllByEvent($data['event']);

        foreach ($tasks as $task)
        {
            var_dump($task->complete($data['user_id'], $data['data']));
        }*/


        //event::fire('bomao.activity.withDrawal', $data);
        //echo json_encode(['registration_time'=>'2014-12-01']);exit;
       //echo serialize(['registration_time'=>'2014-12-01']);exit;
        //$data = ActivityConditionClass::find(1);
        /*$data = ActivityCondition::find(1);

        $object = $data->createObject();

        var_dump($object->isComplete(['registration_time'=>'2014-10-15']));*/

        //ActivityTask::findAllByEvent('auth')
        /*$data = ActivityConditionClassBase::findAllByEvent('bomao.auth.login');

        $tasks = [];

        foreach ($data as $value)
        {
            foreach ($value->conditions()->get() as $condition)
            {
                if (!isset($tasks[$condition['task_id']]))
                {
                    $tasks[$condition['task_id']]   = $condition->task()->first();

                    //在这里处理一下所有相关事物的操作
                }
            }
        }

        print_r($tasks);*/
//        $user_id    = 85;
//        $tasks = ActivityTask::findAllByEvent('bomao.auth.login');
//        $data= ['registration_time'=>'2014-12-15'];
//
//        foreach ($tasks as $task)
//        {
//            $status = $task->complete($user_id, $data);
//            var_dump($status);
//        }


    }
}