<?php

class CashBonusDepositController extends AdminBaseController {
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'cashBonusDeposit';
    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'CashBonusDeposit';
    const SUCCESS = 0;
    const UPDATE_ERROR=1;
    const STATUS_ERROR=2;
    static $aStatus=['success'=>1,'update_error'=>1,'status_error'=>3];
    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        
        $sModelName = $this->modelName;
        
        $aStatus = $sModelName::$aStatus;
        $sModelName::translateArray($aStatus);
        $this->setVars('aStatus', $aStatus);

        switch($this->action){
            case 'auditing':
                $this->viewVars['aColumnSettings']['first_deposit']['type'] = 'text';
                $this->viewVars['aColumnSettings']['note']['type']='text';
                break;
            case 'view':
                $this->viewVars['aColumnSettings']['first_deposit']['type']='text';
                $this->viewVars['aColumnSettings']['note']['type']='text';
                break;
            case 'index':
                break;
            default :
                break;
        }

    }
//    public function index()
//    {
//        $aConditions = [];
//        (isset($this->params['status_deposit']) &&$this->params['status_deposit']!=='')&& $aConditions['status_deposit'] = $this->params['status_deposit'];
//        !empty($this->params['username']) && $aConditions['username'] = $this->params['username'];
//        !empty($this->params['register_ip']) && $aConditions['register_ip'] = $this->params['register_ip'];
//        !empty($this->params['phone']) && $aConditions['phone'] = $this->params['phone'];
//        $oQuery = $this->model->doWhere($aConditions);
//        $aOrderSet = [];
//        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
//            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
//            $aOrderSet[$sOorderColumn] = $sDirection;
//        }
//        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
//        $datas = $oQuery->paginate(static::$pagesize);
//
//        $this->setVars(compact('datas'));
//
//        return $this->render();
//    }

    public function auditing($id=null){
        $params = Input::get();
        if (Request::method() == 'POST' && !empty($params['step'])) {
                $oBonus = $this->model->find($id);
                $t = date('Y-m-d H:i:s');
                if ($params['step'] == 2) {//通过
                    //check transaction
                    $fFirstDeposit = $oBonus->first_deposit;
                    if($fFirstDeposit < 10){
                        return $this->goBackToIndex('error', '没有首充或首充小于10，不能通过审核!');
                    }
                    if($oBonus->status_deposit == CashBonusDeposit::STATUS_DEPOSIT_WAITING_AUDIT){
                        $oBonus->status_deposit=CashBonusDeposit::STATUS_DEPOSIT_AUDIT_FINISH;
                        $oBonus->updated_at = $t;
                        $oBonusSend = new CashBonusSend();
                        DB::connection()->beginTransaction();
                        if ($oBonus->update()) {
                            $oBonusSend->user_id = $oBonus->user_id;
                            $oBonusSend->username = $oBonus->username;
                            $oBonusSend->voucher_id = CashBonusDeposit::VOUCHER_ID;
                            $oBonusSend->status = $oBonusSend::STATUS_NORMAL;
                            $oBonusSend->admin_id = Session::get('admin_user_id');
                            $oBonusSend->admin_username = Session::get('admin_username');
                            $oBonusSend->deposit_amount = $fFirstDeposit;
                            $oBonusSend->note = $params['note'];
                            $oBonusSend->created_at = $t;
                            $oBonusSend->updated_at = $t;
                            if ($oBonusSend->save()) {
                                DB::connection()->commit();
                                return $this->goBackToIndex('success', 'success');
                            } else {
                                DB::connection()->rollback();
                                return $this->goBackToIndex('error', '状态更新错误');
                            }
                        } else {
                            DB::connection()->rollback();
                            return $this->goBackToIndex('error', '状态更新错误');
                        }

                    }else{
                        return $this->goBackToIndex('error', '状态错误');
                    }
                } else if ($params['step'] == 3) {//拒绝

                    if ($oBonus->status_deposit == CashBonusDeposit::STATUS_DEPOSIT_WAITING_AUDIT) {
                        $oBonus->status_deposit = CashBonusDeposit::STATUS_DEPOSIT_AUDIT_REJECT;
                        $oBonus->updated_at = $t;
                        if ($oBonus->update())
                            return $this->goBackToIndex('success', 'success');
                        else
                            return $this->goBackToIndex('error', '状态更新错误');
                    } else {
                        return $this->goBackToIndex('error', '状态错误');
                    }
                }

        }else{
            $oBonus = $this->model->find($id);
            if(!$oBonus) {
                return $this->goBack('error', 'id error');
            }
            $data = $oBonus;
            //FirstDeposit
            $sModelName = $this->modelName;
            $this->setVars(compact('data'));
            $this->setVars('oBonus',$data);
            $this->setVars('aListColumnMaps', $sModelName::$listColumnMaps);
            $this->setVars('aColumnForList', $sModelName::$columnForList);
            $this->setVars('ableEdit',$sModelName::$ableEdit);
//            $this->setVars('aListColumnMaps', CashBonusDeposit::$listColumnMaps);
            $this->view='cashBonusDeposit.audit';
            return $this->render();
        }
    }
}
