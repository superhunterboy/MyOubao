<?php

class AccountController extends AdminBaseController {

    protected $modelName = 'Account';
    public static $listColumnMaps = [
        'blance' => 'serial_number_short',
        'status' => 'formatted_status',
        'bet_number' => 'display_bet_number',
        'amount' => 'amount_formatted',
        'finished_amount' => 'finished_amount_formatted',
        'canceled_amount' => 'canceled_amount_formatted',
    ];
    public static $viewColumnMaps = [
        'status' => 'formatted_status',
        'amount' => 'amount_formatted',
        'bet_number' => 'display_bet_number',
        'finished_amount' => 'finished_amount_formatted',
        'canceled_amount' => 'canceled_amount_formatted',
    ];

    public function setAccount($id) {
        $oAccount = Account::lock($id, $iLocker);
        if (empty($oAccount)) {
            return $this->goBack('error', '账户资金操作繁忙，请稍后再试！！');
        }
        $DB = DB::connection();
        $DB->beginTransaction();

        if ($bSucc = $oAccount->setWithdrawable($oAccount->available)) {
            $DB->commit();
            Account::unLock($oAccount->id, $iLocker, false);
            return $this->goBack('success', '可提现余额设置成功');
        } else {
            $DB->rollback();
            Account::unLock($oAccount->id, $iLocker, false);
            return $this->goBack('error', '可提现余额设置失败');
        }
    }

}
