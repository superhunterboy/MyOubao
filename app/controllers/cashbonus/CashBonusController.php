<?php

class CashBonusController extends AdminBaseController {
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'cashBonus';
    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'CashBonus';
    const SUCCESS = 0;
    const UPDATE_ERROR=1;
    const STATUS_ERROR=2;
    static $aStatus=['success'=>1,'update_error'=>1,'status_error'=>3];
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {

        parent::beforeRender();

        switch($this->action){
            case 'view':

                $this->viewVars['aColumnSettings']['note']['type']='text';
                $this->viewVars['aColumnSettings']['bank_card']=1;
                $this->viewVars['data']->bank_card = $this->isBankCardLocked($this->viewVars['data']->user_id)?'已锁定':'未锁定';
                break;
            case 'index':
                if (isset($this->viewVars['datas'])){
                    $datas = $this->viewVars['datas'];
                    foreach($datas as $d){
                        $d->bank_card = $this->isBankCardLocked($d->user_id)?'已锁定':'未锁定';
                    }
                    $this->setVars(compact('datas'));
                }
                $this->setVars('aStatus', CashBonus::$aStatus);
                break;
        }
    }
//    public function index()
//    {
//
//        $aConditions = [];
//        //$aConditions['status_register']=array('<>',CashBonus::STATUS_SYS_DEL);
//        !empty($this->params['username']) && $aConditions['username'] = $this->params['username'];
//        !empty($this->params['register_ip']) && $aConditions['register_ip'] = $this->params['register_ip'];
//        !empty($this->params['phone']) && $aConditions['phone'] = $this->params['phone'];
//
//        (isset($this->params['status_register']) &&$this->params['status_register']!=='')&& $aConditions['status_register'] = $this->params['status_register'];
//        $oQuery = $this->model->doWhere($aConditions);
//        $aOrderSet = [];
//        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
//            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
//            $aOrderSet[$sOorderColumn] = $sDirection;
//        }
//        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
//        $datas = $oQuery->paginate(static::$pagesize);
//        $queries = DB::getQueryLog();
//        $last_query = end($queries);
//        pr($last_query);
//        foreach($datas as $d){
//            $d->bank_card = $this->isBankCardLocked($d->user_id)?'已锁定':'未锁定';
//        }
//        $this->setVars(compact('datas'));
//        $this->setVars('aStatus', CashBonus::$aStatus);
//
//        return $this->render();
//    }


    public function auditing($id=null){
        $params = Input::get();
        if (Request::method() == 'POST' && !empty($params['step'])) {
                $oBonus = CashBonus::find($id);
            $t = date('Y-m-d H:i:s');
                if ($params['step'] == 2) {//通过
                    //check bank card
                    if(!$this->isBankCardLocked($oBonus->user_id)){
                        return $this->goBackToIndex('error', '银行卡未锁定,不能通过审核');
                    }

                    if ($oBonus->status_register == CashBonus::STATUS_WAITING_AUDIT) {

                        $oBonusSend = new CashBonusSend();
                        $oBonus->status_register = CashBonus::STATUS_AUDIT_FINISH;
                        $oBonus->updated_at = $t;
                        DB::connection()->beginTransaction();
                        if ($oBonus->update()) {
                            $oBonusSend->user_id = $oBonus->user_id;
                            $oBonusSend->username = $oBonus->username;
                            $oBonusSend->voucher_id = CashBonus::VOUCHER_ID;
                            $oBonusSend->status = $oBonusSend::STATUS_NORMAL;
                            $oBonusSend->admin_id = Session::get('admin_user_id');
                            $oBonusSend->admin_username = Session::get('admin_username');
                            $oBonusSend->note = $params['note'];
                            $oBonusSend->created_at = $t;
                            $oBonusSend->updated_at = $t;

                            if ($oBonusSend->save()) {
                                CashBonus::setRepeatBonus($oBonus->username, $oBonus->phone, $oBonus->register_ip, $oBonus->id);
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

                    } else {
                        return $this->goBackToIndex('error', '状态错误');
                    }
                } else if ($params['step'] == 3) {//拒绝

                    if ($oBonus->status_register == CashBonus::STATUS_WAITING_AUDIT) {
                        $oBonus->status_register = CashBonus::STATUS_AUDIT_REJECT;
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
            $this->modelName = 'CashBonus';
            $oBonus = CashBonus::find($id);
            if(!$oBonus) {
                return $this->goBack('error', 'id error');
            }
            $oBonus->bank_card = $this->isBankCardLocked($oBonus->user_id)?'已锁定':'未锁定';
            $this->setVars('oBonus',$oBonus);
            $this->setVars('aColumnForList', CashBonus::$columnForList);
            $this->setVars('aListColumnMaps', CashBonus::$listColumnMaps);
            $this->setVars('ableEdit',CashBonus::$ableEdit);
            $this->view='cashBonus.audit';
            return parent::create();
        }
    }

    protected function isBankCardLocked($uid){
        $uCards = UserBankCard::getUserCardsInfo($uid,['islock']);
        $isLocked = false;
        if($uCards->count()) {
            $isLocked = true;
            foreach ($uCards as $uCard) {
                if ($uCard->islock == UserBankCard::UNLOCKED) {
                    $isLocked = false;
                    break;
                }
            }
        }
        return $isLocked;
    }

    public function view($id=null){

        parent::view($id);
    }






}
