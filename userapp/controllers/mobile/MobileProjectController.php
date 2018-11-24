<?php

class MobileProjectController extends MobileBaseController {

    protected $modelName = 'UserProject';

    /**
     * [generateSelectorData 页面公用下拉框的生成参数]
     * @return [Array] [参数数组]
     */
    private function generateSelectorData() {
        $aSelectColumn = [
            ['name' => 'lottery_id', 'emptyDesc' => '所有游戏', 'desc' => '游戏名称：'],
            ['name' => 'way_group_id', 'emptyDesc' => '所有玩法群', 'desc' => '玩法群：'],
            ['name' => 'way_id', 'emptyDesc' => '所有玩法', 'desc' => '玩法：'],
        ];

        $aSelectorData = [
            'aSelectColumn' => $aSelectColumn,
            'sFirstNameKey' => 'name',
            'sSecondNameKey' => 'title',
            'sThirdNameKey' => 'title',
            'sDataFile' => 'series-way-groups-way-group-ways',
            'sExtraDataFile' => 'lottery-series',
            'sSelectedFirst' => trim(Input::get('lottery_id')),
            'sSelectedSecond' => trim(Input::get('way_group_id')),
            'sSelectedThird' => trim(Input::get('way_id')),
        ];
        return $aSelectorData;
    }

    /**
     * [index 投注列表]
     * @return [Response] [description]
     */
    public function index() {
        // pr($this->params);exit;
        if ($iCount = count($this->params))
            $this->generateSearchParams($this->params);
        if (Session::get('is_agent')) {
            $oUser = User::find(Session::get('user_id'));
            $aUsers = $oUser->getUsersBelongsToAgent();
            $aUserIds = array_map(function ($item) {
                return $item['id'];
            }, $aUsers->toArray());
            $aUserIds[] = Session::get('user_id');
            $this->params['user_id'] = implode(',', $aUserIds);
        } else {
            $this->params['user_id'] = Session::get('user_id');
        }
        $data = parent::mobileIndex(ManProject::$mobileColumns);
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    public function view($id) {
        $data = parent::view($id);
        $data = array_intersect_key($data, array_flip(array_merge(ManProject::$mobileColumns, ['coefficient', 'multiple','winning_number'])));
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
     * [generateSearchParams 生成自定义查询参数]
     * @param  [Array]     & $aParams [查询参数数组的引用]
     */
    private function generateSearchParams(& $aParams) {
        if (isset($aParams['number_value']) && $aParams['number_value']) {
            $aParams[$aParams['number_type']] = $aParams['number_value'];
        }
        unset($aParams['way_group_id'], $aParams['number_type'], $aParams['number_value']);
    }

    /**
     * 撤单
     * @param int $id
     * @return Redirect
     */
    function drop($id) {
        $oProject = UserProject::find($id);
        $Redirect = Redirect::route('projects.view', ['id' => $oProject->id]);
        if (empty($oProject)) {
            $this->halt(false, 'error', Project::ERRNO_PROJECT_MISSING);
        }
        if (Session::get('user_id') != $oProject->user_id) {
            $this->halt(false, 'error', Project::ERRNO_DROP_ERROR_NOT_YOURS);
        }
        $oAccount = Account::lock($oProject->account_id, $iLocker);
        if (empty($oAccount)) {
            $this->halt(false, 'error', Account::ERRNO_LOCK_FAILED);
        }
        DB::connection()->beginTransaction();
        $this->writeLog('begin DB Transaction');
        if (($iReturn = $oProject->drop()) != Project::ERRNO_DROP_SUCCESS) {
            $this->writeLog($iReturn);
            DB::connection()->rollback();
            $this->writeLog('Rollback');
            Account::unLock($oAccount->id, $iLocker, false);
            $this->halt(false, 'error', $iReturn);
        }
        DB::connection()->commit();
        $this->writeLog('Commit');
        $oProject->setCommited();    // 建立销售量更新任务
        Account::unLock($oAccount->id, $iLocker, false);
        $this->halt(true, 'success', Project::ERRNO_DROP_SUCCESS);
    }

}
