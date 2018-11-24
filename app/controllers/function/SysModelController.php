<?php

class SysModelController extends AdminBaseController
{
    protected $modelName = 'SysModel';
    
    public function updateModels(){

        $sFile = realpath(app_path() . '/../vendor/composer/autoload_classmap.php');
        if (!is_readable($sFile)){
            return $this->goBackToIndex('error', __('_basic.file-missing', ['file' => basename($sFile)]));
        }
        $sModelRoot = realpath(app_path() . '/../models');
        $sAppModelRoot = realpath(app_path() . '/models');

        $aClassMaps            = include($sFile);
//        pr($aClassMaps);
//        die($sModelRoot);
        DB::connection()->beginTransaction();

        $sDatabase  = DB::connection()->getConfig('database');
        $sTable     = $this->model->getTable();
        $oColumn      = new SysModelColumn;
        $sColumnTable = $oColumn->getTable();
        $sColumnsOfColumnTable = 'db,table_name,name,ordinal_position,column_default,is_nullable,data_type,max_length,charset_name,column_type,column_comment';
        DB::statement("truncate $sColumnTable");
        DB::statement("truncate $sTable");

        $sGetSql = "select table_schema,table_name,column_name,ordinal_position,column_default,is_nullable,data_type,CHARACTER_MAXIMUM_LENGTH,CHARACTER_SET_NAME,column_type,column_comment from information_schema.columns where table_schema = '$sDatabase'";
//        DB::connection()->
        $sql                   = "insert ignore into $sColumnTable ($sColumnsOfColumnTable) $sGetSql";
        DB::connection()->insert($sql);
        $sql                   = "insert ignore into $sTable ( name,db,table_name ) select table_name,table_schema,table_name from information_schema.tables where table_schema = '$sDatabase'";
        DB::connection()->insert($sql);

        $bSucc = true;
        foreach($aClassMaps as $sClass => $sPath){
            if (strpos($sPath, $sModelRoot) === false && strpos($sPath, $sAppModelRoot) === false) continue;
            if (!method_exists($sClass,'getTable')) continue;
            if ($sClass == 'BaseModel') continue;
            if (get_parent_class($sClass) != 'BaseModel') continue;

//            $sTableName = String::plural(String::snake($sClass));
            $sTableName = App::make($sClass)->getTable();
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
            $sql      = "update $sColumnTable c,$sTable m set c.sys_model_id = m.id,c.sys_model_name = m.name where c.table_name = m.table_name";
            DB::connection()->insert($sql);
            DB::connection()->commit();
            $sLangKey .= 'imported';
            $sMsgType = 'success';
        }
        else{
            DB::connection()->rollback();
            $sLangKey .= 'import-fail';
            $sMsgType = 'error';
//            $aErrorMessage = $oSysModel->validationErrors->toArray();
////            pr($aErrorMessage);
////            exit;
//            $aErrMsg = '';
//            foreach($aErrorMessage as $sColumn => $sMsg){
//                $aErrMsg[] = $sColumn . ': ' . implode(',',$sMsg);
//            }
            $aReplace['reason'] = & $oSysModel->getValidationErrorString();
        }
        return $this->goBackToIndex($sMsgType, __($sLangKey, $aReplace));
    }
}