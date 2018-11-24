<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateProjectBetNum extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'firecat:update-project-display_bet_num';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update project table `display_bet_num`';


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	public function fire()
	{
		$project_id = $this->argument('project_id');
		$pdata = Project::where("display_bet_number","like","%'%");
	//	if($project_id)
		//	$pdata = $pdata->where('id',$project_id);
		$pdata = $pdata -> get();
		foreach($pdata as $pd){
			$display_bet_number = $pd->getDisplayBetNumber();
			$this->line($display_bet_number);
			$r = Project::where('id',$pd->id)->update(array('display_bet_number'=>$display_bet_number));
			if($r)
				$this->line("$pd->id 数据已更新");
		}

		//select * from projects where isnull(display_bet_number) and status_prize>0
	//Encrypt::db_decode($oProject->bet_number);
	}

	protected function getArguments() {
		return array(
			array('project_id', InputArgument::OPTIONAL,null),
		);
	}

}
