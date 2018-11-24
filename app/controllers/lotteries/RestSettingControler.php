<?php

class RestSettingController extends AdminBaseController {

    /**
     * 资源视图目录
     * @var string
     */
    protected $customViewPath = 'lottery.reset';
    protected $resourceName = 'Rest Setting';
    protected $customViews = [
        'create',
        'edit',
    ];

    /**
     * 模型容器名称，初始化后转为模型实例
     * @var string | Illuminate\Database\Eloquent\Model
     */
//    protected $container = 'LotteryContainer';
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'RestSetting';

//    public function __construct() {
//        parent::__construct();
//        $this->container = App::make($this->container);
//    }
//    public function index() {
//        $aLotteries = $this->container->getData();
//        return View::make($this->resourceView.'.index')->with(compact('aLotteries'));
//    }

    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            if ($bSucc = $this->saveData($id)) {
                return $this->goBackToIndex('success', __(sprintf("%s create successful", $this->resourceName)));
            } else {
                if (Config::get('app.debug')) {
                    pr($this->model->validationErrors);
                    exit;
                }
                return $this->goBack('error', __(sprintf("%s create failed", $this->resourceName)), true);
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $this->setVars('lottery_id', $id);
            $sModelName = $this->modelName;
            if ($sModelName::$treeable) {
                $sFirstParamName = 'parent_id';
            } else {
                foreach ($this->paramSettings as $sFirstParamName => $tmp) {
                    break;
                }
            }
//            $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));

            return $this->render();
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
                return $this->goBack('error', __('_basic.update-fail', $this->langVars));
            }
        } else {
            // $table = Functionality::all();
            $parent_id = $this->model->parent_id;
            $data = $this->model;
            $this->setVars('lottery_id', $data->lottery_id);
            $isEdit = true;
            $this->setVars(compact('data', 'parent_id', 'isEdit', 'id'));
            return $this->render();
        }
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aLotteries', Lottery::getTitleList());
        $this->setVars('aRestType', RestSetting::getRestType());
    }

}
