<?php

class AdminLog extends BaseModel {

    protected $table = 'admin_logs';
    public static $resourceName = 'AdminLog';
    protected $fillable = [
        'functionality_id',
        'functionality_title',
        'controller',
        'action',
        'admin_id',
        'admin_name',
        'request_uri',
        'request_data',
    ];
    public static $columnForList = [
        'functionality_title',
        'controller',
        'action',
        'admin_name',
        'created_at',
    ];
    public $orderColumns = [
        'created_at' => 'desc'
    ];
    public static $rules = [
//        'user_id' => 'required|numeric',
//        'functionality_id' => 'required|numeric',
//        'functionality' => 'required|between:1,50',
//        'admin_id' => 'required|numeric',
//        'admin' => 'required|between:4,16',
//        'comment_admin_id' => 'numeric',
//        'comment_admin' => 'between:4,16',
    ];

    public static function createLog($iUserId, $iFunctionalityId, $sFunctionality) {
        $iAdminId = Session::get('admin_user_id');
        $sAdminName = Session::get('admin_username');
        if (!$iAdminId || !$sAdminName || !$iUserId || !$iFunctionalityId || !$sFunctionality) {
            return false;
        }
        $aParam = [
            'user_id' => $iUserId,
            'functionality_id' => $iFunctionalityId,
            'functionality' => $sFunctionality,
            'admin_id' => $iAdminId,
            'admin' => $sAdminName,
        ];
        $oModel = new UserManageLog;
        $oModel->fill($aParam);
        $bSucc = $oModel->save();
        // if (! $bSucc) {
        //     pr($oModel->validationErrors);exit;
        // }
        return $bSucc;
    }

    public static function updateComments($aParams) {
        if (!$aParams)
            return false;
        $aIds = array_keys($aParams);
        // $aComments = array_values($aParams);
        $aModels = self::whereIn('id', $aIds)->get();
        // pr($aIds);
        // pr($aModels->toArray());
        // exit;
        $iAdminId = Session::get('admin_user_id');
        $sAdminName = Session::get('admin_username');
        foreach ($aModels as $oModel) {
            $sComment = $aParams[$oModel->id];
            $data = [
                'comment_admin_id' => $iAdminId,
                'comment_admin' => $sAdminName,
                'comment' => e($sComment),
            ];
            $bSucc = $oModel->update($data);
            if (!$bSucc)
                break;
        }
        return $bSucc;
    }

}
