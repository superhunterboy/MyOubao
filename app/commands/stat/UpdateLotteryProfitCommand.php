<?php

/**
 * 更新彩种盈亏报表
 *
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateLotteryProfitCommand extends BaseCommand {

    /**
     * command name.
     *
     * @var string
     */
    protected $name = 'stat:update-lottery-profit';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'Update the lottery profit data';

    public function doCommand(& $sMsg = null) {
        $sBeginDate = $this->argument('begin_date');
        $sEndDate = $this->argument('end_date');
        $iBeginTime = strtotime($sBeginDate);
        $iEndTime = strtotime($sEndDate);
        $iBeginTime or die("Invalid Begin Date\n");
        $iEndTime or die("Invalid End Date\n");
        $iEndTime >= $iBeginTime or die("End Date Less Than Begin Date!\n");

        for ($iTime = $iBeginTime; $iTime <= $iEndTime; $iTime += 3600 * 24) {
            $sDate = date('Y-m-d', $iTime);
            $oProfit = Profit::getProfitObject($sDate);
            $aLotteryProfits = LotteryProfit::where('date', '=', $sDate)->get();
            foreach ($aLotteryProfits as $oLotteryProfit) {
                $aData = $this->countDateData($sDate, $oLotteryProfit->lottery_id);
                Log::info($aData);
                $oLotteryProfit->fill($aData);
                $oLotteryProfit->setRatio($oProfit);
                empty($oLotteryProfit->getDirty()) or $oLotteryProfit->save();
            }
        }
    }

    protected function countDateData($sDate, $lotteryId) {
        // All
        $fTotalBet = $this->queryTotalAmount($sDate, [TransactionType::TYPE_BET], $lotteryId);
        $fTotalDrop = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DROP], $lotteryId);
        $fTotalPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_PRIZE], $lotteryId);
        $fTotalDropPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_PRIZE], $lotteryId);
        $fTotalCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_COMMISSION], $lotteryId);
        $fTotalDropCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_COMMISSION], $lotteryId);
        // tester
        $fTotalTesterBet = $this->queryTotalAmount($sDate, [TransactionType::TYPE_BET], $lotteryId, True);
        $fTotalTesterDrop = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DROP], $lotteryId, True);
        $fTotalTesterPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_PRIZE], $lotteryId, True);
        $fTotalTesterDropPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_PRIZE], $lotteryId, True);
        $fTotalTesterCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_COMMISSION], $lotteryId, True);
        $fTotalTesterDropCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_COMMISSION], $lotteryId, True);

        // 整理
        $fTotalBet -= $fTotalDrop;
        $fTotalPrize -= $fTotalDropPrize;
        $fTotalCommission -= $fTotalDropCommission;
        $fTotalTesterBet -= $fTotalTesterDrop;
        $fTotalTesterPrize -= $fTotalTesterDropPrize;
        $fTotalTesterCommission -= $fTotalTesterDropCommission;

        $fTotalNetBet = $fTotalBet - $fTotalTesterBet;
        $fTotalNetPrize = $fTotalPrize - $fTotalTesterPrize;
        $fTotalNetCommission = $fTotalCommission - $fTotalTesterCommission;

        $fNetProfit = $fTotalNetBet - $fTotalNetPrize - $fTotalNetCommission;
        $fProfitMargin = $fTotalNetBet ? $fNetProfit / $fTotalNetBet : 0;

        $iPrjCount = $this->queryPrjCount($sDate, $lotteryId);
        $iTesterPrjCount = $this->queryPrjCount($sDate, $lotteryId, True);
        $iNetPrjCount = $iPrjCount - $iTesterPrjCount;
        return [
            'turnover' => $fTotalBet,
            'prize' => $fTotalPrize,
            'commission' => $fTotalCommission,
            'profit' => $fTotalBet - $fTotalPrize - $fTotalCommission,
            'tester_turnover' => $fTotalTesterBet,
            'tester_prize' => $fTotalTesterPrize,
            'tester_commission' => $fTotalTesterCommission,
            'tester_profit' => $fTotalTesterBet - $fTotalTesterPrize - $fTotalTesterCommission,
            'net_turnover' => $fTotalNetBet,
            'net_prize' => $fTotalNetPrize,
            'net_commission' => $fTotalNetCommission,
            'net_profit' => $fNetProfit,
            'profit_margin' => $fProfitMargin,
            'prj_count' => $iPrjCount,
            'tester_prj_count' => $iTesterPrjCount,
            'net_prj_count' => $iNetPrjCount,
        ];
    }

    protected function queryTotalAmount($sDate, $aTransactionTypes, $lotteryId, $bOnlyTester = false) {
        $sTransactionTypes = implode(',', $aTransactionTypes);
        $sSql = "select sum(amount) total_amount from transactions where lottery_id=$lotteryId and type_id in ($sTransactionTypes) and created_at between '$sDate' and '$sDate 23:59:59'";
        !$bOnlyTester or $sSql .= ' and is_tester = 1';
//        Log::info($sSql);
        $aResults = DB::select($sSql);
        return $aResults[0]->total_amount ? $aResults[0]->total_amount : 0;
    }

    protected function queryPrjCount($sDate, $lotteryId, $bOnlyTester = false) {
        $sSql = "select count(*) prj_count from projects where lottery_id=$lotteryId and status <> " . Project::STATUS_DROPED . " and bought_at between '$sDate' and '$sDate 23:59:59'";
//        Log::info($sSql);
        !$bOnlyTester or $sSql .= ' and is_tester = 1';
        $aResults = DB::select($sSql);
        return $aResults[0]->prj_count ? $aResults[0]->prj_count : 0;
    }

    protected function getArguments() {
        return array(
//            array('lottery_id', InputArgument::REQUIRED, null),
            array('begin_date', InputArgument::OPTIONAL, null, date('Y-m-d')),
            array('end_date', InputArgument::OPTIONAL, null, date('Y-m-d')),
        );
    }

}
