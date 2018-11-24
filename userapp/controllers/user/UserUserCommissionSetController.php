<?php

class UserUserCommissionSetController extends UserBaseController {

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $resourceView = 'centerUser.userCommissionSet';
    protected $modelName = 'UserCommissionSet';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
    }

    public function index(){
        $this->params['parent_id'] = Session::get('user_id');
        return $this->render();
    }

    public function diffCommissionRate($id){
        $oRate = $this->model->find($id);

        if (Request::method() == 'POST')
        {
            if($oRate && isset($this->params['commission_rate']) && Session::get('user_id') == $oRate->parent_id)
            {
                $oRate->commission_rate = $this->params['commission_rate'];
                $aReturnMsg = $oRate->validateData();

                if($aReturnMsg['success'])
                {
                    if($oRate->save())
                    {
                        $this->halt(true, 'commission_success', null);
                    }
                    else{
                        $this->halt(false, 'commission_failed', null);
                    }
                }
                else{
                    $this->halt(false, 'commission_error', null);
                }
            }
            else{
                $this->halt(false, 'commission_missing', null);
            }

        }else{
            $rates = $oRate->getDiffCommissionRate();
            $this->halt(true, 'info', null, $a, $a, $rates);
        }

    }

}
