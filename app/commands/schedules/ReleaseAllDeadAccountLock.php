<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 获取所有死锁账户信息，如果加锁者已停止运行，则强制解开，如果失败，则会恢复任务
 */
class ReleaseAllDeadAccountLock extends BaseCommand {

    protected $sFileName = 'releasealldeadaccountlock';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'releaseall-dead-accountlock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'release all dead account lock';

    public function fire() {
        // 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
        $this->writeLog('begin release all dead account lock.');
        $aAccounts = Account::getLockedAccounts();
        foreach ($aAccounts as $oAccount) {
            $bSucc = Account::addReleaseLockTask($oAccount->id);
            if (!$bSucc) {
                $this->writeLog('release lock fail ' . var_export($oAccount, true));
            }
        }
    }

}
