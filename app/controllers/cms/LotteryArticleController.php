<?php

class LotteryArticleController extends AdminBaseController {

    protected $modelName = 'LotteryArticle';
    protected $resourceView = 'cms.lotteryinfo';
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oCategory = new LotteryCategory;
        $aCategories = $oCategory->getTitleList();
        // pr($aCategories);exit;
        $this->setVars(compact('aCategories'));
        $aAdmins = & AdminUser::getTitleList();
        $aStatus = [];
        foreach(LotteryArticle::$aStatusDesc as $key => $value) {
            $aStatus[$key] = __('_cmsarticle.' . $value);
        }
        // pr($aStatus);exit;
        $this->setVars(compact('aAdmins', 'aStatus', 'aCategories'));
        switch ($this->action) {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
        }
    }
    
      /**
     * 下架文章
     */
    public function cancelArticle($id = null) {
        return $this->updateArticle($id, 'status', LotteryArticle::STATUS_RETRACT);
    }
    /**
     * 审核通过
     */
    public function audit($id = null) {
        return $this->updateArticle($id, 'status', LotteryArticle::STATUS_AUDITED);
    }

    /**
     * 审核拒绝
     */
    public function reject($id = null) {
        return $this->updateArticle($id, 'status', LotteryArticle::STATUS_REJECTED);
    }

    /**
     * 取消置顶文章
     */
    public function cancelTopArticle($id = null) {
        return $this->updateArticle($id, 'is_top', LotteryArticle::STATUS_TOP_OFF);
    }

    /**
     * 置顶文章
     */
    public function topArticle($id = null) {
        return $this->updateArticle($id, 'is_top', LotteryArticle::STATUS_TOP_ON);
    }

    private function updateArticle($id, $sField, $iStatus) {
        $oArticle = LotteryArticle::find($id);
        if (!is_object($oArticle)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }

        $sNowStatusDesc = LotteryArticle::$aStatusDesc[$iStatus];
        $sOldStatusDesc = LotteryArticle::$aStatusDesc[$oArticle->status];
        $aAuditArray = [LotteryArticle::STATUS_AUDITED, LotteryArticle::STATUS_REJECTED];
        // pr($sNowStatusDesc . '---' . $sOldStatusDesc);exit;
        if ($sField == 'status' &&
            (($oArticle->status != LotteryArticle::STATUS_NEW && in_array($iStatus, $aAuditArray))
            || ($iStatus != LotteryArticle::STATUS_RETRACT && in_array($oArticle->status, $aAuditArray)) ) ) {
            return $this->goBack('error', __($sNowStatusDesc . ' failed. Record has been ' . $sOldStatusDesc . '.'), true);
        }

        DB::connection()->beginTransaction();
        $oArticle->$sField = $iStatus;
        $oArticle->update_user_id = Session::get('admin_user_id', '');
        if ($bSucc = $oArticle->save()) {
            DB::connection()->commit();
            return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
        } else {
            DB::connection()->rollback();
            $this->langVars['reason'] = & $this->model->getValidationErrorString();
            return $this->goBack('error', __('_basic.update-fail', $this->langVars));
        }
    }
}
