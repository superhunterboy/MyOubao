<?php

class ManualDepositController extends AdminBaseController {

    protected $modelName = 'ManualDeposit';
    protected $customViewPath = 'fund.manualDeposit';
    protected $customViews = [
        'batchDeposit',
    ];
    protected $excelTitle = [
        'username',
        'amount_add_coin',
        'transaction_description',
        'note',
    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();


        switch ($this->action) {
            case 'index':
                $this->setVars('aWidgets', ['w.search_download']);
                $this->setVars('depositStatus', ManualDeposit::$aDepositStatus);
                break;
            case 'view':
            case 'edit':
                break;
            case 'create':
                break;
        }
    }

    public function verify($id) {


        $oManualDeposit = ManualDeposit::find($id);

        if (!is_object($oManualDeposit)) {
            $this->langVars['resource'] = __('_model.manualDeposit');
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $limitAdmin = Config::get('deposite.admin_user_name');
        $adminUsername = Session::get('admin_username');
        $adminUserid = Session::get('admin_user_id');
        if(!in_array($adminUsername,$limitAdmin)){
            $limitAmount = Config::get('deposite.max_amount');
            $totaleAmountByAdmin = ManualDeposit::getTotalAmountByAdmin($adminUserid);

            if(($totaleAmountByAdmin+$oManualDeposit->amount_add_coin)>$limitAmount){
                return $this->goBack('error', __('_basic.deposit-limit'));
            }
        }
        DB::connection()->beginTransaction();
        $bSucc = $oManualDeposit->changeStatus(ManualDeposit::STATUS_NOT_VERIFIED, ManualDeposit::STATUS_VERIFIED);
        if ($bSucc) {
            $bSucc = $oManualDeposit->setAuthor($adminUsername,$adminUserid);
            if($bSucc){
                $aJobData = [
                    'manual_deposit_id' => $oManualDeposit->id,
                ];
                $bSucc = BaseTask::addTask('ManualDepositQueue', $aJobData, 'account');
            }
        }
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBackToIndex('success', __('_manualDeposit.update-success'));
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', __('_manualDeposit.update-error'));
        }
    }

    public function refuse2($id) {
        $adminUsername = Session::get('admin_username');
        $adminUserid = Session::get('admin_user_id');
        $oManualDeposit = ManualDeposit::find($id);
        if (!is_object($oManualDeposit)) {
            $this->langVars['resource'] = __('_model.manualDeposit');
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $bSucc = $oManualDeposit->changeStatus(ManualDeposit::STATUS_NOT_VERIFIED, ManualDeposit::STATUS_REFUSED);
        $aSucc = $oManualDeposit->setAuthor($adminUsername,$adminUserid);
        if ($bSucc && $aSucc) {
            return $this->goBackToIndex('success', __('_manualDeposit.update-success'));
        } else {
            return $this->goBack('error', __('_manualDeposit.update-error'));
        }
    }

    public function batchVerify() {
        $sId = Input::get('id');
        if ($sId == null || strlen($sId) < 1) {
            return $this->goBack('error', __('_manualDeposit.no-data'));
        }
        $aIds = explode(',', $sId);
        $i = 0;
        foreach ($aIds as $id) {
            $oManualDeposit = ManualDeposit::find($id);
            if (!is_object($oManualDeposit)) {
                continue;
            }
            DB::connection()->beginTransaction();
            $bSucc = $oManualDeposit->changeStatus(ManualDeposit::STATUS_NOT_VERIFIED, ManualDeposit::STATUS_VERIFIED);
            if ($bSucc) {
                $aJobData = [
                    'manual_deposit_id' => $oManualDeposit->id,
                ];
                $bSucc = BaseTask::addTask('ManualDepositQueue', $aJobData, 'account');
            }
            if ($bSucc) {
                DB::connection()->commit();
                $i++;
            } else {
                DB::connection()->rollback();
            }
        }
        $this->langVars['successCount'] = $i;
        $this->langVars['totalCount'] = count($aIds);
        return $this->goBackToIndex('success', __('_manualDeposit.batch-verify-success', $this->langVars));
    }

    /**
     * 批量充值
     */
    public function batchDeposit() {
        if (Request::method() == 'POST') {
            $oExcel = Input::file('deposit_file');
            if (!is_object($oExcel)) {
                return $this->goBack('error', __('_manualDeposit.missing-excel-file'));
            }
            $aResult = UpExcel::readExcel($oExcel->getRealPath(), $this->excelTitle);
            if (!$aResult) {
                return $this->goBack('error', __('_manualDeposit.excel-no-data'));
            }
            DB::connection()->beginTransaction();
            $bSucc = false;
            foreach ($aResult as $aDeposit) {
                $oUser = User::findUser($aDeposit['username']);
                if (!is_object($oUser)) {
                    $this->langVars['resource'] = __('_model.user');
                    return $this->goBack('error', __('_basic.missing', $this->langVars));
                }
                $oTransaction = TransactionType::getObjectByParams(['cn_title' => $aDeposit['transaction_description']]);
                if (!is_object($oTransaction)) {
                    $this->langVars['resource'] = __('_model.transactiontype');
                    return $this->goBack('error', __('_basic.missing', $this->langVars));
                }
                $oDeposit = new ManualDeposit;
                $oDeposit->user_id = $oUser->id;
                $oDeposit->username = $aDeposit['username'];
                $oDeposit->is_tester = $oUser->is_tester;
                $oDeposit->amount_add_coin = $aDeposit['amount_add_coin'];
                $oDeposit->transaction_type_id = $oTransaction->id;
                $oDeposit->transaction_description = $aDeposit['transaction_description'];
                $oDeposit->note = $aDeposit['note'];
                $oDeposit->administrator = Session::get('admin_username');
                $oDeposit->admin_user_id = Session::get('admin_user_id');
                $oDeposit->status = ManualDeposit::STATUS_NOT_VERIFIED;
                $bSucc = $oDeposit->save();
                if (!$bSucc) {
                    $this->langVars['reason'] = $oDeposit->getValidationErrorString();
                    break;
                }
            }
            if ($bSucc) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_manualDeposit.save-success'));
            } else {
                DB::connection()->rollback();
                return $this->goBack('error', __('_manualDeposit.save-error', $this->langVars));
            }
        } else {
            return $this->render();
        }
    }

    /**
     * 下载
     * @return type
     */
    public function download() {
        $oQuery = $this->indexQuery();

        set_time_limit(0);
        $sModelName = $this->modelName;
        $aData = $oQuery->get($sModelName::$columnForList);
        $aConvertFields = [
            'created_at' => 'date',
            'status' => 'status',
            'is_tester' => 'is_tester',
        ];
        $aData = $this->_makeData($aData, $sModelName::$columnForList, $aConvertFields);
        return $this->downloadExcel($sModelName::$columnForList, $aData, 'Report', 'ManualDeposit');
    }

//    ManualDeposit::$aDepositStatus
    function _makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }

                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'status':
                            $aDepositStatus = ManualDeposit::$aDepositStatus;
                            $a[] = $aDepositStatus[$oDeposit->status];
                            break;
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'is_tester':
                            $a[] = $oDeposit->$key == 1 ? '是' : '否';
                            break;
                    }
                } else {
                    $a[] = $oDeposit->$key;
                }
            }
            $aResult[] = $a;
        }
//        pr($aResult);exit;
//        echo 123;exit;
        return $aResult;
    }

}
