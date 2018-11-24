<?php

class ActivityUserPrizeController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityUserPrize';
    protected $customViewPath = 'activity.userPrize';
    protected $customViews = [
        'audit', 'view'
    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch ($this->action) {
            case 'index':
                $aPrize = ActivityPrize::getTitleList();
                $aStatus = ActivityUserPrize::$aStatus;
                $this->setVars('aPrizes', $aPrize);
                $this->setVars('aStatus', $aStatus);
            case 'view':
            case 'edit':
            case 'create':
                $aPrizeObj = ActivityPrize::getPrizeByPrizeClassId(6, ['id', 'name']);
                $aPrize = [];
                foreach ($aPrizeObj as $oPrize) {
                    $aPrize[$oPrize->id] = $oPrize->name;
                }
                $aUser = User::getAllUserNameArrayByUserType(User::TYPE_AGENT, 1);
                $this->setVars('aPrizes', $aPrize);
                $this->setVars('aUser', $aUser);
                $this->setVars('aActivities', Activity::getTitleList());
                $this->setVars('aStatuses', ActivityUserPrize::$aStatus);
                $this->setVars('aSources', ActivityUserPrize::$aSources);
                break;
        }
    }

    public function create($id = null) {
        Session::put($this->redictKey, route('activity-user-prizes.index'));
        if (Request::method() == 'POST') {
            $oActivityPrize = ActivityPrize::find(array_get($this->params, 'prize_id'));
            if (is_object($oActivityPrize)) {
                if ($oActivityPrize->prize_class_id != 6) {
                    return $this->goBack('error', __('_activityuserprize.send-prize-not-allowed'));
                }
            } else {
                return $this->goBack('error', __('_activityuserprize.missing-data'));
            }
            $oUser = User::find(array_get($this->params, 'user_id'));
            if (is_object($oUser)) {
                if ($oUser->parent_id) {
                    return $this->goBack('error', __('_user.topagent-allowed'));
                }
            } else {
                return $this->goBack('error', __('_user.missing-data'));
            }
        }
        return parent::create();
    }

    public function download() {
        $oQuery = $this->indexQuery();

        set_time_limit(0);

        $aConvertFields = [
            'status' => 'formatted_status',
            'created_at' => 'date',
            'source' => 'aSources',
            'status' => 'aStatuses',
            'is_verified' => 'aVerifyStatus',
            'is_tester' => 'boolean',
            'status' => 'status_formmatted',
        ];

        $aData = $oQuery->get(ActivityUserPrize::$columnForList);
        $aData = $this->_makeData($aData, ActivityUserPrize::$columnForList, $aConvertFields);
        return $this->downloadExcel(ActivityUserPrize::$columnForList, $aData, 'User Prize Report');
    }

    function _makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
//            pr($oDeposit->getAttributes());continue;
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'formatted_status':
                            $a[] = $oDeposit->formatted_status;
                            break;
                        case 'boolean':
                            $a[] = $oDeposit[$key] ? __('Yes') : __('No');
                            break;
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'aSources':
                            $a[] = ActivityUserPrize::$aSources[$oDeposit->$key];
                            break;
                        case 'aStatuses':
                            $a[] = ActivityUserPrize::$aStatus[$oDeposit->$key];
                            break;
                        case 'aVerifyStatus':
                            $a[] = ActivityUserPrize::$aVerifyStatus[$oDeposit->$key];
                            break;
                        case 'status_formmatted':
                            $a[] = $oDeposit->status_formmatted;
                            break;
                    }
                } else {
                    $a[] = $oDeposit->$key;
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

    /**
     * 受理审核
     */
    public function accept($id) {
        $oUserPrize = ActivityUserPrize::find($id);
        if (!is_object($oUserPrize)) {
            return $this->goBack('error', __('_activityuserprize.missing-data'));
        }
        if (!in_array($oUserPrize->status, [ActivityUserPrize::STATUS_NO_SEND])) {
            return $this->goBackToIndex('error', __('_activityuserprize.status-error'));
        }
        if ($oUserPrize->setAccected(Session::get('admin_user_id'))) {
            $this->view = $this->customViewPath . '.audit';
            $this->setVars(compact('oUserPrize'));
            ActivityUserPrize::deleteCache($oUserPrize->id);
            return $this->render();
        } else {
            return $this->goBackToIndex('error', __('_activityuserprize.accept-failed'));
        }
    }

    /**
     * 审核通过
     */
    public function audit($id) {
        $oUserPrize = ActivityUserPrize::find($id);
        if (!is_object($oUserPrize)) {
            return $this->goBack('error', __('_activityuserprize.missing-data'));
        }
        if (!in_array($oUserPrize->status, [ActivityUserPrize::STATUS_ACCEPTED])) {
            return $this->goBackToIndex('error', __('_activityuserprize.status-error'));
        }
        if ($oUserPrize->accepter_id != Session::get('admin_user_id')) {
            return $this->goBackToIndex('error', __('_activityuserprize.failed-wrong-administrator'));
        }
        if (Request::method() == 'POST') {
            $aExtraInfo = [
                'expired_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            ];
            $oPrize = $oUserPrize->prize()->first();
            if ($oUserPrize->setToVerified(Session::get('admin_user_id'), $aExtraInfo)) {
                ActivityUserPrize::deleteCache($oUserPrize->id);
                if (!$oPrize->need_get) {
                    $bSucc = $oUserPrize->addPrizeTask();
                }
                return $this->goBackToIndex('success', __('_activityuserprize.verified'));
            } else {
                return $this->goBackToIndex('error', __('_activityuserprize.verify-failed'));
            }
        } else {
            $this->setVars(compact('oUserPrize'));
            return $this->render();
        }
    }

    /**
     * 审核拒绝
     */
    public function reject($id) {
        $oUserPrize = ActivityUserPrize::find($id);
        if (!is_object($oUserPrize)) {
            return $this->goBack('error', __('_activityuserprize.missing-data'));
        }
        if (!in_array($oUserPrize->status, [ActivityUserPrize::STATUS_ACCEPTED])) {
            return $this->goBackToIndex('error', __('_activityuserprize.status-error'));
        }
        if ($oUserPrize->accepter_id != Session::get('admin_user_id')) {
            return $this->goBackToIndex('error', __('_activityuserprize.failed-wrong-administrator'));
        }
        $aExtraInfo = [
            'note' => array_get($this->params, 'note'),
        ];
        if ($oUserPrize->setToReject(Session::get('admin_user_id'), $aExtraInfo)) {
            ActivityUserPrize::deleteCache($oUserPrize->id);
            return $this->goBackToIndex('success', __('_activityuserprize.reject'));
        } else {
            return $this->goBackToIndex('error', __('_activityuserprize.reject-failed'));
        }
    }

    /**
     * [changeStatus 改变审核记录状态]
     * @param  [Int] $id   [记录id]
     * @param  [Int] $iStatus [状态类型，见AuditList 的Model]
     * @return [Response]       [框架的响应]
     */
    private function changeStatus($id, $iStatus) {
        $this->model = $this->model->find($id);
        if (!$this->model->exists) {
            $sMsg = __(sprintf('%s not exists', $this->remote_ip));
            return $this->goBack('error', $sMsg);
        }

        // $iStatus = $this->model->status;
        $sNowStatusDesc = ActivityUserPrize::$aVerifyStatus[$iStatus];
        $sOldStatusDesc = ActivityUserPrize::$aVerifyStatus[$this->model->is_verified];
        // pr($sNowStatusDesc . '---' . $sOldStatusDesc);exit;
        if ($this->model->is_verified != ActivityUserPrize::STATUS_NOT_VERIFIED) {
            return $this->goBack('error', __($sNowStatusDesc . ' failed. Record has been ' . $sOldStatusDesc . '.'), true);
        }

        $this->model->is_verified = $iStatus;

        return $this->renderReturn($this->model->save(), ActivityUserPrize::$aVerifyStatus[$iStatus]);
    }

    /**
     * [renderReturn description]
     * @param  [Boolean] $bSucc [成功/失败]
     * @param  [Int]     $sDesc [状态类型描述]
     * @return [Response]       [框架的响应]
     */
    private function renderReturn($bSucc, $sDesc) {
        if ($bSucc) {
            return $this->goBack('success', __('_activityuserprize.change-status-success', ['status' => __('_activityuserprize.' . $sDesc)]));
        } else {
            return $this->goBack('error', __('_activityuserprize.change-status-fail', ['status' => __('_activityuserprize.' . $sDesc)]), true);
        }
    }

}
