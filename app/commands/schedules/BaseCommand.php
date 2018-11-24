<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 命令行程序基类
 */
class BaseCommand extends Command {

    public $logPath;
    public $logFile;
    protected $logFileParam = [];
    public $writeTxtLog = false;
    protected $sFileName = 'basecommand';
    protected $params = [];
    protected $startTime = null;
    protected $finishTime = null;
    protected $costTime = null;

    /**
     * 写文件日志
     * @param string 日志内容
     * @param boolean 是否追加空行
     * @return boolean 写日志成功或者失败
     */
    public function writeLog($sString, $bAppendSpaceLine = false) {
        if (!$this->writeTxtLog)
            return true;
        $sString = date('Y-m-d H:i:s') . ', ' . 'pid=' . posix_getpid() . ': ' . $sString . "\n";
        !$bAppendSpaceLine or $sString .= "\n";
        return file_put_contents($this->logFile, $sString, FILE_APPEND);
    }

    protected function setLogFile(){
        if ($this->writeTxtLog = SysConfig::check('service_write_log', true)) {
            $this->logPath = Config::get('log.root') . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . date('Ym/d');
            if (!file_exists($this->logPath)) {
                @mkdir($this->logPath, 0777, true);
                @chmod($this->logPath, 0777);
            }
//            $this->setLogFileName();
            $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . get_called_class();
            !$this->logFileParam or $this->logFile .= '-' . implode('-', $this->logFileParam);
        }
    }

    protected function fire(){
        $aParams = $this->argument();
        array_shift($aParams);
        $this->params = $aParams;
        unset($aParams);
        $this->setLogFile();
        $this->startTime = microtime(true);
        $this->doCommand($sMsg);
        $this->finishTime = microtime(true);
        $this->costTime = $this->finishTime - $this->startTime;
        $sMsg .= ' cost time: ' . number_format($this->costTime,4) . ' Seconds';
        $this->halt($sMsg);
    }
    
    protected function doCommand(& $sMsg = null){
        
    }
    
    protected function beforeFire(){

    }
    
    protected function halt($sString){
        $this->writeLog($sString, true);
        exit;
    }
}
