<?php

class AgentDomainGroupController extends AdminBaseController
{
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'AgentDomainGroup';
    //protected $customViewPath = 'admin.domain';
    protected $customViews = [
        'create', 'edit', 'view'
    ];
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        $aGroupStatus = [];
        $aDomainTypes = [];
        foreach(AgentDomainGroup::$aGroupStatus as $key => $value) {
            $aDomainTypes[$key] = __('_domain.' . $value);
        }

        foreach(AgentDomainGroup::$aGroupStatus as $key => $value) {
            $aGroupStatus[$key] = __('_domain.' . $value);
        }
        $this->langVars['title'] = __('_model.domain');
        $this->setVars(compact('aDomainTypes', 'aGroupStatus'));
        parent::beforeRender();
    }


}
