<?php

class AdInfoController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string ad_locations
     */
    protected $resourceView = 'advertisement';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'AdInfo';

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
                $aLocations = AdLocation::getAllAdLocationArray();
                $this->setVars(compact('aLocations'));
                break;

          case 'create':
               $aLocationsId = AdLocation::all(['id','name','type_id','type_name']);
                $this->setVars(compact('aLocationsId'));
                break;
        }
    }


/*
 *deleted
  */
  public function destroy($id) {
        $this->model = $this->model->find($id);
        $sModelName = $this->modelName;

        File::delete(
            public_path($this->model->pic_url)
        );

        // pr($this->model->pic_url); exit;
        if ($sModelName::$treeable){
            if ($iSubCount = $this->model->where('parent_id', '=', $this->model->id)->count()) {
                $this->langVars['reason'] = __('_basic.not-leaf', $this->langVars);
                return Redirect::back()->with('error', __('_basic.delete-fail', $this->langVars));
            }
        }
        $sLangKey = '_basic.' . ($this->model->delete() ? 'deleted' : 'delete-fail.');
        return $this->goBackToIndex('success', __($sLangKey, $this->langVars));
    }


/**
     * 资源编辑页面
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $this->model = $this->model->find($id);
        //检测是否有广告位id
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        //是否有图修改判断选择性保存
        if (Request::method() == 'PUT') {
            //pr(Request::method());exit;
            $aDatas = $this->saveImg();
            $oldImage = Input::get('oldimg');
            //判断是否有修改久的图片
            if(  $aDatas[0]['pic_url'] == ''){
                $aDatas[0]['pic_url'] = $oldImage ;
            };
            //多图修改保存
           $bSucc = true;
           DB::connection()->beginTransaction();
           foreach ($aDatas as $data) {
                $sModel = $this->model ->fill($data);
                 if (!$bSucc = $sModel->save([$id])) break;
           }

           //保存并返回原地址，状态信息
            if ($bSucc){
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else{
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else { //无修改图保存方式
            //pr(Request::method());exit;
            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit','id'));
            return $this->render();
        }
    }



    /**
     * 创建广告内容方法
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {

           $aDatas = $this->saveImg();
           $picUrl = $aDatas[0]['pic_url'];

           $bSucc = true;
           DB::connection()->beginTransaction();
           foreach ($aDatas as $data) {
                $sModel = new AdInfo;
                $sModel = $sModel->fill($data);
                 if (!$bSucc = $sModel->save()) break;
           }

            if ($bSucc) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();

                File::delete(
                    public_path($picUrl)
                );

                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }

        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;
            if ($sModelName::$treeable) {
                $sFirstParamName = 'parent_id';
            } else {
                foreach ($this->paramSettings as $sFirstParamName => $tmp) {
                    break;
                }
            }

            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));

            return $this->render();
        }
    }

/*
 *图片上传验证检测方法
 */
 private function  saveImg(){
     $aInputs  = Input::all();
            $sDirPath = public_path() . '/'.Config::get("ad.root").'/';
            $sUrl     = url() . '/'.Config::get("ad.root").'/';
            $sFileObj = 'portrait';
             $bSucc = true;

            $rules = array(
                $sFileObj => 'required|mimes:jpeg,gif,png|max:1024',
            );
            // 自定义验证消息
            $messages = array(
                $sFileObj . '.required' => '请选择需要上传的图片。',
                $sFileObj . '.mimes' => '请上传 :values 格式的图片。',
                $sFileObj . '.max' => '图片的大小请控制在 1M 以内。',
            );
            // pr($sFileObj); exit;
            $picUrl = array();
            $picContent = array();
            $picAD = array();
            foreach ($aInputs['content'] as $key => $value) {
                if( $value != '' ){

                        $validator = Validator::make(  [ 'portrait' => $aInputs['portrait'][$key] ] , $rules, $messages);
                        // pr( $validator ); exit;
                        if ($validator->passes()) {
                           $url = '/'.Config::get("ad.root").'/'. $this->updateFile($aInputs['portrait'][$key] , $sDirPath, $rules, $messages, $sUrl);
                            array_push($picUrl , $url);
                        }
                         array_push($picContent , $value);
                          array_push($picAD , $aInputs['ad_url'][$key]);
                          $bSucc = true;
                }else{
                    $bSucc = false;
                }

            }
            // pr($picContent); exit;
            // pr(Session::get('admin_user_id'));exit;
            $data = [
                'name'                    =>   $aInputs['name'],
                'ad_location_id'   =>  $aInputs['ad_location_id'],
                'is_closed'             => isset($aInputs['is_closed'])  ?  $aInputs['is_closed']  : 0 ,
            ];
            $aDatas = [];
            foreach ($picContent as $key => $value) {
                    $data['pic_url']        = $picUrl ?  $picUrl[$key]  :  '' ;
                    $data['content']      = $value;
                    $data['redirect_url'] = $picAD[$key];
                    $aDatas[] = $data;
            }
            return $aDatas;
 }

/*
 *图片上传方法
 */
    private function updateFile($oFile, $sDirPath, $rules, $messages, $sUrl) {

        file_exists($sDirPath) or mkdir($sDirPath, 0777, 1);
            // pr('asdfasd'); exit;
        $sNewFileName = '';
            //检验一下上传的文件是否有效.
        if (is_object($oFile) && $oFile->isValid()) {
            $ext = $oFile->guessClientExtension();
            $sOriginalName = $oFile->getClientOriginalName(); // 客户端文件名，包括客户端拓展名
            $sNewFileName  = md5($sOriginalName) . '.' . $ext; // 哈希处理过的文件名，包括真实拓展名
            $portrait      = Image::make($oFile->getRealPath());
            $oldImage      = Input::get('oldimg');
        // pr('sfa');
            // pr($oldImage); exit;
            // 删除旧img
                File::delete(
                    public_path($oldImage)
                );

            $portrait->save($sDirPath . $sNewFileName);
             return $sNewFileName;
             //pr($sNewFileName); exit;
        }
         return $sNewFileName;
    }

}