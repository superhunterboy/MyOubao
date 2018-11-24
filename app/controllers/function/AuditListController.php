<?php

class AuditListController extends AdminBaseController {

    protected $modelName = 'AuditList';
    protected $iPasswordTypeId = 1;

    // protected $resourceView = 'admin.audit';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aAuditTypes = AuditType::getAuditTypes();
        $aStatus = [];
        foreach($sModelName::$statusDesc as $key => $value) {
            $aStatus[$key] = __('_auditlist.' . $value);
        }
        // switch($this->action){
        //     case 'index':
        //         break;
        // }
        $this->setVars(compact('aAuditTypes', 'aStatus'));
        // pr($this->viewVars['aStatus']);exit;
    }

    public function index(){
        $this->params['type_id']=$this->iPasswordTypeId;
        return parent::index();
    }

    /**
     * [updateAuditRecord 新增审核记录, 或修改审核状态]
     * @param  [Array] $data [审核记录结构的数据]
     * @return [Boolean]       [成功/失败]
     */
    private function updateAuditRecord() {
        $aRules = AuditList::$rules;
        $aRules['status'] = 'required|' . $aRules['status'];
        $bSucc = false;
        if ($bSucc = $this->model->save($aRules)) {
            // pr($this->model->toArray());exit;
            if ($this->model->status == AuditList::STATUS_AUDITED) {
                $oUser = User::find($this->model->user_id);
                if ($oUser) {
                    if ($bSucc = $this->model->explodeParams($oUser)) {
                        // $aRules = User::$rules;
                        // $aRules['username'] = str_replace('{:id}', $oUser->id, $aRules['username'] );
                        // pr($oUser->toArray());exit;
                        $bSucc = $oUser->save();
                    }
                }
            }
        }
        // pr($bSucc);exit;
        return $bSucc;
    }

    /**
     * [changeStatus 改变审核记录状态]
     * @param  [Int] $id   [记录id]
     * @param  [Int] $iStatus [状态类型，见AuditList 的Model]
     * @return [Response]       [框架的响应]
     */
    private function changeStatus($id, $iStatus) {
        $this->model = $this->model->find($id);
        if ($this->model->type_id != $this->iPasswordTypeId){
            return $this->goBack('error', '');
        }
        if (!$this->model->exists) {
            $sMsg = __(sprintf('%s not exists', $this->resourceName));
            return $this->goBack('error', $sMsg);
        }
        // $iStatus = $this->model->status;
        $sNowStatusDesc = AuditList::$statusDesc[$iStatus];
        $sOldStatusDesc = AuditList::$statusDesc[$this->model->status];
        // pr($sNowStatusDesc . '---' . $sOldStatusDesc);exit;
        if ($this->model->status != AuditList::STATUS_IN_AUDITING) {
            return $this->goBack('error', __($sNowStatusDesc . ' failed. Record has been ' . $sOldStatusDesc . '.'), true);
        }
        // $data = ['id' => $id, 'status' => $iStatus];
         $data = ['status' => $iStatus];
        if (in_array($iStatus, [AuditList::STATUS_AUDITED, AuditList::STATUS_REJECTED])) {
            $data['auditor_id'] = Session::get('admin_user_id');
            $data['auditor_name'] = Session::get('admin_username');
        }
//        $iUserId = $this->model->user_id;
        $this->model->fill($data);
        // pr($this->model->toArray());exit;
        DB::connection()->beginTransaction();
        $bSucc = $this->updateAuditRecord();
        if ($bSucc) {
            DB::connection()->commit();
        } else {
            DB::connection()->rollBack();
        }
        return $this->renderReturn($bSucc, AuditList::$statusDesc[$iStatus]);
    }

    /**
     * [renderReturn description]
     * @param  [Boolean] $bSucc [成功/失败]
     * @param  [Int]     $sDesc [状态类型描述]
     * @return [Response]       [框架的响应]
     */
    private function renderReturn($bSucc, $sDesc) {
        // pr((int)$bSucc . '------------' . $sDesc);exit;
        if ($bSucc) {
            return $this->goBack('success', __('_auditlist.change-status-success',['status' => __('_auditlist.' . $sDesc)]));
        } else {
            return $this->goBack('error', __('_auditlist.change-status-fail', ['status' => __('_auditlist.' . $sDesc)]), true);
        }
    }

    /**
     * [addAuditRecord 增加一条审核记录]
     * @param [Array] $data [数据]
     */
    public function addAuditRecord($data) {
        DB::connection()->beginTransaction();
        $this->model->fill($data);
        $bSucc = $this->updateAuditRecord();
        if ($bSucc) {
            DB::connection()->commit();
        } else {
            DB::connection()->rollBack();
        }
        return $bSucc;
    }

    /**
     * [audit 审核通过]
     * @param  [Int] $id [审核记录id]
     * @return [type]     [description]
     */
    public function audit($id) {
        return $this->changeStatus($id, AuditList::STATUS_AUDITED);
    }

    /**
     * [reject 拒绝一条审核记录]
     * @param  [Int] $id [审核记录id]
     * @return [type]     [description]
     */
    public function reject($id) {
        return $this->changeStatus($id, AuditList::STATUS_REJECTED);
    }

    /**
     * [cancel 取消一条审核记录, 在该审核通过或拒绝之前, 添加该审核的管理员可修改其状态为取消]
     * @param  [Int] $id [审核记录id]
     * @return [type]     [description]
     */
    public function cancel($id) {
        $oAuditList = AuditList::find($id);
        $iAdminId = Session::get('admin_user_id');
        // 只能取消自己提交的审核记录
        if ($oAuditList->admin_id != $iAdminId) {
            return $this->goBack('error', __('_audit_list.not-your-record'));
        }
        return $this->changeStatus($id, AuditList::STATUS_CANCELED);
    }

}
