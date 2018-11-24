<?php

/**
 * 更新总销量表
 *
 * @author white
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateTeamProfitCommand extends BaseCommand {

    /**
     * command name.
     *
     * @var string
     */
    protected $name = 'stat:update-team-profit';
    protected $types = [
        TransactionType::TYPE_BET => 'turnover',
        TransactionType::TYPE_DROP => 'turnover',
        TransactionType::TYPE_SEND_PRIZE => 'prize',
        TransactionType::TYPE_CANCEL_PRIZE => 'prize',
        TransactionType::TYPE_PROMOTIANAL_BONUS => 'bonus',
        TransactionType::TYPE_SEND_COMMISSION => 'commission',
        TransactionType::TYPE_DEPOSIT_COMMISSION => 'commission',
        TransactionType::TYPE_CANCEL_COMMISSION => 'commission',
        TransactionType::TYPE_DEPOSIT => 'deposit',
        TransactionType::TYPE_DEPOSIT_BY_ADMIN => 'deposit',
        TransactionType::TYPE_WITHDRAW => 'withdrawal',
        TransactionType::TYPE_WITHDRAW_BY_ADMIN => 'withdrawal',
    ];
    protected $direction = [
        TransactionType::TYPE_BET => '+',
        TransactionType::TYPE_DROP => '-',
        TransactionType::TYPE_SEND_PRIZE => '+',
        TransactionType::TYPE_CANCEL_PRIZE => '-',
        TransactionType::TYPE_PROMOTIANAL_BONUS => '+',
        TransactionType::TYPE_SEND_COMMISSION => '+',
        TransactionType::TYPE_DEPOSIT_COMMISSION => '+',
        TransactionType::TYPE_CANCEL_COMMISSION => '-',
        TransactionType::TYPE_DEPOSIT => '+',
        TransactionType::TYPE_DEPOSIT_BY_ADMIN => '+',
        TransactionType::TYPE_WITHDRAW => '+',
        TransactionType::TYPE_WITHDRAW_BY_ADMIN => '+',
    ];
    protected $validTranTypes = [];

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'Update the team profit data';

    public function doCommand(& $sMsg = null) {
        $sBeginDate = $this->argument('begin_date');
        $sEndDate = $this->argument('end_date');
        $iBeginTime = strtotime($sBeginDate);
        $iEndTime = strtotime($sEndDate);
        $iBeginTime or die("Invalid Begin Date\n");
        $iEndTime or die("Invalid End Date\n");
        $iEndTime >= $iBeginTime or die("End Date Less Than Begin Date!\n");
//        $this->validTranTypes = array_keys($this->types);
        for ($iTime = $iBeginTime; $iTime <= $iEndTime; $iTime += 3600 * 24) {
            $sDate = date('Y-m-d', $iTime);
//            continue;
//            pr($sDate);
            $aUserProfits = & $this->getUserProfits($sDate);
            $aData = $this->updateData($sDate, $aUserProfits);
            continue;
//            $oProfit = Profit::getProfitObject($sDate);
////            pr($oProfit->toArray());
//            $aData = $this->countDateData($sDate);
//            $oProfit->fill($aData);
//            $oProfit->save();
//            $oProfit->update($aData);
        }
    }

    protected function queryPrjCount($sDate, $bOnlyTester = false) {
        $sSql = "select count(*) prj_count from projects where status <> " . Project::STATUS_DROPED . " and bought_at between '$sDate' and '$sDate 23:59:59'";
        !$bOnlyTester or $sSql .= ' and is_tester = 1';
        $aResults = DB::select($sSql);
        return $aResults[0]->prj_count ? $aResults[0]->prj_count : 0;
    }

    protected function getArguments() {
        return array(
//            array('lottery_id', InputArgument::REQUIRED, null),
            array('begin_date', InputArgument::OPTIONAL, null, date('Y-m-d', time())),
            array('end_date', InputArgument::OPTIONAL, null, date('Y-m-d', time())),
        );
    }

    protected function & getUserProfits($sDate) {
        $sSql = "select * from user_profits where date = '$sDate'";
        $aResults = DB::select($sSql);
//        pr($aResults);
        return $aResults;
    }

    protected function updateData($sDate, $aUserProfits) {
//        pr($aTypeTotalAmounts);
        $aData = $aUserIds = [];
        foreach ($aUserProfits as $oUserProfit) {
            $aUserIds[] = $oUserProfit->user_id;
        }
        $aUserFores = $this->getForeData($aUserIds);
//        pr($aUserIds);
//        pr($aUserFores);
//        pr($aUserProfits);
        $aTeamProfits = [];
        foreach ($aUserProfits as $oUserProfit) {
            $iUserId = $oUserProfit->user_id;

            if (key_exists($iUserId, $aUserFores)) {
                foreach ($aUserFores[$iUserId] as $iForeId) {
                    $this->compileData($iForeId, $oUserProfit, $aTeamProfits);
                }
            }
            if ($oUserProfit->is_agent) {
                $this->compileData($iUserId, $oUserProfit, $aTeamProfits);
            }
        }
//        pr($aTeamProfits);
//        exit;
        foreach ($aTeamProfits as $iAgentId => $aData) {
            $oTeamProfit = TeamProfit::getTeamProfitObject($sDate, $iAgentId);
            $oTeamProfit->fill($aData);
            $oTeamProfit->save();
        }
    }

    function & getForeData($aUserIds = null) {
        $sSql = "select id, forefather_ids, is_agent from users";
        !$aUserIds or $sSql .= " where id in (" . implode(',', $aUserIds) . ")";
        $aResults = DB::select($sSql);
        $aData = [];
        foreach ($aResults as $oUser) {
            if ($oUser->forefather_ids) {
                $aFores = explode(',', $oUser->forefather_ids);
//                !$oUser->is_agent or $aFores[] = $oUser->id;
                $aData[$oUser->id] = $aFores;
            }
        }
        return $aData;
    }

    function compileData($iUserId, $oUserProfit, & $aTeamProfits) {
        if (!key_exists($iUserId, $aTeamProfits)) {
            $aTeamProfits[$iUserId] = [
                'deposit' => 0,
                'withdrawal' => 0,
                'turnover' => 0,
                'commission' => 0,
                'prize' => 0,
                'bonus' => 0,
                'profit' => 0,
            ];
        }
        $aTeamProfits[$iUserId]['deposit'] += $oUserProfit->deposit;
        $aTeamProfits[$iUserId]['withdrawal'] += $oUserProfit->withdrawal;
        $aTeamProfits[$iUserId]['turnover'] += $oUserProfit->turnover;
        $aTeamProfits[$iUserId]['commission'] += $oUserProfit->commission;
        $aTeamProfits[$iUserId]['prize'] += $oUserProfit->prize;
        $aTeamProfits[$iUserId]['bonus'] += $oUserProfit->bonus;
        $aTeamProfits[$iUserId]['profit'] += $oUserProfit->profit;
    }

}
