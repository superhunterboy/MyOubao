<?php
/**
 * 功能管理控制器
 */
class FunctionalityController extends AdminBaseController {

    protected $modelName = 'Functionality';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $this->setVars('aValidRealms', $sModelName::$realms);
        switch($this->action){
//            case 'index':
            case 'view':
//                $functionalitiesTree = $this->model->getTitleList();
//                $this->setVars('functionalitiesTree', $functionalitiesTree);
                break;
            case 'edit':
            case 'create':
                $this->model->getTree($functionalitiesTree,null,null,['title' => 'asc']);
                $this->setVars('aSearchConfigs', SearchConfig::getTitleList());
                $this->setVars('functionalitiesTree', $functionalitiesTree);
                break;
        }
    }
    
    /** 
     * 自动创建CRUD权限
     *
     * @param int $id
     */
    function createSubFunctionalities($id = null) {
        if (!$id && isset($this->params['parent_id'])){
            $id = $this->params['parent_id'];
        }
        if (!$id) {
            return $this->goBack('error', __('InValid functionality'));
        }
        $aActions = array('create', 'view', 'edit', 'destroy');
        if (!$oParentFunctionality = $this->model->find($id)) {
            return $this->goBack('error', __('The functionality not exists!'), true);
        }
        if (!$oParentFunctionality->need_curd) {
            return $this->goBack('error', __('The functionality could not use this action.'));
        }
        $oSubFunctionalities = $this->model->doWhere(['parent_id' => ['=',$id]])->get(['id','controller','action','sequence']);
        $aHadActions = [];
        $iMaxSequence = 0;
        foreach ($oSubFunctionalities as $oFunctionality) {
            if (in_array($oFunctionality->action, $aActions)) {
                $aHadActions[] = $oFunctionality->action;
            }
            $oFunctionality->sequence <= $iMaxSequence or $iMaxSequence = $oFunctionality->sequence;
        }
//        pr($aHadActions);
//        pr($aActions);
        $aNeedActions = array_diff($aActions, $aHadActions);
//        pr($aNeedActions);
//        exit;
//        // 开启事务
        $this->model->getConnection()->beginTransaction();
        if (!$aNeedActions) {
            return $this->goBack('error', __('The CURD functionalities has been existed!'));
        }
        $aWords = explode(' ', $oParentFunctionality->title);
//        $sResourceName = String::singular(array_pop($aWords));
        $sResourceName = str_replace('Controller', '', $oParentFunctionality->controller);
        $sResourceName = String::slug(String::humenlize($sResourceName),'_');
        $bSucc = true;
        foreach ($aNeedActions as $sAction) {
            $aNewData = [
                'parent_id' => $id,
                'title' => String::humenlize($sAction) . ' ' . String::humenlize($sResourceName),
                'controller' => $oParentFunctionality->controller,
                'action' => $sAction,
                'realm' => $oParentFunctionality->realm,
                'menu' => 0,
                'need_curd' => 0,
                'disabled' => 0,
                'sequence' => $iMaxSequence += 10,
            ];
            $oFunctionality = new Functionality;
            $oFunctionality->fill($aNewData);
            if (!$bSucc = $oFunctionality->save(Functionality::$rules)){
                break;
            }
        }
        $bSucc ? $this->model->getConnection()->commit() : $this->model->getConnection()->rollBack();
        $sMessage = $bSucc ? __('Successful: The CURD functionalities has been created.') : __('Failure: The CURD functionalities could not be created. Please, try again.');
        return $this->goBack('message', $sMessage);
    }

    /**
     * auto create default relations
     * @return response
     */
    function updateRelations() {
        $aFields = array('id', 'title', 'controller', 'action', 'sequence');
        $aActions = array(
            'index' => array(
                'create' => 'page',
                'edit' => 'item',
                'view' => 'item',
                'destroy' => 'item',
            ),
            'list' => array(
                'create' => 'page',
                'edit' => 'item',
                'view' => 'item',
                'destroy' => 'item',
            ),
            'view' => array(
                'index' => 'page',
                'edit' => 'page',
                'destroy' => 'page',
            ),
            'create' => array(
                'index' => 'page',
            ),
            'edit' => array(
                'index' => 'page',
                'destroy' => 'page',
            ),
        );
        $aNeedActions = array();
        $oFunctionality = new Functionality;
        $oFunctionalityRelation = new FunctionalityRelation;
        foreach ($aActions as $sMainAction => $aSubActions) {
            $aCondtions = [
                'action' => ['=', $sMainAction]
            ];
            $oBaseFunctionalities = $oFunctionality->doWhere($aCondtions)->get($aFields);
            
            foreach ($oBaseFunctionalities as $oFunctionalityInfo) {
                $aData = $oFunctionalityInfo->getAttributes();
                $aSubCondtions = [
                    'controller' => ['=', $aData['controller']],
                    'action' => ['in', array_keys($aSubActions)]
                ];
                $oSubFunctionalities = $oFunctionality->dowhere($aSubCondtions)->get($aFields);
                foreach ($oSubFunctionalities as $oSubFunctionalityInfo) {
                    $aSubData = $oSubFunctionalityInfo->getAttributes();
                    if (array_key_exists($aSubData['action'], $aSubActions)) {
                        $aTmpConditions = [
                            'functionality_id' => ['=', $aData['id']],
                            'r_functionality_id' => ['=', $aSubData['id']]
                        ];
                        $oExistRelation = $oFunctionalityRelation->doWhere($aTmpConditions)->first();
                        if (empty($oExistRelation->id)) {
                            $bForPage = intval($aSubActions[$aSubData['action']] == 'page');
                            $bForItem = 1 - $bForPage;
                            if ($bForItem) {
                                $aParts = explode(' ', $aSubData['title']);
                                $sLabel = $aParts[0];
                            }
                            else{
                                $sLabel = $aSubData['title'];
                            }
                            $aNeedActions[] = array(
                                'functionality_id' => $aData['id'],
                                'r_functionality_id' => $aSubData['id'],
                                'for_page' => $bForPage,
                                'for_item' => $bForItem,
                                'label' => $sLabel,
                                'sequence' => $aSubData['sequence'],
                                'use_redirector' => intval($aSubData['action'] == 'index')
                            );
                        }
                    }
                }
            }
        }
//            pr($aNeedActions);
//            exit;
        // begin Transaction
        $this->model->getConnection()->beginTransaction();
        // 写数据
        $bSucc = true;
        foreach ($aNeedActions as $aRelation) {
            $oNewRelation = new FunctionalityRelation($aRelation);
            if (!$bSucc = $oNewRelation->save(FunctionalityRelation::$rules)) {
                break;
            }
        }
        $bSucc ? $this->model->getConnection()->commit() : $this->model->getConnection()->rollback();
        $sMessage = $bSucc ? __('Successful: The functionality relations has been created.') : __('Failure: The functionality relations could not be created. Please, try again.');
        return $this->goBackToIndex('success', $sMessage);
    }

    /**
     * 根据实际情况修改验证规则
     * @param model $oModel
     * @return array
     */
    protected function & _makeVadilateRules($oModel) {
        $sClassName = get_class($oModel);
        $id = $oModel->id or $id = 'null';
        $iParentId = $oModel->parent_id or $iParentId = null;
        if (isset($sClassName::$rules['title'])){
            $aRules = array_merge($sClassName::$rules, ['title' => sprintf($sClassName::$rules['title'], $id, $iParentId)]);
        }
        else{
            $aRules = & $sClassName::$rules;
        }
//        pr($iParentId);
        if(empty($iParentId)){
            unset($aRules['controller'], $aRules['action']);
        }
//        pr($aRules);
//        exit;
//        if ($sClassName::$treeable) {
//            $aRules['action'] .= '|' . $this->unique('action', $oModel->id, ['controller' => $oModel->controller]);
////            $aRules['controller'] .= '|' . $this->unique('controller', $oModel->id, ['parent_id' => $oModel->parent_id]);
//        }
//        pr($aRules);
//        exit;
        return $aRules;
    }

}

