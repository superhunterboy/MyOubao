<?php 
/**
* @todo controller for event 流水天梯
*/
class TransactionFlowController extends UserBaseController {

	/**
     * 反馈报名的类名
     */
    const IGNORE_CLASS = 'ActivityTaskFeedBackCondition';

	protected $resourceView = 'events.transaction_flow',
			  $modelName = 'UserUser',
			  $activity_id = 3;

    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter(function(){
            
            //@todo Like a refresh
            ActivityUserTaskFeedBack::updateEvent();

        });
    }

	public function index()
	{
		$oActivityTask = NULL;
		$aActivityUserTask = NULL;

		//Get what is needed to display
 		$oActivityTask = ActivityUserTaskFeedBack::getActivityConditionsNPrice($this->activity_id);

 		foreach($oActivityTask as $per_activity_task => & $oTask)
 		{
 			$oTask->condition_satisfied = $this->isFinished($oTask->id,Session::get('user_id'));
 			
 			$oActivityUserTaskFeedBack = ActivityUserTaskFeedBack::findByTaskUser($oTask->id,Session::get('user_id'));

 			if($oActivityUserTaskFeedBack)
 			{
 				$oTask->signed_in_collected = TRUE;
 			}
 			else
 			{
 				$oTask->signed_in_collected = FALSE;
 			}
 		}
 		
 		//Set the data in the front
		$this->setVars(compact(
			'oActivityTask',
			'aActivityUserTask'
		));

		$this->render();
	}


    /**
     * 领取/反馈
     *
     * 1, 判断除了反馈,其他任务是否都已完成 | In addition to the feedback judgment , whether the other tasks have been completed
     * 2, 如果已完成,则添加数据并且触发事件,否则返回错误 | If you are finished , add data, and trigger events , otherwise it returns an error
     *
     * @param $task_id
     */
	public function update()
	{
        $user_id    = Session::get('user_id');
		$task_id    = Input::get('task');

        $userFeedBack = ActivityUserTaskFeedBack::findByTaskUser($task_id,$user_id);

        if ($userFeedBack)
        {
            //已经领取过了 // already collected | claimed
            /*$this->halt(false, 'feedback-already', ActivityTaskFeedBackCondition::ERRNO_FEEDBACK_ALREADY);*/
            return [
                'isSuccess'=>0,
                'type'=>'user-already-participated',
                'msg'=>'ERRNO_FEEDBACK_ALREADY',
            ];
        }

        if ($this->isFinished($task_id,$user_id))
        {
            $userFeedBack           = new ActivityUserTaskFeedBack();
            $userFeedBack->task_id  = $task_id;
            $userFeedBack->user_id  = $user_id;
            $userFeedBack->date     = date('Y-m-d H:i:s');

            //派奖
            $oActivityTaskPrize = ActivityTaskPrize::where('task_id', '=', $task_id)->first();
            $oActivityPrize = ActivityPrize::getPrizeInfoByPrizeId($oActivityTaskPrize->prize_id,['value'],false,TRUE);

            DB::connection()->beginTransaction();
            if ($userFeedBack->save() && $this->deposit($user_id, $oActivityPrize->value))
            {
                DB::connection()->commit();
                //领取成功
                //$this->halt(true, 'feedback-success', null);
                ActivityUserTaskFeedBack::updateEvent();

                return [
                    'isSuccess'=>1,
                    'type'=>'success',
                ];
            }
            else
            {
                DB::connection()->rollback();
                //$this->halt(false, 'feedback-abnormal', ActivityTaskFeedBackCondition::ERRNO_FEEDBACK_ABNORMAL);
                return [
                    'isSuccess'=>0,
                    'type'=>'feedback-abnormal',
                    'msg'=>'ERRNO_FEEDBACK_NOT_FINISHED',
                ];
            }
        }

        //没有完成任务
        //$this->halt(false, 'feedback-not-finished', ActivityTaskFeedBackCondition::ERRNO_FEEDBACK_NOT_FINISHED);
        return [
            'isSuccess'=>0,
            'type'=>'event-criteria-incomplete',
            'msg'=>'ERRNO_FEEDBACK_NOT_FINISHED',
        ];
	}

	/**
     * 是否已完成任务
     *
     * @param $task_id
     * @param $user_id
     * @return bool
     */
    private function isFinished($task_id, $user_id)
    {	
    	$bool = TRUE;
        $task = ActivityTask::with('conditions')->find($task_id);

        $userConditions = ActivityUserCondition::where('user_id', '=', $user_id)
                ->where('task_id', '=', $task_id)
                ->get();

        $cCount	= $uCount = 0;

        foreach ($task->conditions()->get() as $key => $value)
        {
         	if ($value->getClassName()  != self::IGNORE_CLASS)
         	{
         		$cCount	++;
         	}
         } 	

        //1. how to know user satisfied all 3 before reaching the signed up
        foreach ($userConditions as $key => $userCondition)
        {
   			//Only skip when user is registered
            if ($userCondition->condition()->get()->first()->getClassName() == self::IGNORE_CLASS) //ActivityTaskFeedBackCondition | check whether user already register
            {
                continue;
            }

            $uCount++;
 
 			//Check whether satisfied transaction
 			//Check whether satisfied only 1 IP 1 activties
            if (!$userCondition->isFinsh())
            {
                $bool = FALSE;
                break;
            }
        }

		if ($cCount != $uCount)
        {
            return FALSE;
        }

        return $bool;
        
    }

    /**
     * 促销派奖
     * @param $id
     * @param $amount
     * @return RedirectResponse
     */
    private function deposit($id, $amount)
    {
        $oUser = User::find($id);
        $oTransaction = TransactionType::find(TransactionType::TYPE_PROMOTIANAL_BONUS);

        $oDeposit = new ManualDeposit;
        $oDeposit->user_id = $oUser->id;
        $oDeposit->username = $oUser->username;
        $oDeposit->is_tester = $oUser->is_tester;
        $oDeposit->amount_add_coin = $amount;
        $oDeposit->transaction_type_id = TransactionType::TYPE_PROMOTIANAL_BONUS;
        $oDeposit->transaction_description = $oTransaction->cn_title;
        $oDeposit->note = '流水天梯';
        $oDeposit->administrator = '';
        $oDeposit->admin_user_id = 0;
        $oDeposit->status = ManualDeposit::STATUS_VERIFIED;

        if ($oDeposit->save()) {
            $aJobData = [
                'manual_deposit_id' => $oDeposit->id,
            ];
            return BaseTask::addTask('ManualDepositQueue', $aJobData, 'account');
        }

        return false;
    }








}