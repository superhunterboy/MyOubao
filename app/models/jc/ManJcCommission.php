<?php
namespace JcModel;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-7
 * Time: 上午10:51
 */
class ManJcCommission extends JcCommission
{
    
    public static function setStatusCancel($bet_id){
        $aCommission = self::where('bet_id','=',$bet_id)->get();
        if(!$aCommission->count()){
            return false;
        }
        $bSucc = self::where('bet_id','=',$bet_id)->update(array('status'=>self::STATUS_DROPED));
        return $bSucc;
    }

    public function setToSent(){
        $aConditions = [
            'id'     => ['=',$this->id],
            'status' => ['=',self::STATUS_NORMAL],
        ];
        $data        = [
            'status'  => self::STATUS_SENT,
            'sent_at' => \Carbon::now()->toDateTimeString()
        ];
        return $this->doWhere($aConditions)->update($data) > 0;
    }

    public function send($oProject, $oUser, $oAccount){
        $aExtraData = $oProject->getTransactionData();
        
        $fAmount = $this->amount;
        if ($fAmount <= 0){
            return false;
        }
        $iTransactionType = $oProject->user_id == $oUser->id ? \TransactionType::TYPE_BET_COMMISSION : \TransactionType::TYPE_SEND_COMMISSION;
        
        $iReturn = \Transaction::addTransaction($oUser,$oAccount,$iTransactionType,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    public function saveCommission(){
        return $this->save();
    }


    public static function compileCommissions($oProject, $fSingleAmount = 0, $fMixAmount = 0){
        $aBasicData = [
            'project_id' => $oProject->id,
            'bet_id' => $oProject->bet_id,
            'group_id' => $oProject->group_id,
            'coefficient' => $oProject->coefficient,
            'serial_number' => $oProject->serial_number,
            'lottery_id' => $oProject->lottery_id,
            'multiple' => $oProject->multiple,
            'base_amount' => $fSingleAmount + $fMixAmount,
        ];
        $iUserId = $oProject->user_id;

        $oUser = \User::find($iUserId);
        if(!$oUser) {
            return [];
        }
        $aForefatherIds = [];
        if ($oUser->forefather_ids){
            $aForefatherIds = explode(',', $oUser->forefather_ids);
        }

        array_push($aForefatherIds, $iUserId);
        if(empty($aForefatherIds)){
            return [];
        }

//        $aCommissionUsers = ManJcCommissionUser::getByUserIdsWithUser($aForefatherIds);
//
//        if(count($aCommissionUsers) != count($aForefatherIds)){
//            return [];
//        }
//        $aSortCommissionUsers = [];
//        foreach($aCommissionUsers as $oCommissionUser){
//            $aUserIds[] = $oCommissionUser->user_id;
//            $aSortCommissionUsers[] = $oCommissionUser;
//        }
//        array_multisort($aUserIds, SORT_DESC, $aSortCommissionUsers);

        rsort($aForefatherIds);
        
        $fLastSingleCommissionRate = 0;
        $fLastMixCommissionRate = 0;
        $aCommissions = [];
        foreach($aForefatherIds as $iUserId){
            $aCommissionSets = \UserCommissionSet::getSportUserCommissionSets($iUserId, $oProject->lottery_id);
            if(!isset($aCommissionSets['single']) || $aCommissionSets['single']->commission_rate < $fLastSingleCommissionRate){
                break;
            }
            if(!isset($aCommissionSets['mix']) || $aCommissionSets['mix']->commission_rate < $fLastMixCommissionRate){
                break;
            }
            $oForeUser = \User::find($iUserId);
            if (empty($oForeUser)){
                break;
            }
            $fSingleCommissionRateSet = $aCommissionSets['single']->commission_rate;
            $fMixCommissionRateSet = $aCommissionSets['mix']->commission_rate;
            
            $fSingleCommissionRate = $fSingleCommissionRateSet - $fLastSingleCommissionRate;
            $fMixCommissionRate = $fMixCommissionRateSet - $fLastMixCommissionRate;

            $fCommission = $fSingleAmount * $fSingleCommissionRate / 100 + $fMixAmount * $fMixCommissionRate / 100;
            $fLastSingleCommissionRate = $fSingleCommissionRateSet;
            $fLastMixCommissionRate = $fMixCommissionRateSet;
            if ($fCommission <= 0){
                continue;
            }
            $aCommissionData = [
                'user_id' => $oForeUser->id,
                'account_id' => $oForeUser->account_id,
                'username' => $oForeUser->username,
                'amount' => $fCommission,
                'is_tester' => $oForeUser->is_tester,
                'user_forefather_ids' => $oForeUser->forefather_ids,
            ];
            $aCommissions[] = array_merge($aBasicData, $aCommissionData);
        }

        return $aCommissions;
    }
    
    public static function getUnSendListByProjectIds($aProjectIds = []){
        $aConditions = [
            'project_id' => ['in',$aProjectIds],
            'status' => ['=',ManJcCommission::STATUS_NORMAL]
        ];
        return self::doWhere($aConditions)->get();
    }
    
    public static function getUncalculateByProjectIds($ids){
        $aBets = self::whereIn('project_id',$ids)->where('status','=',self::STATUS_NORMAL)->get();
        return $aBets;
    }

    public static function setCommissionStatusCalculated($iBetId){
        $oBet = ManJcBet::find($iBetId);
        $oBet->commission_status = ManJcBet::COMMISSION_STATUS_CALCULATED;
        return $oBet->save();
    }

    public static function CountUnSentCommission($iProjectId){
        $aConditions = [
            'project_id'     => ['=',$iProjectId],
            'status' => ['=',self::STATUS_NORMAL],
        ];
        return self::doWhere($aConditions)->count();
    }
}