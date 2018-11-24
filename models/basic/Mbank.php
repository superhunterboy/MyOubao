<?php

class Mbank extends BaseModel {
//    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'bank_maintenance';
    public static $resourceName = 'Mbank';
//    protected $primaryKey = 'bank_id';
        /**
     * 状态：维护
     */
    const BANK_STATUS_MAINTENANCE = 1;

    /**
     * 状态：不维护
     */
    const BANK_STATUS_NOT_MAINTENANCE = 0;
    
      /**
     * 模式：银行卡转账
     */
    const BANK_MODE_BANK_CARD = 1;

    /**
     * 模式：网银快充
     */
    const BANK_MODE_THIRD_PART = 2;

    /**
     * 模式：兼容所有
     */
    const BANK_MODE_ALL = 3;
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
         'id',
        'bank_id',
//       'name',
        'mode',
        'status',
//        'bank_card_id',
        'description',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id',
        'bank_id',
//        'name',
        'mode',
        'status',
//        'bank_card_id',
        'description',
         'created_at',
        'updated_at',
    ];

/**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
//        'bank_id'   => 'aBankName',
        'mode' => 'mode',
    ];
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'id',
//        'bank_id' => '',
       // 'name' => '',
        'description' => '',
        'mode' => '',
        'status' => 'in:0,1',
//        'bank_card_id' => '',
    ];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';
    
    /**
     * 获取还没有被维护的银行卡
     * @return type
     */
    public static function getAllBankName(){
      $aBankName = Bank::where('mode', self::BANK_MODE_BANK_CARD)->orwhere('mode',self::BANK_MODE_ALL)->get(array('name', 'id'))->toArray();
      $mBanksName = self::get()->toArray();
      $data = array();
      foreach ($aBankName as $k=>$v) {
           $data[$v['id']] = $v['name'];
      }
      return $data;
    }
    
    public static function getAllMode(){
        return $data = array(self::BANK_MODE_BANK_CARD=>'银行卡维护', self::BANK_MODE_THIRD_PART=>'网银快捷支付维护',self::BANK_MODE_ALL=>'银行卡和网银快捷支付维护');
    }
    
    /**
     * 检测维护中的银行卡
     * @return type
     */
    public static function checkMbank($bank_id, $mode){
        $aMbanks = self::where('bank_id', $bank_id)->where('status', self::BANK_STATUS_MAINTENANCE)->wherein('mode', [self::BANK_MODE_ALL, $mode])->get()->toArray();
        if ( isset($aMbanks) && $aMbanks ) {
            return $aMbanks[0]['description'];
        }
        return false;
    }
    
    /**
     * 判断某张银行卡是不是在维护中
     */
    public static function isMaintainBank($bank_id, $mode) {
        if (isset($bank_id) && $bank_id) {
            $data = self::where('bank_id', $bank_id)->where('status', self::BANK_STATUS_MAINTENANCE)->wherein('mode', [self::BANK_MODE_ALL, $mode])->first();
            if (isset($data) && $data) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 判断银行卡id是否在维护
     * @param type $id
     */
    public static function getCheckMbank($id){
        $oMbank = self::where('bank_id', $id)->first();
        if (isset($oMbank) && $oMbank) {
            return $oMbank;
        }
        return false;
    }
    
    /**
     * 判断银行卡是否存在
     * @param type $banak_id
     * @return type
     */
    public static function isMbank($banak_id){
            return Bank::where('id', $banak_id)->first();
    }
}
