<?php

namespace JcModel;
/**
 * 注单模型
 */
class JcLotteries extends \BaseModel {
    public static $resourceName = 'JcLotteries';
    protected $table = 'jc_lotteries';
    
    public static $titleColumn = 'name';
    
    const ERRNO_LOTTERY_IS_NOT_EXISTS = -10401;
    
    public static function getByLotteryKey($sLotteryKey = ''){
        return self::where('identifier', $sLotteryKey)->limit(1)->first();
    }

    protected static function compileListCaheKey($sLocate){
        return 'jc-lottery-list-' . $sLocate;
    }
    
}
