<?php

/**
 * Class ActivityTaskPrize - 活动任务奖品表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityTaskPrize extends BaseModel {

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
    protected $table = 'activity_task_prizes';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'activity_id',
        'activity_name',
        'prize_id',
        'prize_name',
        'task_id',
        'task_name',
        'is_auto_send',
        'count',
    ];
    public static $resourceName = 'ActivityTaskPrize';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'activity_name',
        'prize_name',
        'task_name',
        'count',
        'is_auto_send',
    ];
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'prize_id' => 'aPrizes',
        'task_id' => 'aTasks',
    ];
    public static $ignoreColumnsInEdit = ['task_name', 'prize_name', 'activity_name'];
    public static $rules = [
        'activity_id' => 'required|integer',
        'activity_name' => 'required|between:1,50',
        'prize_id' => 'required|integer',
        'task_id' => 'required|integer',
        'count' => 'required|integer',
        'task_name' => 'required|between:1,50',
        'prize_name' => 'required|between:1,50',
        'is_auto_send' => 'required|in:0,1',
    ];

    /**
     * 赠送奖品
     *
     * @param $user_id
     * @param int $source
     * @return bool
     */
    public function send($user_id, $aExtraData = null, $source = 1) {
        $model = new ActivityUserPrize();
        $oPrize = ActivityPrize::find($this->prize_id);
        $model->activity_id = $this->activity_id;
        $model->prize_id = $this->prize_id;
//        $model->task_id = $this->task_id;
        is_null($aExtraData) or $model->data = json_encode($aExtraData);
        $model->count = $this->count;
        $model->user_id = $user_id;
        $model->source = $source;
        $model->status = $oPrize->need_review ? ActivityUserPrize::STATUS_NO_SEND : ActivityUserPrize::STATUS_VERIRIED;
        return $model->save();
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
        $oActivityTask = ActivityTask::find($this->task_id);
        if (is_object($oActivityTask)) {
            $this->task_name = $oActivityTask->name;
        } else {
            return false;
        }
        $oActivityPrize = ActivityPrize::find($this->prize_id);
        if (is_object($oActivityPrize)) {
            $this->prize_name = $oActivityPrize->name;
        } else {
            return false;
        }
        return parent::beforeValidate();
    }

}
