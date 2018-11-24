<?php
/**
 *
 * @author Royal
 *
 */

class LuckyCatController extends UserBaseController
{
    protected $resourceView = 'events.luckycat';
    protected $modelName = 'UserUser';

    public static $pagesize = 8;
/**
2元	60%
5元	65%
10元	70%
1%返利券	75%
2%返利券	80%
100元	85%
1000元	90%
金条20g	95%
samsung N4	97%
samsung NE	97%
iphone 6	99%
iphone 6 plus	99%
 */

    protected $prizeMap = [
        1 =>[ 'num'=>0,  'type'=>3, 'beat_rate'=> '0'], //特斯拉
        2 =>[ 'num'=>11, 'type'=>3, 'beat_rate'=> '0' ], //欧洲游套票
        3 =>[ 'num'=>6,  'type'=>3, 'beat_rate'=> '99%' ], //iphone6+
        4 =>[ 'num'=>10, 'type'=>3, 'beat_rate'=> '99%' ], //iphone6
        /*5=>[ 'num'=>3, 'type'=>3, ],*/ //galaxy ne(未知)
        6 =>[ 'num'=>1,  'type'=>3, 'beat_rate'=> '97%' ], //galaxy n4
        7 =>[ 'num'=>4,  'type'=>3, 'beat_rate'=> '95%' ], //金条20g
        8 =>[ 'num'=>7,  'type'=>2, 'beat_rate'=> '90%' ], //1000元
        9 =>[ 'num'=>7,  'type'=>2, 'beat_rate'=> '85%' ], //100元
        10=>[ 'num'=>8,  'type'=>1, 'beat_rate'=> '80%' ], //2%返利券
        11=>[ 'num'=>8,  'type'=>1, 'beat_rate'=> '75%' ], //1%返利券
        12=>[ 'num'=>2,  'type'=>2, 'beat_rate'=> '70%' ], //10元
        13=>[ 'num'=>2,  'type'=>2, 'beat_rate'=> '65%' ], //5元
        14=>[ 'num'=>2,  'type'=>2, 'beat_rate'=> '60%' ], //2元
    ];

    public $current_prizes   = [];
    public $current_tasks    = [];
    public $current_values   = [];
    public $user_id = null;

    public $current_activity_user_condition = null;


    public function firstDeposit()
    {

    }

    public function firstWithdrawal()
    {

    }

    public function yicifan()
    {
        $this->fanqian("yicifan");
    }

    public function sicifan()
    {
        $this->fanqian("sicifan");
    }
    
    private function getSetToken()
    {
        $token = md5(time());
        Session::set("win_prize_token",$token);
        return $token;
    }

    /**
     * 抽奖转换接口
     *
     * type (未中奖0 返利券1 红包2 实物3)
     *
     * @return array
     */
    public function winprize()
    {
        $data       = Input::all();
        
        if(array_get($data, 'token') != Session::get('win_prize_token'))
        {
            return json_encode([
                'isSuccess' => 0,
                'type' => 'Invaid Token',
                'msg' => '访问不合法',
            ]);
        }
        
        if(Session::get('is_tester') != 0)
        {
            return json_encode([
                'isSuccess' => 0,
                'type' => 'Invaid Token',
                'msg' => '测试用户不允许参加',
            ]);
        }
        
        $token = $this->getSetToken();
        $user_id    = Session::get('user_id');
        
        //未登录情况
        if (!$user_id)
        {
            return json_encode([
                'isSuccess' => 0,
                'type'      => 'expired',
                'msg'       => '登陆过期',
                'data'      => ['url' => route('home')],
                'token'     => $token,
            ]);
        }

        $prizeMap   = $this->prizeMap;

        $o_user_activity = new UserActivityController();
        $return = json_decode($o_user_activity->luckyDraw(1)->getContent(true),true);
        $return['code'] == 7 ? $isSuccess = 1 : $isSuccess = 0;

        $noneNum    = [3, 5, 9];

        $result = [
            'isSuccess' => 1,
            'type' => 'success',
            'msg' => '抽奖成功',
            'token'     => $token,
            'data' => [
                //中奖编号
                'num'  => $noneNum[array_rand($noneNum)],
                //奖品类型(未中奖0 返利券1 红包2 实物3)
                'type' => 0,
                //奖品名称
                'title' => '未中奖',
                //价值
                'value' => '0',
                //剩余抽奖次数
                'times' => 0,
            ]
        ];

        //查找用户剩余抽奖次数
        if ($userInfo   = ActivityUserInfo::getObjectByParams(['user_id'=>$user_id, 'activity_id'=>1]))
        {
            $result['data']['times']    = $userInfo['lottery_count'];
        }

        //抽奖情况
        switch ($return['code'])
        {
            case UserActivityController::ERROR_LOTTERY_NO_MORE_CHANCES :
                return [
                    'isSuccess'=>0,
                    'type'=>'notimes',
                    'msg'=>'没有抽奖机会',
                ];
                break;
            case UserActivityController::ERROR_LOTTERY_PRIZE_LIMIT_DAILY :
                return $result;
                break;
            //未中奖的情况
            case UserActivityController::MESSAGE_LOTTERY_NO_PRIZE :
                return $result;
                break;
            //中奖的情况
            case UserActivityController::MESSAGE_LOTTERY_GET_PRIZE :

                if (isset($prizeMap[$return['data']['prize_id']]))
                {
                    $prize  = $prizeMap[$return['data']['prize_id']];

                    $result['data']['num']      = $prize['num'];
                    $result['data']['type']     = $prize['type'];
                    $result['data']['userwin']  = $prize['beat_rate'];
                    $result['data']['title']    = $return['data']['prize_name'];
                    $result['data']['value']    = $return['data']['prize_value'];

                    return json_encode($result);
                }
                break;
        }

        //其他异常情况
        return json_encode([
            'isSuccess' => 0,
            'type' => 'systemError',
            'msg' => '系统错误',
            'token'     => $token,
            'data' => []
        ]);
    }

    //Participate..
    public function fanqian($task_name = '')
    {
        switch ($task_name)
        {
            case "sicifan":
                $task_id = 2;
                break;
            case "yicifan":
                $task_id = 3;
                break;
        }
        $o_activity_user = new ActivityUserTask();
        if($o_activity_user->checkTaskExist([2]))
        {
                return json_encode(["status"=>"0","error"=>"您已经参与了四次返活动已报名!"]);
        }
        elseif($o_activity_user->checkTaskExist([3]))
        {
                return json_encode(["status"=>"0","error"=>"您已经参与了一次返活动已报名!"]);
        }

        $input_data['activity_id']  = 1;
        $input_data['task_id']      = $task_id;
        $input_data['user_id']      = Session::get('user_id');
//        $input_data['status']       = 1;
        $input_data['is_signed']    = 1;
        $input_data['signed_time']  = date("Y-m-d H:i:s");

        $o_activity_user->fill($input_data);
        if($o_activity_user->save())
        {
            echo json_encode(["status"=>"1"]);
            exit;
        }
    }

    /**
     * 首页
     *
     */
    /*public function index()
    {
        $user_id            = Session::get('user_id');
        $account_id         = UserUser::find(Session::get('user_id'))->getAttribute("account_id");
        //当前可用余额
        $avaiableBalance    = Account::getAvaliable($user_id);
        //当前的任务完成情况
        $task_data          = ActivityUserTask::findAllByActivityUser(1, Session::get('user_id'));

        //需要完成的任务数
        $all_tasks          = ActivityTask::all();
        $all_finish_task    = count($all_tasks) - count($task_data);
        $finish_tasks       = [];
        $rolling_prize_list = [];
        $yicisici           = false;

        foreach ($task_data as $td)
        {
            if ($td['isFinsh'])
            {
                $finish_tasks[$td['task_id']] =   $td['task_id'];
            }
        }


        $o_activity_user = new ActivityUserTask();
        if($o_activity_user->checkTaskExist([2,3]))
        {
            $yicisici = $o_activity_user->getObjectByParams(['user_id'=>Session::get('user_id')])->getAttribute('task_id');
        }

        //剩余抽奖次数
        $o_activity_user_info = new ActivityUserInfo();
        $userInfo   = $o_activity_user_info->getObjectByParams(['user_id'=>$user_id, 'activity_id'=>1]);

            $left_prize_count    = $userInfo['lottery_count'];

        $all_task           = [];
        $user_task          = [];
        foreach ($all_tasks as $task)
        {
            $tmp = $task->getAttributes();
            in_array($task->getAttribute('id'), $finish_tasks) ? $tmp['finished'] = 1 : $tmp['finished'] = 0;
            $all_task[] = $tmp;
        }
        $avaiable_task      = [];
        $avaiable_condition = [];
        foreach ($task_data as $task)
        {
        }

        $o_activity_user_prize = new ActivityUserPrize();
        foreach ($o_activity_user_prize->getRollingList() as $p)
        {
            $atts = $p->getAttributes();
            $rolling_prize_list[]   =['username' => '*'.substr($atts['username'], 1, -1).'*' , 'prize_name' => $atts['prize_name'] ];
        }

        $whether_login = false;
        if(!empty(Session::get('user_id')))
        {
            $whether_login = true;
        }

        $this->setVars(compact('avaiableBalance','task_data','all_task','all_finish_task','left_prize_count','rolling_prize_list','whether_login','yicisici', 'finish_tasks'));
        $this->render();
    }*/


    /**
     * 首页
     *
     */
    public function index()
    {
        $user_id = Session::get('user_id');
        $account_id = UserUser::find(Session::get('user_id'))->getAttribute("account_id");
        //当前可用余额
        $avaiableBalance = Account::getAvaliable($user_id);

        //所有完成任务
        $finish_tasks = [];
        $finish_conditions  = [];
        //所有完成条件
        $rolling_prize_list = [];

        //所有完成任务
        $tasks = ActivityUserTask::doWhere(['activity_id'=>['=', 1], 'user_id'=>['=', $user_id]])->get();
        $notify= [];
        foreach($tasks as $task)
        {
            if ($task->isFinsh())
            {
                $finish_tasks[$task['task_id']]    = $task['task_id'];

                //如果没有提示,则提示用户,并且把状态改变
                if (!$task['notify'])
                {
                    $taskInfo   = $task->task()->first();

                    //特殊提示
                    $map    = [
                        1 => '恭喜您！"新手任务"完成啦！<br \/>10元注册礼金和2次抽奖机会已送出，请查收哦。',
                        4 => '恭喜您！"首次提款送10元"完成啦！<br \/>10元提现礼金和1次抽奖机会已经送出，请查收哦。',
                    ];

                    if (isset($map[$task['task_id']]))
                    {
                        $notify[]   = $map[$task['task_id']];
                    }

                    $task->notify   = 1;
                    $task->save();
                }
            }
        }

        //所有完成条件
        $conditions = ActivityUserCondition::doWhere(['activity_id'=>['=', 1], 'user_id'=>['=', $user_id]])->get();
        foreach($conditions as $condition)
        {
            if ($condition->isFinsh())
            {
                $finish_conditions[$condition['condition_id']]    = $condition['condition_id'];
            }
        }

        //需要完成的任务数
        $all_tasks = ActivityTask::all();
        $all_finish_task = count($all_tasks) - count($finish_tasks);

        //剩余抽奖次数
        $o_activity_user_info = new ActivityUserInfo();
        $userInfo = $o_activity_user_info->getObjectByParams(['user_id' => $user_id, 'activity_id' => 1]);

        $left_prize_count = $userInfo['lottery_count'];

        //是否已经报名首充活动
        $isApply    = ActivityUserTask::doWhere(['activity_id'=>['=', 1], 'user_id'=>['=', $user_id], 'task_id'=>['in', [2, 3]]])->exists();


        //中奖历史
        $activityUserPrizes = ActivityUserPrize::doWhere(['activity_id'=>['=', 1], 'source'=>['=', 2]])->take(15)->get();
        foreach ($activityUserPrizes as $p) {
            $atts = $p->getAttributes();
            $rolling_prize_list[] = ['username' => substr($atts['username'], 0, -4) . '****', 'prize_name' => $atts['prize_name']];
        }
        if(count($rolling_prize_list) < 10)
        {
            $rolling_prize_list = [
                ['username' => 'ben****','prize_name' =>'2元'],
                ['username' => 'vip88****','prize_name' =>'2元'],
                ['username' => 'rc****','prize_name' =>'2元'],
                ['username' => 'q12655****','prize_name' =>'2元'],
                ['username' => 'aa7788****','prize_name' =>'2元'],
                ['username' => '9899a***','prize_name' =>'2元'],
                ['username' => 'teddy****','prize_name' =>'2元'],
                ['username' => 'caish****','prize_name' =>'2元'],
                ['username' => 'bettetd***','prize_name' =>'1000元'],
                ['username' => 'ang****','prize_name' =>'2元'],
                ['username' => 'chin***','prize_name' =>'2元'],
                ['username' => 'llaa****','prize_name' =>'2元'],
                ['username' => 'ping****','prize_name' =>'100元'],
                ['username' => 'mon****','prize_name' =>'2元'],
                ['username' => '8811****','prize_name' =>'2元'],
                ['username' => 'qq15****','prize_name' =>'纯金20g'],
            ];
        }

        //用户是否登陆
        $whether_login = false;
        if (!empty(Session::get('user_id'))) {
            $whether_login = true;
        }
        $token = $this->getSetToken();

        $this->setVars(compact('avaiableBalance', 'notify', 'isApply', 'finish_conditions', 'all_finish_task', 'left_prize_count', 'rolling_prize_list', 'whether_login', 'finish_tasks', 'token'));
        $this->render();
    }


    /**
     * 我的奖品列表
     *
     */
    public function myprizes()
    {
        $user_id = Session::get('user_id');
        $type    = intval(Input::get('type'));

        $conditions = ['activity_id'=>1, 'user_id'=>['=', $user_id], 'source'=> ['=', 2]];

        if ($type)
        {
            $map    = [];

            foreach ($this->prizeMap as $key => $value)
            {
                $map[$value['type']][]  = $key;
            }

            $conditions['prize_id'] = ['in', $map[$type]];
        }


        $oQuery  = ActivityUserPrize::doWhere($conditions);

        $datas   = $oQuery->paginate(static::$pagesize);

        $this->setVars(compact('datas'));

        $this->render();
    }

    /**
     * 我的首充奖品
     *
     */
    public function myDepositPrizes()
    {
        $user_id = Session::get('user_id');

        $conditions = ['activity_id'=>['=', 1], 'user_id'=>['=', $user_id], 'is_signed'=>['=', 1], 'task_id'=> ['in', [2, 3]]];
        $oQuery  = ActivityUserTask::doWhere($conditions)->orderBy('status', 'DESC');

        $datas   = $oQuery->first();

        $map    = [
            //四次返
            2=>17,
            //一次返
            3=>16,
        ];

        $amount = 0;
        $pay_time   = '';
        $task   = '';
        $backInfos   = [];

        if ($datas) {

            $prize_id = $map[$datas->task_id];
            //用户的奖品信息
            $prize = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId($user_id, $prize_id)->first();

            if (is_object($prize) && !empty($prize['data']))
            {
                //通过奖品得到第一笔充值订单ID,然后得到充值额
                $amount     = $prize->datas['amount'];
                $pay_time   = $prize->datas['pay_time'];

            }

            //用户的投注信息
            $backInfos   = ActivityCashBack::doWhere(['user_id'=>['=', $user_id], 'prize_id'=>['=', $prize_id]])->get();

            //任务信息
            $task       = $datas->task()->first();
        }


        $this->setVars(compact('datas', 'task', 'amount', 'pay_time', 'backInfos'));

        $this->render();
    }
}