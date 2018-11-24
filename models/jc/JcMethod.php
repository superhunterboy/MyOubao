<?php

namespace JcModel;

/**
 * 玩法模型
 */
class JcMethod extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcMethod';
    protected $table = 'jc_methods';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'lottery_id',
        'name',
        'short_name',
        'identifier',
        'function',
        'description',
        'valid_nums',
        'max_count',
    ];
    protected $fillable = [
        'id',
        'lottery_id',
        'name',
        'short_name',
        'identifier',
        'function',
        'description',
        'valid_nums',
        'max_count',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [
        'lottery_id' => 'validLotteries'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id' => 'required|integer',
        'name' => 'required',
        'short_name' => 'required',
        'identifier' => 'required',
        'function' => '',
        'description' => '',
        'valid_nums' => '',
        'max_count' => 'integer',
    ];

    //public $timestamps = false;

    const STRING_IDENTIFIER_WIN = 'win';
    const STRING_IDENTIFIER_CORRECT_SCORE = 'correctScore';
    const STRING_IDENTIFIER_TOTAL_GOALS = 'totalGoals';
    const STRING_IDENTIFIER_HAFU = 'haFu';
    const STRING_IDENTIFIER_HANDICAP_WIN = 'handicapWin';

    public static function validCode($iLotteryId = 0, $sCode = '') {
        $oMethod = self::getMethodByCode($iLotteryId, $sCode);
        return $oMethod ? true : false;
    }

    public static function getMethodByCode($iLotteryId, $sCode = '') {
        $sIdentifier = self::getIdentifierByCode($sCode);
        if (isset($sIdentifier)) {
            $oMethod = self::getMethodByIdentifier($iLotteryId, $sIdentifier);
            if ($oMethod) {
                $aCodeList = explode(',', $oMethod->valid_nums);
                if (in_array($sCode, $aCodeList)) {
                    return $oMethod;
                }
            }
        }
        return false;
    }

    public static function getIdentifierByCode($sCode = '') {
        $sStrLen = strlen($sCode);
        switch ($sStrLen) {
            case 1: //胜平负
                return self::STRING_IDENTIFIER_WIN;
            case 2: //比分
                return self::STRING_IDENTIFIER_CORRECT_SCORE;
            case 3: //总进球
                return self::STRING_IDENTIFIER_TOTAL_GOALS;
            case 4: //半全场
                return self::STRING_IDENTIFIER_HAFU;
            case 5: //让球胜平负
                return self::STRING_IDENTIFIER_HANDICAP_WIN;
            default :
                return false;
        }
    }

    public static function getAllByLotteryId($iLotteryId = 0) {
        static $aAllMethod = [];
        if (!$aAllMethod) {
            $oQuery = self::where('lottery_id', '=', $iLotteryId)->get();
            foreach ($oQuery as $row) {
                $aAllMethod[$row->id] = $row;
            }
        }
        return $aAllMethod;
    }

    public static function getMethodByIdentifier($iLotteryId = 0, $sIdentifier = '') {
        static $aMethod = [];
        if (isset($aMethod[$iLotteryId][$sIdentifier])) {
            $oMethod = $aMethod[$iLotteryId][$sIdentifier];
            $oNewMethod = new JcMethod($oMethod->getAttributes());
            return $oNewMethod;
        } else {
            $aAllMethod = self::getAllByLotteryId($iLotteryId);
            foreach ($aAllMethod as $oMethod) {
                if ($oMethod->identifier == $sIdentifier) {
                    $aMethod[$iLotteryId][$sIdentifier] = $oMethod;
                    return $aMethod[$iLotteryId][$sIdentifier];
                }
            }
        }
        return [];
    }

    public static function getMaxCountByCode($iLotteryId = 0, $sCode = '') {
        $oMethod = self::getMethodByCode($iLotteryId, $sCode);
        return $oMethod ? $oMethod->max_count : 0;
    }

    private static function _getCode($iHomeScore, $iAwayScore, $iHandicap = 0) {
        $iNewHomeScore = $iHomeScore + $iHandicap;
        if ($iNewHomeScore > $iAwayScore) {
            return 3;
        }
        if ($iNewHomeScore == $iAwayScore) {
            return 1;
        }
        if ($iNewHomeScore < $iAwayScore) {
            return 0;
        }
    }

    private static function _formatSocre($sScore = '0:0') {
        $aScore = explode(':', $sScore);
        if (count($aScore) != 2) {
            return false;
        }
        if (preg_match('/^d+$/', $aScore[0]) || preg_match('/^d+$/', $aScore[1])) {
            return false;
        }
        return $aScore;
    }

    public function getResultTitle($oMatchInfo) {
        if($oMatchInfo->status == \JcModel\JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
            return '已取消';
        }
        $sResult = $this->getResult($oMatchInfo);
        if (isset($sResult)) {
            return $this->formatCodeName($sResult);
        }
    }

    public function getResult($oMatchInfo) {
        if($oMatchInfo->status != \JcModel\JcMatchesInfo::MATCH_END_STATUS_CODE){
            return null;
        }
        $aScore = self::_formatSocre($oMatchInfo->score);
        $aHafuScore = self::_formatSocre($oMatchInfo->half_score);
        $iHandicap = $oMatchInfo->handicap;
        if (empty($aHafuScore) || empty($aScore)) {
            return null;
        }
        $sFunc = 'getCode' . ucfirst($this->identifier);
        return self::$sFunc($aScore, $aHafuScore, $iHandicap);
    }

    public function checkResult($oMatchInfo, $sCode) {
        if (isset($sCode)){
            $sResult = $this->getResult($oMatchInfo);
            return $sResult == $sCode;
        }
    }

    public static function getCodeWin($aScore) {
        return self::_getCode($aScore[0], $aScore[1]);
    }

    public static function getCodeHandicapWin($aScore, $aHafuScore, $iHandicap) {
        return '1000' . self::_getCode($aScore[0], $aScore[1], $iHandicap);
    }

    public static function getCodeCorrectScore($aScore) {
        if ($aScore[0] > $aScore[1] && ($aScore[0] > 5 || $aScore[1] > 2)) {
            return '90';
        } else if ($aScore[0] < $aScore[1] && ($aScore[0] > 2 || $aScore[1] > 5)) {
            return '09';
        } else if ($aScore[0] == $aScore[1] && $aScore[0] > 3) {
            return '99';
        }
        return $aScore[0] . $aScore[1];
    }

    public static function getCodeTotalGoals($aScore) {
        $iScore = min($aScore[0] + $aScore[1], 7);
        return '10' . ($iScore);
    }

    public static function getCodeHafu($aScore, $aHafuScore) {
        return '10' . self::_getCode($aHafuScore[0], $aHafuScore[1]) . self::_getCode($aScore[0], $aScore[1]);
    }

    public static function getCodeName($iLotteryId, $sCode) {
        $oMethod = self::getMethodByCode($iLotteryId, $sCode);
        if (empty($oMethod)) {
            return '-';
        }
        return $oMethod->formatCodeName($sCode);
    }

    public static function getCodeFullName($iLotteryId, $sCode) {
        $sName = self::getCodeName($iLotteryId, $sCode);
        $sIentifier = self::getIdentifierByCode($sCode);
        if ($sIentifier == self::STRING_IDENTIFIER_HANDICAP_WIN) {
            $sName = '让' . $sName;
        }
        return $sName;
    }

    public function formatCodeName($sCode) {
        if (isset($sCode)){
            $sFunc = 'getName' . ucfirst($this->identifier);
            return self::$sFunc($sCode);
        }
    }

    private static function _formatNameByCode($sCode) {
        $aName = [
            '3' => '胜',
            '1' => '平',
            '0' => '负',
        ];
        return isset($aName[$sCode]) ? $aName[$sCode] : '';
    }

    public static function getNameWin($sCode) {//3,1,0
        return self::_formatNameByCode($sCode);
    }

    public static function getNameCorrectScore($sCode) {//10,20,21...99
        $aName = [
            '90' => '胜其他',
            '99' => '平其他',
            '09' => '负其他',
        ];
        if (isset($aName[$sCode])) {
            return $aName[$sCode];
        }
        $aCode = str_split($sCode);
        return implode(':', $aCode);
    }

    public static function getNameTotalGoals($sCode) {//101,102...107
        $sFormatCode = substr($sCode, 2);
        if ($sFormatCode >= 7) {
            return '7+';
        }
        return $sFormatCode;
    }

    public static function getNameHaFu($sCode) {//1033,1031,1030,1013,1011,1010,1003,1001,1000
        $sFormatCode = substr($sCode, 2);
        $aCode = str_split($sFormatCode);
        return self::_formatNameByCode($aCode[0]) . self::_formatNameByCode($aCode[1]);
    }

    public static function getNameHandicapWin($sCode) {//10003,10001,10000
        $sFormatCode = substr($sCode, 4);
        return self::_formatNameByCode($sFormatCode);
    }

}
