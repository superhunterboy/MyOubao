<?php

namespace JcModel;
/**
 * 注单模型
 */
class JcCommissionUser extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcCommissionUser';
    protected $table = 'jc_commission_user';


    protected $fillable = [
        'user_id',
        'single_rate',
        'multiple_rate'
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id'             => 'required|integer',
        'single_rate'             => 'required|numeric|min:0',
        'multiple_rate'              => 'required|numeric|min:0',
    ];
    
    public static function getByUserId($id){
        return self::where('user_id', $id)->limit(1)->first();
    }
    
    public static function getByUserIds($aIds){
        return self::whereIn('user_id', $aIds)->get();
    }
    
    public static function getByUserIdWithUser($id){
        return self::where('user_id', $id)->with('user')->limit(1)->first();
    }
    
    public static function getByUserIdsWithUser($aIds){
        return self::whereIn('user_id', $aIds)->with('user')->get();
    }
    
    public function user(){
        return $this->belongsTo('User');
    }

    public static function generateJcCommissionSettingData($single_rate,$multiple_rate){
        $data = [
            'jc_football_single_commission_rate' => $single_rate,
            'jc_football_multiple_commission_rate' => $multiple_rate
        ];
        return $data;
    }

    public function saveCommissionUser(){
        $iUserId = $this->user_id;
        $oUser = \User::find($iUserId);
        $iParentId = $oUser->parent_id;
        if ($iParentId){
            //有上级代理的情况下 不能大于父级代理的返点
            $oParentCommissionUser = self::getByUserId($iParentId);
            $iMaxSingleRate = 0;
            $iMaxMultipleRate = 0;
            if ($oParentCommissionUser){
                $iMaxSingleRate = $oParentCommissionUser->single_rate;
                $iMaxMultipleRate = $oParentCommissionUser->multiple_rate;
            }
            if ($this->single_rate > $iMaxSingleRate || $this->multiple_rate > $iMaxMultipleRate){
                return false;
            }
        }else{
            //todo 设置全局最大返点范围 总代?
        }
        return $this->save();
    }
    
    public static function getCommissionSetDataByUserId($iUserId){
        $aSetData = [
            'iPlayerMinJcSingleCommissionRate' => 0,
            'iPlayerMaxJcSingleCommissionRate' => 0,
            'iPlayerMinJcMultipleCommissionRate' => 0,
            'iPlayerMaxJcMultipleCommissionRate' => 0,
        ];
        $oCommissionUser = self::getByUserId($iUserId);
        if ($oCommissionUser){
            $aSetData['iPlayerMaxJcSingleCommissionRate'] = $oCommissionUser->single_rate * 100;
            $aSetData['iPlayerMaxJcMultipleCommissionRate'] = $oCommissionUser->multiple_rate * 100;
        }
        return $aSetData;
    }
}
