<?php

class AgentDomainController extends AdminBaseController
{
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'AgentDomain';
    //protected $customViewPath = 'admin.domain';
    protected $customViews = [
        'create', 'edit', 'view'
    ];
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        $aDomainTypes = [];
        $aDomainStatus = [];
        $aDomainGroup = [];
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


}
