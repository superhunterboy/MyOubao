<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActivityHand extends BaseModel {

    /**
     
     * @var string
     */
    protected $table = 'activity_md_settype';
//    public $orderColumns = ['status' => 'desc', 'id' => 'desc'];

    /**
     * 软删除
     * @var boolean
     */
//    protected $softDelete = false;
    protected $fillable = [
        'id',
        'name',
        'money',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'ActivityHand';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
         'id',
          'name',
        'money',
        'created_at',
        'updated_at',
    ];
    public static $titleColumn = 'id';
    public static $rules = [
        'name' => 'required',
        'money' => 'required',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
//    public static $htmlSelectColumns = [
//        'status' => 'aStatus',
//    ];

    public static function getAllHand() {
        return ActivityHand::all();      //取出所有的手柄按钮
    }

    public static function getUserAvaiAble($sumLs) {
        if (empty($sumLs)) {
            return false;
        }
        return ActivityHand::where('money', "<=", $sumLs)->get();
    }

      public static function getHandByMoney($money){
          if(empty($money)){
              return false;
          }
          return ActivityHand::where('money', $money)->first();
      }
}
