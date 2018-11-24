<?php

class EnableTongHuiKaController extends AdminBaseController {
    protected $resourceView = 'huitongka';
    protected $modelName = 'SysConfig';
    protected function beforeRender() {
        parent::beforeRender();
    }
    public function index(){
        $tonghuika_enable = SysConfig::readValue('is_enable_tonghuika');
        $this->setVars('tonghuika_enable', $tonghuika_enable);

        if(Request::method() == 'POST'){
            $aData = Input::all();

            if(!isset($aData['tonghuika_enable'])) $tonghuika_enable = 0;
            else $tonghuika_enable = 1;
            if(SysConfig::setValue('is_enable_tonghuika',$tonghuika_enable))
                return $this->goBack('success', '设置成功！');
            else
                return $this->goBack('error', '设置失败！');
        }
        $this->render();
    }

}
