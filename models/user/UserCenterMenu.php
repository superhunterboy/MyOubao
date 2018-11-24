<?php

/**
 * 用户代金券
 */
class UserCenterMenu extends BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'UserCenterMenu';
    protected $table = 'user_center_menus';

    /**
     * the columns for list page
     * @var array
     */

    protected $fillable = [
        'title',
        'route_name',
        'route_params',
        'params',
        'parent_id',
        'parent_ids',
        'is_menu',
        'is_enable',
        'sequence',
    ];
    public static $columnForList = [
        'title',
        'route_name',
        'route_params',
        'params',
        'parent_id',
        'parent_ids',
        'is_menu',
        'is_enable',
        'sequence',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'title' => '',
        'route_name' => '',
        'route_params' => '',
        'params' => '',
        'parent_id' => 'integer',
//        'parent_ids' => 'integer',
        'is_menu' => 'in:0,1',
        'is_enable' => 'in:0,1',
    ];
    
    
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'parent_id'   => 'aMenus'
    ];
    
    /**
     * 获取菜单栏并且格式url
     * @param type $route   路由别名
     * @param type $param   参数
     */
    public static function getMenu() {
        $data = self::initMenuData();
        $nav = self::recursion($data);
        return $nav;
    }

    /**
     * 获取面包屑导航栏
     * @return type
     */
    public static function getNav() {
        $res = self::getCurrentData();
        if (! $res) {
            return ;
        }
        $formatData = self::formatData();
        $selfId = $res['id'];
        $parentIds = explode(',', $res['parent_ids']);
        $crumb = array();
        foreach ($parentIds as $k => $v) {
            if ( isset($formatData[$v]['title']) && $formatData[$v]['title']) {
                $crumb[] = $formatData[$v]['title'];
            }
        }
        if (isset($formatData[$selfId]['title']) && $formatData[$selfId]['title']) {
            $crumb[] = $formatData[$selfId]['title'];
        }
        return $crumb;
    }

    /**
     * 格式化url
     * @param type $data
     * @return type
     */
    public static function formatUrl($data) {
        $defaultParams = [
            'id' => Session::get('user_id'),
        ];
        if (isset($data) && isset($data['is_menu']) && $data['is_menu']) {
            $url_param = array();
            //传过来的路由别名等于数组中的路由别名
            if (is_array($data['route_params']) && count($data['route_params']) > 0) {
                foreach ($data['route_params'] as $r => $p) {
                    if (isset($defaultParams[$p])) {
                        $url_param[$r] = $defaultParams[$p];
                    }
                }
            }
            if (is_array($data['params']) && count($data['params']) > 0) {
                foreach ($data['params'] as $j => $param) {
                    $url_param[$j] = $param;
                }
            }

          try{
                $url = route($data['route_name'], $url_param);
                $data['url'] = $url;
           }  catch (Exception $e){}
        }
        return $data;
    }

    /**
     * 递归调用
     * @param type $list
     * @return type
     */
    public static function recursion($list) {
        foreach ($list as $k => $v) {
            if (!empty($v['route_name'])) {
                $list[$k] = self::formatUrl($v);
            }
            if (isset($v['children'])) {
                $list[$k]['children'] = self::recursion($v['children']);       //递归
            }
        }
        return $list;
    }

    /**
     * 获取当前位置
     */
    public static function getCurrentData() {
        $data = self::formatData();
        $result = self::recursionTitleAndMenu($data);
        return $result;
    }

    /**
     * 判断当前路由位置
     * @param type $list
     * @return type
     */
    public static function getRoute($list) {
            $sCurrentRouteName = Route::currentRouteName();     //获取当前路由名
            if ($list['route_name'] == $sCurrentRouteName) {       //判断两个路由是否相等
                $queryString = Input::all();
               // var_dump($queryString);exit();
                $aDefaultParamKeys = Route::current()->parameterNames();       //获取当前的路由参数名称
                $aVals = Route::current()->parameters();       //获取当前的路由参数名称
//                var_dump(count($list['route_params']) );exit();
                $flag = true;     //用来标志参数是否一致
                $total = self::where('route_name' ,$sCurrentRouteName)->count();
               if ($total > 1) {
                    if (count($queryString) > 0 &&  isset($list['params']) && $list['params']) {
                        foreach ($list['params'] as $a => $param) {
                            if (isset($queryString[$a]) && $queryString[$a]) {
                                if ($queryString[$a] != $param) {      //判断传过来的参数是否存在
                                    $flag = false;
                                    break;
                                }
                            }
                        }
                    }
                    if (count($aDefaultParamKeys) > 0 && $aVals && isset($list['route_params']) && $list['route_params']) {
                        foreach ($list['route_params'] as $r => $route_param) {
                            if (isset($aDefaultParamKeys) && $aDefaultParamKeys) {
                                if (!in_array($route_param, $aDefaultParamKeys)) {      //判断传过来的参数是否存在
                                    $flag = false;
                                    break;
                                }
                            }
                        }
                    }
                  
                }
                if ($flag) {
                    return $list;
                }
            }
    }

    /**
     * 获取title和menu
     * @param type $nav
     * @return type
     */
    public static function recursionTitleAndMenu($nav) {
        foreach ($nav as $list) {
                $route = self::getRoute($list);
                if (is_array($route)) {
                    return $route;
                }
        }
    }

    /**
     * 初始化数据
     * @return type
     */
    public static function initMenuData() {
        $nav = self::where('is_enable', 1)->orderBy('sequence', 'desc')->get()->toArray();
        $data = self::createTree($nav);
        return $data;
    }

    /**
     * 创建树形数组
     * @param type $array
     * @param type $parentid
     * @return type
     */
    public static function createTree($array, $parentid = 0) {
        $result = array();
        foreach ($array as $key => $val) {
            if ($val['parent_id'] == $parentid && $val['is_menu']) {
                $tmp = $array[$key];
                $tmp['url'] = '';
                if (isset($val['params']) && $val['params']) {
                    $tmp['params'] = json_decode($val['params'], true);
                }
                if (isset($val['route_params']) && $val['route_params']) {
                    $tmp['route_params'] = json_decode($val['route_params'], true);
                }
                unset($array[$key]);
                count(self::createTree($array, $val['id'])) > 0 && $tmp['children'] = self::createTree($array, $val['id']);
                $result[$key] = $tmp;
            }
        }
        return $result;
    }

    /**
     * 格式化原始数组
     * @return type
     */
    public static function formatData() {
        $nav = self::where('is_enable', 1)->get()->toArray();
        $formatData = array();
        foreach ($nav as $k => $v) {
            $formatData[$v['id']] = $v;
            if ( isset($v['params']) && $v['params'] ) {
                  $formatData[$v['id']]['params'] = json_decode($v['params'], true);
            }
            if ( isset($v['route_params']) && $v['route_params'] ) {
                  $formatData[$v['id']]['route_params'] = json_decode($v['route_params'], true);
            }
        }
        return $formatData;
    }
    
    /**
     * 获取所有的菜单
     */
    public static function getAllMenus(){
            $aMenus = self::where('is_enable', 1)->get(array('id', 'title'))->toArray();
            $aData = array();
            foreach ($aMenus as $k=>$v) {
                    $aData[$v['id']] = $v['title'];
            }
            return $aData;
    }
    
    /**
     * 获取pid
     * @param type $id
     */
    public static function getPid($id){
        return self::where('id', $id)->first();
    }
    
    /**
     * 获取最后一条id
     */
    public static function getLastId(){
        return self::orderBy('id', 'desc')->take(1)->first();
    }
    
    /**
     * 更新child_ids
     * @param type $id
     * @param type $childIds
     */
    public static  function updateChilds($id, $childIds){
        return self::where('id', $id)->update(array('child_ids' => $childIds));
    }
    
    public static function updatePids($ids, $pids){
        if (empty($ids) || empty($pids)) {
            return false;
        }
        $aIds = explode(',', $ids);
        $sRow = self::whereIn('id',$aIds)->update(['parent_ids'=>$pids]); 
        foreach($aIds as $k=>$v){
             $oChildsNow = self::getPid($v);
             if ($oChildsNow->parent_ids) {
                 $pids = $oChildsNow->parent_ids . ',' .$v;
             }else{
                 $pids = $v;
             }
             if ($oChildsNow->child_ids) {
                    return self::updatePids($oChildsNow->child_ids, $pids);
             }
        }
        if ($sRow) {
            return true;
        }else{
           return false; 
        }
    }
}
