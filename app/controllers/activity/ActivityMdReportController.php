<?php

class ActivityMdReportController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityMdUser';


    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() 
    {
        parent::beforeRender();
   
    }

    public function index(){
        
        return parent::index();
    }
    
    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        return parent::create($id);
    }
    
     public function view($id) {
        return parent::view($id);
    }
    
     public function edit($id) {
        return parent::edit($id);
    }
    
     public function destroy($id) {
        return parent::destroy($id);
    }
    
    public function sendprize($id){
         $iId = ActivityMdUser::updateSendPrize($id);
         if($iId <=0 ){
             return $this->goBack('error', __('_basic.update-fail', $this->langVars));
         }
          return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
    }
    
}
