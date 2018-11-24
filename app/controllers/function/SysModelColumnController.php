<?php

class SysModelColumnController extends AdminBaseController
{
    protected $modelName = 'SysModelColumn';
    
    public function updateModels(){
        $sFile = realpath(app_path() . '/../vendor/composer/autoload_classmap.php');
        if (!is_readable($sFile)){
            return $this->goBackToIndex('error', __('_basic.file-missing', ['file' => basename($sFile)]));
        }
        $sModelRoot = app_path() . '/models/';
//        die($sModelRoot);
        $aClassMaps = include($sFile);
//        pr($aClassMaps);
        DB::connection()->beginTransaction();
        $bSucc = true;
        foreach($aClassMaps as $sClass => $sPath){
            if (strpos($sPath, $sModelRoot) === false) continue;
            $sTableName = String::plural(String::snake($sClass));
            $oSysModel = SysModel::firstOrNew(['table_name' => $sTableName]);
//            $oSysModel = SysModel::firstOrNew(['table_name' => $sTableName, 'name' => $sClass]);
            $oSysModel->name = $sClass;
            
            if (!$bSucc = $oSysModel->save(SysModel::$rules)){
                break;
            }
        }
        $aReplace = ['resource' => $this->resourceName];
        $sLangKey = '_basic.';
        if ($bSucc){
            $sLangKey .= 'imported';
            $sMsgType = 'success';
            DB::connection()->commit();
        }
        else{
            $sLangKey .= 'import-fail';
            $sMsgType = 'error';
            DB::connection()->rollback();
            $aReplace['reason'] = & $oSysModel->getValidationErrorString();
        }
        return $this->goBackToIndex($sMsgType, __($sLangKey, $aReplace));
    }
}