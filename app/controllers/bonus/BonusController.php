<?php

class BonusController extends AdminBaseController {
    /**
     * 资源视图目录
     * @var string 
     */

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'Bonus';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aStatus', Bonus::$aStatus);
        
        switch ($this->action) {
            case 'index':
                $this->resourceView = 'fund.bonus';
            case 'view':
            case 'edit':
            case 'create':
                break;
        }
    }

    /**
     * 分红审核
     * @param type $id      分红 id
     */
    public function auditBonus($id) {
        $oBonus = Bonus::find($id);
        if ($oBonus->status != Bonus::STATUS_WAITING_AUDIT) {
            return $this->goBackToIndex('error', __('_bonus.status-error'));
        }
        if (!is_object($oBonus)) {
            return $this->goBack('error', __('_bonus.missing-bonus'));
        }
        $oBonus->status = Bonus::STATUS_AUDIT_FINISH;
        $oBonus->auditor_id = Session::get('admin_user_id');
        $oBonus->auditor = Session::get('admin_username');
        $oBonus->verified_at = date('Y-m-d H:i:s');
        $bSucc = $oBonus->save();
        if ($bSucc) {
            if (SysConfig::check('bonus_auto_send_after_audited', true)) {
                return $this->sendBonus($id);
            }
            return $this->goBackToIndex('success', __('_bonus.bonus-audited'));
        } else {
            return $this->goBack('error', __('_bonus.bonus-audited-fail'));
        }
    }

    /**
     * 派发分红
     * @param type $id      分红 id
     */
    public function sendBonus($id) {
        $oBonus = Bonus::find($id);
        if ($oBonus->status != Bonus::STATUS_AUDIT_FINISH) {
            return $this->goBackToIndex('error', __('_bonus.status-error'));
        }
        if (!is_object($oBonus)) {
            return $this->goBack('error', __('_bonus.missing-bonus'));
        }
        $oUser = User::find($oBonus->user_id);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        $oAccount = Account::lock($oUser->account_id, $iLocker);
        if (empty($oAccount)) {
            $oMessage = new Message($this->errorFiles);
            return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }
        DB::connection()->beginTransaction();
        $bSucc = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_SEND_BONUS, $oBonus->bonus) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
        if ($bSucc) {
            $oBonus->status = Bonus::STATUS_BONUS_SENT;
            $oBonus->sent_at = date('Y-m-d H:i:s');
            $bSucc = $oBonus->save();
            $bSucc ? DB::connection()->commit() : DB::connection()->rollback();
        }
        Account::unLock($oUser->account_id, $iLocker, false);
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_bonus.bonus-sent'));
        } else {
            return $this->goBack('error', __('_bonus.bonus-sent-fail'));
        }
    }

    /**
     * 拒绝审核
     * @param type $id      bonus id
     */
    public function rejectBonus($id) {
        $oBonus = Bonus::find($id);
        if ($oBonus->status != Bonus::STATUS_WAITING_AUDIT) {
            return $this->goBackToIndex('error', __('_bonus.status-error'));
        }
        $aValidateData = ['note' => $this->params['note']];
        $aValidateRule = ['note' => Bonus::$rules['note']];
        $validator = Validator::make($aValidateData, $aValidateRule);
        if (!$validator->passes()) {
            return $this->goBack('error', __('_bonus.note-validate-error'));
        }
        if (is_object($oBonus)) {
            $oBonus->status = Bonus::STATUS_AUDIT_REJECT;
            $oBonus->note = $this->params['note'];
            $oBonus->auditor_id = Session::get('admin_user_id');
            $oBonus->auditor = Session::get('admin_username');
            $oBonus->verified_at = date('Y-m-d H:i:s');
            $bSucc = $oBonus->save();
            if ($bSucc) {
                return $this->goBackToIndex('success', __('_bonus.bonus-rejected'));
            } else {
                return $this->goBackToIndex('error', __('_bonus.bonus-rejected-fail'));
            }
        } else {
            return $this->goBackToIndex('error', __('_bonus.missing-bonus'));
        }
    }

}
