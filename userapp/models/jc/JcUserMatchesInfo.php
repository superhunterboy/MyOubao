<?php

namespace JcModel;

/**
 * 赛事数据模型
 */
class JcUserMatchesInfo extends JcMatchesInfo {
    
    protected function getWeatherPicAttribute($sWeatherPic){
        $aReplaceList = [
          'duoyun.gif' => 'cloudy.png',
          'qing.gif' => 'sunny.png',
          'lei.gif' => 'thunder.png',
          'wu.gif' => 'fog.png',
          'xiaoyu.gif' => 'rain.png',
          'zhongyu.gif' => 'heavyrain.png',
        ];
        if (isset($aReplaceList[$sWeatherPic])){
            return $aReplaceList[$sWeatherPic];
        }
        return $sWeatherPic;
    }


    protected function getScoreAttribute($sScore) {
        if ($this->status != self::MATCH_END_STATUS_CODE){
            return null;
        }
        return $sScore;
    }
    
    protected function getHalfScoreAttribute($sScore) {
        if ($this->status != self::MATCH_END_STATUS_CODE){
            return null;
        }
        return $sScore;
    }
    
    public static function getResultListByBetDate($dBetDate = null,$iPageSize = 50, $aColumns = ['*']){
        return self::whereIn('status', [self::MATCH_END_STATUS_CODE, self::MATCH_CANCEL_STATUS_CODE])
                ->where('bet_date', $dBetDate)
                ->orderby('match_id', 'desc')
                ->paginate($iPageSize, $aColumns);
    }
    
     public static function getTotalSaleMatch(){
        return self::where('status', self::MATCH_SELLING_STATUS_CODE)->count();
    }
}
