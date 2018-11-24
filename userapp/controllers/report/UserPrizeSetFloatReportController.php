<?php

# 用户盈亏报表管理

class UserPrizeSetFloatReportController extends UserBaseController {

    protected $resourceView = 'centerUser.report.prizeSetFloat';
    protected $modelName = 'UserPrizeSetFloat';
    public $resourceName = '';

    public function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                $aUserTypes = User::$aUserTypes;
                $this->setVars('reportName', 'prizesetfloat');
                break;
        }
    }

    public function index() {
        $this->params['status'] = UserPrizeSetFloat::STATUS_USED;
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

}
