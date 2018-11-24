<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 计算总代升降点
 */
class UpdatePrizeSet extends BaseCommand {

    protected $sFileName = 'updateprizeset';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'firecat:update-prizeset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update prieset for top agent';

    public function fire() {
        // 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
        $this->writeLog('begin update prize set.');
        $aUserPrizeSet = UserPrizeSetFloat::getUnusedPrizeSet();
        foreach ($aUserPrizeSet as $oUserPrizeSet) {
            $bSucc = true;
            DB::connection()->beginTransaction();
            $aOldUserPrizeSet = UserPrizeSet::getUserLotteriesPrizeSets($oUserPrizeSet->user_id);
            foreach ($aOldUserPrizeSet as $oOldUserPrizeSet) {
                if (is_object($oOldUserPrizeSet)) {
                    $oOldPrizeGroup = PrizeGroup::find($oOldUserPrizeSet->group_id);
                    if (!is_object($oOldPrizeGroup)) {
                        $this->writeLog('user_id=' . $oOldUserPrizeSet->user_id . ' and group_id=' . $oOldUserPrizeSet->group_id . '  missing old prize group ');
                    }
                    $oNewPrizeGroup = PrizeGroup::getPrizeGroupsBySeriesName($oOldPrizeGroup->series_id, $oUserPrizeSet->new_prize_group);
                    if (!is_object($oNewPrizeGroup)) {
                        $this->writeLog('user_id=' . $oOldUserPrizeSet->user_id . ' and series_id =' . $oOldPrizeGroup->series_id . ' and prize_group =' . $oUserPrizeSet->new_prize_group . '  missing new prize group ');
                    }
                    $oOldUserPrizeSet->group_id = $oNewPrizeGroup->id;
                    $oOldUserPrizeSet->prize_group = $oNewPrizeGroup->name;
                    $oOldUserPrizeSet->classic_prize = $oNewPrizeGroup->classic_prize;
                    $bSucc = $oOldUserPrizeSet->save();
                    if ($bSucc) {
                        $oUserPrizeSetFloat = UserPrizeSetFloat::find($oUserPrizeSet->id);
                        $oUserPrizeSetFloat->status = UserPrizeSetFloat::STATUS_USED;
                        $bSucc = $oUserPrizeSetFloat->save();
                    } else {
                        $bSucc = false;
                        break;
                    }
                } else {
                    $this->writeLog('user_id=' . $oUserPrizeSet->user_id . ' and lottery_id=' . $oUserPrizeSet->lottery_id . ' has no prize set ');
                }
            }
            // 更新用户奖金组信息
            $oUser = User::find($oUserPrizeSet->user_id);
            $oUser->prize_group = $oUserPrizeSet->new_prize_group;
            !$bSucc or $bSucc = $oUser->save();
            if ($bSucc) {
                DB::connection()->commit();
                $this->writeLog('user_id=' . $oUserPrizeSet->user_id . ' and prize_group=' . $oUserPrizeSet->new_prize_group . '  save success ');
            } else {
                DB::connection()->rollback();
                $this->writeLog('user_id=' . $oUserPrizeSet->user_id . ' and prize_group=' . $oUserPrizeSet->new_prize_group . '  save fail ');
            }
        }
        exit;
    }

}
