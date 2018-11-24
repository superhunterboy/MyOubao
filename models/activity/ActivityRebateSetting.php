<?php

/**
 * Class Activitys - 活动表
 *
 */
class ActivityRebateSetting extends BaseModel {

    /**
     * 活动状态：开启
     */
    const STATUS_OPEN = 1;

    const OPERATOR_NUMBER = '=';
    const OPERATOR_PERCENTAGE = '%';
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_rebate_settings';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'left_amount',
        'right_amount',
        'operator',
        'rebate_value',
        'status',
    ];
    public static $resourceName = 'ActivityRebateSetting';

    public static $aRebateFeeRateSet = [0.1, 0.15, 0.2, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5];
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'left_amount',
        'right_amount',
        'rebate_value',
        'operator',
        'status',
    ];
//    public static $titleColumn = 'name';
//    public static $ignoreColumnsInEdit = ['admin_id', 'admin_name'];
    public static $rules = [
        'left_amount' =>  'required',
        'right_amount' => 'required',
        'rebate_value'=>'required',
        'operator'=>'required',
        'status' => 'in:0,1',
    ];
//'max:100,operator,%'
    public static $htmlSelectColumns=[
        'operator'=>"aOperators",

    ];
    public static $aOperators=[
        self::OPERATOR_NUMBER=>'百分比',
        self::OPERATOR_PERCENTAGE=>'固定值',
    ];
    public static $customMessages = [
        'left_amount.required' => '上限值必填',
        'right_amount.required' => '下限值必填',
        'left_amount.numeric' => '上限值必须是数字，请重新填写！',
        'right_amount.numeric' => '下限值必须是数字，请重新填写！',
        'rebate_value.required' => '返奖值必填',
        'rebate_value.numeric' => '返奖值必须是数字，请重新填写！',
        'operator.required' => '计算方式必填！',
        'status.in' => '状态错误， 请重新选择！',
    ];
    public function getUserRebate($direct_turnover){
        $rules=self::all();
        foreach($rules as $rule){
            if($rule->left_amount <= $direct_turnover&& $direct_turnover<$rule->right_amount){
                $rebate=$rule->operator==self::OPERATOR_NUMBER?$rule->rebate_value:$direct_turnover*$rule->rebate_value/100;
                return $rebate;
            }
        }
        return false;
    }



}
