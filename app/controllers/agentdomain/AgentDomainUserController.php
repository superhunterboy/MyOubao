<?php

class AgentDomainUserController extends AdminBaseController
{
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'AgentDomainUser';
    //protected $customViewPath = 'admin.domain';
    public $resourceView = 'default';
    protected $customViews = [
        'create', 'edit', 'view'
    ];
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        $aDomainTypes = [];
        $aDomainStatus = [];
        foreach(Domain::$aDomainTypes as $key => $value) {
            $aDomainTypes[$key] = __('_domain.' . $value);
        }

        foreach(Domain::$aDomainStatus as $key => $value) {
            $aDomainStatus[$key] = __('_domain.' . $value);
        }
        $data = AgentDomainGroup::all();
        foreach($data as $key => $d){
            $aDomainGroup[$d->id] = $d->group_name;
        }

        $this->langVars['title'] = __('_model.domain');
        $this->setVars(compact('aDomainTypes', 'aDomainStatus','aDomainGroup'));
        parent::beforeRender();
    }
    public function index(){

        $this-> resourceView = 'admin.domain';
        return parent::index();
    }

    public function createNew($id=null){
        if (Request::method() == 'POST') {

            $this->model->fill($this->params);
            DB::connection()->beginTransaction();
            if ($bSucc = $this->model->save()) {
                DB::connection()->commit();
                return $this->goBack('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            $this-> resourceView = 'admin.domain';

            $oUser = UserUser::find($id);
            $oData = AgentDomainUser::where('user_id',$id)->get()->first();
            if($oData){
                $this-> resourceView = 'default';
                $this->action='edit';
                return $this->edit($oData->id);
            }else{
                $this->setVars(compact('oUser'));
                return $this->render();
            }

        }

    }
}
