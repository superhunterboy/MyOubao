<?php
/*
 * 与日期有关的操作类
 * 
 * @version 1.0.0
 * @author Frank
 * @date 2013-08-07
 */
class Date {
    /** 
     * 星期名字数组，存储英语名
     * @var type array
     */
    public $weeks = [
        1 => 'Monday', 
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        0 => 'Sunday'
    ];
    /**
     * 一星期中所有天的标识数字
     * @var type integer
     */
    public $allDays = 127;
    
    /**
     * 
     * @param integer $iDaysNumber  标识哪些天的数字
     * @param bool $bReturnName     是否返回名字，为假时返回的数组中的值是数字，星期天为0；为真时，数组中的值是英语的名称
     * @return array &
     */
    function & checkWeekDays($iDaysNumber,$bReturnName = false){
        for($i = 0;$i < 7;$i++){
            $iBase = pow(2, $i);
            if (($iDaysNumber & $iBase) == $iBase){
                $aWeek[] = $bReturnName ? $this->weeks[$i] : $i;
            }
        }
        return $aWeek;
    }

    /**
     * 检测是否是合法日期
     * 标准日期格式:2009-08-12
     * 
     * @param string $sDate 	需要检测的日期
     * @param return boolean	返回是否合法
     */
    function isLegalDate($sDate){
        if(empty($sDate)) return false;
        list($sYear,$sMonth,$sDay) = explode('-',$sDate);
        if(!is_numeric($sYear) || !is_numeric($sMonth) || !is_numeric($sDay)) return false;
        if(strlen($sYear) != 4 || strlen($sMonth) != 2 || strlen($sDay) != 2) return false;
        return checkdate($sMonth,$sDay,$sYear);
    }
    
    function makeDaysNumber($aSelected){
        $iNumber = 0;
        foreach($aSelected as $iWeek){
            $iNumber += pow(2, $iWeek);
        }
        return $iNumber;
    }
    
    static function dateAdd($sDate,$iStep = 1,$sLong = 'day'){
        $iStamp = strtotime($sDate);
        list($year,$month,$day,$hour,$minute,$second) = explode('-',date('Y-m-d-H-i-s', $iStamp));
        $$sLong += $iStep;
        $sFormat = $hour > 0 ? 'Y-m-d H:i:s' : 'Y-m-d';
        return date($sFormat,  mktime($hour, $minute, $second, $month, $day, $year));
    }
    
    static function getMonth($iYear, $iMonth, $sSplitChar = '-'){
        $iMonth = str_pad($iMonth,2,'0',STR_PAD_LEFT);
        return $iYear . $sSplitChar . $iMonth;
    }

}