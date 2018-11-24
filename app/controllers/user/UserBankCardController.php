<?php

class UserBankCardController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $customViewPath = 'userBankCard';
    protected $customViews = [
        'searchUserByAccount',
        'create',
        'edit'
    ];

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'UserBankCard';

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    const TEST_USER_ROLE = 20; // 测试用户角色id值

    /**
     * 在渲染前执行，为视图准备变量
     */

    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aStatus = []; // $sModelName::$validStatuses;
        foreach ($sModelName::$validStatuses as $key => $value) {
            $aStatus[] = __('_userbankcard.' . strtolower(Str::slug($value)));
        }
        $aHiddenColumns = $sModelName::$aHiddenColumns;
        switch ($this->action) {
            case 'index':
            case 'view':
                $this->setVars(compact('aStatus', 'aHiddenColumns'));
                break;
            case 'edit':
                $sCurrentUsername = $this->model->username;
                $this->setVars(compact('sCurrentUsername'));
            case 'create':
                // $aBanks     = Bank::getTitleList();
                // $oBank  = new Bank;
                // $aBanks = $oBank->getValueListArray('name', ['status' => ['=', Bank::BANK_STATUS_AVAILABLE]], [], true);
                $aBanks = [];
                $oBanks = Bank::getSupportCardBank();
                foreach ($oBanks as $key => $value) {
                    $aBanks[$value->id] = $value->name;
                }
                $aAllCities = District::getCitiesByProvince();
                $aCities = $this->generateCityies($aAllCities);
                $aProvinces = District::getAllProvinces(0);
                $this->setVars(compact('aBanks', 'aStatus', 'aHiddenColumns', 'aProvinces', 'aAllCities', 'aCities'));
                break;
            case 'searchUserByAccount':
                $aColumnForList = $sModelName::$columnForUserSearchList;
                $datas = $this->generateUserSearchParams();
                // pr($datas->toArray());exit;
                $this->setVars(compact('aColumnForList', 'aStatus', 'datas'));
                $this->setVars('aNoOrderByColumns', $sModelName::$noOrderByColumns);
                break;
        }
    }

    protected function & _makeVadilateRules($oModel) {
        $aRules = parent::_makeVadilateRules($oModel);
        $aRules['account'] = 'required|regex:/^[0-9]*$/|between:16,19|unique:user_bank_cards,account,';
        unset($aRules['account_confirmation']);
        if ($oModel->id) {
            $aRules['account'] .= $oModel->id;
        }

        return $aRules;
    }

    public function create($id = null) {
        // 这里的id传入的是用户id
        if ($id) {
            $this->params['user_id'] = $id;
            $oUser = User::find($id);
            if (!is_object($oUser)) {
                return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
            }
            $sCurrentUsername = $oUser->username;
            $this->setVars(compact('sCurrentUsername'));
        }
        // pr($this->params);exit;
        return parent::create($id);
    }

    // public function edit($id)
    // {
    //     $this->model = $this->model->find($id);
    //     if (!is_object($this->model)) {
    //         return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
    //     }
    //     if (Request::method() == 'PUT') {
    //         $this->params['account_confirmation'] = $this->params['account'];
    //         $this->_fillModelDataFromInput();
    //         if ($bSucc = $this->model->save()) {
    //             return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
    //         } else {
    //             $this->langVars['reason'] = & $this->model->getValidationErrorString();
    //             return $this->goBack('error', __('_basic.update-fail', $this->langVars));
    //         }
    //     } else {
    //         // $data = $this->model;
    //         // $isEdit = true;
    //         // $this->setVars(compact('data', 'isEdit', 'id'));
    //         // return $this->render();
    //         //
    //         return parent::edit($id);
    //     }
    // }
    /**
     * [searchUserByAccount 帐号反查]
     * @return [Response] [description]
     */
    public function searchUserByAccount() {
        $this->setVars('bSequencable', FALSE);
        // $this->view = $this->resourceView . '.' . 'index';
        return $this->index();
    }

    /**
     * [lockUserBankCards 锁定用户银行卡]
     * @param  [Integer] $iUserId [用户id]
     * @return [Response]          [响应视图]
     */
    public function lockUserBankCards($iUserId) {
        return $this->setLockStatus($iUserId, 1);
    }

    /**
     * [unlockUserBankCards 解锁用户银行卡]
     * @param  [Integer] $iUserId [用户id]
     * @return [Response]          [响应视图]
     */
    public function unlockUserBankCards($iUserId) {
        return $this->setLockStatus($iUserId, 0);
    }

    /**
     * [setLockStatus 管理员设置用户银行卡锁定/解锁状态值]
     * @param [Integer] $iUserId [用户id]
     * @param [Integer] $iStatus [锁定/解锁状态值]
     */
    private function setLockStatus($iUserId, $bIsLock) {
        $oUser = User::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-data'));
        }
        if (!UserBankCard::getUserBankCardsCount($iUserId)) {
            return $this->goBack('error', __('_ruserbankcard.no-bankcards'));
        }
        DB::connection()->beginTransaction();
        if ($bIsLock) {
            $bSucc = UserBankCard::setLockStatus($iUserId);
        } else {
            $bSucc = UserBankCard::setInUseStatus($iUserId);
        }
        $sPre = $bIsLock ? 'locked' : 'unlock';
        // pr((int)$bSucc);exit;
        if ($bSucc) {
            $bSucc = $this->createUserManageLog($iUserId);
        }
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBack('success', __('_ruserbankcard.' . $sPre . '-bankcards-success', $this->langVars));
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', __('_ruserbankcard.' . $sPre . '-bankcards-fail'));
        }
    }

    /**
     * [generateCityies]
     * @param  [Array] $aAllCities [省 => [市, 市]格式的数组]
     * @return [Array]             [城市数组]
     */
    private function generateCityies($aAllCities) {
        $province_id = $this->model->province_id or $province_id = 1;
        $arrCities = $aAllCities[$province_id]['children'];
        $aCities = [];
        foreach ($arrCities as $key => $value) {
            $aCities[$value['id']] = $value['name'];
        }
        return $aCities;
    }

    /**
     * [generateUserSearchParams 生成帐号反查功能列表值]
     * @return [Array] [功能反查列表数据对象数组]
     */
    private function generateUserSearchParams() {
        // $oUserBankCard = UserBankCard::where('account', '=', $sAccount)->first();
        $oUserBankCards = $this->viewVars['datas'];
        // $datas = [];
        foreach ($oUserBankCards as $oUserBankCard) {
            $oUser = User::find($oUserBankCard->user_id);
            if($oUser){
                $oUserBankCard->parent_username or $oUserBankCard->parent_username = $oUser->forefathers;
                $oUserBankCard->blocked = intval($oUser->blocked);
                $oUserBankCard->blocked_type = $oUser->formatted_blocked_type;
                $oUserBankCard->is_tester or $oUserBankCard->is_tester = $oUser->is_tester;
            }
        }
        return $oUserBankCards;
    }

    /**
     * [destroy 管理员删除时要添加操作日志]
     * @param  [Integer] $id [用户绑定的银行卡记录id]
     * @return [type]     [description]
     */
    public function destroy($id) {
        $oUserBankCard = $this->model->find($id);
        $this->iUserId = $oUserBankCard->user_id;
        DB::connection()->beginTransaction();
        $oUserBankCard->flushUserBankCardCount();
        parent::destroy($id);
    }

    public function afterDestroy() {
        if ($this->createUserManageLog($this->iUserId)) {
            DB::connection()->commit();
            unset($this->iUserId);
            return true;
        } else {
            DB::connection()->rollback();
            unset($this->iUserId);
            return false;
        }
    }

    public function download() {
        $oQuery = $this->indexQuery();
        set_time_limit(0);
        $aConvertFields = [
            'status' => 'formatted_status',
            'created_at' => 'date',
            'updated_at' => 'date',
            'islock' => 'boolean',
        ];

        $aData = $oQuery->get(UserBankCard::$columnForList);
        $aData = $this->_makeData($aData, UserBankCard::$columnForList, $aConvertFields);
        return $this->downloadExcel(UserBankCard::$columnForList, $aData, 'BankCard Report');
    }

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
                        case 'formatted_status':
                            $a[] = $oDeposit->formatted_status;
                            break;
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'boolean':
                            $a[] = $oDeposit->$key ? __('Yes') : __('No');
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
     * 根据搜索配置生成搜索表单数据
     */
    function _setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('aSearchFields', 'bNeedCalendar'));
        $this->setVars('aSearchConfig', $this->searchConfig);
        $this->addWidget('w.search_download');
    }

}
