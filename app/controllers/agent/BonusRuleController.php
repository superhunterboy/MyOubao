<?php

class BonusRuleController extends AdminBaseController {

    protected $modelName = 'BonusRule';
    protected $resourceView = 'agent.BonusRule';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
            
        switch ($this->action) {
            case 'index':
            case 'view':
            case 'edit':
            case 'create':

            $this->setVars('aUserLevers', BonusRule::$aUserLevers);
                break;
        }
    }
    
    
    public function index(){
         $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;

        $datas = $oQuery->paginate(static::$pagesize);
        
        foreach($datas as $oV){
            $HTML = "<table class='table table-striped table-hover table-bordered text-center'><tr>";
            $aOextra = $oV->extraBonusPolicy;
            foreach($aOextra as $oExtra){
                $HTML.="<td>".$oExtra->loss_formatted."</td>";
            }
            $HTML.="</tr><tr>";
            foreach($aOextra as $oExtra){
                $HTML.="<td>".$oExtra->rate_formatted."</td>";
            }        
            $HTML.= "</tr></table>";
            $oV->extraBonusPolicy = $HTML;
        }
        
        $this->setVars(compact('datas'));
        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1];
            } else {
                $$sMainParamName = null;
            }
            $this->setVars(compact($sMainParamName));
        }
        return $this->render();
    }
    
      /**
     * 资源编辑页面
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            $aData = Input::all();
            DB::connection()->beginTransaction();
            if (  $this->saveData($id) && $this->saveExtraBonusRule($aData) ) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            // $table = Functionality::all();
            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit', 'id'));
            return $this->render();
        }
    }
    
    private function saveExtraBonusRule($aData){
        if(!isset($aData['extra_id'])) return true;
        foreach($aData['extra_id'] as $key=>$bonus_id){
                   
                    if($bonus_id) $oExtraBonusRule = ExtraBonusPolicy::find($bonus_id);
                    else  $oExtraBonusRule = new ExtraBonusPolicy; 
                    $aRules = & $this->_makeVadilateRules($oExtraBonusRule);
                    $oExtraBonusRule->bonus_rules_id = $this->model->id;
                    $oExtraBonusRule->loss = $aData['extra_loss'][$key];
                    $oExtraBonusRule->rate = $aData['extra_rate'][$key];
                     
                   if(!$oExtraBonusRule->save($aRules)){
                       return false;break;
                   } 
             }
        return true;
    }
    
            
}