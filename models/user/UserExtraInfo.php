<?php

/**
 * Description of UserExtraInfo
 *
 */
class UserExtraInfo extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_extra_info';
    public static $resourceName = 'UserExtraInfo';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    
      /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'user_id' => 'aUsers',
    ];

    /**
     * 可写字段
     * @var array 
     */
    protected $fillable = [
        'user_id',
        'username',
        'contribution',
    ];

    /**
     * 字段验证规则
     * @var array 
     */
    public static $rules = [
        'user_id' => 'required|integer|min:1',
        'username' => 'required|alpha_num|between:6,16',
        'contribution' => 'numeric',
    ];
    public static $columnForList = [
        'username',
        'contribution',
    ];

    /**
     * 获取用户扩展信息
     * @param User $oUser
     * @return UserExtraInfo
     */
    public static function findByUser(User $oUser) {
        if (!$oUser) {
            return false;
        }
        $oUserExtraInfo = self::where('user_id', '=', $oUser->id)->first();
        if(!$oUserExtraInfo) {
            $aNewUserExtraInfo = [
                'user_id' => $oUser->id, 
                'username' => $oUser->username, 
                'contribution' => 0,
            ];
            $oUserExtraInfo = new UserExtraInfo($aNewUserExtraInfo);
            $oUserExtraInfo->save();
        }
        return $oUserExtraInfo;
    }

    /**
     * 扣减贡献值
     * @param float $fContribution
     * @return boolean
     */
    public function deductContribution($fContribution) {
        $fContribution = floatval($fContribution);
        if ($fContribution <= 0) {
            return false;
        }
        $iAffectRows = self::where('id', '=', $this->id)
                ->decrement('contribution', $fContribution);
//        dd(DB::getQueryLog());
        $iAffectRows == 1 && $this->contribution -= $fContribution;
        return $iAffectRows == 1;
    }

    /**
     * 增加贡献值
     * @param float $fContribution
     * @return boolean
     */
    public function increaseContribution($fContribution) {
//        file_put_contents('/tmp/user.log', $fContribution."\n", FILE_APPEND);
        $fContribution = floatval($fContribution);
        if ($fContribution <= 0) {
            return false;
        }
        $iAffectRows = self::where('id', '=', $this->id)
                ->increment('contribution', $fContribution);
//        dd(DB::getQueryLog());
        return $iAffectRows == 1;
    }

}
