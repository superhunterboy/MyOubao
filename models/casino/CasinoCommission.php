<?php

class CasinoCommission extends BaseModel {

    public static $resourceName      = 'CasinoCommission';
    protected $table                 = 'casio_commissions';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
            'user_id',
            'account_id',
            'username',
            'father_ids',
            'project_id',
            'project_no',
            'lottery_id',
            'way_id',
            'amount',
            'status',
            'locked',
            'sent_at',
            'created_at',
            'updated_at',

    ];
    public static $htmlSelectColumns = [];

    const STATUS_WAIT   = 0;
    const STATUS_SENDING = 1;
    const STATUS_SENT    = 2;
    const STATUS_DROPED  = 4;

    public static $validStatuses = [
        self::STATUS_WAIT   => 'Waiting',
        self::STATUS_SENDING => 'Sending',
        self::STATUS_SENT   => 'Sent',
        self::STATUS_DROPED => 'Canceled',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'project_id'    => 'required|integer',
        'user_id'       => 'required|integer',
        'project_no'    => 'required|max:32',
        'lottery_id'    => 'required|integer',
        'way_id'        => 'required|integer',
        'amount'        => 'required|numeric',
        'status'        => 'in:0,1,2,3',
        'sent_at'       => 'date_format:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'username',
        'father_ids',
        'project_id',
        'project_no',
        'lottery_id',
        'way_id',
        'amount',
        'status',
        'locked',
        'sent_at',
        'created_at',
        'updated_at',
    ];

    /**
     * add commission set of projects
     * @param array $aSetting
     * @return bool
     */
    public static function addCommission($aSetting){
        $oPrjCommission = new Commission($aSetting);
        return $oPrjCommission->save();
    }

    protected function setAmountAttribute($fAmount){
        $this->attributes[ 'amount' ] = formatNumber($fAmount,6);
    }

    protected function setBaseAmountAttribute($fAmount){
        $this->attributes[ 'base_amount' ] = formatNumber($fAmount,6);
    }

    /**
     * 更新状态为撤单
     * @return bool
     */
    public static function setDroped($iProjectId){
        if ($iCount = self::getCount($iProjectId)){
            return self::setStatus($iProjectId,self::STATUS_DROPED,self::STATUS_WAIT);
        }
        return true;
    }

    /**
     * 更新状态
     *
     * @param int $iToStatus
     * @param int $iFromStatus
     * @return bool
     */
    protected static function setStatus($iProjectId, $iToStatus, $iFromStatus){
        return self::where('project_id' , '=', $iProjectId)->where('status', '=', $iFromStatus)->where('status', '<>', $iToStatus)->update(['status' => $iToStatus]);
    }

    /**
     * 返回指定状态的记录数
     * @param int $iProjectId
     * @param int $iStatus
     * @return int
     */
    protected static function getCount($iProjectId,$iStatus = self::STATUS_WAIT){
        return self::where('project_id' , '=', $iProjectId)->where('status', '=', $iStatus)->count();
    }


    /**
     * 返回指定奖期指定状态的记录数
     * @param int $iProjectId
     * @param int $iStatus
     * @return int
     */
    public static function getCountOfIssue($iLotteryId,$sIssue,$iStatus = self::STATUS_WAIT){
        $aConditions             = [
            'lottery_id' => ['=',$iLotteryId],
            'issue'      => ['=',$sIssue],
        ];
        is_null($iStatus) or $aConditions[ 'status' ] = ['=',$iStatus];
        return self::doWhere($aConditions)->count();
    }

    public static function countCommission($fDiffPrizeSet,$fTheoreticPrize,$fBetAmount){
        $fRate = formatNumber($fDiffPrizeSet / $fTheoreticPrize,4);
        return formatNumber($fRate * $fBetAmount,6);
    }

    /**
     * 返回指定注单的返点详情
     * @param int $iProjectId
     * @param int|null $iStatus
     * @return Containor|PrjPrizeSet
     */
    public static function getDetailsOfProject($iProjectId,$iStatus = self::STATUS_WAIT){
        $aConditions = [
            'project_id' => ['=',$iProjectId]
        ];
        is_null($iStatus) or $aConditions['status'] = ['=', $iStatus];
        return self::doWhere($aConditions)->get();
    }

    /**
     * 发放佣金
     * @param Project $oProject
     * @param array $aUsers
     * @param array $aAccounts
     * @return int              err code
     */
    public function send($oProject,& $aUsers,& $aAccounts){
        $aExtraData                 = $oProject->getAttributes();
        $aExtraData[ 'project_id' ] = $oProject->id;
        $aExtraData[ 'project_no' ] = $oProject->serial_number;
        unset($aExtraData[ 'id' ]);
//        $oProject->getAttributes();
        if($this->user_id != $oProject->user_id)
            $iReturn                    = Transaction::addTransaction($aUsers[ $this->user_id ],$aAccounts[ $this->account_id ],TransactionType::TYPE_SEND_COMMISSION,$this->amount,$aExtraData);
        else
            $iReturn                    = Transaction::addTransaction($aUsers[ $this->user_id ],$aAccounts[ $this->account_id ],TransactionType::TYPE_BET_COMMISSION,$this->amount,$aExtraData);
        return ($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL) ? $this->setToSent() : false;
    }

    public function setToSent(){
        $aConditions = [
            'id'     => ['=',$this->id],
            'status' => ['=',self::STATUS_WAIT],
        ];
        $data        = [
            'status'  => self::STATUS_SENT,
            'sent_at' => Carbon::now()->toDateTimeString()
        ];
        return $this->doWhere($aConditions)->update($data) > 0;
    }

    public static function getTeamCommissionContribution($aUserIds, $aUserPrizeGroups, $iParentId, $iParentPrizeGroup) {
        $aColumns = ['user_id', 'project_id', 'prize_set', 'base_amount', 'amount'];
        $sStartTime = Carbon::parse('first day of this month')->toDateString() . ' 00:00:00';
        // $sStartTime = '2014-10-10 00:00:00';
        $oRecords = self::whereIn('user_id', $aUserIds)->where('user_forefather_ids', '=', $iParentId)->where('sent_at', '>=', $sStartTime)->get($aColumns);
        $aContributions = [];
        foreach ($oRecords as $key => $oRecord) {
            if (isset($aContributions[$oRecord->user_id])) {
                $aContributions[$oRecord->user_id] += $oRecord->amount;
            } else {
                $aContributions[$oRecord->user_id] = 0;
            }
        }
        return $aContributions;
    }

}
