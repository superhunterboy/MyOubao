<?php

/**
 * 后台管理系统基础控制器
 *
 * @author frank
 */
class AdminBaseController extends BaseController {

    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $this->setVars('aWeightFields', $sModelName::$weightFields);
        $this->setVars('aClassGradeFields', $sModelName::$classGradeFields);
        $this->setVars('aFloatDisplayFields', $sModelName::$floatDisplayFields);
        $aTotalsAllPages = [];
        if ($columnsAllPages = $sModelName::$totalColumnsAllPages) {
            $this->setVars('aTotalColumnsAllPages', $sModelName::$totalColumnsAllPages);
            foreach ($sModelName::$columnForList as $column)
                $aTotalsAllPages[$column . "_sum"] = null;

            $result = $this->getSumData($columnsAllPages);
            $aTotalsAllPages = array_merge($aTotalsAllPages, $result);
            $this->setVars(compact('aTotalsAllPages'));
        }
    }

    /**
     * 检查是否登录
     * @return bool
     */
    protected function checkLogin() {
        return boolval(Session::get('admin_user_id'));
    }

    /**
     * 如果未登录时执行的动作
     * @return type
     */
    protected function doNotLogin() {
        if ($this->isAjax) {
            $this->halt(false, 'loginTimeout', Config::get('global_error.ERRNO_LOGIN_EXPIRED'));
        } else {
            return Redirect::route('admin-signin');
        }
    }

    /**
     * 获取可访问的功能ID数组
     *
     * @return Array              根据$returnType得到的不同数组
     */
    protected function & getUserRights() {
        $roleIds = Session::get('CurUserRole');
        $aRights = & AdminRole::getRightsOfRoles($roleIds);
        return $aRights;
    }

    /**
     * 生成面包屑导航
     * @return array
     */
    protected function _getBreadcrumb() {
        return [];
    }

    /**
     * 获取指定角色ID范围所拥有的权限集合
     * @param array $aRoleIds
     * @return array
     */
    public function & getRights($aRoleIds = array()) {
        $aRoles = AdminRole::whereIn('id', $aRoleIds)->get(array('id', 'rights'));
        $aRights = [];
        foreach ($aRoles as $oRole) {
            $aRights = array_merge($aRights, explode(',', $oRole->rights));
        }
        $aRights = array_unique($aRights);
        return $aRights;
    }

    public function __destruct() {
        parent::__destruct();
        if (is_object($this->functionality)) {
            $oAdminLog = new AdminLog;
            $oAdminLog->functionality_id = $this->functionality->id;
            $oAdminLog->functionality_title = $this->functionality->title;
            $oAdminLog->controller = $this->functionality->controller;
            $oAdminLog->action = $this->functionality->action;
            $oAdminLog->admin_id = Session::get('admin_user_id');
            $oAdminLog->admin_name = Session::get('admin_username');
            $oAdminLog->request_uri = $_SERVER['REQUEST_URI'];

            empty($this->params) or $oAdminLog->request_data = json_encode($this->params);

            $oAdminLog->created_at = date('Y-m-d H:i:s');
            $msg = var_export($oAdminLog->toArray(), true);
            $sFile = implode(DIRECTORY_SEPARATOR, ['/tmp/AdminLog', date("Ymd")]);
            if (!file_exists($sFile)) {
                @mkdir($sFile, 0777, true);
            }
            file_put_contents($sFile . DIRECTORY_SEPARATOR . $oAdminLog->admin_id, $msg . PHP_EOL, FILE_APPEND);
//            $oAdminLog->save();
        }
    }

    /**
     * [getSumData 获取统计值]
     * @param  [Array]  $aSumColumns [待统计的列]
     * @param  [boolean] $bPerPage   [是否按页统计，该功能采用视图中操作每页数据的方式实现，以前的逻辑暂时注释掉]
     * @return [Array]               [统计数据]
     */
    public function getSumData($aSumColumns, $bPerPage = false) {
        // TODO 和BaseController中的查询有所重复，后续改进
        $aConditions = & $this->makeSearchConditions();
        $oQuery = $this->model->doWhere($aConditions);
        $aRawColumns = [];
        // $aParams     = array_values($this->params);
        foreach ($aSumColumns as $key => $value) {
            $aRawColumns[] = DB::raw('SUM(' . $value . ') as ' . $value . '_sum');
        }
        $aSum = [];

        $aSum = $oQuery->get($aRawColumns)->toArray();
        if (count($aSum))
            $aSum = $aSum[0];

        return $aSum;
    }

}
