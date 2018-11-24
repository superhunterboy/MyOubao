<?php

use Illuminate\Support\Facades\Redis;

class PrjPrizeSet extends BaseModel {

    public static $resourceName = 'PrjPrizeSet';
    protected $table = 'prj_prize_sets';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'trace_id',
        'project_id',
        'username',
        'lottery_id',
        'issue',
        'way_id',
        'basic_method_id',
        'level',
        'coefficient',
        'prize_set',
        'prize',
        'won_count',
        'status',
        'sent_at',
    ];
    public static $htmlSelectColumns = [];

    const STATUS_WAIT = 0;
    const STATUS_SENDING = 1;
    const STATUS_SENT = 2;
    const STATUS_DROPED = 4;

    public static $validStatuses = [
        self::STATUS_WAIT => 'Waiting',
        self::STATUS_SENDING => 'Sending',
        self::STATUS_SENT => 'Sent',
        self::STATUS_DROPED => 'Canceled',
    ];

    const ERRNO_SET_SENT_FAILED = -910;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'trace_id' => 'integer',
        'project_id' => 'required|integer',
        'user_id' => 'required|integer',
        'project_no' => 'required|max:32',
        'lottery_id' => 'required|integer',
        'issue' => 'required|max:15',
        'way_id' => 'required|integer',
        'level' => 'required|integer',
        'coefficient' => 'required|in:1.00,0.10,0.01',
        'agent_sets' => 'max:1024',
        'prize_set' => 'required|numeric',
        'prize' => 'required|numeric',
        'multiple' => 'required|integer',
        'won_count' => 'integer',
        'status' => 'in:0,1,2,3',
        'sent_at' => 'date_format:Y-m-d H:i:s',
        'is_tester' => 'required|in:0,1'
    ];
    protected $fillable = [
        'trace_id',
        'project_id',
        'user_id',
        'username',
        'is_tester',
        'user_forefather_ids',
        'account_id',
        'project_no',
        'lottery_id',
        'issue',
        'way_id',
        'basic_method_id',
        'level',
        'coefficient',
        'agent_sets',
        'prize_set',
        'multiple',
        'prize',
        'won_count',
        'status',
        'sent_at',
    ];

    /**
     * add prize set of projects
     * @param array $aSetting
     * @return bool
     */
    public static function addPrizeSet($aSetting) {
        $oPrjPrizeSet = new PrjPrizeSet($aSetting);
        return $oPrjPrizeSet->save();
    }

    protected function setPrizeSetAttribute($fAmount) {
        $this->attributes['prize_set'] = formatNumber($fAmount, 4);
    }

    protected function setPrizeAttribute($fAmount) {
        $this->attributes['prize'] = formatNumber($fAmount, 4);
    }

    /**
     * 更新状态
     *
     * @param int $iToStatus
     * @param int $iFromStatus
     * @return bool
     */
    protected static function setStatus($iProjectId, $iToStatus, $iFromStatus) {
        return self::where('project_id', '=', $iProjectId)->where('status', '=', $iFromStatus)->where('status', '<>', $iToStatus)->update(['status' => $iToStatus]);
    }

    /**
     * 返回指定状态的记录数
     * @param int $iProjectId
     * @param int $iStatus
     * @return int
     */
    protected static function getCount($iProjectId, $iStatus = self::STATUS_WAIT) {
        return self::where('project_id', '=', $iProjectId)->where('status', '=', $iStatus)->count();
    }

    /**
     * 返回指定奖期指定状态的记录数
     * @param int $iProjectId
     * @param int $iStatus
     * @return int
     */
    public static function getCountOfIssue($iLotteryId, $sIssue, $iStatus = self::STATUS_WAIT) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
        ];
        is_null($iStatus) or $aConditions['status'] = ['=', $iStatus];
        return self::doWhere($aConditions)->count();
    }

    /**
     * 保存中奖详情
     * @param Project $oProject
     * @param array $aPrize
     * @return int | false      中奖详情ID或FALSE
     */
    public static function addDetail($oProject, & $aPrize) {
        self::compileData($oProject, $aPrize);
//        pr($aPrize);
//        exit;
        $oPrjPrizeSet = new PrjPrizeSet($aPrize);
        if (!$bSucc = $oPrjPrizeSet->save()) {
            pr($oPrjPrizeSet->validationErrors->toArray());
        }
        return $bSucc ? $oPrjPrizeSet->id : false;
    }

    /**
     * 构建中奖详情数据
     * @param Project $oProject
     * @param array & $aPrize
     */
    private static function compileData($oProject, & $aPrize) {
        $aPrize['project_id'] = $oProject->id;
        $aPrize['project_no'] = $oProject->serial_number;
        $aPrize['trace_id'] = $oProject->trace_id;
        $aPrize['user_id'] = $oProject->user_id;
        $aPrize['username'] = $oProject->username;
        $aPrize['is_tester'] = $oProject->is_tester;
        $aPrize['user_forefather_ids'] = $oProject->user_forefather_ids;
        $aPrize['account_id'] = $oProject->account_id;
        $aPrize['lottery_id'] = $oProject->lottery_id;
        $aPrize['issue'] = $oProject->issue;
        $aPrize['way_id'] = $oProject->way_id;
        $aPrize['coefficient'] = $oProject->coefficient;
        $aPrize['multiple'] = $oProject->multiple;
        $aPrize['status'] = self::STATUS_WAIT;
    }

    /**
     * 返回指定注单的中奖详情
     * @param int $iProjectId
     * @param int|null $iStatus
     * @return Containor|PrjPrizeSet
     */
    public static function getPrizeDetailOfProject($iProjectId, $iStatus = self::STATUS_WAIT) {
        $aConditions = [
            'project_id' => ['=', $iProjectId],
            'status' => ['=', $iStatus],
        ];
//        is_null($iStatus) or $aConditions['status'] = ['=', $iStatus];
        return self::doWhere($aConditions)->get();
    }

    /**
     * 发放奖金
     * @param Project $oProject
     * @return True or errno
     */
    public function send($oProject) {
        $aExtraData = $oProject->getAttributes();
        $aExtraData['project_id'] = $oProject->id;
        $aExtraData['project_no'] = $oProject->serial_number;
        unset($aExtraData['id']);

        $iReturn = Transaction::addTransaction($oProject->User, $oProject->Account, TransactionType::TYPE_SEND_PRIZE, $this->prize, $aExtraData);
        return ($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL) ? $this->setToSent() : false;
    }

    /**
     * 发放佣金
     * @param Project $oProject
     * @return True or errno
     */
    private function setToSent() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['=', self::STATUS_WAIT],
        ];
        $data = [
            'status' => self::STATUS_SENT,
            'sent_at' => Carbon::now()->toDateTimeString()
        ];
        if ($this->doWhere($aConditions)->update($data) > 0) {
            return true;
        }
        return false;
    }

    /**
     * 将指定注单的中奖记录设置为已取消
     * @param int $iProjectId
     * @return bool
     */
    public static function cancelOfProject($iProjectId) {
        return self::where('project_id', '=', $iProjectId)->where('status', '=', self::STATUS_SENT)->update(['status' => self::STATUS_DROPED]) > 0;
    }

    /**
     * 返回最新的中奖记录
     * @param int $iCount
     * @return Containor|PrjPrizeSet
     */
    public static function getPrizeDetails($iCount = 5) {
        $redis = Redis::connection();
        $sKey = self::compileListCacheKey();
        if ($bHasInRedis = $redis->exists($sKey)) {
            $aPrizeSetFromRedis = $redis->lrange($sKey, 0, $iCount - 1);
//            pr($aPrizeSetFromRedis);
            $iNeedCount = $iCount - count($aPrizeSetFromRedis);
            foreach ($aPrizeSetFromRedis as $sInfo) {
                $obj = new static;
                $obj = $obj->newFromBuilder(json_decode($sInfo, true));
                $aPrizeSet[] = $obj;
            }
            unset($obj);
        } else {
            $iNeedCount = $iCount;
            $aPrizeSet = [];
        }
        if (!$bHasInRedis || $iNeedCount > 0) {
            $aColumns = ['id', 'title', 'updated_at'];
            $aMorePrizeSet = self::where('status', '<>', self::STATUS_DROPED)->where('prize', '>=', 1000)->orderBy('id', 'desc')->limit($iCount)->get(['username', 'prize', 'lottery_id']);
            foreach ($aMorePrizeSet as $oMoreArticle) {
                $aPrizeSet[] = $oMoreArticle;
                $redis->rpush($sKey, json_encode($oMoreArticle->toArray()));
            }
        }
//        pr($aPrizeSet);
//        exit;
        return $aPrizeSet;
    }
	public static function getCurrentWonProjects($iCount = 10) {
        return Project::where('status', Project::STATUS_WON)
        					->where('amount', '>=', 100)
        					->orderBy('id', 'desc')
        					->take($iCount)
        					->get(['username','lottery_id','amount']);
    }
    
    private static function compileListCacheKey() {
        return self::getCachePrefix(true) . 'list';
    }

    public static function deleteListCache() {
        $redis = Redis::connection();
        $sKey = self::compileListCacheKey();
        $redis->del($sKey);
    }

    protected static function getCachePrefix($bPlural = false) {
        $sClass = self::getRealClassForCache();
        !$bPlural or $sClass = Str::plural($sClass);
        return Config::get('cache.prefix') . $sClass . '-';
    }

    protected static function getRealClassForCache() {
        $sClass = get_called_class();
        !static::$cacheUseParentClass or $sClass = get_parent_class($sClass);
        return $sClass;
    }

}
