<?php

class User_MytransferController extends UserBaseController {

    protected $resourceView = 'centerUser.transaction';
    protected $modelName = 'UserTransaction4RelatedUser';

    private static $aTransactionTypeMyDeposit = [1,18];
    private static $aTransactionTypeMyWithdraw = [2, 19];
    private static $aTransactionTypeMyTransfer = [3, 4];

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('related_user',false);
        $aCoefficients = Config::get('bet.coefficients');
//        $aLotteries    = & Lottery::getTitleList();
        $aSeriesWays = & SeriesWay::getTitleList(); // TODO
            
                $this->action = 'index';
                $this->setVars('reportName', 'transfer');
                $this->setVars('related_user',true);
                $this->setVars('depositTransactionType',self::$aTransactionTypeMyTransfer);
               
        $aTransactionTypes = TransactionType::getAllTransactionTypes();
        $aSelectorData = $this->generateSelectorData();
        // pr($aTransactionTypes);exit;
        $this->setVars(compact('aCoefficients', 'aSeriesWays', 'aTransactionTypes', 'aSelectorData'));
    }

    
    /**
     * 我的转账
     * @param null $iUserId
     * @return Response
     */
    public function myTransfer() {
          
      //  $this->resourceView = 'centerUser.transaction.mytransfer';
        $this->params['user_id'] = Session::get('user_id');
        if(empty($this->params['type_id'])){
            $this->params['type_id'] = implode(',',self::$aTransactionTypeMyTransfer);
        }
        if(Session::get('is_agent')  && !empty($this->params['username'])  && $this->params['username']!= Session::get('username')){
            $oUser = User::findUser($this->params['username']);
            $oSelf = User::find(Session::get('user_id'));
            if(!$oUser || ($oUser->parent_id !=Session::get('user_id') && $oSelf->parent_id != $oUser->id )) 
                return Redirect::route('user-transactions.mytransfer', Session::get('user_id'))->withInput()->with('error', '此用户不是你的直属上/下级。');
            $this->params['related_user_name'] = $this->params['username'];
            unset($this->params['username']);
            unset($this->params['user_id']);
        }
 
        return parent::index();
    }
}
