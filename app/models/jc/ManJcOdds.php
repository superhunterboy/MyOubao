<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-10
 * Time: 下午2:50
 */

namespace JcModel;


class ManJcOdds extends JcOdds
{
    public static function getOddsByMatchId($matchId){
        $aOddses = self::where('match_id','=',$matchId)->orderBy('method_id','asc')->orderBy('code','desc')->get();
        return $aOddses;
    }
    
    public function getMethodAttribute(){
        $method_id = self::getAttribute('method_id');
        $oMethod = JcMethod::find($method_id);
        return $oMethod->name;
    }
    
    public static function saveOdds($oMatche,$iLotteryId){
        $aCrs = $oMatche->crs ? json_decode($oMatche->crs,true) : self::getInitOdds('crs');
        $aHad = $oMatche->had ? json_decode($oMatche->had,true) : self::getInitOdds('had');
        $aHafu = $oMatche->hafu ? json_decode($oMatche->hafu,true) : self::getInitOdds('hafu');
        $aHhad = $oMatche->hhad ? json_decode($oMatche->hhad,true) : self::getInitOdds('hhad');
        $aTtg = $oMatche->ttg ? json_decode($oMatche->ttg,true) : self::getInitOdds('ttg');

        $aMaps = [
            'CRS' => JcMethod::STRING_IDENTIFIER_CORRECT_SCORE,
            'HAD' => JcMethod::STRING_IDENTIFIER_WIN,
            'HAFU' => JcMethod::STRING_IDENTIFIER_HAFU,
            'HHAD' => JcMethod::STRING_IDENTIFIER_HANDICAP_WIN,
            'TTG' => JcMethod::STRING_IDENTIFIER_TOTAL_GOALS,
        ];

        $aOdds = ['HAD'=>$aHad, 'HHAD'=>$aHhad, 'HAFU'=>$aHafu, 'CRS'=>$aCrs, 'TTG'=>$aTtg ];

        $aOddsData['match_id'] = $oMatche->match_id;

        $iCount = self::where('match_id','=',$oMatche->match_id)->count();
        if($iCount) return true;
        $aFilter = ['id','p_code','o_type','p_id','p_status','single','allup','goalline','fixedodds','cbt','int','vbt','h_trend','a_trend','d_trend','l_trend','updated_at'];
        $isHhad = 0;
        $bSucc = true;
        foreach($aOdds as $key => $value){
            $oMethod = JcMethod::getMethodByIdentifier($iLotteryId,$aMaps[$key]);
            $aOddsData['method_id'] = $oMethod->id;
            $aOddsData['lottery_id'] = $iLotteryId;
            if($key=='HHAD') $isHhad = 1;

            if(is_array($value) && !empty($value)) {
                foreach($value as $k => $v){
                    if(in_array($k,$aFilter)) continue;
                    $aOddsData['odds'] = floatval($v);
                    $aOddsData['code'] = self::getOddsCode($k,$isHhad);
                    $oOdds = self::create($aOddsData);
                    $bSucc = $oOdds->id > 0;
                    if (!$bSucc){
                        break;
                    }
                }
            }
        }
        return $bSucc;
    }

    public static function getOddsCode($k,$isHhad){
        $crs_pattern = '/^([-0])([0-5])([-0])([adh0-5])$/';
        $had_hhad_pattern = '/^[adh]$/';
        $hafu_pattern = '/^([adh])([adh])$/';
        $ttg_pattern = '/^s([0-7])$/';
        if(preg_match($crs_pattern,$k,$matches)){
            switch($matches[4]){
                case 'a' :
                    $code = '09';
                    break;
                case 'd' :
                    $code = '99';
                    break;
                case 'h' :
                    $code = '90';
                    break;
                default :
                    $code = $matches[2].$matches[4];
            }
        }elseif(preg_match($had_hhad_pattern,$k,$matches)){
            $code = self::strReplaceAssoc($k);
            if($isHhad) $code = '1000'.$code;
        }elseif(preg_match($hafu_pattern,$k,$matches)){
            $code = '10'.self::strReplaceAssoc($k);
        }elseif(preg_match($ttg_pattern,$k,$matches)){
            $code = '10'.$matches[1];
        }else{
            return null;
        }
        return $code;
    }
    public static function strReplaceAssoc( $subject, array $replace = ['h' => '3', 'd' => '1', 'a' => '0']) {
        return str_replace(array_keys($replace), array_values($replace), $subject);
    }

    private static function getInitOdds($type){
        $aOdds = [];
        switch($type){
            case 'crs' :
                $aOdds = ['-1-a'=>0,'-1-d'=>0,'-1-h'=>0];
                for($i = 0; $i <= 5; $i++){
                    for($j = 0; $j <= 5; $j++){
                        $aOdds['0'.$i.'0'.$j] = 0;
                    }
                }
                break;
            case 'had' :
            case 'hhad' :
                $aOdds = ['a'=>0,'d'=>0,'h'=>0];
                break;
            case 'hafu' :
                $aHad = ['a','d','h'];
                foreach ($aHad as $a) {
                    foreach ($aHad as $h) {
                        $aOdds[$a.$h] = 0;
                    }
                }
                break;
            case 'ttg' :
                for($i=0; $i <= 7; $i++){
                    $aOdds['s'.$i] = 0;
                }
            break;
        }

        return $aOdds;
    }
}