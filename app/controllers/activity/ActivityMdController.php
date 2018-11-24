<?php

class ActivityMdController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActivityMdAdd';


    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() 
    {
        parent::beforeRender();
   
    }

    
    public function distribute(){
        $oActivity = ActivityMdAdd::first();
//        pr($oActivity);exit();
        if(isset($oActivity) && $oActivity){
             $this->action = "edit";
            return$this->edit($oActivity->id);
        }else{
            $id = null;
            $this->action = "create";
            return$this->create($id);
        }
    }
    /**
     * 资源创建页面
     * @return Response
     */
   /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
//        echo 33;exit();
        if (Request::method() == 'POST') {
            DB::connection()->beginTransaction();
//            $this->model->bank_id = $id;
            $this->model->admin_id = Session::get('admin_user_id');
            $this->model->admin_name = Session::get('admin_username');
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
//                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
                return Redirect::route('activity-md-rules.index');
            } else {

                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();

                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;

            list($sFirstParamName, $tmp) = each($this->paramSettings);
      
            !isset($sFirstParamName) or $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));

            return $this->render();
        }
    }


/**
     * 资源编辑页面
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
//        $oMbank = Mbank::getCheckMbank($id);
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();
            $this->model->admin_id = Session::get('admin_user_id');
            $this->model->admin_name = Session::get('admin_username');
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                 return Redirect::route('activity-md-rules.index');
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            // $table = Functionality::all();
//            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $isEdit = true;
            $this->setVars(compact('data', 'isEdit', 'id'));
            return $this->render();
        }
    }

    
}

