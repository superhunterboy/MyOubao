<?php

# 追号

class MobileTraceController extends MobileBaseController {

    protected $modelName = 'UserTrace';

    /**
     * [index 自定义追号记录列表查询, 代理用户需要可以查询其子用户的记录]
     * @return [Response] [description]
     */
    public function index() {
        if ($iCount = count($this->params))
            $this->generateSearchParams($this->params);
        if (Session::get('is_agent')) {
            $oUser = User::find(Session::get('user_id'));
            $aUsers = $oUser->getUsersBelongsToAgent();
            $aUserIds = array_map(function ($item) {
                return $item['id'];
            }, $aUsers->toArray());
            $aUserIds[] = Session::get('user_id');
            $this->params['user_id'] = implode(',', $aUserIds);
        } else {
            $this->params['user_id'] = Session::get('user_id');
        }
        $data = parent::mobileIndex(ManTrace::$mobileColumns);
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
     * [view 查看追号记录的详情]
     * @param  [Integer] $id [追号记录id]
     * @return [Response]     [description]
     */
    public function view($id) {
        $data = parent::view($id);
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        Request::merge(['page' => array_get($this->params, 'page')]);
        $aTraceDetailList = TraceDetail::getListByTraceId($id, $iPageSize, TraceDetail::$mobileColumns);
        $data = array_intersect_key($data, array_flip(array_merge(ManTrace::$mobileColumns, ['coefficient'])));
        $data['detail_list'] = $aTraceDetailList->toArray();
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
     * 终止追号任务
     * @param int $iTraceId
     * @return Redirect
     */
    public function drop($iTraceId) {
        $oTrace = Trace::find($iTraceId);
        if ($oTrace->user_id != Session::get('user_id')) {
            $this->halt(false, 'error', Trace::ERRNO_STOP_ERROR_NOT_YOURS);
        }
        if ($oTrace->status != Trace::STATUS_RUNNING) {
            $this->halt(false, 'error', Trace::ERRNO_STOP_ERROR_STATUS);
        }
        $oAccount = Account::lock($oTrace->account_id, $iLocker);
        if (empty($oAccount)) {
            $this->halt(false, 'error', Trace::ERRNO_STOP_ERROR_STATUS_UPDATE_ERROR);
        }
        $oUser = User::find($oTrace->user_id);
        $oTrace->setAccount($oAccount);
        $oTrace->setUser($oUser);
        $DB = DB::connection();
        $DB->beginTransaction();
        if (($iReturn = $oTrace->terminate()) === true) {
            $DB->commit();
            $sLangKey = Trace::ERRNO_STOP_SUCCESS;
            $sMsgType = 'success';
        } else {
            $DB->rollback();
            $sLangKey = $iReturn;
            $sMsgType = 'error';
        }
        Account::unLock($oTrace->account_id, $iLocker, false);
        $this->halt($iReturn > 0, $sMsgType, $sLangKey);
    }

    /**
     * 撤单
     * @param int $iTraceId
     * @param array $aDetailId
     * @return Redirect
     */
    public function cancel($iTraceId) {
        $oTrace = UserTrace::find($iTraceId);
        if (!is_object($oTrace)) {
            $this->halt(false, 'error', Trace::ERRNO_TRACE_MISSING);
        }
        if ($oTrace->user_id != Session::get('user_id')) {
            $this->halt(false, 'error', Trace::ERRNO_STOP_ERROR_NOT_YOURS);
        }
        $oAccount = Account::lock($oTrace->account_id, $iLocker);
        if (empty($oAccount)) {
            $this->halt(false, 'error', Trace::ERRNO_STOP_ERROR_STATUS_UPDATE_ERROR);
        }
        $oUser = User::find($oTrace->user_id);
        $oTrace->setAccount($oAccount);
        $oTrace->setUser($oUser);
        $DB = DB::connection();
        $DB->beginTransaction();
        $aDetailIds = array_get($this->params, 'aDetailIds');
        if ($aDetailIds && count($aDetailIds) > 0) {
            if ($iReturn = $oTrace->cancelDetail($aDetailIds) == Trace::ERRNO_DETAIL_CANCELED) {
                $DB->commit();
                $sLangKey = Trace::ERRNO_DETAIL_CANCELED;
                $sMsgType = 'success';
            } else {
                $DB->rollback();
                $sLangKey = Trace::ERRNO_DETAIL_CANCEL_FAILED;
                $sMsgType = 'error';
            }
        } else {
            if (($iReturn = $oTrace->terminate()) === true) {
                $DB->commit();
                $sLangKey = Trace::ERRNO_STOP_SUCCESS;
                $sMsgType = 'success';
            } else {
                $DB->rollback();
                $sLangKey = $iReturn;
                $sMsgType = 'error';
            }
        }
        Account::unLock($oTrace->account_id, $iLocker, false);
        $this->halt($iReturn > 0, $sMsgType, $sLangKey);
    }

    private function generateSearchParams(& $aParams) {
        if (isset($aParams['number_type']) && isset($aParams['number_value'])) {
            $aParams[$aParams['number_type']] = $aParams['number_value'];
        }
        unset($aParams['way_group_id'], $aParams['number_type'], $aParams['number_value']);
    }

}
