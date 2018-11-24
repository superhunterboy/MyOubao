<?php
class Math {
   
    static function combin($iBase, $iChoosed){
        if ($iBase < $iChoosed) return 0;
        if ($iBase == $iChoosed) return 1;
        if (($iEqual = $iBase - $iChoosed) < $iChoosed){
            return self::combin($iBase, $iEqual);
        }
        return self::permut($iBase, $iChoosed) / self::factorial($iChoosed);
    }
    
    static function permut($iBase, $iChoosed){
        if ($iBase < $iChoosed) return 0;
        for($i = 0, $p = 1; $i < $iChoosed; $p *= ($iBase - $i++));
        return $p; 
    }
    
    static function factorial($iNum){
        for($f = 1, $i = 2; $i <= $iNum; $f *= $i++);
        return $f;
    }
    /**
     * n个数里取m个数的全组合
     * @param type $arrayN
     * @param type $arrayM
     */
    static function getCombinationToString($arr,$m)
    {
        if(!is_array($arr) || count($arr) < $m) return [];

        $result = array();
        if ($m ==1)
        {
        return $arr;
        }

        if ($m == count($arr))
        {
        $result[] = implode(',' , $arr);
        return $result;
        }

        $temp_firstelement = $arr[0];
        unset($arr[0]);
        $arr = array_values($arr);
        $temp_list1 = self::getCombinationToString($arr, ($m-1));

        foreach ($temp_list1 as $s)
        {
        $s = $temp_firstelement.','.$s;
        $result[] = $s;
        }
        unset($temp_list1);
        $temp_list2 = self::getCombinationToString($arr, $m);
        foreach ($temp_list2 as $s)
        {
        $result[] = $s;
        }
        unset($temp_list2);

        return $result;
    }
    
    static function getCombin4Renxun($n=2,$aT = [1,1,1,1,1]){
        $sum = 0;
        switch($n){
                case 2:
                        for($i=0;$i<5-1;$i++)
                           for($j=$i+1;$j<5;$j++)
                                $sum += $aT[$i]*$aT[$j];
                break;
                case 3:
                        for($i=0;$i<5-2;$i++)
                           for($j=$i+1;$j<5-1;$j++)
                                for($k=$j+1;$k<5;$k++)
                                $sum += $aT[$i]*$aT[$j]*$aT[$k];
                break;
                case 4:
                        for($i=0;$i<5-3;$i++)
                           for($j=$i+1;$j<5-2;$j++)
                                for($k=$j+1;$k<5-1;$k++)
                                 for($l=$k+1;$l<5;$l++)
                                $sum += $aT[$i]*$aT[$j]*$aT[$k]*$aT[$l];
                break;
                    case 5:
                        for($i=0;$i<5-4;$i++)
                           for($j=$i+1;$j<5-3;$j++)
                                for($k=$j+1;$k<5-2;$k++)
                                 for($l=$k+1;$l<5-1;$l++)
                                   for($g=$l+1;$g<5;$g++)
                                $sum += $aT[$i]*$aT[$j]*$aT[$k]*$aT[$l]*$aT[$g];

                break;

        }
        return $sum;
    }
    
    //求集合笛卡尔积
    static function getCartesianProduct($sets = []){
        return self::_getCartesianProduct($sets);
    }
    //求集合笛卡尔积 带key
    static function getCartesianProductWithKey($sets = []){
        return self::_getCartesianProductWithKey($sets);
    }
    
    private static function _getCartesianProduct($sets = [], $i = 0, $result = []){
        $list = [];
        for ($j = 0; $j < count($sets[$i]); ++$j){
            $result[$i] = $sets[$i][$j];
            if ($i == count($sets) - 1){
                $list[] = $result;
            }
            else{
                $tmplist = self::_getCartesianProduct($sets, $i + 1, $result);
                foreach($tmplist as $v){
                    $list[] = $v;
                }
            }
        }
        return $list;
    }
    
    private static function _getCartesianProductWithKey($sets = [], $result = []){
        $list = $arr = [];
        foreach($sets as $sets_key => $arr){
            unset($sets[$sets_key]);
            break;
        }
        foreach($arr as $val){
            $result[$sets_key] = $val;
            if (count($sets) == 0){
                $list[] = $result;
            }else{
                $list = array_merge($list, self::_getCartesianProductWithKey($sets, $result));
//                $list += self::_getCartesianProduct($sets, $result);
            }
        }
        return $list;
    }
    
    /**
     * 四舍六入五成双
     * @param float $num
     * @param int $precision
     * @return float
     */
    public static function roundoff($num,$precision = 0){
        $pow = pow(10,$precision);
        $format_num = $num * $pow * 10;
        $floor_num = floor($format_num);
        //舍去位为5 && 舍去位后无数字 && 舍去位前一位是偶数    =》 不进一
        if(  ($floor_num % 5 == 0) && ($floor_num == $format_num) && (floor($num * $pow) % 2 ==0) ){
            return floor($num * $pow)/$pow;
        }else{
            //四舍五入
            return round($num,$precision);
        }
    }
    
}