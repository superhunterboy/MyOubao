<?php

/**
 * Class ActivityPrize - 活动奖品表
 *
 *
 */
class ActivityPrize extends BaseModel implements FactoryClassInterface {

    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    const PRIZE_TOP_AGENT_DAILY_SALARY = 24;
    const PRIZE_DAILY_BET = 28;
    const PRIZE_DAILY_DEPOSIT = 29;
    const PRIZE_NEWER_DEPOSIT = 30;
    const PRIZE_DAILY_SIGN = 31;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_prizes';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'task_id',
        'name',
        'prize_class_id',
        'prize_class_name',
        'activity_id',
        'activity_name',
        'value',
        'params',
        'need_review',
        'need_get',
        'prize_category',
        'prize_desc',
    ];
    public static $resourceName = 'ActivityPrize';
    public static $titleColumn = 'name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'prize_class_name',
        'task_name',
        'value',
        'params',
        'need_review',
        'need_get',
    ];
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'prize_class_id' => 'aPrizeClasses',
        'task_id' => 'aTasks',
        'prize_category' => 'aCategories',
    ];
    public static $ignoreColumnsInEdit = [];
    public static $rules = [
        'name' => 'required|between:0,50',
//        'task_id' => 'required|integer',
//        'activity_name' => 'between:0,50',
        'prize_class_id' => 'required|integer',
//        'prize_class_name' => 'between:0,50',
        'params' => 'between:0,5000',
        'value' => 'numeric',
        'need_review' => 'in:0,1',
        'need_get' => 'in:0,1',
//        'prize_desc' => 'between:0,512',
//        'prize_category' => 'required',
    ];

    /**
     * 条件类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prizeClass() {
        return $this->belongsTo('ActivityPrizeClass', 'prize_class_id', 'id');
    }

    /**
     * 获得实现类类名
     *
     * @return mixed
     */
    public function getClassName() {
        $class = $this->prizeClass()->first();

        return $class->class_name;
    }

    /**
     * 获得实现类所需参数
     *
     * @return mixed
     */
    public function getParams() {
        return json_decode($this->params, true);
    }

    /**
     * 验证之前操作
     *
     * @return bool
     */
    protected function beforeValidate() {
        $oActivity = Activity::find($this->activity_id);
        if (is_object($oActivity)) {
            $this->activity_name = $oActivity->name;
        } else {
            return false;
        }
        $oActivityPrizeClass = ActivityPrizeClass::find($this->prize_class_id);
        if (is_object($oActivityPrizeClass)) {
            $this->prize_class_name = $oActivityPrizeClass->name;
        } else {
            return false;
        }
        return parent::beforeValidate();
    }

    /**
     * 按照奖品分类获取奖品信息
     * @param int $iPrizeClassId   奖品分类id
     */
    public static function getPrizeByPrizeClassId($iPrizeClassId, $aColumns = null) {
        $aCondition = [
            'prize_class_id' => ['=', $iPrizeClassId]
        ];
        $oQuery = self::doWhere($aCondition);
        if (is_null($aColumns)) {
            return $oQuery->get();
        } else {
            return $oQuery->get($aColumns);
        }
    }

}
