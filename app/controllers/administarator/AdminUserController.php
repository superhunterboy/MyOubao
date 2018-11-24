<?php
# 管理员管理
class AdminUserController extends AdminBaseController
{
    protected $customViewPath = 'admin.admin';
    protected $customViews    = [
        'create', 'index', 'resetPassword', 'edit', 'view'
    ];

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'AdminUser';

    protected function & _makeVadilateRules ($oModel)
    {
        parent::_makeVadilateRules($oModel);
        $sClassName = get_class($oModel);
        $aRules = $sClassName::$rules;
        $isEdit = $oModel->exists;
        // pr((int)$isEdit);exit;
        if ($isEdit) {
            array_forget($aRules, 'password');
            array_forget($aRules, 'password_confirmation');
        }
        if ($oModel->id) {
            $aRules['username'] = 'required|between:4,32|unique:admin_users,username,' . $oModel->id;
        }
        return $aRules;
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch($this->action){
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
//                $sc = new SysConfig;
                $sLanguageSource = SysConfig::readDataSource('sys_support_languages');
                // pr($sLanguageSource);
                $aLanguages = SysConfig::getSource($sLanguageSource);
                $this->setVars(compact('aLanguages'));
                break;
        }
    }

    public function create($id = null) {
        if (Request::method() == 'POST') {
            // $this->model->password = $this->model->generatePasswordStr();
            $aReturnMsg = $this->model->generateUserInfo($this->params);
            if (!$aReturnMsg['success']) {
                $this->langVars['reason'] = $aReturnMsg['msg'];
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
            // pr($aReturnMsg);exit;
            DB::connection()->beginTransaction();
            // pr($this->model->toArray());exit;
            $bSucc = $this->model->save();

            if ($bSucc) {
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
            //            if ($sModelName::$treeable){
//                $sFirstParamName = 'parent_id';
//            }
//            else{
//foreach($this->paramSettings as $sFirstParamName => $tmp){
//                    break;
//                }
            list($sFirstParamName, $tmp) = each($this->paramSettings);
            //            }
            // pr($sModelName);
            // exit;
            !isset($sFirstParamName) or $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));
            return $this->render();
        }
    }

    public function changePassword() {
        $id = Session::get('admin_user_id');
        $this->action = 'resetPassword';
        return $this->updatePasswrod($id);
    }
    /**
     * [resetPassword 重置管理员密码]
     * @param  [Int] $id [管理员id]
     * @return [Response]     [description]
     */
    public function resetPassword($id)
    {
        return $this->updatePasswrod($id);
    }

    /**
     * [updatePasswrod 重置密码]
     * @param  [Int] $id [管理员id]
     * @return [Response]     [description]
     */
    private function updatePasswrod($id)
    {
        $this->model  = $this->model->find($id);
        $isAdminReset = Session::get('admin_user_id');

        // pr($this->model->toArray());exit;
        if (!$this->model) {
            $sMsg = __(sprintf('%s not exists', $this->resourceName));
            return $this->goBack('error', $sMsg);
        }
        if (Request::method() == 'PUT') {
            if ($isAdminReset) {
                $sNewPassword             = trim(Input::get('password'));
                $sNewPasswordConfirmation = trim(Input::get('password_confirmation'));
                $aFormData = [
                    'password'              => $sNewPassword,
                    'password_confirmation' => $sNewPasswordConfirmation,
                ];
                $aReturnMsg = $this->model->resetPassword($aFormData);
                $bSucc = $aReturnMsg['success'];
                if (! $bSucc) {
                    return $this->goBack('error', $aReturnMsg['msg'], true);
                }
                $sDesc = $bSucc ? __('_user.password-updated') : __('_basic.update-password-fail');
                // $sDesc = 'Reset password ' . ($bSucc ? 'success.' : 'fialed. ') . ' ' . $aReturnMsg['msg'];
                // $sDesc = __($sDesc);
            } else {
                return $this->goBack('error', __('_basic.no-rights'), true);
            }
            return $this->renderReturn($bSucc, $sDesc);
        } else {
            $data = $this->model;
            $this->setVars(compact('data'));
            return $this->render();
        }
    }

    public function renderReturn($bSucc, $sDesc)
    {
        // pr((int)$bSucc . '------------' . $sDesc);exit;
        if ($bSucc) {
            return $this->goBackToIndex('success', $sDesc);
        } else {
            return $this->goBack('error', $sDesc, true);
        }
    }


}
