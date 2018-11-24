<?php

class DictionaryController extends AdminBaseController {

    protected $modelName = 'Dictionary';

    /**
     * 从模型表的数据建立对应的字典
     * @return Redirect
     */
    public function createFromModels(){
        $aModels = & SysModel::getTitleList();
        foreach ($aModels as $sModelName){
            $aConditons  = [
                'name' => [ '=',$sModelName]
            ];
            $modelName  = $this->modelName;
            $oQuery      = $modelName::doWhere($aConditons);
            $oDictionary = $oQuery->get(['id'])->first();
            if (!empty($oDictionary)){
                continue;
            }
//            pr($sModelName . ' not exists');
//            continue;
            $aDictionary = [
                'name'      => $sModelName,
                'models'    => 'SysModelColumn.name|sys_model_name=' . $sModelName,
                'zh_column' => 'column_comment'
            ];
//            pr($aDictionary);
            $aFailed     = [];
            $oDictionary = new Dictionary($aDictionary);
            if (!$oDictionary->save()){
//                pr($oDictionary->validationErrors->toArray());
                $aFailed[] = $sModelName;
            }
        }
        if ($bSucc = empty($aFailed)){
            $sKey     = 'success';
            $sMessage = __('_dictionary.model-dictionaries-created');
        }
        else{
            $sKey     = 'error';
            $sMessage = __('_dictionary.model-dictionaries-create-failed',['models' => implode(',',$aFailed)]);
        }
        return $this->goBackToIndex($sKey,$sMessage);
    }

    /**
     * 生成all语言包文件，存放在相应语言包位置app/lang/en|zh-CN
     * @return response
     */
    public function generateAll(){
        $aDics = & Dictionary::getTitleList();
        $bSucc = true;
        foreach($aDics as $id => $name){
            $aReturn = $this->_generate($id);
            if (!$bSucc = $aReturn['successful']){
                break;
            }
        }
        $sKey = $bSucc ? 'success' : 'error';
        $sMsg = $bSucc ? __('_basic.all-generated',$this->langVars) : $aReturn['message'];
        return $this->goBackToIndex($sKey, $sMsg);
    }

    /**
     * 生成语言包文件，存放在相应语言包位置app/lang/en|zh-CN
     * @param int $iDirectoryId 字典id
     * @return response
     */
    public function generate($iDirectoryId = null) {
        if ($iDirectoryId == null) {
            $sMsg = __('_basic.missing', $this->langVars);
            return $this->goBackToIndex('error', $sMsg);
        }
        $aReturn = $this->_generate($iDirectoryId);
        $sKey = $aReturn['successful'] ? 'success' : 'error';
        return $this->goBackToIndex($sKey, $aReturn['message']);
    }
    
    /**
     * 生成语言包文件，存放在相应语言包位置app/lang/en|zh-CN
     * @param int $iDirectoryId 字典id
     * @return array    [successful,message]
     */
    private function _generate($iDirectoryId = null) {
        if ($iDirectoryId == null) {
            $sMsg = __('invalid dictionary!');
            return [
                'successful' => false,
                'message' => __('_basic.missing', $this->langVars)
            ];
        }
        $aConditions = [
            'dictionary_id' => ['=',$iDirectoryId],
        ];
//        pr(Str::slug('zh-CN', '_'));exit;
        $sLanguages = SysConfig::readValue('sys_support_languages');
        $aLanguages = explode(',', $sLanguages);
        
        $aLangs = [];
        $oVocabulary = new Vocabulary;
        $aOriginalColumns = Schema::getColumnListing( $oVocabulary->getTable() );

        $aMissingFields = [];
        foreach($aLanguages as $sLang){
            $sField = Str::slug($sLang, '_');
            if (!in_array($sField, $aOriginalColumns)){
                $aMissingFields[] = $sLang;
            }
            $aFields[] = $sField;
            $aLangs[$sField] = $sLang;
        }
        if ($aMissingFields){
            return [
                'successful' => false,
                'message' => __('_basic.generate-fail') . ':' . __('_basic.missing-columns', ['columns' => implode(',',$aMissingFields)])
            ];
        }
        unset($aOriginalColumns);

        $aFields[] = 'keyword';
        $sDicName = strtolower(Dictionary::find($iDirectoryId)->getAttribute('name'));
        $sRootPath = app_path() . '/lang/';
        $aWords = [];
        if ($oVocabularies = $oVocabulary->doWhere($aConditions)->get($aFields)){
            foreach ($oVocabularies as $aVal) {
                foreach($aLangs as $sField => $sLang){
                    if (empty($aVal->$sField)) continue;
                    $aWords[$sLang][$aVal->keyword] = $aVal->$sField;
                }
            }
        }
        unset($oVocabularies);
        if (empty($aWords)){
            return ['successful' => true, 'message' => __('_basic.no-data')];
        }
        $bSucc = true;
        $sDisplayName = null;
        foreach($aWords as $sLang => $aWordOfLang){
            $sPath = $sRootPath . $sLang;
            $sFile = '_' . $sDicName . '.php';
            $sDisplayName = $sLang . '/' . $sFile;
            $sFileName = $sPath . '/' . $sFile;
            if (file_exists($sFileName)){
                if (!is_writable($sFileName)){
                    return ['successful' => false, 'message' => __('_basic.file-not-writable', ['file' => $sDisplayName])];
                }
            }
            else{
                if (!is_writeable($sPath)){
                    return ['successful' => false, 'message' => __('_basic.file-write-fail-path', ['path' => $sPath])];
                }
            }
            if (!$bSucc = @file_put_contents($sFileName, "<?php\nreturn " . var_export($aWordOfLang, true) . ';')){
                break;
            }
        }
        $sLangKey = '_basic.' . ($bSucc ? 'generated' : 'file-write-fail');
        $aReturn = [
            'successful' => $bSucc,
            'message' => __($sLangKey, ['file' => $sDisplayName])
        ];
        return $aReturn;
    }

}