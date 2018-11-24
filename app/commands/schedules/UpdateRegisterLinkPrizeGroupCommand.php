<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 当添加新的彩种时，更新所有的玩家开户链接的彩种奖金组字段
 */
class UpdateRegisterLinkPrizeGroupCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:update-register-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update users register links prize_group_sets field in register_links table.';

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
        $updator = new RegisterLinkPrizeGroupSetUpdator;
        $result = $updator->updatePrizeGroup();
        $sDesc = '';
        if ($result['succeed']) {
            $sDesc .= 'These links updated succeed: ' . implode(',', $result['succeed']) . ';';
        }
        if ($result['failed']) {
            $sDesc .= 'These links updated failed: ' . implode(',', $result['failed']) . ';';
        }
        if (! isset($result) || (! $result['succeed'] && ! $result['failed']) ) {
            $sDesc .= 'No links need be updated.';
        }
        $this->line($sDesc);
    }
}