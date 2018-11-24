<?php

class MbankController extends AdminBaseController {
                    protected $modelName = 'Mbank';
//                     protected $resourceView = 'mbank';
                  //  protected $customViewPath = 'mbank';
                  //  protected $customViews = ['create'];
                    protected function beforeRender() {
                        parent::beforeRender();
                         $aBankName = Mbank::getAllBankName();
//                        $aBankName = Bank::getAllBankInfo();
                         $mode = Mbank::getAllMode();
                         $this->setVars(compact('aBankName',  'mode'));
                    }
           
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
                            return parent::index();
	}
                    

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
//	public function create($id = null){
//                        if ($_POST){
//                            $id = $this->params['name'];
//                            $oBank = Bank::find($id);
//                            $this->params['id'] = $oBank->id;
//                            $this->params['name'] = $oBank->name;
//                        }
//                        parent::create();
//	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
//	public function store()
//	{
//		//
//	}
//

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
//	public function show($id)
//	{
//		//
//	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
//	public function edit($id)
//	{
//                      
//	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
//	public function update($id)
//	{
//		//
//	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
//	public function destroy($id)
//	{
//		//
//	}
        
        
        public function editMbank($id){
            $oBank = Mbank::isMbank($id);
            if ( empty($oBank) ) {
                 $this->langVars['reason'] = & $this->model->getValidationErrorString();
                 return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
            if ( $oBank->mode != Bank::BANK_MODE_BANK_CARD && $oBank->mode != Bank::BANK_MODE_ALL ) {
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                 return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
            $oMbank = Mbank::getCheckMbank($id);
            if( empty($oMbank) ){
                    $this->action='create';
                    return $this->create($id);
            }else{
                  $this->action='edit';
                  return $this->edit($id);
            }
        }

/**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null) {
        if (Request::method() == 'POST') {
            DB::connection()->beginTransaction();
            $this->model->bank_id = $id;
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
//                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
                return Redirect::route('banks.index');
            } else {

                DB::connection()->rollback();
                $this->langVars['reason'] = & $this->model->getValidationErrorString();

                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;

            list($sFirstParamName, $tmp) = each($this->paramSettings);
      
            !isset($sFirstParamName) or $this->setVars($sFirstParamName, $id);
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
        $oMbank = Mbank::getCheckMbank($id);
        $this->model = $this->model->find($oMbank->id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if (Request::method() == 'PUT') {
            DB::connection()->beginTransaction();
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                 return Redirect::route('banks.index');
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
