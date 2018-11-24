<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DataClearCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'data:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'clear table data';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$sTableName = $this->argument('tableName');
		if(!in_array($sTableName, array('issues','projects','transactions')))
		{
			echo $this->error('参数错误 ');die;
		}
		$iDays = 40;	//删除固定天数以前的数据
		$iNums = 5000;	//计划任务每次执行时需要删除的数据条数
		$oFirstRow = DB::table($sTableName)->first();
		$iID = $oFirstRow->id + $iNums;
		$sField = 'created_at';
		if($sTableName == 'issues')
		{
			$iDays = 20;
			$sField = 'end_time2';
		}
       	$sCreatedTime = date('Y-m-d 00:00:00',strtotime('-'.$iDays.' day'));
		try {
       		$iDelNums = DB::table($sTableName)
       						->where('id', '<', $iID)
       						->where($sField, '<', $sCreatedTime)
       						->delete();
			echo $this->info($iDelNums.' rows were deleted');	
       	}
       	catch (Exception $e)
       	{
       		file_put_contents('/tmp/DataClearError.txt', date('Y-m-d H:i:s').'  '.$e->getMessage()."\n",FILE_APPEND);
	       	echo $this->error($e->getMessage());
       	}
		
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('tableName', InputArgument::REQUIRED, null),
		);
	}



}
