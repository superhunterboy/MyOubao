<?php

class BaseResource extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = '';

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $model = '';

    protected $composerViews = array(
        'view',
        'index',
        'create',
        'edit',
    );

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = array();

    /**
     * 初始化
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_getUserRole();
        $this->beforeFilter('enabled-actions');

        // 实例化资源模型
        $this->model  = App::make($this->model);
        // 公共内容
        $resource     = $this->resource;
        $resourceName = $this->resourceName;
        $menus        = null; // $this->getUserMenus();
        $buttons      = $this->_getButtons();
        $breadcrumb   = $this->_getBreadcrumb();
        // 视图合成器
        $views = [];
        foreach ($this->composerViews as $key => $value) {
            array_push($views, $this->resourceView . '.' . $value);
        }
        View::composer($views, function ($view) use ($resource, $resourceName, $menus, $buttons, $breadcrumb) {
            $view->with(compact('resource', 'resourceName', 'menus', 'buttons', 'breadcrumb'));
        });
    }

    /**
     * 资源列表页面
     * GET         /resource
     * @return Response
     */
    public function index($id = null, $isSub = false)
    {
        $datas = $this->model->orderBy('created_at', 'ASC')->paginate(15);
        return View::make($this->resourceView.'.index')->with(compact('datas'));
    }

    /**
     * 资源创建页面
     * GET         /resource/create
     * @return Response
     */
    public function create($cur_id = NULL)
    {
        if (Request::method() == 'PUT') {
            // 获取所有表单数据.
            $data   = Input::all();
            // 创建验证规则
            $unique = $this->unique();
            $rules  = array(
                # --- --- --- --- --- --- --- --- --- --- 此处添加验证规则 #
            );
            // 自定义验证消息
            $messages = $this->validatorMessages;
            // 开始验证
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->passes()) {
                // 验证成功
                // 添加资源
                $model = $this->model;
                # --- --- --- --- --- --- --- --- --- --- 此处为模型对象的属性赋值 #
                if ($model->save()) {
                    // 添加成功
                    return Redirect::back()
                        ->with('success', '<strong>'.$this->resourceName.'添加成功：</strong>您可以继续添加新'.$this->resourceName.'，或返回'.$this->resourceName.'列表。');
                } else {
                    // 添加失败
                    return Redirect::back()
                        ->withInput()
                        ->with('error', '<strong>'.$this->resourceName.'添加失败。</strong>');
                }
            } else {
                // 验证失败
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
        return View::make($this->resourceView.'.create');
    }

    /**
     * 资源展示页面
     * GET         /resource/{id}
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 资源编辑页面
     * GET         /resource/{id}/edit
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (Request::method() == 'PUT') {
            // 获取所有表单数据.
            $data = Input::all();
            // 创建验证规则
            $rules = array(
                # --- --- --- --- --- --- --- --- --- --- 此处添加验证规则 #
            );
            // 自定义验证消息
            $messages  = $this->validatorMessages;
            // 开始验证
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->passes()) {
                // 验证成功
                // 更新资源
                $model = $this->model->find($id);
                # --- --- --- --- --- --- --- --- --- --- 此处为模型对象的属性赋值 #
                if ($model->save()) {
                    // 更新成功
                    return Redirect::back()
                        ->with('success', '<strong>'.$this->resourceName.'更新成功：</strong>您可以继续编辑'.$this->resourceName.'，或返回'.$this->resourceName.'列表。');
                } else {
                    // 更新失败
                    return Redirect::back()
                        ->withInput()
                        ->with('error', '<strong>'.$this->resourceName.'更新失败。</strong>');
                }
            } else {
                // 验证失败
                return Redirect::back()->withInput()->withErrors($validator);
            }
        } else {
            $data = $this->model->find($id);
            return View::make($this->resourceView.'.edit')->with('data', $data);
        }
    }

    /**
     * 资源删除动作
     * DELETE      /resource/{id}
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = $this->model->find($id);
        if (is_null($data))
            return Redirect::back()->with('error', '没有找到对应的'.$this->resourceName.'。');
        elseif ($data->delete())
            return Redirect::back()->with('success', $this->resourceName.'删除成功。');
        else
            return Redirect::back()->with('warning', $this->resourceName.'删除失败。');
    }

    /**
     * 资源回收站
     * GET      /resource/recycled
     * @param  int  $id
     * @return Response
     */
    public function recycled()
    {
        //
    }

    /**
     * 资源还原动作
     * PATCH      /resource/{id}
     * @param  int  $id
     * @return Response
     */
    public function restore($id)
    {
        //
    }

    /**
     * 构造 unique 验证规则
     * @param  string $column 字段名称
     * @param  int    $id     排除指定 ID
     * @return string
     */
    protected function unique($column = null, $id = null, $extraParam = null)
    {
        $rule = 'unique:' . $this->resourceTable;
        if (!is_null($column))
            $rule .= ',' . $column;
        if (!is_null($id))
            $rule .= ',' . $id . ',id';
        else
            $rule .= ',NULL,id';
        if (!is_null($extraParam) && is_array($extraParam)) {
            foreach ($extraParam as $key => $value) {
                $rule .= ',' . $key . ',' . $value;
            }
        }
        return $rule;
    }

}