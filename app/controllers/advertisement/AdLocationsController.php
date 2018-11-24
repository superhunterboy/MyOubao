<?php

class AdLocationsController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string ad_locations
     */
    //protected $resourceView = 'admin.advertisement';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'AdLocation';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        switch($this->action){
            case 'index':
            case 'view':
            case 'edit':
            case 'create':
                $aTypes = AdType::getAllAdTypeArray();

                $this->setVars(compact('aTypes'));
                //pr($aTypes);exit;
                break;
        }
    }

/**
     *  生成广告位
     * @param  iLocationId  $id
     * @return goBackToIndex
     */
    public function generate($iLocationId = null) {
        $aTypes = AdType::getAllAdTypeArray(); //广告类型
        $aAdInfos = AdInfo::getAdInfosByLocationId($iLocationId); //当前广告位广告详情数据
        $oLocation =AdLocation:: find($iLocationId); //当前广告位数据
        // 检测是否有广告位id
        if ($iLocationId == null) {
            $sMsg = __('_basic.missing', $this->langVars);
            return $this->goBackToIndex('error', $sMsg);
        }

        //抽离模板循环关键部分
        $rTempHtml = $this->rTemplates($aTypes[$oLocation->type_id]);
        //为生成文件做数据准备
        $rTemp=[];
        foreach ($aAdInfos as $key => $oAdInfo) {
                $sHtml = [];
                $search = [];
                $replace = [];
                $oAdInfo->num = $key + 1;
                $oAdInfo->pic_width = $oLocation->pic_width;
                $oAdInfo->pic_height = $oLocation->pic_height;
                foreach ($oAdInfo->toArray() as $key => $value) {
                    $search[] = '%'.$key.'%';
                    $replace[] = $value;
                }
               $rTemp[]= str_replace($search, $replace, $rTempHtml);
        }

        //循环填充模板
        $aLiImg = [];
        $aLiNum = [];
        foreach ($rTemp as $key => $value) {
            $aLiImg[] = $value[0];
            if(count($value) >1){
                $aLiNum[] =  $value[1];
            }

        }
        $aLiImg = implode(" ",  $aLiImg) ;
        $aLiNum = implode(" ",  $aLiNum);
        //合并分离的模板，得到完成新文件内容
         $newHtml = $this->newTemplates($aTypes[$oLocation->type_id],  $aLiImg , $aLiNum);
         //存储模板文件，并返回状态
         $aReturn = $this->generateHtmlFile($newHtml  , $oLocation->id );
        $sKey = $aReturn['successful'] ? 'success' : 'error';
        //回退原路径页面，返回状态信息
        return $this->goBackToIndex($sKey, $aReturn['message']);
    }


    /**
     *读取模板文件
     */
    public function rederTemplates($adType){
            $filename = app_path() . "/views/advertisement/adTemplates/".strtolower($adType).".html";
            Log::info($filename);
            $handle = fopen($filename, "r");
            $contents =fread($handle, filesize ($filename));
            fclose($handle);
            return $contents;
    }

    /*
     *抽离模板循环关键部分
     */
     public function rTemplates($adType){
                $contents =  $this->rederTemplates($adType);
                //正则处理模板-> 得到循环标记标签
                $pz = '/<!\-\-\{split\}\-\->(.*)<!\-\-\{endsplit\}\-\->/Us';
               preg_match_all($pz,$contents,$mat);
                // pr( $mat[1]); exit;
                return $mat[1];
     }

     /**
       *合并拼装html,得到完成新文件内容
       */
     public function newTemplates($adType, $aLiImg ,$aLiNum){
            // pr($aLiNum); exit;
                $contents = $this-> rederTemplates($adType);
                //正则处理模板-> 得到循环标记标签
                $pz = '/<!\-\-\{split\}\-\->(.*)<!\-\-\{endsplit\}\-\->/Us';
               preg_match_all($pz,$contents,$mat);

                $contents= str_replace(  $mat[1][0], $aLiImg ,$contents);
                if($aLiNum){
                    $contents= str_replace(  $mat[1][1], $aLiNum ,$contents);
                }
                return $contents;
     }


     /*
      *完成存储文件模板，返回状态信息
      */
     public function generateHtmlFile($contents  , $aTypes )
    {
        $sPath     = public_path() . '/'.Config::get("adTemp.root").'/';
        $sFileName = $aTypes;
        $sSuffix   = '.blade.php';
        $sFile     = $sPath . $sFileName . $sSuffix;
        if (file_exists($sFile)){
            if (!is_writable($sFile)){
                return ['successful' => false, 'message' => 'File ' . $sFileName . ' not writable'];
            }
        }
        else{
            if (!is_writeable($sPath)){
                return ['successful' => false, 'message' => 'File ' . $sFileName . ' written in Path ' . $sPath . ' not writable'];
            }
        }

        $bSucc    = @file_put_contents($sFile, $contents);
        $sLangKey = ($bSucc ? ' generated' : ' write failed');
        $aReturn  = [
            'successful' => $bSucc,
            'message' => 'File ' . $sFileName . $sLangKey
        ];
        return $aReturn;
    }




}
