<?php

/**
 * 更新用户盈亏表
 *
 * @author white
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateUserProfitCommand extends BaseCommand {

    /**
     * command name.
     *
     * @var string
     */
    protected $name = 'stat:update-user-profit';
    protected $types = [
        TransactionType::TYPE_BET => 'turnover',
        TransactionType::TYPE_DROP => 'turnover',
        TransactionType::TYPE_SEND_PRIZE => 'prize',
        TransactionType::TYPE_CANCEL_PRIZE => 'prize',
        TransactionType::TYPE_SETTLING_CLAIMS => 'prize',
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
        TransactionType::TYPE_SETTLING_CLAIMS => '+',
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
    protected $description = 'Update the user profit data';

    public function doCommand(& $sMsg = null) {
        $sBeginDate = $this->argument('begin_date');
        $sEndDate = $this->argument('end_date');
        $iBeginTime = strtotime($sBeginDate);
        $iEndTime = strtotime($sEndDate);
        $iBeginTime or die("Invalid Begin Date\n");
        $iEndTime or die("Invalid End Date\n");
        $iEndTime >= $iBeginTime or die("End Date Less Than Begin Date!\n");
        $this->validTranTypes = array_keys($this->types);
        for ($iTime = $iBeginTime; $iTime <= $iEndTime; $iTime += 3600 * 24) {
            $sDate = date('Y-m-d', $iTime);
//            continue;
//            pr($sDate);
            $aTypeTotalAmounts = & $this->getTotalAmountOfTypes($sDate);
            $aData = $this->updateData($sDate, $aTypeTotalAmounts);
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

    protected function & getTotalAmountOfTypes($sDate) {
        $sSql = "select user_id, username, is_tester, user_forefather_ids, type_id, sum(amount) amount from transactions where created_at >= '" . $sDate . " 00:00:00'  and created_at <='" . $sDate . " 23:59:59'  group by user_id, type_id";
        $aResults = DB::select($sSql);
//        pr($aResults);
        return $aResults;
    }

    protected function updateData($sDate, $aTypeTotalAmounts) {
//        pr($aTypeTotalAmounts);
        $aData = [];
//        pr($this->validTranTypes);
//        pr($this->direction);
//        exit;
        foreach ($aTypeTotalAmounts as $oStat) {
            if (!in_array($oStat->type_id, $this->validTranTypes)) {
                continue;
            }
            $sField = $this->types[$oStat->type_id];
            $fAmount = $this->direction[$oStat->type_id] == '-' ? - $oStat->amount : $oStat->amount;
            $sKey = md5($sDate . '-' . $oStat->user_id);
            if (!isset($aData[$sKey])) {
                $aData[$sKey]['user_id'] = $oStat->user_id;
                $aData[$sKey]['date'] = $sDate;
            }
            isset($aData[$sKey][$sField]) ? $aData[$sKey][$sField] += $fAmount : $aData[$sKey][$sField] = $fAmount;
        }
        $aFields = array_unique(array_values($this->types));
        foreach ($aData as $data) {
            $oUserProfit = UserProfit::getUserProfitObject($data['date'], $data['user_id']);
            $aExistsOfData = array_keys($data);
            $aDiff = array_diff($aFields, $aExistsOfData);
            $oUserProfit->fill($data);
            foreach ($aDiff as $sField) {
                $oUserProfit->$sField = 0;
            }
            $oUserProfit->profit = $oUserProfit->countProfit();
            if (!$oUserProfit->save()) {
                $aError = $oUserProfit->validationErrors->toArray();
                file_put_contents('/tmp/userprofit', var_export($aError, true), FILE_APPEND);
            }
        }
    }

}
