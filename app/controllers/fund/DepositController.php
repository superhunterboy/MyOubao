<?php

class DepositController extends ComplicatedSearchController {

    protected $customViewPath = 'fund.deposit';
    protected $customViews = [
        'index',
        'setToWaitLoad',
        'view',
        'setToWaitVerify'
    ];
    protected $modelName = 'Deposit';
    protected $searchBlade = 'w.user_deposit_search';

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
        $this->setVars('deposit_mode', $this->params['deposit_mode']);
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
        $this->setVars('aPaymentPlatform', PaymentPlatform::getTitleList());
        $this->setVars('aDepositMode', Deposit::$aDepositMode);
        $oBank = new Bank;
        $this->setVars('aBanks', $oBank->getTitleList());
        switch ($this->action) {
            case 'index':
                $this->resourceView = 'fund.deposit';
                $sMode = __('_deposit.' . Deposit::$aDepositMode[$this->params['deposit_mode']]);
                $this->viewVars['sPageTitle'] .= ' - ' . $sMode;
                $this->viewVars['resourceName'] .= '(' . $sMode . ')';
                if ($this->params['deposit_mode'] == Deposit::DEPOSIT_MODE_BANK_CARD) {
                    $aListColumns = Deposit::$columnForList;
                    $iPos = array_search('order_no', $aListColumns);
                    $iPos === false or $aListColumns[$iPos] = 'postscript';
                    $this->setVars('aColumnForList', $aListColumns);
                }
                $this->setVars('accounts', PaymentAccount::getTitleList());
//                pr($this->viewVars['sPageTitle']);
                break;
            case 'setToWaitLoad':
                $sModelName = $this->modelName;
                $this->setVars('aViewColumnMaps', $sModelName::$viewColumnMaps);
                break;
        }
    }

    public function download() {

        $oQuery = $this->indexQuery();

        set_time_limit(0);

        $aConvertFields = [
            'status' => 'formatted_status',
            'bank_id' => 'bank',
            'deposit_mode' => 'deposit_mode',
            'created_at' => 'date',
            'updated_at' => 'deposit_add_game_money_time',
        ];

        $aBanks = Bank::getTitleList();
        $aData = $oQuery->get(Deposit::$columnForList);
        $aData = $this->model->makeData($aData, Deposit::$columnForList, $aConvertFields, $aBanks);
        return $this->downloadExcel(Deposit::$columnForList, $aData, 'Deposit Report');
    }

    function makePlusSearchConditions() {
        $aConditions = [];
        if (isset($this->params['real_time'][0]) && !empty($this->params['real_time'][0]) || isset($this->params['real_time'][1]) && !empty($this->params['real_time'][1])) {
            $aConditions['status'] = ['=', Deposit::DEPOSIT_STATUS_SUCCESS];
        }
        return $aConditions;
    }

    /**
     * 客服受理
     */
    function accept($id) {
        $oDeposit = Deposit::find($id);
        if (!in_array($oDeposit->status, [Deposit::DEPOSIT_STATUS_NEW, Deposit::DEPOSIT_STATUS_RECEIVED])) {
            return $this->goBack('error', '_deposit.status-error');
        }
        if ($bSucc = $oDeposit->setAccected(Session::get('admin_user_id'))) {
            $this->view = $this->customViewPath . '.setToWaitVerify';
            $oBank = Bank::find($oDeposit->bank_id);
            $this->setVars(compact('oDeposit', 'oBank'));
            return $this->render();
        } else {
            return $this->goBack('error', '_deposit.accept-failed');
        }
    }

    /**
     * 客服受理
     */
    function acceptVerify($id) {
        $oDeposit = Deposit::find($id);
        if (!in_array($oDeposit->status, [Deposit::DEPOSIT_STATUS_WAITING_VERIFY])) {
            return $this->goBack('error', '_deposit.status-error');
        }
        if ($bSucc = $oDeposit->setVerifyAccected(Session::get('admin_user_id'))) {
            $sModelName = $this->modelName;
            $this->setVars('aViewColumnMaps', $sModelName::$viewColumnMaps);
            $this->view = $this->customViewPath . '.setToWaitLoad';
            return $this->view($id);
        } else {
            return $this->goBack('error', '_deposit.accept-failed');
        }
    }

    /**
     * 掉单处理
     * @param int $id
     */
    function check($id) {
        $oDeposit = Deposit::find($id);
        if (empty($oDeposit)) {
            return $this->goBack('error', __('_basic.no-data'));
        }
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_RECEIVED) {
            return $this->goBack('error', __('_deposit.status-error'));
        }
        Deposit::addCheckTask($oDeposit->id);
        return $this->goBackToIndex('success', __('_deposit.check-task-seted'));
    }

    /**
     * 建立发放佣金任务
     * @param int $id
     */
    function setCommissionTask($id) {
        $oDeposit = Deposit::find($id);
        if (empty($oDeposit)) {
            return $this->goBack('error', __('_basic.no-data'));
        }
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_SUCCESS) {
            return $this->goBack('error', __('_deposit.status-error'));
        }
        $oDeposit->addCommissionTask();
//        Deposit::addCheckTask($oDeposit->id);
        return $this->goBackToIndex('success', __('_deposit.commission-task-seted'));
    }

    public function createDepositTask($id) {
        $oDeposit = Deposit::find($id);
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_CHECK_SUCCESS) {
            return $this->goBack(('error'), __('_deposit.status-error'));
        }
        if (Deposit::addDepositTask($oDeposit->id)) {
            return $this->goBackToIndex('success', __('_deposit.waiting-load-seted'));
        }
    }

    /**
     * 设置等待审核状态
     * @param type $id
     * @return type
     */
    public function setToWaitVerify($id) {
        $oDeposit = Deposit::find($id);
        if (!in_array($oDeposit->status, [Deposit::DEPOSIT_STATUS_ACCEPTED, Deposit::DEPOSIT_STATUS_VERIFY_REJECTED])) {
            return $this->goBack('error', __('_deposit.failed-status-error'));
        }
        if ($oDeposit->accepter_id != Session::get('admin_user_id')) {
            return $this->goBack('error', __('_deposit.failed-wrong-administrator'));
        }
        if (Request::method() == 'POST') {
            if ($oDeposit->deposit_mode == Deposit::DEPOSIT_MODE_BANK_CARD) {
                $aServiceData['service_bank_seq_no'] = $this->params['service_bank_seq_no'];
                $aServiceData['amount'] = $this->params['amount'];
            }
            if (!$oDeposit->setWaitingVerify()) {
                return $this->goBack('error', __('_deposit.set-status-wait-failed'));
            } else {
                return $this->goBackToIndex('success', __('_deposit.set-to-wait-load-success'));
            }
        } else {
            $oBank = Bank::find($oDeposit->bank_id);
            $this->setVars(compact('oDeposit', 'oBank'));
            return $this->render();
        }
    }

    /**
     * 设置等待加游戏币状态
     * @param type $id
     * @return type
     */
    public function setToWaitLoad($id) {
        $oDeposit = Deposit::find($id);
        if ($oDeposit->verify_accepter_id != Session::get('admin_user_id')) {
            return $this->goBack('error', __('_deposit.failed-wrong-administrator'));
        }
        if (Request::method() == 'POST') {
            if ($oDeposit->status != Deposit::DEPOSIT_STATUS_VERIFY_ACCEPTED) {
                return $this->goBack('error', __('_deposit.failed-status-error'));
            }
            if (!$oDeposit->setWaitingLoad()) {
                return $this->goBack('error', __('_deposit.set-status-wait-verify-failed'));
            }
            // 加币
            if (Deposit::addDepositTask($oDeposit->id)) {
                $oPaymentAccount = PaymentAccount::getAccountByNo($oDeposit->platform_id, $oDeposit->merchant_code);
                if(is_object($oPaymentAccount))
                {
                	$oPaymentAccount->updateStat($oDeposit);
                }
                return $this->goBackToIndex('success', __('_deposit.waiting-load-seted'));
            }
        } else {
            return $this->view($id);
        }
    }

    public function setReject($id) {
        $oDeposit = Deposit::find($id);
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_VERIFY_ACCEPTED) {
            return $this->goBack('error', __('_deposit.reject-status-error'));
        }
        if ($oDeposit->verify_accepter_id != Session::get('admin_user_id')) {
            return $this->goBack('error', __('_deposit.failed-wrong-administrator'));
        }
        $aServiceData = [
            'transaction_pic_url' => '',
            'note' => $oDeposit->note . '<br>' . array_get($this->params, 'note'),
        ];
        if ($oDeposit->deposit_mode == Deposit::DEPOSIT_MODE_BANK_CARD) {
            $aServiceData['service_bank_seq_no'] = '';
            $aServiceData['amount'] = 0.00;
        }

        if ($oDeposit->setReject($aServiceData)) {
            return $this->goBackToIndex('success', __('_deposit.set-to-reject-success'));
        } else {
            return $this->goBack('error', __('_deposit.set-to-reject-failed'));
        }
    }

    public function setFailed($id) {
        $oDeposit = Deposit::find($id);
        if (!in_array($oDeposit->status, [Deposit::DEPOSIT_STATUS_ACCEPTED, Deposit::DEPOSIT_STATUS_VERIFY_REJECTED])) {
            return $this->goBack('error', __('_deposit.failed-status-error'));
        }
        if ($oDeposit->accepter_id != Session::get('admin_user_id')) {
            return $this->goBack('error', __('_deposit.failed-wrong-administrator'));
        }
        if ($oDeposit->setClosed()) {
            return $this->goBack('success', __('_deposit.set-to-failed-success'));
        } else {
            return $this->goBack('error', __('_deposit.set-to-failed-failed'));
        }
    }

    public function setException($id) {
        $oDeposit = Deposit::find($id);
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_ACCEPTED) {
            return $this->goBack('error', __('_deposit.failed-status-error'));
        }
        if ($oDeposit->accepter_id != Session::get('admin_user_id')) {
            return $this->goBack('error', __('_deposit.failed-wrong-administrator'));
        }
        DB::connection()->beginTransaction();
        if ($oDeposit->setException()) {
            $oExceptionDeposit = new ExceptionDeposit;
            $oExceptionDeposit->fill($oDeposit->toArray());
//            pr($oExceptionDeposit->getAttributes());exit;
            $bSucc = $oExceptionDeposit->save();
            if ($bSucc) {
                DB::connection()->commit();
                return $this->goBack('success', __('_deposit.set-to-exception-success'));
            } else {
                DB::connection()->rollback();
                return $this->goBack('error', __('_deposit.save-exception-error'));
            }
        } else {
            return $this->goBack('error', __('_deposit.set-to-exception-failed'));
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
