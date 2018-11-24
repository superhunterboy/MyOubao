<?php

class MenuController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'UserCenterMenu';

 

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
            parent::beforeRender();
            $aMenus = UserCenterMenu::getAllMenus();
            $this->setVars(compact('aMenus'));
    }
    
    public function index(){
        return parent::index();
    }
  
    public function create($id = null) {
        if (Request::method() == 'POST') {
            $oMaxId = UserCenterMenu::getLastId();
            $this->model->id = $oMaxId->id + 1;
            if (isset($_POST['parent_id']) && $_POST['parent_id']) {
                $oPids = UserCenterMenu::find($_POST['parent_id']);
                if ($oPids->parent_ids) {
                    $this->model->parent_ids = $oPids->parent_ids . ',' . $_POST['parent_id'];
                } else {
                    $this->model->parent_ids = $_POST['parent_id'];
                }
                if ($oPids->child_ids) {
                    $child_ids = $oPids->child_ids . ',' . $this->model->id;
                } else {
                    $child_ids = $this->model->id;
                }
            }else{
                $this->model->parent_id = 0;
            }
            DB::connection()->beginTransaction();
            if ($bSucc = $this->saveData($id)) {
                if (isset($_POST['parent_id']) && $_POST['parent_id']) {
                    $bStatus = UserCenterMenu::updateChilds($_POST['parent_id'], $child_ids);
                    if (! $bStatus) {
                        DB::connection()->rollback();
                        $this->langVars['reason'] = & $this->model->getValidationErrorString();
                        return $this->goBack('error', __('_basic.create-fail', $this->langVars));
                    }
                }
                  DB::connection()->commit();
                  return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
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
    
        

    public function edit($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            $params = Input::all(); //
            DB::connection()->beginTransaction();
            $oChilds = UserCenterMenu::getPid($this->model->parent_id);
            if (isset( $oChilds ) && $oChilds) {
            $sChilds = preg_replace('# #', '', $oChilds->child_ids);
            /*             * *****更新原来父级的子集id start******** */
            if ($params['parent_id'] != $oChilds->id) {     //
                if (strpos($sChilds, ',' . $id) === false) {
                    $sChilds = str_replace($id, '', $sChilds);
                } else {
                    $sChilds = str_replace(',' . $id, '', $sChilds);
                }
                $bStatus = UserCenterMenu::updateChilds($this->model->parent_id, $sChilds);
                if (!$bStatus) {
                    DB::connection()->rollback();
                    return $this->goBack('error', __('_basic.update-fail', $this->langVars));
                }
            }
            /*             * *****更新原来父级的子集id end******** */

            /*             * *****更新本身的父级ids start******** */
            $oPids = UserCenterMenu::getPid($params['parent_id']);
            if ($oPids->parent_ids) {
                $this->model->parent_ids = $oPids->parent_ids . ',' . $params['parent_id'];
            } else {
                $this->model->parent_ids = $params['parent_id'];
            }
            /*             * *****更新本身的父级ids start******** */

            /*             * *****更新现在的父级子集ids start******** */
            $oPidNow = UserCenterMenu::getPid($params['parent_id']);
            if ($oPidNow->child_ids) {
                $sChilds = $oPidNow->child_ids . ',' . $this->model->id;
            } else {
                $sChilds = $this->model->id;
            }
            $bStatus = UserCenterMenu::updateChilds($params['parent_id'], $sChilds);
            if (!$bStatus) {
                DB::connection()->rollback();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
            /*             * *****更新现在的父级子集ids end******** */

            /*             * *****更新下级子集ids的父级ids start******** */
            $oChildsNow = UserCenterMenu::getPid($id);
            if ($oChildsNow->child_ids) {
                $pids = $this->model->parent_ids . ',' . $id;
                $bStatus = UserCenterMenu::updatePids($oChildsNow->child_ids, $pids);
                if (!$bStatus) {
                    DB::connection()->rollback();
                    return $this->goBack('error', __('_basic.update-fail', $this->langVars));
                }
            }
            /*             * *****更新下级子集ids的父级ids end******** */
                if ($bSucc = $this->saveData($id)) {
                    DB::connection()->commit();
                    return $this->goBackToIndex('success', __('_basic.edit', $this->langVars));
                } 
            }else {
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

    /**
     * 删除
     * @param type $id
     */
//    public function destroy($id){
//        $oPidNow = UserCenterMenu::getPid($id);
//        /********更新父级的子集ids start***************/
//        if( $oPidNow->parent_id ){
//            $oChilds = UserCenterMenu::getPid($oPidNow->parent_id);
//             $sChilds = preg_replace('# #', '', $oChilds->child_ids);
//              if (strpos($sChilds,','.$id) === false) {
//                    $sChilds = str_replace($id, '', $sChilds); 
//                }else{
//                    $sChilds = str_replace(','.$id, '', $sChilds); 
//                }
//               $bStatus = UserCenterMenu::updateChilds($oPidNow->parent_id, $sChilds);
//        }
//        /********更新父级的子集ids end***************/
//        
//        /********更新子集的父级以及ids start***************/
//        if ( $oPidNow->child_ids ) {
//            
//        }    
//        $bSucc = $this->saveData($id)
        /********更新子集的父级以及ids end***************/
//        
//    }
}

