<?php

class UserCommissionController extends AdminBaseController {


    protected $errorFiles = ['system'];

    protected $customViewPath = 'fund.userCommission';
    protected $customViews = [
        'view',
    ];

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'UserCommission';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                
                $this->setVars('aWidgets', ['w.search_user_commission']);
                break;
        }
        $sModelName = $this->modelName;
        $aStatus = $aCommissionType = [];
        foreach(UserCommission::$aStatus as $key => $value) {
            $aStatus[$key] = __('_usercommission.' . $value);
        }
        foreach(UserCommission::$aCommissionType as $key => $value) {
            $aCommissionType[$key] = __('_usercommission.' . $value);
        }
        $this->setVars(compact('aCommissionType', 'aStatus'));

    }

    public function refuse2($id){
        $oUserCommission = UserCommission::find($id);
        if (!is_object($oUserCommission)) {
            $this->langVars['resource'] = __('_model.UserCommission');
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $bSucc = $oUserCommission->changeStatus(UserCommission::STATUS_WAITING_AUDIT, UserCommission::STATUS_AUDIT_REJECT);
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_usercommission.update-success'));
        } else {
            return $this->goBack('error', __('_usercommission.update-error'));
        }
    }

    public function verify($id){
        $oUserCommission = UserCommission::find($id);
        if (!is_object($oUserCommission)) {
            $this->langVars['resource'] = __('_model.usercommission');
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $oUser = User::find($oUserCommission->user_id);
        if (!is_object($oUser)) {
            $this->langVars['resource'] = __('_model.user');
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }

        $oAccount = Account::lock($oUser->account_id, $iLocker);
        if (empty($oAccount)) {
            $oMessage = new Message($this->errorFiles);
            return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }

        $iReturn = 0;
        DB::connection()->beginTransaction();

        if($oUserCommission->changeStatus(UserCommission::STATUS_WAITING_AUDIT, UserCommission::STATUS_AUDIT_FINISH))
        {
            $aExtraData = ['related_user_id'=>$oUser->id,'related_user_name'=>$oUser->username];
            $iTransactionType = UserCommission::$aToTransactionType[$oUserCommission->commission_type];
            $iReturn = Transaction::addTransaction($oUser,$oAccount,$iTransactionType, $oUserCommission->commission,$aExtraData);
        }

        Account::unLock($oUser->account_id, $iLocker, false);

        if($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL){
            DB::connection()->commit();
            return $this->goBackToIndex('success', __('_usercommission.update-success'));
        }else{
            DB::connection()->rollback();
            return $this->goBack('error', __('_usercommission.update-error'));
        }
    }

    /**
     * view model
     * @param int $id
     * @return bool
     */
    public function view($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $aCommissionSettings = CommissionSettings::getAllSettingsByTypeId($this->model->commission_type);
        foreach($aCommissionSettings as $key=>$aCommissionSetting){
            $aCommissionSettings[$key]['multiple_amount'] = $aCommissionSetting['multiple'] * $aCommissionSetting['amount'];
        }
        $this->aCommissionSettings = array_reverse(array_sort($aCommissionSettings,  function($value) {
            return $value['multiple_amount'];
        }));

        $iMinAmount = min(array_column($this->aCommissionSettings, 'amount'));

        $datas = [];
        $commission_type = $this->model->commission_type;

        switch ($commission_type)
        {
            case UserCommission::COMMISSION_TYPE_DEPOSIT:

                $startTime = date('Y-m-d 00:00:00', strtotime($this->model->date));
                $endTime = date('Y-m-d 23:59:59', strtotime($this->model->date));

                $sql = " select * from (
                          select tmp2.*,user_profits.turnover from (
                          select * from (
                            select  user_id,username,amount,user_forefather_ids from transactions where type_id in(1,18)
                            and created_at >='".$startTime."'
                            and created_at <='".$endTime."'
                            order by created_at,id ASC
                          ) as tmp group by tmp.user_id
                        ) as tmp2
                        left join  user_profits on user_profits.user_id = tmp2.user_id and date='". $this->model->date ."'
                        ) as tmp3
                        where FIND_IN_SET(".$this->model->user_id.",tmp3.user_forefather_ids)
                        and amount>=".$iMinAmount." and turnover>=". $iMinAmount;

                $oDeposits = DB::select($sql);

                foreach($oDeposits as $key=>$oDeposit){
                    $aUserId = array_slice(array_reverse(explode(',', $oDeposit->user_forefather_ids)), 0, 3);

                    if(false !== $pos = array_search($this->model->user_id, $aUserId)){
                        $sub_level = $pos + 1;

                        $data = ['user_id' =>$aUserId[$pos], 'sub_level' => $sub_level, 'username'=>$oDeposit->username, 'amount'=>$oDeposit->amount, 'turnover'=>$oDeposit->turnover];

                        foreach($this->aCommissionSettings as $key=>$aCommissionSetting){
                            if($oDeposit->amount >= $aCommissionSetting['amount'] && $oDeposit->turnover >= $aCommissionSetting['multiple_amount']){
                                $data['commission'] = $aCommissionSetting['return_money_' . $sub_level];
                                break;
                            }
                        }
                        $datas[] = $data;
                    }
                }
                break;

            case UserCommission::COMMISSION_TYPE_TURNOVER:
            case UserCommission::COMMISSION_TYPE_PROFIT:

                $aCommissionType = [ 2 => 'turnover', 3 => 'profit'];
                $iField = $aCommissionType[$commission_type];

                $aUsers = [];
                $oUser = User::find($this->model->user_Id);

/*                User::whereRaw(' find_in_set(?, user_forefather_ids)', [$this->model->user_Id])->get()
                    ->hasMany('user_profits', 'user_id')->get();*/

                $oSubUsers = User::whereRaw(' find_in_set(?, forefather_ids)', [$this->model->user_id])->get();

                foreach($oSubUsers as $oSubUser){
                    $aUserId = array_slice(array_reverse(explode(',', $oSubUser->forefather_ids)), 0, 3);
                    if(false !== $pos = array_search($this->model->user_id, $aUserId)){
                        $aUsers[$oSubUser->id] = ['user_id' =>$oSubUser->id, 'sub_level' => $pos+1, 'username'=>$oSubUser->username];
                    }
                }
                $columns = ['user_id', 'username', $iField];

                foreach ($aUsers as $iUserId=>$aUser)
                {
                    $oQuery = $this->model->commission_type == UserCommission::COMMISSION_TYPE_PROFIT ? UserProfit::where($iField, '<=', -$iMinAmount) : UserProfit::where($iField, '>=', $iMinAmount);

                    $oUserProfit = $oQuery->where('user_id', '=', $iUserId)->where('date', '=', $this->model->date)->first($columns);

                    if(is_object($oUserProfit))
                    {
                        foreach ($this->aCommissionSettings as $aCommissionSetting)
                        {
                            if(abs($oUserProfit->$iField) >= $aCommissionSetting['amount']) {
                                $commission = $aCommissionSetting['return_money_' . $aUser['sub_level']];
                                break;
                            }
                        }
                        $datas[] = array_merge($aUser, ['commission' => $commission, 'amount' => $oUserProfit->$iField]);
                    }
                }

            break;
        }

        $this->setVars(compact('datas', 'commission_type'));
        return $this->render();
    }

    /**
     * 批量审核通过
     */
    public function batchVerify(){
//        pr(Input::all());exit;
        $date = Input::get('date');
        $commission_type = Input::get('commission_type');
//        $date = '2015-09-12';
//        $commission_type = 2;
        if(!$date){
            return $this->goBack('error', '时间不能为空');
        }

        //根据条件获取记录
        $userCommissions = UserCommission::where('date','=',$date)->where('status','=',UserCommission::STATUS_WAITING_AUDIT);
        if($commission_type)
            $userCommissions = $userCommissions->where('commission_type','=',$commission_type);

        $userCommissions = $userCommissions->get();

        //获取所有的记录的id
        if(!is_object($userCommissions)){
            $this->langVars['resource'] = __('_model.usercommission');
            return $this->goBack('error', __('_basic.missing', $this->langVars));
        }
        $ids = [];
        foreach($userCommissions as $key => $userCommission){
            $ids[$key] = $userCommission->id;
        }

        //根据记录ID进行审核，修改记录状态
        if(empty($ids)){
            return $this->goBack('error', '没有可以审核的佣金记录');
        }

        foreach($ids as $id){
            $this->verify($id);
        }

        return $this->goBackToIndex('success', '审核成功！');
    }

}