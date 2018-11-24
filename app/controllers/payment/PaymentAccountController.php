<?php

class PaymentAccountController extends AdminBaseController {

    protected $modelName = 'PaymentAccount';
    protected $customViewPath = 'paymentAccount';
    protected $customViews = [
        'create', 'edit'
    ];

    protected function beforeRender() {
        $this->setVars('aValidStatuses', PaymentAccount::$validStatuses);
        $this->setVars('aPlatforms', PaymentPlatform::getTitleList());
        $this->setVars('aPayTypes', PaymentAccount::$aPayTypes);
        parent::beforeRender();
    }

    public function setDefault($id) {
        $oAccount = PaymentAccount::find($id);
        if (!is_object($oAccount)) {
            return $this->goBack('error', __('_paymentaccount.missing-data'));
        }
        $oAccount->is_default = 1;
        $bSucc = $oAccount->save();
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_paymentaccount.default-success'));
        } else {
            return $this->goBack('error', __('_paymentaccount.default-failed'));
        }
    }

    public function close($id) {
        $oAccount = PaymentAccount::find($id);
        if (!is_object($oAccount)) {
            return $this->goBack('error', __('_paymentaccount.missing-data'));
        }
        $oAccount->status = Paymentaccount::STATUS_NOT_AVAILABLE;
        $bSucc = $oAccount->save();
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_paymentaccount.close-success'));
        } else {
            return $this->goBack('error', __('_paymentaccount.close-failed'));
        }
    }
    
        public function open($id) {
        $oAccount = PaymentAccount::find($id);
        if (!is_object($oAccount)) {
            return $this->goBack('error', __('_paymentaccount.missing-data'));
        }
        $oAccount->status = Paymentaccount::STATUS_AVAILABLE;
        $bSucc = $oAccount->save();
        if ($bSucc) {
            return $this->goBackToIndex('success', __('_paymentaccount.open-success'));
        } else {
            return $this->goBack('error', __('_paymentaccount.open-failed'));
        }
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
            $aDatas = $this->saveImg();
            $picUrl = $aDatas[0]['pic_url'];
            //多图修改保存
            $bSucc = true;
            DB::connection()->beginTransaction();
            foreach ($aDatas as $data) {
                $sModel = $this->model->fill($data);
                if (!$bSucc = $sModel->save([$id]))
                    break;
            }

            //保存并返回原地址，状态信息
            if ($bSucc) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else { //无修改图保存方式
            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit', 'id'));
            return $this->render();
        }
    }

    /*
     * 图片上传验证检测方法
     */

    private function saveImg() {
        $aInputs = Input::all();
        $sDir='/depositsQrCode';
        $sDirPath = public_path() . $sDir . '/';
        $sFileObj = 'qrcode';
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
        $aContent = [$aInputs['serial_number']];
        foreach ($aContent as $key => $value) {
            if ($value != '') {

                $validator = Validator::make([ 'qrcode' => $aInputs['qrcode'][$key]], $rules, $messages);
                if ($validator->passes()) {
                    $url = $sDirPath . $this->updateFile($aInputs['qrcode'][$key], $sDirPath, $rules, $messages, $value);
                    array_push($picUrl, $url);
                }else{
                    pr($validator->messages());
                    exit;
                }
                array_push($picContent, $value);
                $bSucc = true;
            } else {
                $bSucc = false;
            }
        }
        // pr($picContent); exit;
        // pr(Session::get('admin_user_id'));exit;
        $aDatas = [];
        foreach ($picContent as $key => $value) {
            $aInputs['pic_url'] = $picUrl ? $picUrl[$key] : '';
            $aInputs['content'] = $value;
            $aDatas[] = $aInputs;
        }
        return $aDatas;
    }

    /*
     * 图片上传方法
     */

    private function updateFile($oFile, $sDirPath, $rules, $messages, $sFileName) {
        file_exists($sDirPath) or mkdir($sDirPath, 0777, 1);
        // pr('asdfasd'); exit;
        $sNewFileName = '';
        //检验一下上传的文件是否有效.
        if (is_object($oFile) && $oFile->isValid()) {
            $ext = $oFile->guessClientExtension();
            $sOriginalName = $oFile->getClientOriginalName(); // 客户端文件名，包括客户端拓展名
            $sNewFileName = $sFileName . '.' . $ext; // 哈希处理过的文件名，包括真实拓展名
            move_uploaded_file($oFile->getRealPath(), $sDirPath . $sNewFileName);
//            $portrait      = Image::make($oFile->getRealPath());
            $oldImage = Input::get('oldimg');
            // pr('sfa');
            // pr($oldImage); exit;
            // 删除旧img
            File::delete(
                    public_path($oldImage)
            );

//            $portrait->save($sDirPath . $sNewFileName);
            return $sNewFileName;
            //pr($sNewFileName); exit;
        }
        return $sNewFileName;
    }

}
