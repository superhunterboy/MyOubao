<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ActivityLoadExcelCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'activity:load-excel';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Load activity data';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $data   = $this->getData();

        if ($this->confirm('Do you wish to continue? [yes|no]'))
        {
            DB::connection()->beginTransaction();
            try
            {
                DB::table('activity_user_prizes')->insert($data);
                DB::connection()->commit();

            }
            catch (Exception $e)
            {
                DB::connection()->rollBack();
                echo $e->getMessage();
            }
        }

    }

    /**
     * 获取数据
     *
     * @return array
     */
    protected function getData()
    {
        $file   = $this->argument('file');

        $phpExcel = PHPExcel_IOFactory::load($file);

        $sheetData =$phpExcel->getActiveSheet()->toArray(null,true,true,true);
        $data   = [];
        $errorData = [];
        //过滤掉第一行标题
        unset($sheetData[1]);

        foreach ($sheetData as $key=> $value)
        {
            $username   = trim($value['B']);
            $temp =[
                'activity_id'=>1,
                'prize_id'=>18,
                'source'=>1,
                'count'=>1,
                'status'=>2,
                'is_verified'=>1,
                'created_at'=>'2015-03-01',
                'updated_at'=>'2015-03-01',
                'prize_name'=>'注册送10元',
                'activity_name'=>'博猫12月活动',

                'username' => $username,
                'remote_ip'=> trim($value['D']),
            ];

            $user   = User::findUser($username);

            if ($user)
            {
                $temp['user_id']    = $user->id;
                $data[] = $temp;

                $this->info($temp['user_id']. '--'. $temp['username']. '--'.$temp['remote_ip']);
            }
            else
            {
                $errorData[$temp['username']] = $temp;
            }

        }

        if(!empty($errorData))
        {
            $this->error('以下用户不存在:'. implode(', ', array_keys($errorData)));
        }

        return $data;
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('file', InputArgument::REQUIRED, 'Excel file path.'),
		);
	}

}