<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-1-15
 * Time: 下午4:23
 */
class JackpotsController extends AdminController
{
    protected $resourceView = 'jackpot';
    protected $modelName = 'Jackpots';
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;

        switch($this->action){
            case 'edit':
            case 'create':
                $aLotteries = Lottery::getBmLotteries();
                $this->setVars('aLotteries', $aLotteries);
        }
    }

    /**
     * 用表单数据填充模型实例
     */
    protected function _fillModelDataFromInput() {
//        $this->model = $id ? $this->model->find($id) : $this->model;
        $data = $this->params;
        $sModelName = $this->modelName;
        !empty($this->model->columnSettings) or $this->model->makeColumnConfigures();
        foreach ($this->model->columnSettings as $sColumn => $aSettings) {
            if ($sColumn == 'id')
                continue;
            if (!isset($aSettings['type']))
                continue;
            switch ($aSettings['type']) {
                case 'bool':
                case 'numeric':
                case 'integer':
                    !empty($data[$sColumn]) or $data[$sColumn] = 0;
                    break;
                case 'select':
                    if (isset($data[$sColumn]) && is_array($data[$sColumn])) {
                        sort($data[$sColumn]);
                        $data[$sColumn] = implode(',', $data[$sColumn]);
                    }
            }
        }

        $id = isset($this->model->id) ? $this->model->id : '';
        if(isset($data['lotteries_limit']) && !empty($data['lotteries_limit'])) {
            $iCount = Jackpots::isExist($id,$data['lotteries_limit']);

            if($iCount) $data['lotteries_limit'] = '';
        }

        $this->model = $this->model->fill($data);

        if ($sModelName::$treeable) {
            $this->model->parent_id or $this->model->parent_id = null;
            if ($sModelName::$foreFatherColumn) {
                $this->model->{$sModelName::$foreFatherColumn} = $this->model->setForeFather();
            }
        }
    }

    /**
     * 资源编辑页面
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.update-fail', $this->langVars).' 或者该彩种已经存在');
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