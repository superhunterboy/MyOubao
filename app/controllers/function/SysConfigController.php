<?php

class SysConfigController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
    protected $customViewPath = 'sysConfig';
    protected $customViews = [
        'settings',
        'setValue'
    ];


    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'SysConfig';

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch($this->action){
            case 'settings':
//                $this->resourceView = 'function.sysConfig';
                $this->setVars('aColumnForList', $sModelName::$columnForList);
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
                $aValidDataTypes = $sModelName::$validDataTypes;
                $aValidInputTypes = $sModelName::$validInputTypes;
                $aValidValidateTypes = $sModelName::$validValidateRules;
                $aParentSysConfigs = $sModelName::getGoupArray(true);
                $this->setVars(compact('aValidDataTypes', 'aValidInputTypes', 'aValidValidateTypes', 'aParentSysConfigs'));
                break;
        }
    }

    /**
     * for settings
     */
    public function settings(){
        return $this->index();
    }

    /**
     * 根据实际情况修改验证规则
     * @param model $oModel
     * @return array
     */
    protected function & _makeVadilateRules($oModel) {
        $sClassName = get_class($oModel);
//        $id = $oModel->id or $id = 'null';
        $iParentId = $oModel->parent_id or $iParentId = null;
        $aRules = $sClassName::$rules;
        if (!$iParentId){
            unset($aRules['value'], $aRules['data_type'], $aRules['data_type'], $aRules['form_face'], $aRules['validation']);
        }
//        pr($aRules);
//        exit;
        return $aRules;
    }

    function setValue($id) {
        if (!$id && empty($this->params)) {
            return $this->goBack('error', __('_basic.no-data'));
            $this->_goBackUrl('index');
        }
//        pr($this->request->data);exit;
        $this->model = $this->model->find($id);
        if (Request::method() == 'POST') {
//            pr($this->params);
            $mValue = isset($this->params['value']) ? $this->params['value'] : null;
//            pr($this->model->getAttributes());
            $this->model->value = $mValue;
//            pr($this->model->getAttributes());
            $aTrans = ['item' => $this->model->item];
            if ($bSucc = $this->model->save()){
                return $this->goBackToIndex('success', __('_sysconfig.seted', $aTrans, 0));
            }
            else{
                return $this->goBackToIndex('error', __('_sysconfig.set-failed', $aTrans, 0));
            }
            
        }
        $parent_id = $this->model->parent_id;
        $data = $this->model;
        $isEdit = true;
        $this->setVars(compact('data', 'parent_id', 'isEdit','id'));
        if (in_array($data->form_face, ['select', 'multi_select', 'checkbox', 'radio'])) {
            $aDataSource = $data->getSource($data->data_source);
            $this->setVars(compact('aDataSource'));
        }
        return $this->render();

//        if (empty($this->request->data)) {
//            $this->request->data = $this->SysConfig->read(null, $id);
//        }
//        $this->request->data['SysConfig']['default_value'] = $this->SysConfig->format($this->request->data['SysConfig']['default_value'], $this->request->data['SysConfig']['validation']);
//        $dataValidition = $this->SysConfig->validate['form_face']['inlist']['rule'][1];
//        $parentSysConfigs = $this->SysConfig->ParentSysConfig->find('list');
//        $this->set(compact('parentSysConfigs', 'dataValidition', 'aDataSource'));
    }

}

