<?php

/**
 * 用户返点表
 *
 * @author wallace
 */
class UserCommission extends BaseModel {

    protected $table = 'user_commissions';
    public static $resourceName = 'UserCommission';

    protected $fillable = [
        'date',
        'user_id',
        'username',
        'is_tester',
        'status',
        'commission_type',
        'commission',
    ];

    public static $rules = [
        'date' => 'required:date',
        'user_id' => 'integer',
        'username' => 'required',
        'is_tester' => 'in:0, 1',
        'status' => 'in:0,1,2',
        'commission_type' => 'required|integer',
        'commission' => 'numeric',
    ];

    public $orderColumns = [
        'date' => 'desc',
        'status' => 'asc',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'user_id',
        'username',
        'is_tester',
        'status',
        'commission_type',
        'commission',
    ];

    public static $htmlSelectColumns = [
        'commission_type' => 'aCommissionType',
        'status' => 'aStatus', // 0:审核中, 1: 审核通过, 2: 审核拒绝, 3: 撤销密码重置
    ];

    const STATUS_WAITING_AUDIT = 0;
    const STATUS_AUDIT_FINISH = 1;
    const STATUS_AUDIT_REJECT = 2;
    const STATUS_BONUS_SENT = 3;

    const COMMISSION_TYPE_DEPOSIT = 1;
    const COMMISSION_TYPE_TURNOVER = 2;
    const COMMISSION_TYPE_PROFIT = 3;

    public static $aStatus = [
        self::STATUS_WAITING_AUDIT => 'waiting audit',
        self::STATUS_AUDIT_FINISH => 'audited',
        self::STATUS_AUDIT_REJECT => 'rejected',
        self::STATUS_BONUS_SENT => 'sent',
    ];

    public static $aCommissionType = [
        self::COMMISSION_TYPE_DEPOSIT => 'deposit commission',
        self::COMMISSION_TYPE_TURNOVER => 'turnover commission',
        self::COMMISSION_TYPE_PROFIT => 'profit commission',
    ];

    public static $aToTransactionType = [
        self::COMMISSION_TYPE_DEPOSIT => TransactionType::TYPE_DEPOSIT_COMMISSION,
        self::COMMISSION_TYPE_TURNOVER => TransactionType::TYPE_TURNOVER_COMMISSION,
        self::COMMISSION_TYPE_PROFIT => TransactionType::TYPE_PROFIT_COMMISSION,
    ];

    protected function getFriendlyStatusAttribute() {
        return __('_usercommission.' . self::$aStatus[$this->status]);
    }

    public function changeStatus($iFromStatus, $iToStatus) {
        $aExtraData['status'] = $iToStatus;
        $bSucc = self::where('id', '=', $this->id)->where('status', '=', $iFromStatus)->update($aExtraData);
        return $bSucc;
    }
}
