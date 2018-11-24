<?php
namespace JcModel;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-7
 * Time: 上午10:51
 */
class ManJcBet extends JcBet
{
    
    public static $columnForList = [
        'id',
        'serial_number',
        'username',
        'ways',
        'multiple',
        'total',
        'amount',
        'prize',
        'danma',
        'status',
        'prize_status',
        'created_at',
    ];
    
    public $orderColumns = [
        'id' => 'desc',
    ];

    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'prize_status' => 'formatted_prize_status',
    ];
    
//    protected function getFormattedPrizeStatusAttribute() {
//        if (isset(self::$validPrizeStatus[$this->attributes['prize_status']])){
//            return __(self::$defaultPreFix . '_manjcbet.' . strtolower(self::$validPrizeStatus[$this->attributes['prize_status']]));
//        }
//        return '';
//    }

    protected function getWaysAttribute() {
        if (isset($this->attributes['game_extra'])){
            $aGameExtras = explode(',',$this->attributes['game_extra']);
            $aJcWays = JcWay::getWayByLotteryIdAndIdentifiers($this->lottery_id,$aGameExtras);
            $aWays = [];
            foreach($aJcWays as $oJcWay){
                $aWays[] = $oJcWay->name;
            }
            return implode(',',$aWays);
        }
        return '';
    }

    public static function getBet($id){
        $aData = [];
        $oBet = self::find($id);
        $aData['bet_id'] = $id;
        $aData['way'] = $oBet->ways;
        $aData['multiple'] = $oBet->multiple;
        $aData['amount'] = $oBet->amount;
        $aData['prize'] = $oBet->prize;
        $aData['single_amount'] = $oBet->single_amount;
        $sDanmas = $oBet->danma;
        $aData['matches'] = self::getMatches($id);
        if($sDanmas){
            $aDanmas = explode(',',$sDanmas);
            foreach($aDanmas as $sDanma){
                $aData['matches'][$sDanma]['is_danma'] = 1;
            }
        }

        return $aData;
    }

    public static function getDetail($id){
        $aData = [];
        $oBet = self::find($id);
        $aData['multiple'] = $oBet->multiple;
        $aData['single_amount'] = $oBet->single_amount;
        $aBetsDetails = ManJcBetsDetail::getBetsDetailByBetId($id);
        if($aBetsDetails->count()){
            $aMatchIds = [];
            $oBetsMatchList = [];
            foreach($aBetsDetails as $oBetsDetail){
                $oBetsMatches = ManJcBetsMatch::getBetsMatchesByBetIdAndDetailId($id,$oBetsDetail->id);
                foreach ($oBetsMatches as $oBetsMatche) {
                    $iLotteryId = $oBetsMatche->lottery_id;
                    $aMatchIds[$oBetsMatche->match_id] = $oBetsMatche->match_id;
                    $oBetsMatchList[$oBetsDetail->id][] = $oBetsMatche;
                }
            }
            $aOdds = ManJcOdds::getOddsByMatchIds($iLotteryId, $aMatchIds);
            $aOddsList = [];
            foreach($aOdds as $oOdds){
                $aOddsList[$oOdds->code] = $oOdds;
            }
            foreach($aBetsDetails as $oBetsDetail){
                $total_odds = 1;
                $oBetsMatches = $oBetsMatchList[$oBetsDetail->id];
                foreach ($oBetsMatches as $oBetsMatche) {
                    $oOdds = $aOddsList[$oBetsMatche->code];
                    $aData['bet_detail'][$oBetsDetail->id]['detail'][] = substr($oBetsMatche->match_id,8).'('.$oOdds->name.'['.$oBetsMatche->odds.'])';
                    $aData['bet_detail'][$oBetsDetail->id]['odds'][] = $oBetsMatche->odds;
                    $aData['bet_detail'][$oBetsDetail->id]['a'][] = substr($oBetsMatche->match_id,8).'='.$oBetsMatche->code;
                    $total_odds *= $oBetsMatche->odds;
                }

                $aData['bet_detail'][$oBetsDetail->id]['prize'] = $total_odds*$aData['single_amount']*$aData['multiple'];
                $aData['bet_detail'][$oBetsDetail->id]['real_prize'] = $oBetsDetail->prize;
                $aData['bet_detail'][$oBetsDetail->id]['status'] = $oBetsDetail->status;
                $aData['bet_detail'][$oBetsDetail->id]['total_matches'] = $oBetsDetail->total_matches;
            }
        }
        return $aData;
    }

    public static function getMatches($id){
        $aData = [];
        $oBet = ManJcBet::find($id);
        $aBetsMatches = ManJcBetsMatch::getMatchesByBetId($id);
        if($aBetsMatches->count()){
            $aMatchIds = [];
            foreach($aBetsMatches as $oBetsMatches){
                $aMatchIds[] = $oBetsMatches->match_id;
            }
            $aOdds = \JcModel\JcOdds::getOddsByMatchIds($oBet->lottery_id, $aMatchIds);
            $aOddsList = [];
            foreach($aOdds as $oOdds){
                $aOddsList[$oOdds->code] = $oOdds;
            }
            foreach($aBetsMatches as $oBetsMatches){
                pr($oBetsMatches->getAttributes());
            }
            $aMatchesInfos = ManJcMatchesInfo::getMatchesByMatchIds($aMatchIds);

            foreach($aMatchesInfos as $oMatchesInfo){
                $aData[$oMatchesInfo->match_id]['match_time'] = $oMatchesInfo->match_time;
                $aData[$oMatchesInfo->match_id]['match_teams'] = $oMatchesInfo->home_team_name.'VS'.$oMatchesInfo->away_team_name;
            }
        }
        return $aData;
    }

    public static function getForGrowthByLotteryIdAndDate($iLotteryId = 0, $dDate = null){
        $dStartTime = date('Y-m-d', strtotime($dDate));
        $dEndTime = date('Y-m-d', strtotime($dDate) + 86400);
        return \JcModel\ManJcBet::where('lottery_id', $iLotteryId)
            ->where('status', \JcModel\ManJcBet::STATUS_PRIZE_SENT)
            ->where('sent_at', '>=', $dStartTime)
            ->where('sent_at', '<', $dEndTime)
            ->get();
    }
}
