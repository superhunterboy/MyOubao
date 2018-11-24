<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 计算总代升降点
 */
class CreateActivityReportData extends BaseCommand {

    const STAT_PLAYERS = 'activity_report_player_stats';

    protected $sFileName = 'createactivityreportdata';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'activity:create-report-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create activity report data';

    public function fire() {
        $aTableInfos = Config::get('activity.reportTables');
        $sMainTableName = Config::get('activity.mainTable');
        $aMatchInfo = Config::get('activity.matchInfo');
        $aMatchInfoUpdate = Config::get('activity.matchInfoUpdate');
        foreach ($aTableInfos as $sTableName => $aTableInfo) {
            if ($sTableName == self::STAT_PLAYERS) {
                $aStatPlayers = $this->_statPlayers($sMainTableName, $sTableName, $aTableInfo);
                foreach ($aStatPlayers as $val) {
                    $oData = DB::table($sTableName)->where('username', $val['username'])->first();
                    if (is_object($oData)) {
                        $bSucc = DB::table($sTableName)->where('id', $oData->id)->update($val);
                    } else {
                        DB::table($sTableName)->insert($val);
                    }
                }
                continue;
            }
            if (key_exists($sTableName, $aMatchInfo)) {
                $aPrizeId = $aMatchInfo[$sTableName];
                $sLastStatTime = DB::table($sTableName)->orderBy('created_at', 'desc')->pluck('created_at');
//                $oQuery = DB::table($sMainTableName)->whereIn('prize_id', $aPrizeId);
                $oQuery = DB::table($sMainTableName)->whereIn('prize_id', $aPrizeId);
                if ($sLastStatTime) {
                    $oQuery->where('created_at', '>', $sLastStatTime);
                }
                $aUserPrizeInfo = $oQuery->get();
                $aData = $this->_makeData($aUserPrizeInfo, $aTableInfo['field'], $aTableInfo['convertField']);
//                pr($aData);
//                exit;
                if (count($aData) > 0) {
                    DB::table($sTableName)->insert($aData);
                }
            } else if (key_exists($sTableName, $aMatchInfoUpdate)) {
                $aPrizeId = $aMatchInfoUpdate[$sTableName];
                $oQuery = DB::table($sMainTableName)->whereIn('prize_id', $aPrizeId);
                $aUserPrizeInfo = $oQuery->get();
                $aData = $this->_makeData($aUserPrizeInfo, $aTableInfo['field'], $aTableInfo['convertField']);
                foreach ($aData as $val) {
                    $oData = DB::table($sTableName)->whereIn('prize_id', $aPrizeId)->where('username', $val['username'])->where('created_at', $val['created_at'])->first();
                    if (is_object($oData)) {
                        $bSucc = DB::table($sTableName)->where('id', $oData->id)->update($val);
                    } else {
                        DB::table($sTableName)->insert($val);
                    }
                }
            }
        }
    }

    private function _makeData($aData, $aFields, $aConvertFields) {
//        pr($aData);
//        pr($aFields);
//        pr($aConvertFields);
//        exit;
        $aResult = array();
        foreach ($aData as $obj) {
            $a = [];
            foreach ($aFields as $key) {
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'source':
                            $a[$key] = __('activityuserprize.' . ActivityUserPrize::$aSources[$obj->source]);
                            break;
                        case 'status':
                            $a[$key] = __('activityuserprize.' . ActivityUserPrize::$aStatus[$obj->source]);
                            break;
                        case 'is_verified':
                            $a[$key] = __('activityuserprize.' . ActivityUserPrize::$aVerifyStatus[$obj->source]);
                            break;
                        case 'user':
                            $oUser = User::find($obj->user_id);
                            if (is_object($oUser)) {
                                if (is_object($oUser->$key)) {
                                    $a[$key] = $oUser->$key->toDateTimeString();
                                } else {
                                    $a[$key] = $oUser->$key;
                                }
                            } else {
                                $a[$key] = '';
                            }
                            break;
                        case 'deposit':
                            $aCompanyOrderNum = json_decode($obj->data);
                            $oDeposit = Deposit::findDepositByCompanyOrderNum($aCompanyOrderNum->company_order_num);
                            $a[$key] = 0;
                            if (is_object($oDeposit)) {
                                $a[$key] = $oDeposit->$key;
                            }
                            break;
                        case 'withdrawal':
                            $aCompanyOrderNum = json_decode($obj->data);
                            $oWithdrawal = Withdrawal::findDepositBySerialNumberNum($aCompanyOrderNum['serial_number']);
                            if (is_object($oWithdrawal)) {
                                $a[$key] = $oWithdrawal->$key;
                            } else {
                                $a[$key] = 0;
                            }
                            break;
                        case 'calculate_cash_weekly':
                            $aCompanyOrderNum = json_decode($obj->data);
                            $oDeposit = Deposit::findDepositByCompanyOrderNum($aCompanyOrderNum->company_order_num);
                            $a[$key] = 0;
                            if (is_object($oDeposit)) {
                                $a[$key] = $oDeposit->real_amount / 4;
                            }
                            break;
                        case 'activity_stat_help':
                            $aCompanyOrderNum = json_decode($obj->data);
                            if (in_array($obj->prize_id, Config::get('activity.matchInfoUpdate.activity_report_deposit_4times'))) {
                                $a[$key] = 'N/a';
                                $fFinalTurnover = 0;
                                $aActivityCashBacks = ActivityCashBack::getDataByUserId($obj->user_id, $obj->prize_id);
                                foreach ($aActivityCashBacks as $val) {
                                    if ($key == 'first_week' && starts_with($val['end_date'], '2014-12-28')) {
                                        $a[$key] = $val['total_turnover'];
                                    } else if ($key == 'second_week' && starts_with($val['end_date'], '2015-01-04')) {
                                        $a[$key] = $val['total_turnover'];
                                    } else if ($key == 'third_week' && starts_with($val['end_date'], '2015-01-11')) {
                                        $a[$key] = $val['total_turnover'];
                                    } else if ($key == 'fourth_week' && starts_with($val['end_date'], '2015-01-18')) {
                                        $a[$key] = $val['total_turnover'];
                                    } else if ($key == 'fifth_week' && starts_with($val['end_date'], '2015-01-25')) {
                                        $a[$key] = $val['total_turnover'];
                                    } else if ($key == 'sixth_week' && starts_with($val['end_date'], '2015-02-01')) {
                                        $a[$key] = $val['total_turnover'];
                                    } else if ($key == 'seventh_week' && starts_with($val['end_date'], '2015-02-08')) {
                                        $a[$key] = $val['total_turnover'];
                                    }
                                }
                            } else if (in_array($obj->prize_id, Config::get('activity.matchInfoUpdate.activity_report_deposit_1times'))) {
                                $a[$key] = NULL;
                                $aActivityCashBacks = ActivityCashBack::getDataByUserId($obj->user_id, $obj->prize_id);
                                foreach ($aActivityCashBacks as $val) {
                                    if ($aCompanyOrderNum->real_amount * 64 < $val['total_turnover']) {
                                        $a[$key] = $val['end_date'];
                                        break;
                                    }
                                }
                            }
                            break;
                        case 'calculate_deposit_total_turnover':
                            $a[$key] = 0;
                            $oActivityCashBacks = ActivityCashBack::getDataByUserId($obj->user_id, $obj->prize_id)->first();
                            if (is_object($oActivityCashBacks)) {
                                $a[$key] = $oActivityCashBacks->total_turnover;
                            }
                            break;
                        case 'parent_user':
                            $oUser = User::find($obj->user_id);
                            $a[$key] = '';
                            if (is_object($oUser)) {
                                $oParentUser = User::find($oUser->parent_id);
                                if (is_object($oParentUser)) {
                                    if (is_object($oParentUser->$key)) {
                                        $a[$key] = $oParentUser->toDateTimeString();
                                    } else {
                                        $a[$key] = $oParentUser->$key;
                                    }
                                }
                            }
                            break;
                        case 'turnover_48h':
                            $sBeginDate = date('Y-m-d', strtotime($obj->created_at . '+1 day'));
                            $sEndDate = date('Y-m-d', strtotime($sBeginDate . ' +1 days'));
//                            pr($sBeginDate);
//                            pr($sEndDate);
                            $fUserTotalTurnover = UserProfit::getUserTotalTurnover($sBeginDate, $sEndDate, $obj->user_id);
                            $a[$key] = $fUserTotalTurnover;
                            break;
                        case 'activity_turnover':
                            $oActivity = Activity::find($obj->activity_id);
                            if (is_object($oActivity)) {
                                $sBeginDate = date('Y-m-d', strtotime($oActivity->start_time));
                            }
                            $sEndDate = date('Y-m-d', strtotime($obj->created_at));
                            $fUserTotalTurnover = UserProfit::getUserTotalTurnover($sBeginDate, $sEndDate, $obj->user_id);
                            $a[$key] = $fUserTotalTurnover;
                            break;
                        case 'calculate_turnover_48h':
                            $a[$key] = 0;
                            if (starts_with($obj->prize_name, '1%')) {
                                $a[$key] = $a['turnover_48h'] * 0.01;
                            } else if (starts_with($obj->prize_name, '2%')) {
                                $a[$key] = $a['turnover_48h'] * 0.02;
                            }
                            break;
                        case 'bank_card':
                            $oUser = User::find($obj->user_id);
                            if (is_object($oUser)) {
                                $aUserBankCards = UserBankCard::getUserCardsInfo($oUser->id, ['id', 'lock_time', 'islock']);
                                $sLockCardTime = '';
                                foreach ($aUserBankCards as $oUserBankCard) {
                                    if ($oUserBankCard->islock == UserBankCard::LOCKED) {
                                        $sLockCardTime = $oUserBankCard->lock_time;
                                        break;
                                    }
                                }
                                $a[$key] = $sLockCardTime;
                            } else {
                                $a[$key] = '';
                            }
                            break;
                        case 'activity_prize':
                            $oPrize = ActivityPrize::find($obj->prize_id);
                            $a[$key] = '';
                            if (is_object($oPrize)) {
                                $a[$key] = $oPrize->value;
                            }
                            break;
                        case 'four_times':
                            break;
                    }
                } else {
                    $a[$key] = $obj->$key;
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

    /**
     * 
     * @param string $sMainTableName 奖品表名称
     * @param string $sTableName    统计后数据存入表的名称
     * @param array $aTableInfo     统计后数据存入表的字段信息
     * @return type
     */
    private function _statPlayers($sMainTableName, $sTableName, $aTableInfo) {
        $aUserPrizeInfo = DB::table($sMainTableName)->get();
        $aStatPlayers = [];
        foreach ($aUserPrizeInfo as $obj) {
//            $aStatPlayers[$obj->user_id] = [];
            foreach ($aTableInfo['field'] as $sFieldName) {
                if (array_key_exists($sFieldName, $aTableInfo['convertField'])) {
                    $val = $aTableInfo['convertField'][$sFieldName];
                    if (starts_with($sFieldName, 'count')) {
                        if (in_array($obj->prize_id, $val)) {
                            if (isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                                $aStatPlayers[$obj->user_id][$sFieldName] = $aStatPlayers[$obj->user_id][$sFieldName] + $obj->count;
                            } else {
                                $aStatPlayers[$obj->user_id][$sFieldName] = $obj->count;
                            }
                        } else {
                            if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                                $aStatPlayers[$obj->user_id][$sFieldName] = 0;
                            }
                        }
                    } else if (starts_with($val, 'prize_total_price')) {
                        $oPrize = ActivityPrize::find($obj->prize_id);
                        if (is_object($oPrize)) {
                            if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                                $aStatPlayers[$obj->user_id][$sFieldName] = $oPrize->value;
                            } else {
                                $aStatPlayers[$obj->user_id][$sFieldName] = $aStatPlayers[$obj->user_id][$sFieldName] + $oPrize->value;
                            }
                        }
                    } else if (starts_with($val, 'deposit_total_amount')) {
                        if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                            $oActivity = Activity::find($obj->activity_id);
                            if (!is_object($oActivity)) {
                                countinue;
                            }
                            $fDepositAmount = Deposit::getTotalAmountByDate($oActivity->start_time, $oActivity->end_time, $obj->user_id);
                            $aStatPlayers[$obj->user_id][$sFieldName] = $fDepositAmount;
                        }
                    } else if (starts_with($val, 'activity_total_turnover')) {
                        if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                            $oActivity = Activity::find($obj->activity_id);
                            if (!is_object($oActivity)) {
                                countinue;
                            }
                            $sBeginDate = date('Y-m-d', strtotime($oActivity->start_time));
                            $fTotalTurnover = UserProfit::getUserTotalTurnover($sBeginDate, $oActivity->end_time, $obj->user_id);
                            $aStatPlayers[$obj->user_id][$sFieldName] = $fTotalTurnover;
                        }
                    } else if (starts_with($val, 'activity_total_profits')) {
                        if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                            $oActivity = Activity::find($obj->activity_id);
                            if (!is_object($oActivity)) {
                                countinue;
                            }
                            $sBeginDate = date('Y-m-d', strtotime($oActivity->start_time));
                            $fTotalTurnover = UserProfit::getUserTotalProfit($sBeginDate, $oActivity->end_time, $obj->user_id);
                            $aStatPlayers[$obj->user_id][$sFieldName] = $fTotalTurnover;
                        }
                    } else if (starts_with($val, 'player_contributions')) {
                        $oUser = User::find($obj->user_id);
                        if (is_object($oUser)) {
                            $oUserExtraInfo = UserExtraInfo::findByUser($oUser);
                            $aStatPlayers[$obj->user_id][$sFieldName] = $oUserExtraInfo->contribution;
                        } else {
                            $aStatPlayers[$obj->user_id][$sFieldName] = 0;
                        }
                    } else if (starts_with($val, 'user')) {
                        if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                            $oUser = User::find($obj->user_id);
                            $aStatPlayers[$obj->user_id][$sFieldName] = '';
                            if (is_object($oUser)) {
                                $aStatPlayers[$obj->user_id][$sFieldName] = $oUser->$sFieldName;
                            }
                        }
                    } else if (starts_with($val, 'parent_user')) {
                        if (!isset($aStatPlayers[$obj->user_id][$sFieldName])) {
                            $oUser = User::find($obj->user_id);
                            $aStatPlayers[$obj->user_id][$sFieldName] = '';
                            if (is_object($oUser)) {
                                $oParentUser = User::find($oUser->parent_id);
                                if (is_object($oParentUser)) {
                                    $aStatPlayers[$obj->user_id][$sFieldName] = $oParentUser->$sFieldName;
                                }
                            }
                        }
                    }
                } else {
                    $aStatPlayers[$obj->user_id][$sFieldName] = $obj->$sFieldName;
                }
            }
        }
        return $aStatPlayers;
    }

}
