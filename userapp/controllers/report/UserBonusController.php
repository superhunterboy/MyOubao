<?php

# 用户盈亏报表管理

class UserBonusController extends UserBaseController {

    protected $resourceView = 'centerUser.report.bonus';
    protected $modelName = 'Bonus';
    public $resourceName = '';

    public function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                $aUserTypes = User::$aUserTypes;
                $this->setVars(compact('aUserTypes'));
                $this->setVars('reportName', 'bonus');
                break;
        }
    }

    public function index() {
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

}
