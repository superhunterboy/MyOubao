<?php

class DomainController extends AdminBaseController
{
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'Domain';
    protected $customViewPath = 'admin.domain';
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
        $this->langVars['title'] = __('_model.domain');
        $this->setVars(compact('aDomainTypes', 'aDomainStatus'));
        parent::beforeRender();
    }
    /**
     * [edit 自定义编辑，TODO 由于BaseController中的edit无法更改rule变量，暂用自定义的edit方法]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $this->model->fill($this->params);
        // pr($this->model->toArray());exit;
        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();

            if ($bSucc = $this->model->save()) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            // $table = Functionality::all();
            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit', 'id'));
            return $this->render();
        }
    }

}
