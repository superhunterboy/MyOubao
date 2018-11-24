<?php
/**
 * 奖金组控制器
 */
class PrizeGroupController extends AdminBaseController
{
    /**
     * self view path
     * @var string
     */
    protected $customViewPath = 'prize_group';
    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = ['create'];
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'PrizeGroup';

    
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aSeries',Series::getTitleList());
        $this->setVars('aLotteryTypes', Lottery::$validTypes);
    }

    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            isset($this->params['step']) or $this->params['step'] = 1;
            DB::connection()->beginTransaction();
            for($iPrize = $this->params['from']; $iPrize <= $this->params['to']; $iPrize += $this->params['step']){
                $aAttributes = [
                    'series_id' => $this->params['series_id'],
                    'classic_prize' => $iPrize
                ];
                $oPrizeGroup = new PrizeGroup($aAttributes);
                if (!$bSucc = $oPrizeGroup->save()){
                    break;
                }
//                pr($oPrizeGroup->getAttributes());
            }
            if ($bSucc){
                DB::connection()->commit();
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            }
            else{
                // pr($this->model->toArray());
                // pr('---------');
//                 pr($this->model->validationErrors);exit;
                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
//                pr($this->langVars);
//                exit;
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;
            if ($sModelName::$treeable){
                $sFirstParamName = 'parent_id';
            }
            else{
                foreach($this->paramSettings as $sFirstParamName => $tmp){
                    break;
                }
            }
//            pr($sFirstParamName);
//            exit;
//            $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));

            return $this->render();
        }
    }

}