<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActiveRedEnvelopeWay extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'active_red_envelope_ways';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'lottery_id',
        'way_id',
    ];
    public static $resourceName = 'ActiveRedEnvelopeWay';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'way_id',
    ];
    public static $titleColumn = 'id';
    public static $rules = [
        'lottery_id' => 'required|numeric',
        'way_id' => 'required|numeric',
    ];
  
    /**
     * way 是否在活动
     *
     * @return bool
     */
    public static function isValidateWay($lottery_id,$way_id) {
       return self::where('lottery_id', '=', $lottery_id)
                        ->where('way_id', '=', $way_id)
                        ->first();
    }
    
    
    
    /**
     * 批量保存
     * @param type $aData
     * @return type
     */
  public function saveAll($aData) {
        return DB::table($this->table)->insert($aData);
    }


}
