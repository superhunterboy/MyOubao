<?php

class CashBonusSendController extends AdminBaseController {
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'cashBonusSend';
    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'CashBonusSend';
    const SUCCESS = 0;
    const UPDATE_ERROR=1;
    const STATUS_ERROR=2;
    static $aStatus=['success'=>1,'update_error'=>1,'status_error'=>3];
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {

        parent::beforeRender();
        $this->viewVars['aColumnSettings']['voucher_id']['type'] = 'select_voucher';
        switch($this->action){
            case 'view':
                $this->viewVars['aColumnSettings']['amount']['type']='text';
                $this->viewVars['aColumnSettings']['mincost']['type']='text';
        }
    }
    public function index()
    {
        $this->setVars('aStatus', CashBonusSend::$aStatus);
        $this->setVars('aVoucher',CashBonusSend::$aVoucheType);

        return parent::index();
    }


    public function auditing($id=null){
        $params = Input::get();
        if (Request::method() == 'POST' && !empty($params['step'])) {
            $oBonusSend = CashBonusSend::find($id);
            if($oBonusSend){
                $t = date('Y-m-d H:i:s');
                if($params['step'] ==2){
                    if($oBonusSend->status == CashBonusSend::STATUS_NORMAL){
                        $oBonusSend->status = CashBonusSend::STATUS_AUDIT_FINISH;
                        $oBonusSend->updated_at = $t;
                        !empty($params['note']) && $oBonusSend->note = $params['note'];
                        DB::connection()->beginTransaction();
                        if($oBonusSend->update()){
                            if($oBonusSend->voucher_id == CashBonus::VOUCHER_ID) {
                                $oVoucher = Voucher::find(CashBonus::VOUCHER_ID);
                            }
                            else if($oBonusSend->voucher_id == CashBonusDeposit::VOUCHER_ID){
                                $oVoucher = Voucher::find(CashBonusDeposit::VOUCHER_ID);
                            }
                            if($oVoucher){
                                $oUser = User::find($oBonusSend->user_id);
                                if($oVoucher->sendVoucher($oUser,$oBonusSend->deposit_amount)){
                                    DB::connection()->commit();
                                    return $this->goBackToIndex('success', '派奖成功');
                                }else{
                                    DB::connection()->rollback();
                                    return $this->goBackToIndex('error', '派奖失败');
                                }
                            }else{
                                DB::connection()->rollback();
                                return $this->goBackToIndex('error', '派奖类型voucher错误');
                            }
                        }else{
                            DB::connection()->rollback();
                            return $this->goBackToIndex('error', '状态更新错误');
                        }
                    }else{
                        return $this->goBackToIndex('error', '状态错误');
                    }

                }else if($params['step'] == 3){
                    if($oBonusSend->status == CashBonusSend::STATUS_NORMAL){
                        $oBonusSend->status = CashBonusSend::STATUS_AUDIT_REJECT;
                        $oBonusSend->updated_at = $t;
                        !empty($params['note']) && $oBonusSend->note = $params['note'];
                        if($oBonusSend->update()){
                            return $this->goBackToIndex('success', 'success');
                        }else{
                            return $this->goBackToIndex('error', '状态更新错误');
                        }
                    }
                }
            }else{
                return $this->goBack('error', 'id error');
            }

        }else{
            $oBonusSend = CashBonusSend::find($id);
            if(!$oBonusSend) {
                return $this->goBack('error', 'id error');
            }

            $this->setVars(compact('oBonusSend'));
            $this->setVars('aColumnForList', CashBonusSend::$columnForList);
            $this->setVars('aListColumnMaps', CashBonusSend::$listColumnMaps);

            $this->setVars('ableEdit',CashBonusSend::$ableEdit);
            $this->view='cashBonusSend.audit';
            return parent::create();
        }
    }

    public function auditing_all(){
        $condition = [];
        $condition['status'] = CashBonusSend::STATUS_NORMAL;
        $oCashBonusSendAll = CashBonusSend::getBonusByCondition($condition);
        $iCount=0;
        $timeStamp = date('Y-m-d H:i:s');
        foreach($oCashBonusSendAll as $oCashBonusSend){
            $oCashBonusSend->status = CashBonusSend::STATUS_AUDIT_FINISH;
            $oCashBonusSend->note = '系统自动审核';
            $oCashBonusSend->updated_at =$timeStamp;
            DB::connection()->beginTransaction();
            if($oCashBonusSend->update()){
                if($oCashBonusSend->voucher_id == CashBonus::VOUCHER_ID) {
                    $oVoucher = Voucher::find(CashBonus::VOUCHER_ID);
                }
                else if($oCashBonusSend->voucher_id == CashBonusDeposit::VOUCHER_ID){
                    $oVoucher = Voucher::find(CashBonusDeposit::VOUCHER_ID);
                }
                if($oVoucher){
                    $oUser = User::find($oCashBonusSend->user_id);
                    if($oVoucher->sendVoucher($oUser,$oCashBonusSend->deposit_amount)){
                        ++$iCount;
                        DB::connection()->commit();
                    }else{
                        DB::connection()->rollback();
                    }
                }else{
                    DB::connection()->rollback();
                }
            }
        }

        return $this->goBackToIndex('success', '派奖完成');
    }

}
