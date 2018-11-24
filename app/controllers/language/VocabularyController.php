<?php
/**
 * 词汇管理控制器
 */
class VocabularyController extends AdminBaseController {

    protected $modelName = 'Vocabulary';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();

        $aDicts = & Dictionary::getTitleList();
        $this->setVars(compact('aDicts'));
    }

    /**
     * 导入所有需要国际化的信息
     * @param int $id 字典id
     */
    public function import($id = null) {
        $iDictionaryId = $id;
        if ($iDictionaryId == null) {
            $sMsg = __(sprintf('invalid dictionary !'));
            return $this->goBackToIndex('error', $sMsg);
        }
        $aDic = Dictionary::find($iDictionaryId)->getAttributes();
//        pr($aDic);
//        exit;
        if (is_array($aDic) && $aDic['models']) {
            $this->model->getConnection()->beginTransaction();
            $aVocabulary = $aKeyWords = [];
            foreach (explode(",", $aDic['models']) as $val) {
                $aConfig = explode('|', $val);
                $aModel = explode(".", $aConfig[0]);
                $sModelName = $aModel[0];
                $sField = count($aModel) == 2 ? $aModel[1] : $sModelName::$titleColumn;
//                die($sField);
//                    continue;
                $oModel = new $sModelName;
                $aConditions = [];

                if (isset($aConfig[1])){
//                    die($aConfig[1]);
                    $aTmp = explode('=',$aConfig[1]);
                    $aConditions = [
                        $aTmp[0] => ['=', $aTmp[1]]
                    ];
                }
                else{
//                    echo $aModel[1];
                    $aConditions = [
                        $aModel[1] => ['!=', ''],
//                        $aModel[1] => ['!=', 'null'],
                    ];
                }
//                pr($aConditions);
//                pr($sField);
//                exit;
                $aValues = $oModel->getValueListArray($sField, $aConditions);
//                pr($aValues);
//                exit;
                $aFields = [$sField];
                $aTranslateFields = [];
                if ($aDic['en_column']){
                    $aFields[] = $aDic['en_column'];
                    $aTranslateFields['en'] = $aDic['en_column'];
                }
                if ($aDic['zh_column']){
                    $aFields[] = $aDic['zh_column'];
                    $aTranslateFields['zh_cn'] = $aDic['zh_column'];
                }
//                !$aDic['zh_column'] or $aFields[] = $aTranslateFields[] = $aDic['zh_column'];
                $aOriValues = $oModel->doWhere($aConditions)->get($aFields)->toArray();
//                pr($aOriValues);
//                exit;
                $aValues = [];
                foreach($aOriValues as $aInfo){
                    $aValues[strtolower($aInfo[$sField])] = $aInfo;
                }
//                pr($aValues);
//                exit;
                $aToImportKeywords = array_keys($aValues);
//                pr($aToImportKeywords);
//                exit;
                $aToImportKeywords = array_map('strtolower', $aToImportKeywords);
                $aKeyWords         = array_merge($aKeyWords, $aToImportKeywords);
//                pr($aKeyWords);
//                exit;
            }
            $aKeyWords = array_unique($aKeyWords);
            $aConditions = [
                'dictionary_id' => ['=', $iDictionaryId],
//                'keyword' => ['in', $aKeyWords],
            ];
            empty($aKeyWords) or $aConditions['keyword'] = ['in', $aKeyWords];
//            pr($aKeyWords);
//            pr($aConditions);
            $aExistKeyWords = $this->model->getValueListArray('keyword', $aConditions, null, false);
//            pr($aExistKeyWords);
            $aKeyWords = array_diff($aKeyWords, $aExistKeyWords);
//            pr($aKeyWords);
//            exit;
            foreach ($aKeyWords as $sKeyword) {
                $data = [
                    'dictionary_id' => $iDictionaryId,
                    'dictionary' => $aDic['name'],
                    'keyword' => $sKeyword
                ];
                foreach ($aTranslateFields as $sToField => $sKey) {
                    $data[$sToField] = $aValues[$sKeyword][$sKey];
                }
                $aVocabulary[] = $data;
            }
//            pr($aVocabulary);
//            exit;
            if (count($aVocabulary)) {
                if ($bSucc = $this->model->insert($aVocabulary)) {
                    DB::connection()->commit();
                    $sMsg = __('_basic.imported',$this->langVars);
                    $sMsgKey = 'success';
                } else {
                    DB::connection()->rollback();
                    $this->langVars['reason'] = $this->model->getValidationErrorString();
                    $sMsg = __('_basic.import-fail', $this->langVars);
                    $sMsgKey = 'error';
                }
            } else {
                $sMsg = __('_basic.no-data');
                $sMsgKey = 'error';
            }
            return $this->goBack($sMsgKey, $sMsg);
        }
        return $this->goBack('error', __('_basic.no-data'));
    }

}