<?php

use Illuminate\Support\Facades\Redis as Redis;
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-7-4
 * Time: 下午2:41
 */

abstract class  CasinoBasic extends BaseModel{
    protected $redis=NULL;
    protected $oLottery=NULL;
    protected $oTable = NULL;
    static $instance=null;
    protected $oEncode=NULL;
    protected $oJacpot=NULL;
    protected $oUser = NULL;
    protected $oAccount=NULL;
    function __construct($oLottery=NULL,$oTable=NULL,$oUser=null)
    {
        $this->oJacpot = BlackJackJacpot::find($oTable->jacpot_id);
        $this->oLottery = $oLottery;
        $this->oTable = $oTable;
        $this->redis = Redis::connection('default');

        if(is_null($oUser))
            $this->oUser = UserUser::find( Session::get('user_id'));
        else
            $this->oUser = $oUser;
        $this->oEncode = new BlackJackEncode($oLottery,$oTable,$this->oUser);
        $this->oAccount = Account::find($this->oUser->account_id);
    }
    static function getInstance($oLottery,$oTable,$aBetData){

       return self::$instance = new $oLottery->model_name($oLottery,$oTable,$aBetData);
    }
    static function doWays(CasinoBasic $casino,$casinoWay){

        $wn_function = $casinoWay->wn_function;
        $casino->wayId = $casinoWay->id;

        return $casino->$wn_function();
    }
    protected function beforeDoWay(){}

}