<?php

/**
 * 游戏管理类
 *
 */
class ManLottery extends Lottery {

    const LOTTERY_MMC = 18;

    protected static $cacheUseParentClass = true;
    public static $columnForList = [
        'name',
        'type',
        'lotto_type',
        'identifier',
        'days',
        'begin_time',
        'end_time',
        'daily_issue_count',
        'trace_issue_count',
        'status',
        'sequence',
    ];
    protected $fillable = [
        'series_id',
        'name',
        'type',
        'lotto_type',
        'high_frequency',
        'sort_winning_number',
        'valid_nums',
        'buy_length',
        'wn_length',
        'identifier',
        'days',
        'issue_over_midnight',
        'issue_format',
        'begin_time',
        'end_time',
        'status',
        'need_draw',
        'series_ways',
        'sequence',
        'daily_issue_count',
        'trace_issue_count'
    ];

    private function _compileDigitalNumber9() {
        $fp = fopen("/dev/urandom", 'rb');
        if ($fp === false) {
            return '';
//            return $this->_compileDigitalNumber1;
        }
        do {
            $r = fgets($fp, 64);
        } while (strlen($r) < 4);
        fclose($fp);
        if ($r === false) {
            return '';
//            return $this->_compileDigitalNumber1;
        }
        //$a = unpack("cchars/nint",$r);
        $a = unpack("N", $r);
//        pr($a);
        $s = $a[1];
        $iLength = strlen($s);
        if ($iLength < $this->wn_length) {
            for ($i = 0; $i < $this->wn_length - $iLength; $i++) {
                $s .= mt_rand(0, 9);
            }
        }
        if ($iLength > $this->wn_length) {
            $s = substr($s, -$this->wn_length);
        }
//        pr($s);
//        $s = trim($s);
        return $s;
    }

    private function _compileDigitalNumberK3() {
        $fp = fopen("/dev/urandom", 'rb');
        if ($fp === false) {
            return '';
//            return $this->_compileDigitalNumber1;
        }
        do {
            $r = fgets($fp, 64);
        } while (strlen($r) < 4);
        fclose($fp);
        if ($r === false) {
            return '';
//            return $this->_compileDigitalNumber1;
        }
        //$a = unpack("cchars/nint",$r);
        $a = unpack("N", $r);
        $s = $a[1];
        $iLength = strlen($s);
        if ($iLength < $this->wn_length) {
            for ($i = 0; $i < $this->wn_length - $iLength; $i++) {
                $s .= mt_rand(1, 6);
            }
        }
        if ($iLength > $this->wn_length) {
            $s = substr($s, -$this->wn_length);
        }
//        pr($s);
//        $s = trim($s);
        return $s;
    }

    public function compileWinningNumber() {
        switch ($this->type) {
            case self::LOTTERY_TYPE_DIGITAL:
                $aBaseNumbers = explode(',', $this->valid_nums);
                for ($i = 0, $iShuffleTimes = mt_rand(1, 9); $i < $iShuffleTimes; shuffle($aBaseNumbers), $i++)
                    ;
                $aNumbers = [];
                for ($i = 0, $iMaxIndex = count($aBaseNumbers) - 1; $i < $this->wn_length; $i++) {
                    $iIndex = mt_rand(0, $iMaxIndex);
                    $aNumbers[] = $aBaseNumbers[$iIndex];
                }
                if (in_array($this->series_id, [Series::LOTTERY_SERIES_K3, Series::LOTTERY_SERIES_K3_DICE])) {
                    return implode('', $aNumbers);
                }
                if (in_array($this->series_id, [Series::LOTTERY_SERIES_LHD])) {
                    $aValidBalls = $this->getValidNums($this->valid_nums, self::LOTTERY_TYPE_DIGITAL);
                    return $this->compileSingleLottoWinningNumber($aValidBalls);
                }
                if (in_array($this->series_id, [Series::LOTTERY_SERIES_BJL])) {
                    $aValidBalls = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52];
                    return $this->compileSingleLottoWinningNumber($aValidBalls);
                }
                if (in_array($this->series_id, [Series::LOTTERY_SERIES_PK10])) {
                    $aValidBalls = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                    return $this->compileSingleLottoWinningNumber($aValidBalls);
                }
                return $this->_compileDigitalNumber9();
//                return $number = str_pad(mt_rand(0, pow(10, $this->wn_length) - 1), $this->wn_length, '0', STR_PAD_LEFT);
                break;
            case self::LOTTERY_TYPE_LOTTO:
                switch ($this->lotto_type) {
                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
                        $aValidBalls = explode(',', $this->valid_nums);
                        return $this->compileSingleLottoWinningNumber($aValidBalls);
                        break;
                }
                break;
            case self::LOTTERY_TYPE_GAME:
                $aValidBalls = explode(',', $this->valid_nums);
                return $this->compileSingleLottoWinningNumber($aValidBalls);
                break;
        }
    }

    /**
     * 格式化中奖号码，返回规范化的中奖号码
     *
     * @param string $sWinningNumber
     * @param $sSplitChar 双区乐透类型时的区分隔符，非双区乐透型时无效
     * @return string
     */
    public function formatWinningNumber($sWinningNumber, $sSplitChar = '+') {
        $this->recursive = -1;
        switch ($this->type) {
            case self::LOTTERY_TYPE_DIGITAL:    // num type
                $pattern = '/[^\d]/';
                $sWinningNumber = preg_replace($pattern, '', $sWinningNumber);
                if ($this->sort_winning_number) {
                    $a = str_split($sWinningNumber, 1);
                    sort($a);
                    $sWinningNumber = implode($a);
                }
                return $sWinningNumber;
                break;
            case self::LOTTERY_TYPE_LOTTO:
                switch ($this->lotto_type) {
                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
                        return $this->_formatWinningNumberForSingleLotto($sWinningNumber, $this->sort_winning_number);
                        break;
                    case self::LOTTERY_TYPE_LOTTO_DOUBLE:
                        $aAreas = explode($sSplitChar, $sWinningNumber);
                        $aBonusCode = [];
                        foreach ($aAreas as $iKey => $sBonusCodeForArea) {
                            $aBonusCode[$iKey] = $this->_formatWinningNumberForSingleLotto($sBonusCodeForArea, $this->sort_winning_number);
                        }
                        return implode(self::WINNING_SPLIT_FOR_DOUBLE_LOTTO, $aBonusCode);
                    default :       // 尚不支持多区乐透型
                        return false;
                }
                break;
            default:  // 尚不支持其他类型
                return false;
        }
    }

    /**
     * 格式化单区乐透型的号码
     * @param string $sWinningNumber
     * @param bool $bSort
     * @return string
     */
    private function _formatWinningNumberForSingleLotto($sWinningNumber, $bSort) {
        $sWinningNumber = preg_replace('/[^\d]/', ' ', $sWinningNumber);
        $aNums = array_unique(explode(' ', $sWinningNumber));
        $aNums = array_diff($aNums, ['']);
        !$bSort or sort($aNums);
        $aBalls = [];
        foreach ($aNums as $iNum) {
            $aBalls[] = $this->formatBall($iNum, self::LOTTERY_TYPE_LOTTO, self::LOTTERY_TYPE_LOTTO_SINGLE);
        }
        return implode(' ', $aBalls);
    }

    private function compileSingleLottoWinningNumber($aValidBalls) {
        $aBalls = [];
        for ($i = 0, $iShuffleTimes = mt_rand(1, 9); $i < $iShuffleTimes; shuffle($aValidBalls), $i++)
            ;
        for ($i = 0, $iMaxIndex = count($aValidBalls) - 1; $i < $this->wn_length; $i++) {
            do {
                $iIndex = mt_rand(0, $iMaxIndex);
                $iBall = $aValidBalls[$iIndex];
            } while (in_array($iBall, $aBalls));
            $aBalls[] = $iBall;
        }
        $aBalls = array_map(function($j) {
            return str_pad($j, 2, 0, STR_PAD_LEFT);
        }, $aBalls);
        if($this->series_id==Series::LOTTERY_SERIES_PK10) {
            return implode(',', $aBalls);
        } else {
            return implode(Config::get('bet.split_char_lotto_in_area'), $aBalls);
        }
    }

    protected function setValidNumsAttribute($sValidNumber) {
        $a = explode(',', $sValidNumber);
        $aValidNumbers = [];
        foreach ($a as $sNum) {
            $b = explode('-', $sNum);
            for ($i = 0; $i < count($b); $aValidNumbers[] = $b[$i++])
                ;
        }
        sort($aValidNumbers);
        $this->attributes['valid_nums'] = implode(',', $aValidNumbers);
    }

    /**
     * 检查是否存在相同的游戏名称
     *
     * @return boolean
     */
    protected function _existName() {
        
    }

    /**
     * 检查是否存在相同的游戏代码
     *
     * @return boolean
     */
    protected function _existCode() {
        
    }

    /**
     * 检验号码是否正确, move in from ec,need modify
     *
     * @param string $sWinningNumber
     * @return boolean
     */
//    public function checkWinningNumber($sWinningNumber) {
//        switch ($this->type) {
//            case self::LOTTERY_TYPE_DIGITAL:    // num type
//                $sPattern = '/^[' . str_replace(',', '', $this->valid_nums) . ']{' . ($this->wn_length) . '}$/uis';
//                pr($sPattern);exit;
//                return preg_match($sPattern, $sWinningNumber);
//                break;
//            case self::LOTTERY_TYPE_LOTTO:
//                switch ($this->lotto_type) {
//                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
//                        $aValidBalls = $this->getValidNums($this->valid_nums, $this->type, $this->lotto_type);
//                        return $this->_checkWinningNumberForSingleLotto($sWinningNumber, $aValidBalls, $this->wn_length);
//                        break;
//                    case self::LOTTERY_TYPE_LOTTO_DOUBLE:
//                        $aBonusCode = explode(self::BONUS_CODE_SPLIT_CHAR_FOR_DOUBLE_LOTTO, $sWinningNumber);
//                        if (count($aBonusCode) != 2) {
//                            return false;
//                        }
//                        $aValidBalls = $this->getValidNums($this->valid_nums, $this->type, $this->lotto_type);
//                        $aCodeLen = explode('|', $this->wn_length);
//                        $bValid = true;
//                        foreach ($aBonusCode as $iArea => $sBonusCodeOfArea) {
//                            if (!$bValid = $this->_checkWinningNumberForSingleLotto($sBonusCodeOfArea, $aValidBalls[$iArea], $aCodeLen[$iArea])) {
//                                break;
//                            }
//                        }
//                        return $bValid;
//                        break;
//                    default :       // 尚不支持多区乐透型
//                        return false;
//                }
//                break;
//            default:  // 尚不支持其他类型
//                return false;
//        }
//    }

    /**
     * 检验号码是否正确,用于单区乐透型
     *
     * @param string $sWinningNumber
     * @param array $aValidBalls
     * @param int $iCodeLen
     * @return bool
     */
    private function _checkWinningNumberForSingleLotto($sWinningNumber, & $aValidBalls, $iCodeLen) {
                if($this->series_id==Series::LOTTERY_SERIES_PK10) {
            return implode(',', $aBalls);
        } else {
            return implode(Config::get('bet.split_char_lotto_in_area'), $aBalls);
        }
        $aBalls = array_unique(explode(' ', $sWinningNumber));
        foreach ($aBalls as $i => $iBall) {
            $iBall = $this->formatBall($iBall, self::LOTTERY_TYPE_LOTTO, self::LOTTERY_TYPE_LOTTO_SINGLE);
            if (!in_array($iBall, $aValidBalls)) {
                return false;
            }
            $aBalls[$i] = $iBall;
        }
        $aDiff = array_diff($aBalls, $aValidBalls);
//        $sWinningNumber = implode(' ',$aBalls);
        return empty($aDiff) && count($aBalls) == $iCodeLen;
    }

    /**
     * 返回给定的奖期号的下一周期的开始日期
     * @param bool $bOverMidNight
     * @param string $sLastIssue
     * @param int $iLastEndTime
     * @return type
     */
    function getNextDay($iLastEndTime) {
        $bOverMidNight = $this->issue_over_midnight;
        if (!$bOverMidNight && date('H:i:s', $iLastEndTime) == '00:00:00') {
            $bOverMidNight = TRUE;
        }
        $iNextDay = $bOverMidNight ? $iLastEndTime : $iLastEndTime + 3600 * 24;
        return date('Y-m-d', $iNextDay);
    }

    /**
     * 判断期号是否是单调增
     * @param string $sIssueRule
     * @return bool
     */
    public function isAccumulating() {
        return (strpos($this->issue_format, 'T') !== false || strpos($this->issue_format, 'C') !== false);
    }

    /**
     * 返回期号规则
     *
     * @return string
     */
    public function getIssueFormat() {
        return $this->issue_format;
    }

    /**
     * 设置开售停售状态
     *
     * @return type
     */
    public function setOpenClose() {
        if ($this->open == self::STATUS_NOT_AVAILABLE) {
            $this->open = self::STATUS_AVAILABLE_FOR_NORMAL_USER;
        } else {
            $this->open = self::STATUS_NOT_AVAILABLE;
        }
        return $this->save();
    }

}
