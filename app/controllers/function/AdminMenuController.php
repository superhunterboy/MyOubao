<?php

class AdminMenuController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
//    protected $resourceView = 'default';

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'AdminMenu';

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = array(
        // 'title.required'   => '请输入菜单名。',
        // // 'title.alpha_dash' => '菜单名格式不正确。',
        // 'title.between'    => '菜单名长度请保持在:min到max位之间。',
        // // 'title.unique'     => '此用户名已被使用。',
        // 'disabled.in'      => '非法输入。',
        // 'new_win.in'       => '非法输入。',
        // 'sequence.numeric' => '必须是数字。'
    );

    protected $rules  = array(
        'title'     => 'required|between:1,30',
        'disabled'  => 'in:0,1',
        'new_window'   => 'in:0,1',
        'sequence'  => 'numeric'
    );

    /**
     * render the menu
     * @return reponse
     */
    public function display() {
//        exit;
        $menus = $this->getUserMenus();
        // pr(json_encode($menus));exit;
        return View::make('w.sideMenu')->with(compact('menus'));
    }

    public function getHeader()
    {
        // $menus = $this->getUserMenus();
        $title = __('_basic.app-name');
        // pr(json_encode($menus));exit;
        return View::make('w.header')->with(compact('title'));
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $oFunctionality = new Functionality;
        switch($this->action){
            case 'index':
            case 'view':
                $aMenuTree = & $this->model->getTitleList();
                $aFunctionalities = & $oFunctionality->getTitleList();
                break;
            case 'edit':
            case 'create':
                $this->model->getTree($aMenuTree,null,null,['title' => 'asc']);
                $oFunctionality->getTree($aFunctionalities, null, ['menu' => ['=', 1]], ['title' => 'asc']);
        }
        $this->setVars(compact('aMenuTree','aFunctionalities'));
    }

    /**
     * [getUserMenus 获取用户菜单数据]
     * @return [Array] [两层树形结构的array]
     */
    public function getUserMenus() {
        $aColumns = ['id', 'functionality_id', 'title', 'controller', 'action', 'params', 'new_window', 'sequence'];
        $rightIds = $this->getUserRights(3, true);
        // pr(json_encode($rightIds));exit;
        $aMainMenus = AdminMenu::whereNull('parent_id')->where('disabled', '=', 0)->orderBy('sequence', 'ASC')->get($aColumns);
//        print_r(count($aMainMenus));
//        exit;
//        $menus = AdminMenu::all();
        $menus = [];
        foreach ($aMainMenus as $oMainMenu) {
            $menus[$oMainMenu->id] = $oMainMenu->getAttributes();
            $menus[$oMainMenu->id]['children'] = [];
            $aSubMenus = AdminMenu::where('parent_id', '=', $oMainMenu->id)->where('disabled', '=', 0)->whereIn('functionality_id', $rightIds)->orderBy('sequence', 'ASC')->get($aColumns);
            foreach ($aSubMenus as $oMenu) {
                $route_name = $oMenu->controller . '@' . $oMenu->action;
//                echo $route_name . "<br>";
                // file_put_contents('/tmp/route', $route_name . "\n", FILE_APPEND);
                if ($route_name != '_main@_main')
                    $oMenu->route_name = $this->_getRouterName($route_name);
                $menus[$oMainMenu->id]['children'][] = $oMenu->getAttributes();
//                $menus[$oMainMenu->id]['children'][$oMenu->id] = $oMenu;
            }
        }
        unset($aSubMenus, $aMainMenus, $oMainMenu, $oMenu, $rightIds, $aColumns);
       // print_r($menus);
//        exit;
        return $menus;
    }

//     public function getMenu()
//     {
//         $menus = $this->getUserMenus();
//         $menus = isset($menus) ? $menus : [];
// //        pr($menus);
// //        exit;
//         return View::make('w.sideMenu')->with(compact('menus'));
//     }

    /**
     * 用表单数据填充模型实例
     */
    protected function _fillModelDataFromInput() {
        parent::_fillModelDataFromInput();
        $this->model->functionality_id or $this->model->functionality_id = null;
    }

}
