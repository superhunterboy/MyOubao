<?php

class ActivityController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'Activity';

//    public function index() {
//        $i = 0;
//        $sBeginDate = date('Y-m-d', strtotime("2014-12-24" . "+1 days"));
////        $sBeginDate = date('Y-m-d', strtotime($sBeginDate . ' this monday'));
//        $fActivityEndTime = '2015-02-08';
//        //todo：事务
//        while ($i < 4) {
//            $sEndDate = date('Y-m-d', strtotime($sBeginDate . ' this sunday'));
//            if (strtotime($sEndDate) <= strtotime($fActivityEndTime)) {
//                pr($sBeginDate);
//                pr($sEndDate);
//                pr('=======================');
//            } else {
//                break;
//            }
//            $i++;
//            $sBeginDate = date('Y-m-d', strtotime($sBeginDate . '+1 weeks  last monday'));
//        }
//        exit;
//    }

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
                break;
            case 'create':
                break;
        }
    }

    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            DB::connection()->beginTransaction();
            $aData['admin_id'] = Session::get('admin_user_id');
            $aData['admin_name'] = Session::get('admin_username');
            $aData = array_merge($aData, $this->params);
            $this->model->fill($aData);
            if ($bSucc = $this->model->save()) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
//                pr($this->langVars);
//                exit;
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;
            //            if ($sModelName::$treeable){
//                $sFirstParamName = 'parent_id';
//            }
//            else{
//foreach($this->paramSettings as $sFirstParamName => $tmp){
//                    break;
//                }
            list($sFirstParamName, $tmp) = each($this->paramSettings);
            //            }
            // pr($sModelName);
            // exit;
            !isset($sFirstParamName) or $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));

            return $this->render();
        }
    }

}
