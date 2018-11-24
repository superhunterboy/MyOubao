<?php

class DistrictController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
    // protected $resourceView = 'admin.admin';

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'District';

    protected function beforeRender()
    {
        parent::beforeRender();
        $aProvinces = District::getAllProvinces();
        $this->setVars(compact('aProvinces'));
    }

    public function generateWidget()
    {
        $aDistricts = District::getCitiesByProvince();
        $sRootPath = realpath(app_path() . '/../widgets/');
        $sPath = $sRootPath . '/data/';
        $sDataName = 'province_cities';
        $sFile = $sDataName . '.blade.php';
        $sDisplayName = $sFile;
        $sFileName = $sPath . $sFile;
        // pr($sFileName);exit;
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
        if (!$bSucc = @file_put_contents($sFileName, "var selectorData =  " . json_encode($aDistricts) . '')){
            break;
        }
        $sLangKey = '_basic.' . ($bSucc ? 'generated' : 'file-write-fail');
        $aReturn = [
            'successful' => $bSucc,
            'message' => __($sLangKey, ['file' => $sDisplayName])
        ];
        $sKey = $aReturn['successful'] ? 'success' : 'error';
        return $this->goBackToIndex($sKey, $aReturn['message']);
    }

}
