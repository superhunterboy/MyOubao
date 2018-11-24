<?php

use Illuminate\Support\Facades\Redis;

class Project extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 1;
    protected $table = 'projects';
    public static $resourceName = 'Project';
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'amount' => 2,
        'commission' => 2,
    ];
    //真是奖金组配置
    public static $aLotteriesPrizeDiff = [8 => 20, 9 => 20, 10 => 20, 12 => 20, 13 => 30, 14 => 30];
    public static $columnForList = [
        'id',
        'serial_number',
        'trace_id',
        'username',
        'multiple',
        'lottery_id',
        'issue',
        'title',
        'bet_number',
        'position',
        'coefficient',
        'amount',
        'commission',
        'prize',
        'bought_at',
        'ip',
        'status',
    ];
    public static $totalColumns = [
        'amount',
        'prize',
    ];
    protected $fillable = [
        'trace_id',
        'user_id',
        'username',
        'is_tester',
        'prize_group',
        'account_id',
        'multiple',
        'serial_number',
        'user_forefather_ids',
        'issue',
        'title',
        'bet_number',
        'position',
        'is_overprize',
        'display_bet_number',
//        'compress_bet_number',
        'lottery_id',
        'method_id',
        'way_id',
        'coefficient',
        'single_amount',
        'amount',
        'commission',
        'status',
        'prize_set',
        'ip',
        'proxy_ip',
        'bought_at',
        'canceled_at',
        'canceled_by',
        'bet_source',
        'series_id',
        'series_set_id',
    ];
    public static $rules = [
        'trace_id' => 'integer',
        'user_id' => 'required|integer',
        'account_id' => 'required|integer',
        'multiple' => 'required|integer',
        'serial_number' => 'required|max:32',
        'user_forefather_ids' => 'max:1024',
        'issue' => 'required|max:12',
        'title' => 'required|max:100',
        'bet_number' => '',
        'commission' => 'regex:/^[\d]+(\.[\d]{0,4})?$/',
        'note' => 'max:250',
        'lottery_id' => 'required|integer',
        'way_id' => 'required|integer',
        'prize_added' => 'numeric',
        'coefficient' => 'in:1.00,0.10,0.01',
        'single_amount' => 'regex:/^[\d]+(\.[\d]{0,6})?$/',
        'amount' => 'regex:/^[\d]+(\.[\d]{0,6})?$/',
        'status' => 'in:0,1,2,3',
        'ip' => 'required|ip',
        'proxy_ip' => 'required|ip',
        'bought_at' => 'date_format:Y-m-d H:i:s',
        'canceled_at' => 'date_format:Y-m-d H:i:s',
        'canceled_by' => 'max:16',
        'is_encoded' => 'in:0,1',
    ];
    public $orderColumns = [
        'id' => 'desc'
    ];

    const STATUS_NORMAL = 0;
    const STATUS_DROPED = 1;
    const STATUS_LOST = 2;
    const STATUS_WON = 3;
    const STATUS_PRIZE_SENT = 4;
    const STATUS_DROPED_BY_SYSTEM = 5;
    const DROP_BY_USER = 1;
    const DROP_BY_ADMIN = 2;
    const DROP_BY_SYSTEM = 3;
    const COMMISSION_STATUS_WAITING = 0;
    const COMMISSION_STATUS_SENDING = 1;
    const COMMISSION_STATUS_PARTIAL = 2;
    const COMMISSION_STATUS_SENT = 4;
    const PRIZE_STATUS_WAITING = 0;
    const PRIZE_STATUS_SENDING = 1;
    const PRIZE_STATUS_PARTIAL = 2;
    const PRIZE_STATUS_SENT = 4;

    public static $validStatuses = [
        self::STATUS_NORMAL => 'Normal',
        self::STATUS_DROPED => 'Canceled',
        self::STATUS_LOST => 'Lost',
        self::STATUS_WON => 'Counted',
        self::STATUS_PRIZE_SENT => 'Prize Sent',
        self::STATUS_DROPED_BY_SYSTEM => 'Canceled By System'
    ];
    public static $commissionStatuses = [
        self::COMMISSION_STATUS_WAITING => 'Waiting',
        self::COMMISSION_STATUS_SENDING => 'Sending',
        self::COMMISSION_STATUS_PARTIAL => 'Partial',
        self::COMMISSION_STATUS_SENT => 'Done',
    ];
    public static $prizeStatuses = [
        self::PRIZE_STATUS_WAITING => 'Waiting',
        self::PRIZE_STATUS_SENDING => 'Sending',
        self::PRIZE_STATUS_PARTIAL => 'Partial',
        self::PRIZE_STATUS_SENT => 'Done',
    ];
    public static $aHiddenColumns = [];
    public static $aReadonlyInputs = [];
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'serial_number';

    /**
     * User
     * @var User|Model
     */
    public $User;

    /**
     * Account
     * @var Account|Model
     */
    public $Account;

    /**
     * Lottery
     * @var Lottery|Model
     */
    public $Lottery;

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
        'status' => 'aStatusDesc',
        'coefficient' => 'aCoefficients',
    ];

    const ERRNO_BET_SUCCESSFUL = -200;
    const ERRNO_PROJECT_MISSING = -201;
    const ERRNO_BET_SLAVE_DATA_SAVED = -202;
    const ERRNO_SLAVE_DATA_CANCELED = -203;
    const ERRNO_COUNT_ERROR = -204;
    const ERRNO_PRIZE_OVERFLOW = -205;
    const ERRNO_COUNT_AMOUNT_ERROR = -206;
    const ERRNO_BET_ERROR_SAVE_ERROR = -210;
    const ERRNO_BET_ERROR_COMMISSIONS = -211;
    const ERRNO_BET_ERROR_DATA_ERROR = -213;
    const ERRNO_BET_ERROR_LOW_BALANCE = -214;
    const ERRNO_BET_ERROR_ISSUE_LIMIT = -215;
    const ERRNO_DROP_SUCCESS = -230;
    const ERRNO_DROP_ERROR_STATUS = -231;
    const ERRNO_DROP_ERROR_NOT_YOURS = -232;
    const ERRNO_DROP_ERROR_STATUS_UPDATE_ERROR = -233;
    const ERRNO_DROP_ERROR_PRIZE = -234;
    const ERRNO_DROP_ERROR_COMMISSIONS = -235;
    const ERRNO_BET_TURNOVER_UPDATE_FAILED = -236;
    const ERRNO_BET_ALL_CREATED = -500;
    const ERRNO_BET_PARTLY_CREATED = -501;
    const ERRNO_BET_FAILED = -502;
    const ERRNO_BET_NO_RIGHT = -999;

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);

        if (isset($attributes['is_encoded'])) {
            $this->is_encoded = $attributes['is_encoded'];
        }
    }

    protected function beforeValidate() {
        if (!$this->single_amount || !$this->amount || !$this->user_id || !$this->lottery_id || !$this->issue || !$this->way_id) {
            return false;
        }
        if (!$this->user_forefather_ids || !$this->account_id) {
            $oUser = User::find($this->user_id);
            $this->user_forefather_ids = $oUser->forefather_ids;
            $this->account_id = $oUser->account_id;
            $this->is_tester = $oUser->is_tester;
//            $this->prize_group = $oUser->prize_group;
        }
        $this->trace_id or $this->trace_id = null;
        $this->prize_added or $this->prize_added = 0;
        $this->coefficient or $this->coefficient = 0;
        $this->single_amount or $this->single_amount = 0;
        $this->serial_number or $this->serial_number = self::makeSeriesNumber($this->user_id);
        return parent::beforeValidate();
    }

    /**
     * check project data
     * @return boolean
     */
    public function checkProject() {
        return true;
    }

    /**
     * save prize setting of this project
     * @return boolean
     */
    protected function saveCommissions() {
        if (!$this->id) {
            return self::ERRNO_PROJECT_MISSING;
        }
        $aCommissions = & $this->compileCommissions();
//        $aSelfCommissions = & $this->compileSelfCommissions();
//        $aCommissions = array_merge($aCommissions, $aSelfCommissions);
        if ($aCommissions) {
            foreach ($aCommissions as $data) {
                $oPrjCommission = new Commission($data);
                //            pr($oPrjCommission->getAttributes());
                if (!$bSucc = $oPrjCommission->save()) {
                    pr($oPrjCommission->validationErrors->toArray());
                    return self::ERRNO_BET_ERROR_COMMISSIONS;
                }
            }
        }
        return self::ERRNO_BET_SLAVE_DATA_SAVED;
    }

    /**
     * 计算奖金与返点
     * @return array
     */
    protected function & compileCommissions() {
        $aCommissions = $aUserIds = $oUsers = $oCommissionSets = [];

        $aGroupIds = UserPrizeSet::getGroupIdOfUsers([$this->user_id], $this->lottery_id);
        $aGroupId = $aGroupIds[$this->user_id]; //['奖金组ID', '奖金组']


        $oSeriesWay = SeriesWay::find($this->way_id);
        $oSeries = Series::find($oSeriesWay->series_id);

        $aMethodIds = explode(',', $oSeriesWay->basic_methods);
        $aPrize = PrizeDetail::getPrizes($aGroupId[0], $aMethodIds[0]);
        list($iLevel, $iPrize) = each($aPrize);

        $aBasicData = [
            'project_id' => $this->id,
            'project_no' => $this->serial_number,
            'trace_id' => $this->trace_id,
            'coefficient' => $this->coefficient,
            'lottery_id' => $this->lottery_id,
            'issue' => $this->issue,
            'multiple' => $this->multiple,
            'way_id' => $this->way_id,
            'basic_method_id' => $aMethodIds[0],
            'level' => $iLevel,
            'base_amount' => $this->amount,
            'prize_set' => $iPrize,
        ];
        $aUserIds = explode(',', $this->user_forefather_ids);
        array_push($aUserIds, $this->user_id);
        $aUserIds = array_reverse($aUserIds);
        $aUserData = [];

        $aUserCommissionSets = UserCommissionSet::whereIn('user_id', $aUserIds)->where('series_set_id', '=', SeriesSet::getSeriesSetIdBySeriesId($oSeries->id))->get(["commission_rate", "user_id"]);
        foreach ($aUserCommissionSets as $oComissionSet) {
            $aUserData[$oComissionSet->user_id] = $oComissionSet->commission_rate;
        }

        $aCommissions = [];
        $lastRate = 0;
//        Log::info('oooooooooooooooooooooooooo');
        foreach ($aUserIds as $user_id) {
            if (!isset($aUserData[$user_id])) {
                $lastRate = UserCommissionSet::getRateByPrizeGroup($aGroupId[1]);
//                Log::info('user_id='.$user_id . ', last_rate=' .$lastRate);
                continue;
            }
            $oUser = User::find($user_id);
            if (!$oUser)
                continue;
            $commission = [
                'is_tester' => $oUser->is_tester,
                'user_id' => $user_id,
                'account_id' => $oUser->account_id,
                'username' => $oUser->username,
                'user_forefather_ids' => $oUser->forefather_ids,
            ];
            if ($user_id == $this->user_id)//自己投注返点
                $commission['amount'] = $this->amount * $this->commission;
            else                                              //代理返点
                $commission['amount'] = ($aUserData[$user_id] - $lastRate) / 100 * $this->amount;

            $lastRate = $aUserData[$user_id];

            if ($commission['amount'] <= 0)
                continue;
            $aCommissions[] = array_merge($aBasicData, $commission);
        }
//        Log::info($aCommissions);
//        Log::info('***************************');
        return $aCommissions;
    }

    /*
      protected function & compileCommissions(){
      $aCommissions = $oFores = [];

      $aBasicData = [
      'project_id' => $this->id,
      'project_no' => $this->serial_number,
      'trace_id' => $this->trace_id,
      'coefficient' => $this->coefficient,
      'lottery_id' => $this->lottery_id,
      'issue' => $this->issue,
      'multiple' => $this->multiple,
      'way_id' => $this->way_id,
      ];

      $aGroupIds = UserPrizeSet::getGroupIdOfUsers([$this->user_id], $this->lottery_id);
      $aGroupId = $aGroupIds[$this->user_id]; //['奖金组ID', '奖金组']

      $oSeriesWay = SeriesWay::find($this->way_id);
      $aMethodIds = explode(',', $oSeriesWay->basic_methods);
      $oSeries = Series::find($oSeriesWay->series_id);
      $iClassicAmount = $oSeries->classic_amount;

      $aFores = $this->user_forefather_ids ? array_reverse(explode(',', $this->user_forefather_ids)) : [];

      //配置返点奖级
      $fCommissionAmount = CommissionConfig::getCommissionAmount($this->way_id, $this->bet_number, $this->amount);

      $oUser = User::find($this->user_id);
      $diffForePrize = 0;
      foreach($aFores as $i => $iForeId)
      {
      $oFores[$iForeId] =  User::find($iForeId);
      if ($i == 0) $iLastId = $this->user_id;
      if($i == (count($aFores) -1)) $diffForePrize = $oFores[$iForeId]->prize_group - $oUser->prize_group;
      }
      if($diffForePrize <= 0) return $aCommissions;

      foreach($oFores as $iForeId => $oFore)
      {
      if($oFore->prize_group > $oUser->prize_group && (!$oSeries->min_commission_prize_group || ($oFore->prize_group > $oSeries->min_commission_prize_group)))
      {
      foreach($aMethodIds as $iMethodId)
      {
      $aPrize = PrizeDetail::getPrizes($aGroupId[0], $iMethodId);
      list($iLevel, $iPrize) = each($aPrize);

      $aCommissionData = [
      'user_id' => $iForeId,
      'account_id' => $oFore->account_id,
      'username' => $oFore->username,
      'user_forefather_ids' => $oFore->forefather_ids,
      'is_tester' => $oFore->is_tester,
      'basic_method_id' => $iMethodId,
      'level' => $iLevel,
      'base_amount' => $this->amount,
      //                    'amount' => $this->amount * ($oFore->prize_group - $this->prize_group)/$iClassicAmount,
      //                    'prize_set' => $aPrize[$level] - $reduceRate*$aPrize[$level],
      'prize_set' => $iPrize,
      ];

      if($iLastId == $this->user_id) $iLastPrizeGroup = $oUser->prize_group;
      else $iLastPrizeGroup =  $oFores[$iLastId]->prize_group;

      if($oFore->prize_group == $iLastPrizeGroup) break;

      $iLastPrizeGroup = max($iLastPrizeGroup, $oSeries->min_commission_prize_group);

      if(is_numeric($fCommissionAmount))
      {
      $aCommissionData['amount'] = ($oFore->prize_group - $iLastPrizeGroup) / $diffForePrize * $fCommissionAmount;
      }else{
      $aCommissionData['amount'] = $this->amount * ($oFore->prize_group - $iLastPrizeGroup)/$iClassicAmount;
      }
      if($aCommissionData['amount'] <= 0) break;

      $aCommissions[] = array_merge($aBasicData, $aCommissionData);
      break;
      }
      }
      $iLastId = $iForeId;
      }
      return $aCommissions;
      }
     */

    public function & compileSelfCommissions() {

        $aCommissions = [];
        $oUser = User::find($this->user_id);
        if ($this->prize_group == $oUser->prize_group)
            return $aCommissions;

        $aBasicData = [
            'project_id' => $this->id,
            'project_no' => $this->serial_number,
            'trace_id' => $this->trace_id,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_forefather_ids' => $this->user_forefather_ids,
            'account_id' => $this->account_id,
            'coefficient' => $this->coefficient,
            'lottery_id' => $this->lottery_id,
            'issue' => $this->issue,
            'multiple' => $this->multiple,
            'way_id' => $this->way_id,
        ];

        $aGroupIds = UserPrizeSet::getGroupIdOfUsers([$this->user_id], $this->lottery_id);
        $aGroupId = $aGroupIds[$this->user_id]; //['奖金组ID', '奖金组']

        $oSeriesWay = SeriesWay::find($this->way_id);
        $aMethodIds = explode(',', $oSeriesWay->basic_methods);
        $iClassicAmount = Series::find($oSeriesWay->series_id)->classic_amount;

        $reduceRate = ($oUser->prize_group - $this->prize_group) / $oUser->prize_group;

        foreach ($aMethodIds as $iMethodId) {
            $aPrize = PrizeDetail::getPrizes($aGroupId[0], $iMethodId);
            $level = key($aPrize);

            $aCommissionData = [
                'is_tester' => $this->is_tester,
                'basic_method_id' => $iMethodId,
                'level' => key($aPrize),
                'prize_set' => $aPrize[$level] - $reduceRate * $aPrize[$level],
                'base_amount' => $this->amount,
                'amount' => $this->amount * ($oUser->prize_group - $this->prize_group) / $iClassicAmount,
            ];
            $aCommissions[] = array_merge($aBasicData, $aCommissionData);
            break;
        }

        return $aCommissions;
    }

    /**
     * set status to counted
     *
     * @param decimal $fPrize
     * @return boolean
     */
//    protected function setPrizeCounted($sWnNumber,$aPrizes){
//        pr($aPrizes);
//        exit;
//        return true;
//    }

    protected function setSingleAmountAttribute($fAmount) {
        $this->attributes['single_amount'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setAmountAttribute($fAmount) {
        $this->attributes['amount'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setTotalPrizeAttribute($fAmount) {
        $this->attributes['total_prize'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPrizeAttribute($fAmount) {
        $this->attributes['prize'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setCoefficientAttribute($fCoefficient) {
        $aCoefficient = Config::get('bet.coefficients');
        $fCoefficient = formatNumber($fCoefficient, 2);
        if (!array_key_exists($fCoefficient, $aCoefficient)) {
            return false;
        }
        return $this->attributes['coefficient'] = $fCoefficient;
    }

    protected function getSerialNumberShortAttribute() {
        return substr($this->serial_number, 0, 4) . '...';
    }
    
    private function getRealBetNumber() {
    	$sRealBetStr = '';
    	if(isset($this->display_bet_number))
    	{
    		$sRealBetStr = $this->display_bet_number;
    	}
        else 
        {
	        if(in_array($this->lottery_id,array(53,60)))
	        {
	        	$sRealBetNumber = Encrypt::db_decode($this->bet_number);
	        	$aRealBetNumber = str_split($sRealBetNumber);
	        	$sStr = '';
	        	foreach ($aRealBetNumber as $sItem)
	        	{
	        		if(is_numeric($sItem))
	        		{
	        			$sStr .= (intval($sItem)+1).',';
	        		}
	        		else 
	        		{
	        			$sStr .= $sItem;
	        		}
	        	}
	        	$sRealBetStr = str_replace(',|', '|', trim($sStr,','));
	        }
	        else 
	        {
	        	$sRealBetStr = Encrypt::db_decode($this->bet_number);
	        }
        }
        if(strlen($sRealBetStr) > 50)
        {
        	return substr($sRealBetStr, 0, 50) . "...";
        }
        return $sRealBetStr;
    }
    
    protected function getDisplayBetNumberFormalAttribute() {
        //return $this->status == 0 ? "***" : substr($this->display_bet_number, 0, 10) . "...";
        return $this->status == 0 ? $this->getRealBetNumber() : substr($this->display_bet_number, 0, 10) . "...";
    }

    protected function getDisplayBetNumberAttribute($sDisplayBetNumber) {
        if (isset($sDisplayBetNumber) && $sDisplayBetNumber !== '') {
            return $sDisplayBetNumber;
        }
        if ($this->status != self::STATUS_NORMAL || Session::get('user_id') == $this->user_id) {
            $sDisplayBetNumber = $this->getDisplayBetNumber();
        }
        return $sDisplayBetNumber;
    }

    public function getDisplayBetNumber() {
        $aProject = $this->getAttributes();
        $aProject['bet_number'] = $this->bet_number;
        if (!isset($aProject['bet_number'])) {
            return null;
        }
        $sCacheKey = 'display_number_' . $aProject['way_id'] . '_' . md5($aProject['bet_number']);
        if (Cache::has($sCacheKey)) {
            $sDisplayBetNumber = Cache::get($sCacheKey);
        } else {
            $oSeriesWay = SeriesWay::find($aProject['way_id']);
            $oSeriesWay->count($aProject);
            $sDisplayBetNumber = isset($aProject['display_bet_number']) ? $aProject['display_bet_number'] : $aProject['bet_number'];
            Cache::put($sCacheKey, $sDisplayBetNumber, 1800);
        }
        return $sDisplayBetNumber;
    }

    /**
     * 生成序列号
     * @param int $iUserId
     * @return string
     */
    public static function makeSeriesNumber($iUserId) {
        return md5($iUserId . microtime(true) . mt_rand());
    }

    /**
     * 保存注单
     * @return int      0: 成功; -1: 交易明细数据错误; -2: 账变保存失败; -3: 账户余额保存失败; -4: 余额不足 -5: 注单保存失败 -6: 佣金数据保存失败 -7: 奖金数据保存失败
     */
    public function addProject($bNeedIP = true) {
        if ($this->Account->available < $this->amount) {
            return self::ERRNO_BET_ERROR_LOW_BALANCE;
        }
        $rules = Project::$rules;
        if (!$bNeedIP) {
            unset($rules['ip'], $rules['proxy_ip']);
        }

        //先unset掉，开奖后再写入display_bet_number
        if (isset($this->attributes['display_bet_number'])) {
            unset($this->attributes['display_bet_number']);
        }

        if (!$this->save($rules)) {
            return self::ERRNO_BET_ERROR_SAVE_ERROR;
        }

        $aExtraData = $this->getAttributes();
        $aExtraData['project_id'] = $this->id;
        $aExtraData['project_no'] = $this->serial_number;
        unset($aExtraData['id']);
        $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_BET, $this->amount, $aExtraData);
        $iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL or $iReturn = $this->saveCommissions();
        if ($iReturn == self::ERRNO_BET_SLAVE_DATA_SAVED) {
            // 处理销售量
//            $this->addBackTask(true);
//            $aTaskData = [
//                'user_id' => $this->user_id,
//                'amount' => $this->amount,
//                'date' => substr($this->bought_at,0,10)
//            ];
//            $bSucc = BaseTask::addTask('StatUpdateTurnover',$aTaskData,'stat');
//            $iReturn = UserProfit::updateTurnOver(substr($this->bought_at,0,10),$this->User,$this->amount) ? self::ERRNO_BET_SUCCESSFUL : self::ERRNO_BET_TURNOVER_UPDATE_FAILED;
        }
//        return $iReturn;
        return $iReturn == self::ERRNO_BET_SLAVE_DATA_SAVED ? self::ERRNO_BET_SUCCESSFUL : $iReturn;
//        if (($iSlaveDataSaved = $this->saveCommissions()) != self::ERRNO_BET_SLAVE_DATA_SAVED){
//            return $iSlaveDataSaved;
//        }
//        $iReturn = $this->save() ? 0 : -4;
//        return self::ERRNO_BET_SUCCESSFUL;
    }

    /**
     * 组合注单数据
     * @param array     $aOrder
     * @param SeriesWay $oSeriesWay
     * @param Lottery   $oLottery
     * @param array     $aExtraData
     * @return array &
     */
    public static function & compileProjectData($aOrder, $oSeriesWay, $oLottery, $aExtraData = []) {
        if (isset($aOrder['user_id'])) {
            $iUserId = $aOrder['user_id'];
            $sForeFatherIds = $aOrder['user_forefather_ids'];
            $sUsername = $aOrder['username'];
            $iAccountId = $aOrder['account_id'];
        } else {
            $iUserId = Session::get('user_id');
            $sForeFatherIds = Session::get('forefather_ids');
            $sUsername = Session::get('username');
            $iAccountId = Session::get('account_id');
        }


        $iUserId = isset($aOrder['user_id']) ? $aOrder['user_id'] : Session::get('user_id');


        $data = [
            'trace_id' => isset($aOrder['trace_id']) ? $aOrder['trace_id'] : 0,
            'user_id' => $iUserId,
            'username' => $sUsername,
            'account_id' => $iAccountId,
            'multiple' => $aOrder['multiple'],
//            'serial_number'       => '',
            'user_forefather_ids' => $sForeFatherIds,
            'issue' => $aOrder['issue'],
            'title' => $oSeriesWay->compileDisplayName(),
            'bet_number' => $aOrder['bet_number'],
            'position' => isset($aOrder['position']) ? $aOrder['position'] : null,
            'display_bet_number' => isset($aOrder['display_bet_number']) ? $aOrder['display_bet_number'] : $aOrder['bet_number'],
            //'compress_bet_number' => gzcompress($aOrder[ 'bet_number' ]),
            'lottery_id' => $oLottery->id,
            'way_id' => $oSeriesWay->id,
            'coefficient' => $aOrder['coefficient'],
            'single_amount' => $aOrder['single_amount'],
            'amount' => $aOrder['single_amount'] * $aOrder['multiple'],
            'bought_at' => Carbon::now()->toDateTimeString(),
            'bet_source' => $aOrder['bet_source'],
//            'series_number' => Project::makeSeriesNumber(Session::get('user_id'))
        ];
        //获取lottery的series_id和series_set_type_id;
        $lottery_map = Config::get('lottery_map');
        if (!empty($lottery_map) && isset($lottery_map[$oLottery->id])) {
            $data['series_id'] = $lottery_map[$oLottery->id]['series_id'];
            $data['series_set_id'] = $lottery_map[$oLottery->id]['series_set_id'];
        }

        if (isset($aOrder['prize_set'])) {
            $data['prize_set'] = $aOrder['prize_set'];
            $data['prize_group'] = $aOrder['prize_group'];
        } else {
            $aPrizeSettingOfUsers = UserPrizeSet::getPrizeSetOfUsers([ $iUserId], $oLottery->id, $oSeriesWay->id, $aGroupNames);
            $data['prize_set'] = json_encode($aPrizeSettingOfUsers[$iUserId]);
            $data['prize_group'] = $aGroupNames[$iUserId];
        }
        if (isset($aOrder['commission'])) {
            $data['commission'] = $aOrder['commission'];
        }

        if (isset($aOrder['is_encoded'])) {
            $data['is_encoded'] = $aOrder['is_encoded'];
        }

        if (isset($aExtraData['client_ip'])) {
            $data['ip'] = $aExtraData['client_ip'];
            $data['proxy_ip'] = $aExtraData['proxy_ip'];
        }
        $data['is_tester'] = $aExtraData['is_tester'];
        if ($data['trace_id']) {
            $oIssue = Issue::getIssue($oLottery->id, $aOrder['issue']);
            if ($oIssue->status == Issue::ISSUE_CODE_STATUS_FINISHED)
                $data['winning_number'] = $oIssue->wn_number;
        }


        return $data;
    }

    /**
     * 撤单
     * @param int $iType self::DROP_BY_USER | self::DROP_BY_ADMIN | self::DROP_BY_SYSTEM
     * @return int errno self::ERRNO_DROP_SUCCESS 成功
     */
    public function drop($iType = self::DROP_BY_USER) {
        if ($this->status != self::STATUS_NORMAL) {
            return self::ERRNO_DROP_ERROR_STATUS;
        }
        if ($iType == self::DROP_BY_USER) {
            if ($this->user_id != Session::get('user_id')) {
                return self::ERRNO_DROP_ERROR_NOT_YOURS;
            }
            $oIssue = Issue::getIssue($this->lottery_id, $this->issue);
            if (empty($oIssue)) {
                return Issue::ERRNO_ISSUE_MISSING;
            }
            $iEndTime = $oIssue->end_time;
            if (in_array($this->lottery_id, Lottery::$aKl28Lotteries)) {
                $oLottery = Lottery::find($this->lottery_id);
                $iEndTime = $oIssue->end_time - $oLottery->entertained_time;
            }

            if (time() > $iEndTime) {
                return Issue::ERRNO_ISSUE_EXPIRED;
            }
            unset($oIssue);
        }
        is_object($this->User) or $this->User = User::find($this->user_id);
        is_object($this->Account) or $this->Account = Account::find($this->account_id);
        $aExtraData = $this->getAttributes();
        $aExtraData['project_id'] = $this->id;
        $aExtraData['project_no'] = $this->serial_number;
//        if ($iType == self::DROP_BY_ADMIN){
//            $aExtraData['admin_user_id'] = Session::get('admin_user_id');
//            $aExtraData['canceled_by'] = Session::get('admin_username');
//        }
        unset($aExtraData['id']);
        $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_DROP, $this->amount, $aExtraData);
        $iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL or $iReturn = $this->setDroped($iType);
//        $iReturn != self::ERRNO_DROP_SUCCESS or $this->addBackTask(false);      // 修正用户销售额
        return $iReturn;
    }

    /**
     * 更新状态为撤单
     * @return bool
     */
    protected function setDroped($iType = self::DROP_BY_USER) {
        if (($iReturn = $this->cancelCommissons()) != self::ERRNO_SLAVE_DATA_CANCELED) {
            return $iReturn;
        }
        $data = ['canceled_at' => date('Y-m-d H:i:s')];
        $iType != self::DROP_BY_ADMIN or $data['canceled_by'] = Session::get('admin_username');
        $iStatus = $iType == self::DROP_BY_SYSTEM ? self::STATUS_DROPED_BY_SYSTEM : self::STATUS_DROPED;
        if (!$this->setStatus($iStatus, self::STATUS_NORMAL, $data)) {
            return self::ERRNO_DROP_ERROR_STATUS_UPDATE_ERROR;
        }
        $this->canceled_at = $data['canceled_at'];
        $this->status = $iStatus;
        $iType != self::DROP_BY_ADMIN or $this->canceled_by = $data['canceled_by'];
        return self::ERRNO_DROP_SUCCESS;
    }

    /**
     * 更新状态
     *
     * @param int $iToStatus
     * @param int $iFromStatus
     * @param $aExtraData
     * @return int  0: success; -1: prize set cancel fail; -2: commissions cancel fail
     */
    protected function setStatus($iToStatus, $iFromStatus, $aExtraData = []) {
        $aExtraData['status'] = $iToStatus;
        if ($bSucc = Project::where('id', '=', $this->id)->where('status', '=', $iFromStatus)->where('status', '<>', $iToStatus)->update($aExtraData)) {
            $this->deleteCache();
        }
        return $bSucc;
    }

    /**
     * 撤销佣金记录
     * @return int  self::ERRNO_SLAVE_DATA_CANCELED or self::ERRNO_DROP_ERROR_COMMISSIONS
     */
    protected function cancelCommissons() {
        if (!Commission::setDroped($this->id)) {
            return self::ERRNO_DROP_ERROR_COMMISSIONS;
        }
        return self::ERRNO_SLAVE_DATA_CANCELED;
//        return self::errno;
    }

    /**
     * set Account Model
     * @param Account $oAccount
     */
    public function setAccount($oAccount) {
        if (!empty($this->account_id) && $this->account_id == $oAccount->id) {
            $this->Account = $oAccount;
        }
    }

    /**
     * set User Model
     * @param User $oUser
     */
    public function setUser($oUser) {
        if (!empty($this->user_id) && $this->user_id == $oUser->id) {
            $this->User = $oUser;
        }
    }

    /**
     * set Lottery Model
     * @param Lottery $oLottery
     */
    public function setLottery($oLottery) {
        $this->Lottery = $oLottery;
    }

    public static function getBetNumbers($iLotteryId, $sIssue, $iSeriesWayId) {
        $sql = "select distinct bet_number from projects where lottery_id = '$iLotteryId' and issue = '$sIssue' and way_id = '$iSeriesWayId' order by bet_number";
//        $sql = "select distinct bet_number from projects where lottery_id = '$iLotteryId' and way_id = '$iSeriesWayId' order by bet_number";
        return DB::select($sql);
//        pr($data);
    }

    public static function getUnCalcutatedCount($iLotteryId, $sIssue, $mSeriesWayId = null, $bTask = null, $fAmountMin = 0, $bIsFilterTester = 0) {
        $aCondtions = [
            'lottery_id' => $iLotteryId,
            'issue' => $sIssue,
            'status' => self::STATUS_NORMAL,
            'amount >=' => $fAmountMin,
        ];
        if ($bIsFilterTester)
            $aCondtions['is_tester'] = [0];

        is_null($mSeriesWayId) or $aCondtions['way_id'] = $mSeriesWayId;
        if (!is_null($bTask)) {
            $sOperator = $bTask ? '<>' : '=';
            $aCondtions["trace_id"] = [$sOperator, null];
        }
        return self::getCount($aCondtions);
    }

    public static function getCount($aParams) {
        $aCondtions = [];
        foreach ($aParams as $sColumn => $mValue) {
            $a = explode(' ', $sColumn);
            if (count($a) == 1) {
                $sOperator = is_array($mValue) ? 'in' : '=';
            } else {
                $sColumn = $a[0];
                $sOperator = $a[1];
            }
            $aCondtions[$sColumn] = [$sOperator, $mValue];
        }
//        exit;
        return self::doWhere($aCondtions)->count();
    }

    protected function getFormattedStatusAttribute() {
        return __('_project.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    protected function setSerialNumberAttribute($sSerialNumber) {
        $this->attributes['serial_number'] = strtoupper($sSerialNumber);
    }

    protected function getPrizeFormattedAttribute() {
        return $this->attributes['prize'] ? $this->getFormattedNumberForHtml('prize') : null;
    }

    protected function getAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('amount');
    }

    protected function getUpdatedAtTimeAttribute() {
        return substr($this->updated_at, 5, -3);
    }

    protected function getPositionStringAttribute() {
        $r = "";
        if ($this->position) {
            $asPosition = ['万', '千', '百', '十', '个'];
            $aiPosition = str_split($this->position);
            foreach ($aiPosition as $index) {
                $r.=$asPosition[$index];
            }
        }
        return $r;
    }

    protected function getShortBetNumberAttribute() {

        $r = mb_substr($this->display_bet_number, 0, 10);
        if ($this->position) {
            $asPosition = ['万', '千', '百', '十', '个'];
            $aiPosition = str_split($this->position);
            foreach ($aiPosition as $index) {
                $r.=$asPosition[$index];
            }
        }
        return $r;
    }

    protected function getCompressBetNumberAttribute() {

        return $this->attributes['compress_bet_number'] ? gzuncompress($this->attributes['compress_bet_number']) : null;
    }

    protected function getCommissionPercentsAttribute() {
        return $this->prize_group_real . '-' . $this->commission_formatted . '%';
    }

    /**
     * 向后台任务队列增加任务
     * @param boolean $bPlus
     */
    public function addTurnoverStatTask($bPlus = true) {
        $sField = $bPlus ? 'bought_at' : 'canceled_at';
        $aTaskData = [
            'type' => 'turnover',
            'user_id' => $this->user_id,
            'amount' => $bPlus ? $this->amount : -$this->amount,
            'date' => substr($this->$sField, 0, 10),
            'lottery_id' => $this->lottery_id,
            'issue' => $this->issue,
        ];
        return BaseTask::addTask('StatTurnoverUpdateProfit', $aTaskData, 'statTurnover');
    }

    protected static function compileUserDataListCachePrefix($iUserId, $iLotteryId = '') {
        return self::compileListCacheKeyPrefix() . $iUserId . '-' . $iLotteryId;
    }

    protected static function compileListCacheKeyPrefix() {
        return self::getCachePrefix(true) . 'for-user-';
    }

    public static function deleteUserDataListCache($iUserId) {
        $sKeyPrifix = self::compileUserDataListCachePrefix($iUserId);
        $redis = Redis::connection();
        if ($aKeys = $redis->keys($sKeyPrifix . '*')) {
            foreach ($aKeys as $sKey) {
                $redis->del($sKey);
            }
        }
    }

    public function setCommited() {
        $this->updateBuyCommitTime();
        $this->addTurnoverStatTask(true);
        $this->deleteUserDataListCache($this->user_id);
//        $this->updateUserBetList();
    }

    public function updateBuyCommitTime() {
        $data = ['bet_commit_time' => Carbon::now()->timestamp];
        if ($bSucc = $this->update($data)) {
            $this->bet_commit_time = $data['bet_commit_time'];
        }
        return $bSucc;
    }

    /**
     * 获取用户当前时间的有效投注金额
     * @param int $iUserId     用户id
     * @param string $currentDateTime     当前时间
     */
    public static function getCurrentDayTurnover($iUserId, $currentDateTime, $endDateTime = null) {
        $oQuery = self::where('user_id', $iUserId)->where('bought_at', '>=', $currentDateTime);

        !$endDateTime or $oQuery->where('bought_at', '<=', $endDateTime);

        $aTurnover = $oQuery->whereIn('status', [
                    Project::STATUS_LOST,
                    Project::STATUS_WON,
                    Project::STATUS_PRIZE_SENT
                ])
                ->get(['amount']);

        $aTotalTurnover = [];

        foreach ($aTurnover as $data) {
            $aTotalTurnover[] = $data['amount'];
        }

        $fTotalTurnover = array_sum($aTotalTurnover);

        return $fTotalTurnover;
    }

    /**
     * 获取用户当前时间的有效投注金额
     * @param int $iUserId     用户id
     * @param string $currentDateTime     当前时间
     */
    public static function getCurrentDayTurnover2($iUserId, $currentDateTime, $endDateTime = null) {
        $oQuery = self::where('user_id', $iUserId)->where('bought_at', '>=', $currentDateTime);

        !$endDateTime or $oQuery->where('bought_at', '<=', $endDateTime);

        $aTurnover = $oQuery->whereIn('status', [
                    Project::STATUS_LOST,
                    Project::STATUS_WON,
                    Project::STATUS_PRIZE_SENT
                ])
                ->sum('amount');

        return $aTurnover;
    }

    public static function getProjectIdByLotteryIdAndIssue($lottery_id, $issue, $user_id = null) {
        $oQuery = self::where('lottery_id', '=', $lottery_id)->where('issue', '=', $issue)->whereNotIn('status', [
            Project::STATUS_DROPED,
            Project::STATUS_DROPED_BY_SYSTEM
        ]);

        if ($user_id) {
            $oQuery->where('user_id', '=', $user_id);
        }

        return $oQuery->get(['id']);
    }

    /**
     * 获取自己的返点奖金比例
     * @return float
     */
    public function getCommissionFormattedAttribute() {
        return number_format($this->commission * 100, 2);
    }

    public function getPrizeGroupRealAttribute() {
        $lottery_prize_type = self::$aLotteriesPrizeDiff;
        $diffNum = isset($lottery_prize_type[$this->lottery_id]) ? $lottery_prize_type[$this->lottery_id] : 0;
        if ($this->way_id == 269)
            $diffNum = 30;
        return $this->prize_group - $diffNum;
    }

    /**
     * 获取返点金额
     * @return float
     */
    public function getCommissionAmount() {
        return $this->amount * $this->commission;
    }

    protected function afterValidate() {
        $sBetNumber = $this->bet_number;
        if (isset($sBetNumber) && $this->is_encoded) {
            $sBetNumberEncode = Encrypt::db_encode($sBetNumber);

            $sBetNumberKey = md5($sBetNumberEncode);
            $sCacheKey = 'BetNumberDeocde_' . $sBetNumberKey;
            Cache::put($sCacheKey, $sBetNumber, 1800);

            $this->bet_number = $sBetNumberEncode;
        }
    }

    public function getBetNumberAttribute($sBetNumber) {
        if ($this->getOriginal('is_encoded') && isset($sBetNumber)) {
            static $aBetNumber = [];
            $sBetNumberKey = md5($sBetNumber);
            if (!isset($aBetNumber[$sBetNumberKey])) {
                $sCacheKey = 'BetNumberDeocde_' . $sBetNumberKey;
                if (Cache::has($sCacheKey)) {
                    $sBetNumberDecode = Cache::get($sCacheKey);
                } else {
                    $sBetNumberDecode = Encrypt::db_decode($sBetNumber);
                    Cache::put($sCacheKey, $sBetNumberDecode, 1800);
                }
                $aBetNumber[$sBetNumberKey] = $sBetNumberDecode;
            }
            return $aBetNumber[$sBetNumberKey];
        }
        return $sBetNumber;
    }

}
