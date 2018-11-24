<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 生成公共下拉框静态json数据或html文件，使用该命令时，需要切换paths.php中的路径定义为具体路径，不能用变量
 */
class CalculateEachAgentTurnoverNumber extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'firecat:turnover-number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Each Agent Turnover Number';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function fire()
    {
        $aAgents = [
            1067=>'bomao998',
            59553=>'atm0508',
            3750=>'mb0508',
            1045=>'duoduo888',
            2297=>'zjhtyb',
            45014=>'maomao1314',
            671=>'helloy5',
            56191=>'blackie',
            613=>'haoyun168',
            53570=>'best2018',
            3957=>'bbs0086',
            34809=>'mymaming888',
            48815=>'bm1bm1',
            34932=>'baoshijie999',
            3279=>'vip10086',
            3902=>'bm4499',
            22154=>'yanfushui',
            44194=>'csy019',
            59387=>'fy6688',
            92=>'fff000',
            29079=>'tiandaochouqin',
            51237=>'666888vip',
            22341=>'relaxing',
            44987=>'bomaozhishu',
            48447=>'zoop888',
            49685=>'bmctlm8',
            2928=>'a5481683',
            4792=>'aq408945473',
            1681=>'ds1166',
            994=>'cheng2',
            5523=>'s7222559',
            4408=>'yunfa888',
            44703=>'shizi1168',
            55863=>'kenji168',
            2883=>'bomao99168',
            55937=>'bomao88168',
            29576=>'bm88168',
            44598=>'lcm168',
            233=>'facaivip88',
            230=>'vipvip',
            742=>'bmlingxiu',
            2345=>'wanglei888',
            22292=>'kkk1828',
            3855=>'doors2014',
            56749=>'yanzong168',
            5711=>'jingwei245',
            22237=>'awbzs123',
            4880=>'admin2014',
            21277=>'lkk777',
            1483=>'suqingqing',
            32214=>'dongfeng8',
            70=>'bmonline',
            58504=>'qq501504',
            34089=>'fafafa8988802',
            54675=>'cc1678',
            4949=>'mxcm888',
            18=>'caipiao99',
            36021=>'tiantiandeyi',
            47602=>'vip888999',
            59532=>'ff11999',
            4956=>'wxszzz',
            50349=>'qianqian168',
            1733=>'bmcai999',
            4808=>'bomao01',
            4153=>'song7763530',
            5290=>'longcai',
            60601=>'goldboss2016',
            60781=>'caesar168',
            1097=>'hts123',
            59128=>'he5858',
            57846=>'a510928615',
            56209=>'daqi2016'
        ];
        $aAgents = [
            1 => 'testlgv',
            3 => 'testendless'
        ];
        $aAgentsIds = array_keys($aAgents);

        $aDate = ['2016-06-16','2016-06-22'];

        $aNumber = [];
        foreach ($aAgentsIds as $iAgentId) {
            $aUsers = UserUser::getAllUsersBelongsToAgent($iAgentId);
            $sInfo = $aAgents[$iAgentId];
            array_push($aUsers, $iAgentId);
            foreach ($aDate as $sDate) {
                $iCount = UserProfit::whereIn('user_id', $aUsers)->where('turnover','>',0)->where('date', $sDate)->count();
                $aNumber[$aAgents[$iAgentId]][$sDate] = $iCount;
                $sInfo .= ' '.$iCount;
            }
            file_put_contents('/tmp/turnover-number',$sInfo."\n\r",FILE_APPEND);
        }

    }
}