<?php

class CmsArticleController extends AdminBaseController {

    protected $modelName = 'CmsArticle';
    protected $resourceView = 'cms.article';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oCategory = new CmsCategory;
        $aCategories = $oCategory->getTitleList();
        // pr($aCategories);exit;
        $this->setVars(compact('aCategories'));
        $aAdmins = & AdminUser::getTitleList();
        $aStatus = [];
        foreach(CmsArticle::$aStatusDesc as $key => $value) {
            $aStatus[$key] = __('_cmsarticle.' . $value);
        }
        // pr($aStatus);exit;
        $this->setVars(compact('aAdmins', 'aStatus'));
        switch ($this->action) {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
        }
    }

    /**
     * 创建文章内容
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            // pr(Input::all() );exit;
            DB::connection()->beginTransaction();
            if ($bSucc = $this->saveData($id)) {

                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;
            if ($sModelName::$treeable) {
                $sFirstParamName = 'parent_id';
            } else {
                foreach ($this->paramSettings as $sFirstParamName => $tmp) {
                    break;
                }
            }
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));

            return $this->render();
        }
    }

    /**
     * 下架文章
     */
    public function cancelArticle($id = null) {
        return $this->updateArticle($id, 'status', CmsArticle::STATUS_RETRACT);
    }
    /**
     * 审核通过
     */
    public function audit($id = null) {
        return $this->updateArticle($id, 'status', CmsArticle::STATUS_AUDITED);
    }

    /**
     * 审核拒绝
     */
    public function reject($id = null) {
        return $this->updateArticle($id, 'status', CmsArticle::STATUS_REJECTED);
    }

    /**
     * 取消置顶文章
     */
    public function cancelTopArticle($id = null) {
        return $this->updateArticle($id, 'is_top', CmsArticle::STATUS_TOP_OFF);
    }

    /**
     * 置顶文章
     */
    public function topArticle($id = null) {
        return $this->updateArticle($id, 'is_top', CmsArticle::STATUS_TOP_ON);
    }

    private function updateArticle($id, $sField, $iStatus) {
        $oArticle = CmsArticle::find($id);
        if (!is_object($oArticle)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }

        $sNowStatusDesc = CmsArticle::$aStatusDesc[$iStatus];
        $sOldStatusDesc = CmsArticle::$aStatusDesc[$oArticle->status];
        $aAuditArray = [CmsArticle::STATUS_AUDITED, CmsArticle::STATUS_REJECTED];
        // pr($sNowStatusDesc . '---' . $sOldStatusDesc);exit;
        if ($sField == 'status' &&
            (($oArticle->status != CmsArticle::STATUS_NEW && in_array($iStatus, $aAuditArray))
            || ($iStatus != CmsArticle::STATUS_RETRACT && in_array($oArticle->status, $aAuditArray)) ) ) {
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
