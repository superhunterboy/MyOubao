<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActiveRedEnvelope extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'active_red_envelopes';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'start_time',
        'end_time',
        'status',
        'amount',
        'balance',
    ];
    public static $resourceName = 'ActiveRedEnvelope';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'start_time',
        'end_time',
        'status',
        'amount',
        'balance',
    ];
    public static $titleColumn = 'id';
    public static $rules = [
        'amount' => 'required|numeric',
        'start_time' => 'required|date',
        'end_time' => 'required|date',
        'status' => 'integer',
    ];
    
   /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];
    public static $aStatus=[
        '0'=>'未处理',
        '1'=>'正在活动中',
        '2'=>'已经结束',
    ];
    /**
     * 活动是否有效
     *
     * @return bool
     */
    public function isValidateActivity() {
        $now = date('Y-m-d H:i:s');
        if ($this->start_time <= $now && $this->end_time >= $now) {
            return true;
        }
        return false;
    }
  function saveAll($aData) {
        return DB::table($this->table)->insert($aData);
    }
    
    /***
     * 检查开始时间和结束时间是否已经存在不能生成
     */
    public function checkStartDate($date){
        if(self::where('start_time','<=',$date)->where('end_time','>=',$date)->first()){
            return false;
        }
        return true;
    }/***
     * 检查开始时间和结束时间是否已经存在不能生成
     */
    public function checkEndDate($date){
        if(self::where('end_time','<=',$date)->where('end_time','>=',$date)->first()){
            return false;
        }
        return true;
    }

        /**
     * 获得有效的活动
     *
     * @return mixed
     */
    public static function getCurrentRedEnvelope() {
        $now = date('Y-m-d H:i:s');
        //缓存5分钟
        return self::where('start_time', '<=', $now)
                        ->where('end_time', '>=', $now)
                        ->where('status', '=', 0)
                        ->first();
    }
    public static function getNextRedEnvelope(){
        $now = date('Y-m-d H:i:s');
        return self::where('start_time', '>=', $now)
//                        ->where('end_time', '>=', $now)
                        ->where('status', '=', 0)
                        ->first();
    }
}
