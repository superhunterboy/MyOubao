<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 当添加新的彩种时，更新所有的玩家开户链接的彩种奖金组字段
 */
class AddUserPrizeSetForNewLottery extends BaseCommand {

    protected $sFileName = 'userprizesetupdate';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user_prize_set:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add user prize set for new lottery.';

    protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('prize_group', InputArgument::OPTIONAL, null),
        );
    }

    public function doCommand(& $sMsg = null) {
        $iLotteryId = $this->argument('lottery_id');
        $iPrizeGroup = $this->argument('prize_group');
        $oLottery = ManLottery::find($iLotteryId);
        if (!$oLottery) {
            $this->exitPro("missing lottery, lottery_id=" . $iLotteryId, false);
        }
        $aUsers = User::all();
        DB::connection()->beginTransaction();
        $bSucc = true;
        foreach ($aUsers as $oUser) {
            $oSSCUserPrizeSet = UserPrizeSet::getUserLotteriesPrizeSets($oUser->id, 1);
            if(isset($iPrizeGroup)){
                $iFinalPrizeGroup = $iPrizeGroup;
            }else{
                $iFinalPrizeGroup = $oSSCUserPrizeSet->classic_prize;
            }
            $oNewLotteryPrizeGroup = PrizeGroup::getObjectByParams(['classic_prize'=>$iFinalPrizeGroup, 'series_id'=>$oLottery->series_id]);
            $oUserprizeSet = UserPrizeSet::getObjectByParams(['user_id' => $oUser->id, 'lottery_id' => $oLottery->id]);
            if (is_object($oUserprizeSet)) {
                continue;
            }
            $oUserPrizeSet = new UserPrizeSet;
            $oUserPrizeSet->user_id = $oUser->id;
            $oUserPrizeSet->user_parent_id = $oUser->parent_id;
            $oUserPrizeSet->user_parent = $oUser->parent;
            $oUserPrizeSet->username = $oUser->username;
            $oUserPrizeSet->lottery_id = $oLottery->id;
            $oUserPrizeSet->group_id = $oNewLotteryPrizeGroup->id;
            $oUserPrizeSet->prize_group = $oNewLotteryPrizeGroup->classic_prize;
            $oUserPrizeSet->classic_prize = $oNewLotteryPrizeGroup->classic_prize;
            $oUserPrizeSet->valid = 1;
            $oUserPrizeSet->is_agent = $oUser->is_agent;
            $bSucc = $oUserPrizeSet->save();
            if (!$bSucc) {
                break;
            }
        }
        if ($bSucc) {
            DB::connection()->commit();
        } else {
            DB::connection()->rollback();
        }
    }

}
