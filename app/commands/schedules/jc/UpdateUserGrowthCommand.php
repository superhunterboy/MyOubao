<?php

namespace JcCommand;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 抓取竞彩赛果
 */
class UpdateUserGrowthCommand extends \BaseCommand {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jc:update-user-growth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $sFileName = 'update-user-growth';
    
    protected function getArguments() {
      return array(
          array('lottery_id', InputArgument::REQUIRED, null),
          array('date', InputArgument::OPTIONAL, null),
      );
    }
    
    protected function getOptions()
    {
            return array(
//                array('force', null, InputOption::VALUE_OPTIONAL, 'skip check update time.', null),
            );
    }
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
            parent::__construct();
            
            $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
    }
    
    public function fire()
    {
        //盈利方案
//        $aJcBets = \JcModel\ManJcBet::where('status', \JcModel\ManJcBet::STATUS_PRIZE_SENT)
//            ->where('created_at', '>=', date('Y-m-d', time() - 86400))
//            ->where('created_at', '<', date('Y-m-d'))
//            ->groupby('user_id')
//            ->get(['user_id']);
        $iLotteryId = intval($this->argument('lottery_id'));
        $oLottery = \JcModel\JcLotteries::find($iLotteryId);
        if (empty($oLottery)){
            $this->writeLog('lottery is empty. id: '. $iLotteryId);
            $this->info('lottery is empty. id: '. $iLotteryId);
            return ;
        }
        $dDate = $this->argument('date') ? $this->argument('date') : date('Y-m-d', time() - 86400);
        $aJcBets = \JcModel\ManJcBet::getForGrowthByLotteryIdAndDate($iLotteryId, $dDate);
        
        if (empty($aJcBets)){
            $this->writeLog('bet is empty.');
            $this->info('bet is empty.');
            return ;
        }
//        var_dump($aJcBets);die;
        $aUserIds = $aSilverUserIds = [];
        foreach($aJcBets as $oBet){
            $iUserId = $oBet->user_id;
            $aUserIds[$iUserId] = $iUserId;
        }
        
        foreach($aUserIds as $iUserId){
            $iDropCount = \JcModel\ManJcProject::countDropedByUserIdAndDate($iUserId, $dDate);
            if ($iDropCount < 3){
                //撤单不大于3个的用户才进行统计
                $aSilverUserIds[] = $iUserId;
            }
        }
        
        $aGrowthData = [];
        foreach($aJcBets as $oBet){
            $this->writeLog('calculating growth. bet id: '. $oBet->id);
            $fGains = $oBet->prize - $oBet->amount;
            $bIsDrop = false;
            if ($oBet->group_id > 0){
                $oGroupBuy = \JcModel\JcGroupBuy::find($oBet->group_id);
                $aDropStatus = [
                    \JcModel\JcGroupBuy::STATUS_DROPED,
                    \JcModel\JcGroupBuy::STATUS_DROPED_BY_SYSTEM,
                ];
                $bIsDrop = in_array($oGroupBuy->status, $aDropStatus);
            }else{
                $oProject = \JcModel\JcProject::getByBetId($oBet->id);
                $aDropStatus = [
                    \JcModel\JcProject::STATUS_DROPED,
                    \JcModel\JcProject::STATUS_DROPED_BY_SYSTEM,
                ];
                $bIsDrop = in_array($oProject->status, $aDropStatus);
            }
            $iGoldGrowth = $iSilverGrowth = 0;
            if (!$bIsDrop){
                $iGoldGrowth = \JcModel\JcUserGrowth::countGoldGrowth($fGains, $oBet->amount);
            }else if (in_array($iUserId, $aSilverUserIds)){
                $iSilverGrowth = \JcModel\JcUserGrowth::countSilverGrowth($fGains, $oBet->amount);
            }
            
            if (!isset($aGrowthData[$oBet->user_id][$oBet->method_group_id])){
                $aGrowthData[$oBet->user_id][$oBet->method_group_id] = [
                    'gold_growth' => $iGoldGrowth,
                    'silver_growth' => $iSilverGrowth,
                ];
            }else{
                $aGrowthData[$oBet->user_id][$oBet->method_group_id]['gold_growth'] += $iGoldGrowth;
                $aGrowthData[$oBet->user_id][$oBet->method_group_id]['silver_growth'] += $iSilverGrowth;
            }
        }
        
        if (empty($aGrowthData)){
            $this->writeLog('growth data is empty.');
            return ;
        }else{
            $this->info('count: ' . count($aGrowthData));
            $this->info('data: ' . var_export($aGrowthData, 1));
        }
//            var_dump($oBet->id, $iGoldGrowth, $iSilverGrowth);
        foreach($aGrowthData as $iUserId => $aValue){
            \DB::beginTransaction();
            $bSucc = false;
            foreach($aValue as $iMethodGroupId => $aGrowth){
                if ($aGrowth['gold_growth'] <= 0 && $aGrowth['silver_growth'] <= 0){
                    continue;
                }
                $oUserGrowth = \JcModel\JcUserGrowth::getByLotteryIdAndUserIdAndMethodGroupId($iLotteryId, $iUserId, $iMethodGroupId);
                $this->writeLog('user id: ' . $iUserId . '. growth data:' . var_export($aGrowth, 1));
                if (empty($oUserGrowth)){
                    $aData = $aGrowth;
                    $aData['lottery_id'] = $iLotteryId;
                    $aData['user_id'] = $iUserId;
                    $aData['method_group_id'] = $iMethodGroupId;
                    $aData['last_update'] = $dDate;
                    $oUserGrowth = new \JcModel\JcUserGrowth($aData);
                    $bSucc = $oUserGrowth->addUserGrowth();
                }else{
                    if ($oUserGrowth->last_update > $dDate){
                        $this->writeLog('skip user. id: '. $iUserId);
                        break;
                    }
                    $oUserGrowth->gold_growth += $aGrowth['gold_growth'];
                    $oUserGrowth->silver_growth += $aGrowth['silver_growth'];
                    $oUserGrowth->last_update = $dDate;
                    $bSucc = $oUserGrowth->saveUserGrowth();
//                    $bSucc = \JcModel\JcUserGrowth::updateUserExtra($iUserId, $aGrowth);
                }
                if (!$bSucc){
                    $this->writeLog(var_export($oUserGrowth->errors()->getMessages(), 1));
                    $this->writeLog('failed. id: '. $iUserId);
                    break;
                }
            }
            if ($bSucc){
                $this->writeLog('success. id: '. $iUserId);
                \DB::commit();
            }else{
                \DB::rollback();
            }
        }
    }
}