<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TraceMakeup extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:trace-make-up';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'make up the delayed trace';


	/**
	 * Get the console command arguments./
	 *
	 * @return array
	 */
	public function fire()
	{
		$lottery_id = $this->argument('lottery_id');
                /*
                 * 用于将已经超时未完成的追号尽快塞入队列
                 * 
                 * 查找此游戏下影响当前issue的所有历史追号，并按issue的自然顺序将trace压入队列
                 * 算法：
                 * 1.判断当前issue的前一个issue的状态是否为Issue::CALCULATE_PARTIAL部分完成。如果非则退出。否则将此issue塞入记忆数组，并进入2.
                 * 2.继续查找上一个issue的状态是否为Issue::CALCULATE_PARTIAL部分完成，. 如果非则进入3.否则将此issue塞入记忆数组，并循环2.
                 * 3.将记忆数组中的所有array_unquue(trace_id) 按原时间顺序加入trace队列
                 * @return type
                 */
                
                $oLastIssue =  Issue::where('status',2)->where('lottery_id',$lottery_id)->get(['issue'])->first();
                if(!$oLastIssue) return ;
                $aTrace=DB::table('trace_details')->where('lottery_id','=',$lottery_id)->where('issue','=',$oLastIssue->issue)->where('status','=',0)->get(['trace_id']);

               foreach($aTrace as $trace)
                {
                    if(!$besuccess=BaseTask::addTask('CreateProject',['trace_id'=>$trace->trace_id],Config::get('schedule.trace'))) 
                            BaseTask::addTask('CreateProject',['trace_id'=>$trace->trace_id],Config::get('schedule.trace'));
                    
                }
       }

       protected function getArguments() {
                        return array(
                                array('lottery_id', InputArgument::OPTIONAL,null),
                        );
       }

}
