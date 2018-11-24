<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 生成公共下拉框静态json数据或html文件，使用该命令时，需要切换paths.php中的路径定义为具体路径，不能用变量
 */
class GenerateSelectorCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:selector-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate selector templates and data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        parent::__construct();
//    }
      protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('issue', InputArgument::REQUIRED, null),
            array('type', InputArgument::OPTIONAL, null),
        );
    }
    public function fire()
    {
        $lottery_id = $this->argument('lottery_id');
        $issue = $this->argument('issue');
        $type = $this->argument('type') ? $this->argument('type') : 'commission';
        $aProjects = Project::where("lottery_id","=",$lottery_id)->where('issue','>=',$issue)->where('status_commission','=','0')->get(['id']);
        $aProjectIds = [];
        
        
        foreach($aProjects as $o){
            array_push($aProjectIds,$o->id);
        }
//        
//        $aProjectIds = [
//      1739736,
//1739739,
//1739744
//
//        ];
         
        
        Queue::push('SendMoney',[ 'type' => 'prize','projects' => $aProjectIds],Config::get('schedule.send_money'));
        
//        $generator = new SelectTemplateGenerator;
//        $result = $generator->generate();
//        // $result = $generator->generateGroupWays(); // 专门用于彩种-玩法群-玩法三联下拉框数据的生成
//        $this->line(json_encode($result));
//        // @file_put_contents('/home/snowan/Documents/tmp/group-ways.json', json_encode($result));
    }
}