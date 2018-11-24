<?php

class SuspiciousCardController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'SuspiciousCard';


    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() 
    {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch ($this->action) 
        {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
        }
    }
//
    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            $username=e(Input::get('username'));
            $account=e(Input::get('account'));
            //用户名是否存在
            if(!$oUser=User::where('username',$username)->first()){
                return $this->goBackToIndex('error', '用户名不存在！');
            }
            //卡号是否存在有效
            if($oSuspiciousCard=SuspiciousCard::where('account',$account)->where('status',SuspiciousCard::DISABLESTATUS)->first()){
                return $this->goBackToIndex('error', '可疑银行卡已存在！');
                }

            $this->model->parent_name=!empty($oUser->parent)?$oUser->parent:'';
            $this->model->parent_id=!empty($oUser->parent_id)?$oUser->parent_id:'';
            $this->model->user_id=$oUser->id;
        } 
        parent::create($id);
    }
    
    
      /**
     * 资源编辑页面
     * @return Response
     */
    public function edit($id = null) {
        if (Request::method() == 'POST') {
            
            $username=e(Input::get('username'));
            $account=e(Input::get('account'));
            //用户名是否正确
            if(!$oUser=User::where('username',$username)->first()){
                return $this->goBackToIndex('error', '用户名不存在！');
            }
               //卡号是否存在有效
            if($oSuspiciousCard=SuspiciousCard::where('account',$account)->where('status',SuspiciousCard::DISABLESTATUS)->first()){
                return $this->goBackToIndex('error', '可疑银行卡已存在！');
                }
            $this->model->parent_name=!empty($oUser->parent)?$oUser->parent:'';
            $this->model->parent_id=!empty($oUser->parent_id)?$oUser->parent_id:'';
            $this->model->user_id=$oUser->id;
        } 
        parent::edit($id);
    }
    

}
