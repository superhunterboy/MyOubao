<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-9
 * Time: 上午9:56
 */

namespace JcModel;


class ManJcMatchesInfo extends JcMatchesInfo
{
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'match_id',
        'original_id',
        'bet_date',
        'match_time',
        'is_hot',
        'league_name',
        'home_team_name',
        'away_team_name',
        'handicap',
        'half_score',
        'status',
        'prize_status',
        'score',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'num' => '',
//        'match_id' => 'required|integer',
        'lottery_id' => 'required|integer',
        'original_id' => 'required|integer',
        'bet_date' => 'required|date',
        'match_time' => 'required|date',
        'is_hot' => 'integer',
//        'status' => 'integer',
//        'prize_status' => 'integer',
        'league_id' => 'required|integer',
        'home_id' => 'required|integer',
        'away_id' => 'required|integer',
        'handicap' => 'integer',
//        'half_score' => 'sometimes|regex:/[\d]+:[\d]+$/',
//        'score' => 'sometimes|regex:/[\d]+:[\d]+$/',
    ];

    /**
     * ignore columns for edit
     * @var array
     */
    public static $ignoreColumnsInEdit = [
        'score',
        'half_score'
    ];


    public static function verifiedMatche($oMatche){
        //将联赛信息写入jc_league
        $oLeague = JcLeague::saveLeague($oMatche);
        if (empty($oLeague)){
            return false;
        }
        //将主队客队信息写入jc_team
        $aTeams = JcTeam::saveTeams($oMatche);
        if (empty($aTeams)){
            return false;
        }
        //将赛事数据插入jc_matches_info
        $oLottery = JcLotteries::getByLotteryKey('football');
        $iLotteryId = $oLottery->id;

        $aMatcheInfoData = [
            'num' => sprintf('%03d',substr($oMatche->match_id,-1,3)),
            'match_id' => $oMatche->match_id,
            'lottery_id' => $iLotteryId,
            'original_id' => $oMatche->id,
            'bet_date' => $oMatche->getBetDate(),
            'num' => substr($oMatche->match_id, -3),
            'match_time' => self::matcheTimeFormat($oMatche->date,$oMatche->time),
            'league_id' => $oLeague->id,
            'home_id' => $aTeams[0],
            'away_id' => $aTeams[1],
            'weather' => $oMatche->weather,
            'temperature' => self::_formatTemperature($oMatche->temperature),
            'weather_pic' => self::_formatWeatherPic($oMatche->weather_pic),
            'is_hot' => $oMatche->hot,
            'status' => \JcModel\JcMatchesInfo::MATCH_SELLING_STATUS_CODE,
            'handicap' => self::getHandicap($oMatche->hhad)
        ];
        $oMatcheInfo = self::saveMatcheInfo($aMatcheInfoData);
        if (empty($oMatcheInfo)){
            return false;
        }

        //保存单关信息
        $oSingle = ManJcMatchMethod::saveSingle($oMatche, $oLottery->id);
        if (empty($oSingle)){
            return false;
        }

        $oOdds = ManJcOdds::saveOdds($oMatche,$oLottery->id);
        if (empty($oOdds)){
            return false;
        }

        return true;
    }

    public static function matcheTimeFormat($date,$time){
        return $date.' '.$time;
    }

    public static function saveMatcheInfo($aData){
        $oMatcheInfo = self::where('match_id','=',$aData['match_id'])->first();
        if(!$oMatcheInfo) {
            return self::create($aData);
        }
        return true;

    }
    
    public static function updateMatches($aData,$matchId){
        $oMatche = self::where('match_id','=',$matchId)->first();
        if($oMatche){
            $oMatche->match_time = self::matcheTimeFormat($aData['date'],$aData['time']);
            $oMatche->handicap = self::getHandicap($aData['hhad']);
            
            $sWeather = '';
            if (isset($aData['weather'])){
                $sWeather = trim($aData['weather']);
            }
            $sWeatherPic = '';
            if (isset($aData['weather_pic'])){
                $sWeatherPic = self::_formatWeatherPic($aData['weather_pic']);
            }
            $sTemperature = '';
            if (isset($aData['temperature'])){
                $sTemperature = self::_formatTemperature($aData['temperature']);
            }
            $oMatche->weather = $sWeather;
            $oMatche->weather_pic = $sWeatherPic;
            $oMatche->temperature = $sTemperature;
            
            if($oMatche->save()){
                return true;
            }else{
                return false;
            }
        }
    }
    
    private static function _formatWeatherPic($sWeatherPic){
        if ($sWeatherPic){
            $aPathInfo = pathinfo($sWeatherPic);
            if (!empty($aPathInfo) && is_array($aPathInfo)){
                $sWeatherPic = $aPathInfo['filename'] . '.' . $aPathInfo['extension'];
            }
        }
        return $sWeatherPic;
    }
    
    private static function _formatTemperature($sTemperature){
        $aTemperature = explode('&', $sTemperature);
        return $aTemperature[0];
    }

    public static function getWaitingMatches(){
        return self::where('status','=',self::MATCH_WAITING_STATUS_CODE)->get();
    }

}