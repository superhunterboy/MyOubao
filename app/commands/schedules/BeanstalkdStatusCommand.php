<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 生成公共下拉框静态json数据或html文件，使用该命令时，需要切换paths.php中的路径定义为具体路径，不能用变量
 */
class BeanstalkdStatusCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'status:beanstalkd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show beanstalkd status.';

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
        $data = array ();
        try {
            $messageQueue = $this->getServer();
            $tubes = $messageQueue->listTubes();
            foreach ($tubes as $tube) {
                $tubeArray = array();
                // Next job ready
                try {
                    /** @var Pheanstalk_Job $job */
                    $job = $messageQueue->peekReady($tube);
                    $tubeArray[] = array (
                        'id'      => $job->getId(),
                        'data'    => $job->getData(),
                        'status'  => 'ready',
                    );
                } catch (Pheanstalk_Exception_ServerException $e) {
                    // No job found
                }
                // Next job buried
                try {
                    /** @var Pheanstalk_Job $job */
                    $job = $messageQueue->peekBuried($tube);
                    $tubeArray[] = array (
                        'id'      => $job->getId(),
                        'data'    => $job->getData(),
                        'status'  => 'buried',
                    );
                } catch (Pheanstalk_Exception_ServerException $e) {
                    // No job found
                }
                $data[$tube] = $tubeArray;
            }
        } catch (Pheanstalk_Exception_ConnectionException $e) {
            $this->getResponse()->setHttpResponseCode(400);
            $data = "Unable to connect to the Beanstalkd server";
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $data = $e->getMessage();
        }

        pr(($data));exit;
    }

    private function getServer() {
        // $server = $this->_getParam("server");
        // $exp = explode(":", $server);
        $exp = [];
        $server = isset($exp[0]) ? $exp[0] : "localhost";
        $port = isset($exp[1])?$exp[1]:"11300";
        return new Pheanstalk_Pheanstalk($server, $port);
    }
}