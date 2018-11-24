<?php

/**
 * 更新总销量表
 *
 * @author white
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateProfitCommand extends BaseCommand {

    /**
     * command name.
     *
     * @var string
     */
    protected $name = 'stat:update-daily-profit';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'Update the daily profit data';

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
//            continue;
//            pr($sDate);
            $oProfit = Profit::getProfitObject($sDate);
//            pr($oProfit->toArray());
            $aData = $this->countDateData($sDate);
            $oProfit->fill($aData);
            $oProfit->signed_count = UserLogin::getLoginUserCount($oProfit->date);
            $oProfit->registered_count = User::getRegisterCount($oProfit->date);
            $oProfit->registered_top_agent_count = User::getRegisterCount($oProfit->date, true);
            $oProfit->bought_count = ManProject::getBoughtUserCount($oProfit->date);
            empty($oProfit->getDirty()) or $oProfit->save();
            $aLotteryProfits = LotteryProfit::where('date', '=', $oProfit->date)->get();
            foreach ($aLotteryProfits as $oLotteryProfit) {
                $oLotteryProfit->setRatio($oProfit);
            }

//            $oProfit->update($aData);
        }
    }

    protected function countDateData($sDate) {
        // All
        $fTotalDeposit = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DEPOSIT, TransactionType::TYPE_DEPOSIT_BY_ADMIN,]);
        $fTotalWithdraw = $this->queryTotalAmount($sDate, [TransactionType::TYPE_WITHDRAW, TransactionType::TYPE_WITHDRAW_BY_ADMIN]);
        $fTotalBet = $this->queryTotalAmount($sDate, [TransactionType::TYPE_BET]);
        $fTotalDrop = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DROP]);
        $fTotalPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_PRIZE]);
        $fTotalDividend = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_BONUS]);
        $fTotalDropPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_PRIZE]);
        $fTotalCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_COMMISSION]);
        $fTotalDepositCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DEPOSIT_COMMISSION]);
        $fTotalDropCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_COMMISSION]);
        $fTotalCompensation = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SETTLING_CLAIMS]);
        $fTotalBonus = $this->queryTotalAmount($sDate, [TransactionType::TYPE_PROMOTIANAL_BONUS]);
        // tester
        $fTotalTesterDeposit = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DEPOSIT, TransactionType::TYPE_DEPOSIT_BY_ADMIN], True);
        $fTotalTesterWithdraw = $this->queryTotalAmount($sDate, [TransactionType::TYPE_WITHDRAW, TransactionType::TYPE_WITHDRAW_BY_ADMIN], True);
        $fTotalTesterBet = $this->queryTotalAmount($sDate, [TransactionType::TYPE_BET], True);
        $fTotalTesterDrop = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DROP], True);
        $fTotalTesterPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_PRIZE], True);
        $fTotalTesterDividend = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_BONUS], True);
        $fTotalTesterDropPrize = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_PRIZE], True);
        $fTotalTesterCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SEND_COMMISSION], True);
        $fTotalTesterDepositCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_DEPOSIT_COMMISSION], True);
        $fTotalTesterDropCommission = $this->queryTotalAmount($sDate, [TransactionType::TYPE_CANCEL_COMMISSION], True);
        $fTotalTesterCompensation = $this->queryTotalAmount($sDate, [TransactionType::TYPE_SETTLING_CLAIMS], True);
        $fTotalTesterBonus = $this->queryTotalAmount($sDate, [TransactionType::TYPE_PROMOTIANAL_BONUS], True);

        // 整理
        $fTotalBet -= $fTotalDrop;
        $fTotalPrize += $fTotalCompensation - $fTotalDropPrize;
        $fTotalCommission -= $fTotalDropCommission - $fTotalDepositCommission;
        $fTotalTesterBet -= $fTotalTesterDrop;
        $fTotalTesterPrize += $fTotalTesterCompensation - $fTotalTesterDropPrize;
        $fTotalTesterCommission -= $fTotalTesterDropCommission - $fTotalTesterDepositCommission;

        $fTotalNetDeposit = $fTotalDeposit - $fTotalTesterDeposit;
        $fTotalNetWithdraw = $fTotalWithdraw - $fTotalTesterWithdraw;
        $fTotalNetBet = $fTotalBet - $fTotalTesterBet;
        $fTotalNetPrize = $fTotalPrize - $fTotalTesterPrize;
        $fTotalNetDividend = $fTotalDividend - $fTotalTesterDividend;
        $fTotalNetCommission = $fTotalCommission - $fTotalTesterCommission;
        $fTotalNetBonus = $fTotalBonus - $fTotalTesterBonus;
        $fNetProfit = $fTotalNetBet - $fTotalNetPrize - $fTotalNetCommission - $fTotalNetBonus - $fTotalNetDividend;
        $fProfitMargin = $fTotalNetBet ? $fNetProfit / $fTotalNetBet : 0;

        $iPrjCount = $this->queryPrjCount($sDate);
        $iTesterPrjCount = $this->queryPrjCount($sDate, True);
        $iNetPrjCount = $iPrjCount - $iTesterPrjCount;

//        Log::info('net turnover=' . $fTotalNetBet);
//        Log::info('net prize=' . $fTotalNetPrize);
//        Log::info('net commission=' . $fTotalNetCommission);
//        Log::info('net bonus=' . $fTotalNetBonus);
//        Log::info('net salary=' . $fTotalNetSalary);
//        Log::info('net profit=' . $fNetProfit);
        return [
            'deposit' => $fTotalDeposit,
            'withdrawal' => $fTotalWithdraw,
            'turnover' => $fTotalBet,
            'prize' => $fTotalPrize,
            'dividend' => $fTotalDividend, 
            'commission' => $fTotalCommission,
            'bonus' => $fTotalBonus,
            'profit' => $fTotalBet - $fTotalPrize - $fTotalCommission - $fTotalBonus - $fTotalDividend,
            'tester_deposit' => $fTotalTesterDeposit,
            'tester_withdrawal' => $fTotalTesterWithdraw,
            'tester_turnover' => $fTotalTesterBet,
            'tester_prize' => $fTotalTesterPrize,
            'tester_dividend' => $fTotalTesterDividend,
            'tester_commission' => $fTotalTesterCommission,
            'tester_bonus' => $fTotalTesterBonus,
            'tester_profit' => $fTotalTesterBet - $fTotalTesterPrize - $fTotalTesterCommission - $fTotalTesterBonus - $fTotalTesterDividend,
            'net_deposit' => $fTotalNetDeposit,
            'net_withdrawal' => $fTotalNetWithdraw,
            'net_turnover' => $fTotalNetBet,
            'net_prize' => $fTotalNetPrize,
            'net_dividend' => $fTotalNetDividend, 
            'net_commission' => $fTotalNetCommission,
            'net_bonus' => $fTotalNetBonus,
            'net_profit' => $fNetProfit,
            'profit_margin' => $fProfitMargin,
            'prj_count' => $iPrjCount,
            'tester_prj_count' => $iTesterPrjCount,
            'net_prj_count' => $iNetPrjCount,
        ];
    }

    protected function queryTotalAmount($sDate, $aTransactionTypes, $bOnlyTester = false) {
        $sTransactionTypes = implode(',', $aTransactionTypes);
        $sSql = "select sum(amount) total_amount from transactions where type_id in ($sTransactionTypes) and created_at between '$sDate' and '$sDate 23:59:59'";
        !$bOnlyTester or $sSql .= ' and is_tester = 1';
        $aResults = DB::select($sSql);
        return $aResults[0]->total_amount ? $aResults[0]->total_amount : 0;
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

}
