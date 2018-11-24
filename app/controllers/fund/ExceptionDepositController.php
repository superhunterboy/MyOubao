<?php

class ExceptionDepositController extends ComplicatedSearchController {

    protected $customViewPath = 'fund.exceptionDeposit';
    protected $customViews = [
        'index',
        'submitDocument',
        'view',
        'setToVerified',
    ];
    protected $modelName = 'ExceptionDeposit';

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index() {
        $oQuery = $this->indexQuery();

        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        $datas = $oQuery->paginate($iPageSize);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        // pr(($datas->toArray()));exit;
        $this->setVars(compact('datas'));
        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1];
            } else {
                $$sMainParamName = null;
            }
            $this->setVars(compact($sMainParamName));
        }

        return $this->render();
    }

    public function indexQuery() {
        $aConditions = & $this->makeSearchConditions();
        $aPlusConditions = $this->makePlusSearchConditions();
        $aConditions = array_merge($aConditions, $aPlusConditions);
        $oQuery = $this->model->doWhere($aConditions);
        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(Input::get('_withTrashed', 0));
        // pr($bWithTrashed);exit;
        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('validStatuses', Deposit::$validStatuses);
        $this->setVars('aDepositMode', Deposit::$aDepositMode);
        $oBank = new Bank;
        $this->setVars('aBanks', $oBank->getTitleList());
        switch ($this->action) {
            case 'index':
                $this->resourceView = 'fund.deposit';
                break;
        }
    }

    function makePlusSearchConditions() {
        $aConditions = [];
        if (isset($this->params['real_time'][0]) && !empty($this->params['real_time'][0]) || isset($this->params['real_time'][1]) && !empty($this->params['real_time'][1])) {
            $aConditions['status'] = ['=', Deposit::DEPOSIT_STATUS_SUCCESS];
        }
        return $aConditions;
    }

    /**
     * 财务受理
     */
    function accept($id) {
        $oExceptionDeposit = ExceptionDeposit::find($id);
        if (!is_object($oExceptionDeposit)) {
            return $this->goBack('error', '_exceptiondeposit.missing-data');
        }
        if (!in_array($oExceptionDeposit->status, [ExceptionDeposit::EXCEPTION_DEPOSIT_STATUS_EXCEPTION])) {
            return $this->goBack('error', '_exceptiondeposit.status-error');
        }
        if ($bSucc = $oExceptionDeposit->setAccected(Session::get('admin_user_id'))) {
            return $this->goBack('success', '_exceptiondeposit.accepted');
        } else {
            return $this->goBack('error', '_exceptiondeposit.accept-failed');
        }
    }

    /**
     * 提交汇款凭证
     */
    public function submitDocument($id) {
        $oExceptionDeposit = ExceptionDeposit::find($id);
        if (!is_object($oExceptionDeposit)) {
            return $this->goBack('error', '_exceptiondeposit.missing-data');
        }
        if ($oExceptionDeposit->accepter_id != Session::get('admin_user_id')) {
            return $this->goBack('error', __('_exceptiondeposit.failed-wrong-administrator'));
        }
        if (Request::method() == 'POST') {
            if ($oExceptionDeposit->status != ExceptionDeposit::EXCEPTION_DEPOSIT_STATUS_ACCEPTED) {
                return $this->goBack('error', __('_exceptiondeposit.failed-status-error'));
            }
            $sUrl = $this->saveImg();
            if ($sUrl == false) {
                return $this->goBack('error', __('_exceptiondeposit.save-img-error'));
            }
            $aServiceData = [
                'transaction_pic_url' => $sUrl,
            ];
            if (!$oExceptionDeposit->setWaitingVerify($aServiceData)) {
                return $this->goBack('error', __('_exceptiondeposit.set-status-wait-verify-failed'));
            } else {
                return $this->goBackToIndex('success', __('_exceptiondeposit.waiting-verify-seted'));
            }
        } else {
            return $this->view($id);
        }
    }

    /**
     * 客服受理审核
     */
    function acceptVerify($id) {
        $oExceptionDeposit = ExceptionDeposit::find($id);
        if (!is_object($oExceptionDeposit)) {
            return $this->goBack('error', '_exceptiondeposit.missing-data');
        }
        if (!in_array($oExceptionDeposit->status, [ExceptionDeposit::EXCEPTION_DEPOSIT_STATUS_WAITING_VERIFY])) {
            return $this->goBack('error', '_exceptiondeposit.status-error');
        }
        if ($bSucc = $oExceptionDeposit->setVerifyAccected(Session::get('admin_user_id'))) {
            return $this->goBack('success', '_exceptiondeposit.accepted');
        } else {
            return $this->goBack('error', '_exceptiondeposit.accept-failed');
        }
    }

    /**
     * 设置等待加游戏币状态
     * @param type $id
     * @return type
     */
    public function setToVerified($id) {
        $oExceptionDeposit = ExceptionDeposit::find($id);
        if (!is_object($oExceptionDeposit)) {
            return $this->goBack('error', '_exceptiondeposit.missing-data');
        }
        if (Request::method() == 'POST') {
            if ($oExceptionDeposit->verify_accepter_id != Session::get('admin_user_id')) {
                return $this->goBack('error', __('_exceptiondeposit.failed-wrong-administrator'));
            }
            if ($oExceptionDeposit->status != ExceptionDeposit::EXCEPTION_DEPOSIT_STATUS_VERIFY_ACCEPTED) {
                return $this->goBack('error', __('_exceptiondeposit.failed-status-error'));
            }
            if (!$oExceptionDeposit->setToVerified()) {
                return $this->goBack('error', __('_exceptiondeposit.set-status-verified-failed'));
            } else {
                return $this->goBackToIndex('success', __('_exceptiondeposit.set-status-verified-success'));
            }
        } else {
            return $this->view($id);
        }
    }

    /*
     * 图片上传验证检测方法
     */

    private function saveImg() {
        $aInputs = Input::all();
        $sDirPath = SysConfig::readValue('deposit_transaction_pic_path') . '/';
        $sFileObj = 'transaction_pic';
        $bSucc = true;

        $rules = array(
            $sFileObj => 'required|mimes:jpeg,gif,png|max:1024',
        );
        // 自定义验证消息
        $messages = array(
            $sFileObj . '.required' => '请选择需要上传的图片。',
            $sFileObj . '.mimes' => '请上传 :values 格式的图片。',
            $sFileObj . '.max' => '图片的大小请控制在 1M 以内。',
        );
        $validator = Validator::make([ 'transaction_pic' => $aInputs['transaction_pic']], $rules, $messages);
        if ($validator->passes()) {
            $url = $this->updateFile($aInputs['transaction_pic'], $sDirPath, $rules, $messages);
        } else {
            $url = false;
        }
        return $url;
    }

    /*
     * 图片上传方法
     */

    private function updateFile($oFile, $sDirPath, $rules, $messages) {
        file_exists($sDirPath) or mkdir($sDirPath, 0777, 1);
        // pr('asdfasd'); exit;
        $sNewFileName = '';
        //检验一下上传的文件是否有效.
        if (is_object($oFile) && $oFile->isValid()) {
            $ext = $oFile->guessClientExtension();
            $sOriginalName = $oFile->getClientOriginalName(); // 客户端文件名，包括客户端拓展名
            $sNewFileName = md5($sOriginalName . time()) . '.' . $ext; // 哈希处理过的文件名，包括真实拓展名
            $portrait = Image::make($oFile->getRealPath());
            $oldImage = Input::get('oldimg');
            // pr('sfa');
            // 删除旧img
            File::delete(
                    public_path($oldImage)
            );
            $portrait->save($sDirPath . $sNewFileName);
        }
        return $sNewFileName;
    }

    public function loadImg($id, $imgName) {
        $oDeposit = Deposit::find($id);
        if (!is_object($oDeposit)) {
            $this->goBack('error', __('_deposit.missing-data'));
        }
        if ($oDeposit->transaction_pic_url != $imgName) {
            $this->goBack('error', __('_deposit.pic-name-not-match'));
        }
        $n = new imgdata;
        $n->getdir(SysConfig::readValue('deposit_transaction_pic_path') . '/' . $imgName);
        $n->img2data();
        $n->data2img();
        exit;
    }

}
