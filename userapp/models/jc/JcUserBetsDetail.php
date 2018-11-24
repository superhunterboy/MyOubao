<?php

namespace JcModel;
/**
 * 注单详细模型
 */
class JcUserBetsDetail extends JcBetsDetail {
    
    public static function getListByBetId($iBetId, $iPageSize = 20, $aColumns = ['*']){
        return self::where('bet_id', $iBetId)
                ->orderby('id', 'asc')
                ->paginate($iPageSize, $aColumns);
    }
}
