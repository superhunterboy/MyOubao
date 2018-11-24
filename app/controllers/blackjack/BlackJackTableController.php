<?php

class BlackJackTableController extends AdminBaseController {
    /**
     * 资源视图目录
     * @var string
     */
    // protected $resourceView = 'cashBonus';
    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'BlackJackTable';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {

        parent::beforeRender();


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










}
